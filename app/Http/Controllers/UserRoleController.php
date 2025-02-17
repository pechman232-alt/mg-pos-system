<?php

namespace App\Http\Controllers;

use App\Models\ActiveMenuModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserRolesModel;
use App\Models\MenuModel;

class UserRoleController extends Controller
{
    public function userRoleIndex(){
        $userRoles = UserRolesModel::get();
        return view('UserRoles.user_role_index',['userRoles'=>$userRoles]);
    }

    public function formUserRole(){
        $mainMenus = MenuModel::where('sub_of', '0')->get();
        $subMenus = MenuModel::where('sub_of', '!=', '0')->get();
        return view('UserRoles.add_role',['mainMenus'=>$mainMenus, 'subMenus'=>$subMenus]);
    }

    public function storeUserRole(Request $request){
        // if(Auth::guard('users')->check()){
        //     return ("Add User Roles");
        // } else {
        //     return ("Ot mean user te");
        // }
        $request->validate([
            'name' => 'required',
        ],
        ['name' => 'Please Enter UserRole Name!']
        );

        $role_name = $request->name;
        $ch_main = $request->ch_main;
        $ch_sub = $request->ch_sub;

        $userRole = new UserRolesModel();
        $userRole->name = $role_name;
        $userRole->save();

        if(!empty($ch_main)) {
            for($i = 0; $i < count(value: $ch_main); $i++){
                $main = New ActiveMenuModel();
                $main->role_id = $userRole->id;
                $main->menu_id = $ch_main[$i];
                $main->save();
            }
        }

        if(!empty($ch_sub)) {
            for($i = 0; $i < count($ch_sub); $i++){
                $main = New ActiveMenuModel();
                $main->role_id = $userRole->id;
                $main->menu_id = $ch_sub[$i];
                $main->save();
            }
        }
        return redirect('create/roles')->with('success','The Role is Created Successfully');
    }

    public function fnEditUserRole($id){
        $ID = decrypt($id);
        if(Auth::guard('users')->check()){
            $data = ActiveMenuModel::where('role_id', $ID)->get();
            $userRole = UserRolesModel::where('id', $ID)->first();
            $mainMenus = MenuModel::where('sub_of', '0')->get();
            $subMenus = MenuModel::where('sub_of', '!=', '0')->get();

            foreach ($mainMenus as $i1 => $m){
                foreach ($data as $i2 => $d){
                    if ($m->id == $d->menu_id){
                        $mainMenus[$i1]['checked'] = '1';
                    }
                }
            }

            foreach ($subMenus as $i1 => $m){
                foreach ($data as $i2 => $d){
                    if ($m->id == $d->menu_id){
                        $subMenus[$i1]['checked'] = '1';
                    }
                }
            }
            return view('UserRoles.edit_role',['mainMenus'=>$mainMenus, 'subMenus'=>$subMenus, 'userRole'=>$userRole]);
        } else {
            return redirect('login');
        }
    }

    public function fnUpdateUserRole(Request $request, $id){

        $ID = decrypt($id);

        $role_name = $request->name;
        $ch_main = $request->ch_main;
        $ch_sub = $request->ch_sub;

        $userRole = UserRolesModel::where('id', $ID)->firstorfail();
        $userRole->name = $role_name;
        $userRole->save();

        // Delete before add new(Call update)
        $main_d = ActiveMenuModel::where('role_id', $ID)->delete();

        if(!empty($ch_main)) {
            for($i = 0; $i < count($ch_main); $i++){
                $main = New ActiveMenuModel();
                $main->role_id = $userRole->id;
                $main->menu_id = $ch_main[$i];
                $main->save();
            }
        }

        if(!empty($ch_sub)) {
            for($i = 0; $i < count($ch_sub); $i++){
                $main = New ActiveMenuModel();
                $main->role_id = $userRole->id;
                $main->menu_id = $ch_sub[$i];
                $main->save();
            }
        }

        $activeMenus = ActiveMenuModel::selectRaw(
            'menus.id as menuid,
            menus.name as menuName,
            menus.route,
            menus.icon
            '
        )
        ->join('menus', 'menus.id', '=', 'active_menus.menu_id')
        ->where('active_menus.role_id', auth('users')->user()->role_id)
        ->where('menus.sub_of', '=' ,'0')
        ->get();

        session()->put('activeMenus', $activeMenus);

        $activeSubMenus = ActiveMenuModel::selectRaw(
            'menus.id as menu,
            menus.name as menuName,
            menus.sub_of,
            menus.route
            ',
        )
        ->join('menus', 'menus.id', '=', 'active_menus.menu_id')
        ->where('active_menus.role_id', auth('users')->user()->role_id)
        ->where('menus.sub_of', '!=', '0')
        ->get();

        session()->put('activeSubMenus', $activeSubMenus);

        return redirect('userRoles')->with('info', 'The Role is updated successfully.');
    }
}
