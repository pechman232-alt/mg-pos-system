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
    public function imageUser($path){
        $storagePath = storage_path('app/public/userProfile/'.$path);
        if (!file_exists($storagePath)) {
            abort(404, 'File ImageUser not found');
        }
        return response()->file($storagePath);
    }
    public function usersIndex(){
        $users = UsersModel::selectRaw(
            'users.full_name,
            users.id,
            users.user_id,
            users.username,
            users.gender,
            users.email,
            users.start_work_date,
            user_roles.name as user_role_name,
            branches.branch_name as branchName,
            departments.name as departName,
            positions.name as positionName,
            users.status,
            users.profile_pic
            '
        )
        ->join('branches', 'branches.id','=', 'users.branch_id')
        ->join('departments', 'departments.id','=','users.department_id')
        ->join('user_roles','user_roles.id','=','users.role_id')
        ->join('positions', 'positions.id', '=', 'users.position_id')
        ->where('users.status' , '=', '1')
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

        $userRole = UserRolesModel::get();
        return view('Users.add_user',['branchs'=>$branchs, 'departs'=>$departs, 'userRole'=>$userRole]);
    }

    public function storeUsers(UsersRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['password'] = Hash::make($request->password);

            if ($request->hasFile('profile_pic')) {
                $file = $request->file('profile_pic');
                $name = md5($file->getFilename() . time()) . '.' . $file->getClientOriginalExtension();
                $file_dir = 'userProfile';
                $file->move(storage_path("app/public/$file_dir"), $name);
                $validated['profile_pic'] = $name;
            }

            UsersModel::create($validated);
            return redirect('users/create')->with('success', 'The User is created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function editUsers(Request $request, $id){
        try {
            $ID = decrypt($id);
            $users = UsersModel::find($ID);
            if ($request->ajax()) {
                $search = $request->input('search');
                $positions = PositionModel::where('status', '=', '1')
                    ->where('name', 'LIKE', "%{$search}%")
                    ->select('id', 'name')
                    ->get();
                if ($users && $users->position_id) {
                    $selectedPosition = PositionModel::find($users->position_id);
                    if ($selectedPosition) {
                        $positions->prepend($selectedPosition);
                    }
                }
                return response()->json($positions);
            }
            $userRole = UserRolesModel::all();
            $branchs = BranchModel::all();
            $departs = DepartmentModel::all();
            return view('Users.edit_user',['users'=>$users,'branchs'=>$branchs,'departs'=>$departs,'userRole'=>$userRole]);
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function updateUsers(Request $request, $id){
        try {
            $ID = decrypt($id);
            $users = UsersModel::where('id', $ID)->firstorfail();

            if ($request->filled('password')) { 
                $users->password = Hash::make($request->password);
            }

            if ($request->hasFile('profile_pic')) {
                $file = $request->file('profile_pic');
                $name = md5($file->getFilename() . time()) . '.' . $file->getClientOriginalExtension();
                $file_dir = 'userProfile';
                $file->move(storage_path("app/public/$file_dir"), $name);
                $users['profile_pic'] = $name;
            }

            $updateData = $request->except(['password', 'profile_pic']);
            $users->update($updateData);

            return redirect('users')->with('info', 'The Users is Updated successfully.');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function destroyUsers($id){
        try {
            $ID = decrypt($id);
            $users = UsersModel::find($ID);

            if (!$users) {
                return redirect()->back()->with('error', 'User not found.');
            }

            $users->status = 0;
            $users->save();
            return redirect('users')->with('error', 'The Users is Deleted successfully.');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    
}
