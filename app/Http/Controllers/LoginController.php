<?php

namespace App\Http\Controllers;

use App\Models\UsersModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // 🌟 កុំភ្លេច Import DB
use App\Models\ActiveMenuModel;

class LoginController extends Controller
{
    public function loginPage(){
        return view('layouts.loginPage');
    }

    public function fnLogin(Request $request){
        $username = $request->username;
        $password = $request->password;

        // កែសម្រួល Syntax PHP ត្រង់នេះឱ្យបានត្រឹមត្រូវ
        if(!empty($username) && !empty($password)){
            
            $users = UsersModel::where('username', $username)->first();
            
            if (!empty($users) && $users->status == 1) {
                
                // ដកវង់ក្រចកដែលមិនចាំបាច់ចេញពី $password
                if(Hash::check($password, optional($users)->password)){
                    
                    // 🌟 ជំហានទី១៖ លុប Session ចាស់ៗរបស់ User នេះចេញពី Database ទាំងអស់
                    DB::table('sessions')->where('user_id', $users->id)->delete();

                    if(Auth::guard('users')->loginUsingId($users->id, true)){

                        // 🌟 ជំហានទី២៖ បង្កើត Session ID ថ្មី
                        $request->session()->regenerate();

                        // 🌟 ជំហានទី៣៖ (គន្លឹះសំខាន់) ចាប់បង្ខំឱ្យ Update user_id ចូលទៅក្នុង table sessions 
                        // ដើម្បីឱ្យប្រាកដថាពេលមានគេ Login ចូលក្រោយ កូដលុបខាងលើអាចដំណើរការបាន
                        DB::table('sessions')
                            ->where('id', session()->getId())
                            ->update(['user_id' => $users->id]);

                        $activeMenus = ActiveMenuModel::selectRaw(
                            'menus.id as menuid,
                            menus.name as menuName,
                            menus.route,
                            menus.icon'
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
                            menus.route'
                        )
                        ->join('menus', 'menus.id', '=', 'active_menus.menu_id')
                        ->where('active_menus.role_id', auth('users')->user()->role_id)
                        ->where('menus.sub_of', '!=', '0')
                        ->get();
                
                        session()->put('activeSubMenus', $activeSubMenus);

                        // 🌟 ជំហានទី៤៖ Role-Based Redirect (បោះទៅកាន់ Dashboard តាមផ្នែក)
                        $roleId = (int) auth('users')->user()->role_id;

                        // ១. សម្រាប់ផ្នែកសេវាកម្ម (Service / Workshop)
                        // ចំណាំ៖ សូមដូរលេខ 3, 4 ទៅជា Role ID របស់ Service នៅក្នុង Database របស់បង
                        if (in_array($roleId, [10, 11, 14, 12])) {
                            return redirect()->route('services.index'); // Route ទៅ Service
                        }

                        // ២. សម្រាប់ផ្នែកលក់ (Showroom / Sales)
                        // ចំណាំ៖ សូមដូរលេខ 2, 5 ទៅជា Role ID របស់ Sales នៅក្នុង Database របស់បង
                        if (in_array($roleId, [2, 5])) {
                            // បើសិនជាបងមិនទាន់មានឈ្មោះ Route ទេ អាចប្រើ URL ផ្ទាល់ដូចជា return redirect('/sales'); ក៏បាន
                            return redirect('/sales'); 
                        }

                        if (in_array($roleId, [18, ])) {
                            // for user pos 
                            return redirect('/pos/checkout'); 
                        }

                        // ៣. សម្រាប់ Admin (1), IT (6) និង Group Manager (11) (ចូលទៅទំព័រដើម / Default)
                        return redirect('');
                        
                    } else {
                        return redirect()->back()->with('error', 'Something went wrong.');
                    }
                } else {
                    return redirect()->back()->with('error', 'Your password is incorrect.');
                }
            } else {
                return redirect()->back()->with('error', 'Your account does not exist or is inactive.');
            }
        } else {
            return redirect()->back()->with('error', 'Please Enter Your Username and Password.');
        }
    }

    public function fnLogout(Request $request){
        Auth::guard('users')->logout();
        
        // 🌟 Clear Session និង CSRF Token ពេល Logout ដើម្បីសុវត្ថិភាព
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login');
    }
}