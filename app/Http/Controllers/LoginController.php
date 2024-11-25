<?php

namespace App\Http\Controllers;

use App\Models\UsersModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\ActiveMenuModel;

class LoginController extends Controller
{
    public function loginPage(){
        return view('layouts.loginPage');
    }

    public function fnLogin(Request $request){
        $username = $request->username;
        $password = $request->password;

        $users = UsersModel::where('username', $username)->first();
        if(!empty($username && $password)){
            if (!empty($users) && $users->status == 1) {
                if(Hash::check(($password), optional($users)->password)){
                    if(Auth::guard('users')->loginUsingId($users->id, true)){

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

                        // after login go to HomePage
                        return redirect('');
                        
                    } else {
                        return redirect()->back()->with('error', value: 'Something went wrong.');
                    }
                } else {
                    return redirect()->back()->with('error', 'Your password is incorrect.');
                }
            } else {
                return redirect()->back()->with('error', 'Your account does not exist.');
            }
        } else {
            return redirect()->back()->with('error', 'Please Enter Your Username and Password.');
        }
    }

    public function fnLogout(){
        Auth('users')->logout();
        return redirect('login');
    }
}
