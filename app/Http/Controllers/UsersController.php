<?php

namespace App\Http\Controllers;

use App\Models\UsersModel;
use Illuminate\Http\Request;
use App\Models\BranchModel;
use App\Models\DepartmentModel;
use App\Models\PositionModel;
use App\Models\UserRolesModel;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UsersRequest;

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

    public function formUsers(Request $request){
        $branchs = BranchModel::get();
        $departs = DepartmentModel::get();

        if ($request->ajax()) {
            $search = $request->input('search');
            $positions = PositionModel::where('status', '=', '1')
                ->where('name', 'LIKE', "%{$search}%")
                ->select('id', 'name')
                ->get();
            return response()->json($positions);
        }
        
        $positions = PositionModel::get();

        $userRole = UserRolesModel::get();
        return view('Users.add_user',['branchs'=>$branchs, 'departs'=>$departs, 'positions'=>$positions, 'userRole'=>$userRole]);
    }

    public function storeUsers(UsersRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['password'] = Hash::make($request->password);
            $file = $validated->file('profile_pic');
            if ($file) {
                $name = md5($file->getFilename() . time()) . '.' . $file->getClientOriginalExtension();
                $file_dir = 'userProfile';
                $file->move(storage_path("app/public/$file_dir"), $name);
                $validated['profile_pic'] = "$file_dir/$name";
            }
            UsersModel::create($validated);

            return redirect('create/users')->with('success', 'The User is created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

}
