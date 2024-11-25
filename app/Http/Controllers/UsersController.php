<?php

namespace App\Http\Controllers;

use App\Models\UsersModel;
use Illuminate\Http\Request;
use App\Models\BranchModel;
use App\Models\DepartmentModel;
use App\Models\PositionModel;
use App\Models\UserRolesModel;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function usersIndex(){
        $users = UsersModel::selectRaw(
            'users.full_name,
            users.user_id,
            users.username,
            users.gender,
            users.email,
            users.start_work_date,
            user_roles.name as user_role_name,
            branches.branch_name as branchName,
            departments.name as departName,
            users.status,
            users.profile_pic
            '
        )
        ->join('branches', 'branches.id','=', 'users.branch_id')
        ->join('departments', 'departments.id','=','users.department_id')
        ->join('user_roles','user_roles.id','=','users.role_id')
        ->get();
        return view('Users.user_index',['users'=>$users]);
    }

    public function formUsers(){
        $branchs = BranchModel::get();
        $departs = DepartmentModel::get();
        $positions = PositionModel::get();
        $userRole = UserRolesModel::get();
        return view('Users.add_user',['branchs'=>$branchs, 'departs'=>$departs, 'positions'=>$positions, 'userRole'=>$userRole]);
    }

    public function storeUsers(Request $request){
        $request->validate([
                'full_name' => 'required',
                'username' => 'required',
                'user_id' => 'required',
                'gender' => 'required',
                'email' => 'required',
                'phone_number' => 'required',
                'branch_id' => 'required',
                'department_id' => 'required',
                'position_id' => 'required',
                'start_work_date' => 'required',
                'password' => 'required|min:8',
                'user_role_id' => 'required',
                'profile_pic' => 'required|image|mimes:jpeg,png,jpg',
            ],
            [
                'full_name' => 'Enter a valid Full Name!',
                'username' => 'Enter a valid Username!',
                'user_id' => 'Enter a valid User-ID!',
                'gender' => 'Please select Gender!',
                'email' => 'Enter a valid Email!',
                'phone_number' => 'Enter a valid Phone Numeber!',
                'branch_id' => 'Please Select Branch Name!',
                'department_id' => 'Please Select Department Name!',
                'position_id' => 'Please Select Position Name!',
                'start_work_date' => 'Please Select Start Work Date!',
                'password' => 'Enter a valid Password!',
                'password.min' => 'Password must be 8 charater or later',
                'user_role_id' => 'Please Select UserRole!',
                'profile_pic' => 'Please upload a profile picture.',
                'profile_pic.image' => 'The uploaded file must be an image.',
                'profile_pic.mimes' => 'Only JPEG, PNG, and JPG formats are allowed.',
            ]
        );

        $users = new UsersModel();
        $users->full_name = $request->full_name;
        $users->username = $request->username;
        $users->user_id = $request->user_id;
        $users->gender = $request->gender;
        $users->email = $request->email;
        $users->phone_number = $request->phone_number;
        $users->branch_id = $request->branch_id;
        $users->department_id = $request->department_id;
        $users->position_id = $request->position_id;
        $users->start_work_date = $request->start_work_date;
        $users->role_id = $request->user_role_id;
        $users->password = Hash::make($request->password);

        $file = $request->file('profile_pic');
        if ($file) {
            $name = md5($file->getFilename() . time()) . '.' . $file->getClientOriginalExtension();
            $file_dir = 'userProfile';
            $file->move(storage_path("app/public/$file_dir"), $name);
            $users->profile_pic = "$file_dir/$name";
        }

        $users->save();
        return redirect('create/users')->with('success','The User is created Succesfully.');

    }
}
