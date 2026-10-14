<?php

namespace App\Http\Controllers;

use App\Models\UsersModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ActiveMenuModel;

class LoginController extends Controller
{
    public function loginPage(){
        return view('layouts.loginPage');
    }

    public function fnLogin(Request $request){
        $username = $request->username;
        $password = $request->password;

        if(!empty($username) && !empty($password)){
            
            $users = UsersModel::where('username', $username)->first();
            
            if (!empty($users) && $users->status == 1) {
                
                if(Hash::check($password, optional($users)->password)){
                    
                    // លុប Session ចាស់ៗរបស់ User នេះចេញពី Database
                    DB::table('sessions')->where('user_id', $users->id)->delete();

                    if(Auth::guard('users')->loginUsingId($users->id, true)){

                        $request->session()->regenerate();

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

                      // 🌟 STEP 4: Role-Based Redirect 🌟
                      $roleId = (int) auth('users')->user()->role_id;

                      // 1. For Service / Workshop team
                      if (in_array($roleId, [10, 11, 14, 12, 16])) {
                          return redirect()->route('services.index'); 
                      }

                      // 2. For Showroom / Sales team
                      if (in_array($roleId, [2, 5])) {
                          return redirect('/sales'); 
                      }

                      // 3. For POS Users
                      if (in_array($roleId, [18])) {
                          return redirect('/pos/checkout'); 
                      }

                      // 🌟 4. For Admin (1) and Superadmin/IT (6) -> Redirect to Performance Overview 🌟
                      if (in_array($roleId, [1, 6])) {
                          return redirect('/performance-overview'); 
                          
                          // Note: If you are using a named route for this page, use this instead:
                          // return redirect()->route('services.performance');
                      }

                      // 5. Default fallback for any other unlisted roles
                      return redirect()->route('home');
                      
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
          return redirect()->back()->with('error', 'Please enter your username and password.');
      }
  }

    public function fnLogout(Request $request){
        Auth::guard('users')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 🌟 ប្រើប្រាស់ route('login') ដើម្បីឱ្យវាស្គាល់ URL 'admin-portal' ដែលយើងបានប្តូរ 🌟
        return redirect()->route('login');
    }
}