<?php

namespace App\Http\Controllers;

use App\Models\ActiveMenuModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserRolesModel;
use App\Models\MenuModel;
use Illuminate\Support\Facades\DB;

class UserRoleController extends Controller
{
    public function userRoleIndex()
    {
        $userRoles = UserRolesModel::orderBy('id', 'desc')->get();
        return view('UserRoles.user_role_index', compact('userRoles'));
    }

    public function formUserRole()
    {
        $mainMenus = MenuModel::where('sub_of', '0')->get();
        $subMenus = MenuModel::where('sub_of', '!=', '0')->get();
        return view('UserRoles.add_role', compact('mainMenus', 'subMenus'));
    }

    public function storeUserRole(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:user_roles,name',
        ], [
            'name.required' => 'សូមបញ្ចូលឈ្មោះតួនាទី (Role Name)!',
            'name.unique' => 'ឈ្មោះតួនាទីនេះមានរួចហើយ!'
        ]);

        try {
            DB::beginTransaction();

            $userRole = new UserRolesModel();
            $userRole->name = $request->name;
            $userRole->save();

            // រៀបចំទិន្នន័យសម្រាប់ Bulk Insert (លឿនជាង save ក្នុង loop)
            $permissions = [];
            
            if ($request->has('ch_main')) {
                foreach ($request->ch_main as $menu_id) {
                    $permissions[] = ['role_id' => $userRole->id, 'menu_id' => $menu_id];
                }
            }

            if ($request->has('ch_sub')) {
                foreach ($request->ch_sub as $menu_id) {
                    $permissions[] = ['role_id' => $userRole->id, 'menu_id' => $menu_id];
                }
            }

            if (!empty($permissions)) {
                ActiveMenuModel::insert($permissions);
            }

            DB::commit();
            return redirect('/userRoles')->with('success', 'បង្កើតតួនាទីថ្មីបានជោគជ័យ។');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'មានបញ្ហាបច្ចេកទេស៖ ' . $e->getMessage());
        }
    }

    public function fnEditUserRole($id)
    {
        $ID = decrypt($id);
        
        if (Auth::guard('users')->check()) {
            $userRole = UserRolesModel::findOrFail($ID);
            $activeMenuIds = ActiveMenuModel::where('role_id', $ID)->pluck('menu_id')->toArray();

            $mainMenus = MenuModel::where('sub_of', '0')->get();
            $subMenus = MenuModel::where('sub_of', '!=', '0')->get();

            // បន្ថែម Attribute 'checked' ទៅឱ្យ Menu
            $mainMenus->map(function ($menu) use ($activeMenuIds) {
                $menu->checked = in_array($menu->id, $activeMenuIds) ? '1' : '0';
                return $menu;
            });

            $subMenus->map(function ($menu) use ($activeMenuIds) {
                $menu->checked = in_array($menu->id, $activeMenuIds) ? '1' : '0';
                return $menu;
            });

            return view('UserRoles.edit_role', compact('mainMenus', 'subMenus', 'userRole'));
        }
        
        return redirect('login');
    }

    public function fnUpdateUserRole(Request $request, $id)
    {
        $ID = decrypt($id);

        $request->validate([
            'name' => 'required|unique:user_roles,name,' . $ID,
        ]);

        try {
            DB::beginTransaction();

            $userRole = UserRolesModel::findOrFail($ID);
            $userRole->name = $request->name;
            $userRole->save();

            // លុបសិទ្ធិចាស់ៗចេញ
            ActiveMenuModel::where('role_id', $ID)->delete();

            // បញ្ចូលសិទ្ធិថ្មី (Bulk Insert)
            $permissions = [];
            $all_menus = array_merge($request->ch_main ?? [], $request->ch_sub ?? []);
            
            foreach ($all_menus as $menu_id) {
                $permissions[] = ['role_id' => $ID, 'menu_id' => $menu_id];
            }

            if (!empty($permissions)) {
                ActiveMenuModel::insert($permissions);
            }

            // 🔄 បច្ចុប្បន្នភាព Session Menu ភ្លាមៗ (Optional: បើ User កំពុង Login នឹងឃើញការប្រែប្រួល)
            $this->refreshUserSessionMenus($ID);

            DB::commit();
            return redirect('userRoles')->with('info', 'តួនាទីត្រូវបានធ្វើបច្ចុប្បន្នភាពជោគជ័យ។');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'ការ Update បរាជ័យ!');
        }
    }

    // Helper Function សម្រាប់ Refresh Menu ក្នុង Session
    private function refreshUserSessionMenus($roleId)
    {
        if (auth('users')->check() && auth('users')->user()->role_id == $roleId) {
            $activeMenus = DB::table('active_menus')
                ->join('menus', 'menus.id', '=', 'active_menus.menu_id')
                ->where('active_menus.role_id', $roleId)
                ->where('menus.sub_of', '0')
                ->select('menus.id as menuid', 'menus.name as menuName', 'menus.route', 'menus.icon')
                ->get();

            $activeSubMenus = DB::table('active_menus')
                ->join('menus', 'menus.id', '=', 'active_menus.menu_id')
                ->where('active_menus.role_id', $roleId)
                ->where('menus.sub_of', '!=', '0')
                ->select('menus.id as menu', 'menus.name as menuName', 'menus.sub_of', 'menus.route')
                ->get();

            session()->put('activeMenus', $activeMenus);
            session()->put('activeSubMenus', $activeSubMenus);
        }
    }

    public function fnDeleteUserRole($id)
{
    try {
        // ១. បកលេខកូដ ID (ព្រោះយើងបញ្ជូនមកជា Encrypted String)
        $ID = decrypt($id); 

        DB::beginTransaction();

        // ២. លុបសិទ្ធិ (Menus) ដែលជាប់ជាមួយ Role នេះជាមុនសិន ដើម្បីកុំឱ្យទិន្នន័យហល់
        \App\Models\ActiveMenuModel::where('role_id', $ID)->delete();

        // ៣. លុប Role នេះចេញពី Database
        $role = \App\Models\UserRolesModel::findOrFail($ID);
        $role->delete();

        DB::commit();
        
        return redirect('/userRoles')->with('success', 'តួនាទីត្រូវបានលុបចេញពីប្រព័ន្ធដោយជោគជ័យ!');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect('/userRoles')->with('error', 'មិនអាចលុបបានទេ! ប្រហែលជាមានបុគ្គលិកកំពុងប្រើប្រាស់ Role នេះ។');
    }
}
}