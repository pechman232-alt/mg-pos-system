<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeModel extends Model
{
    protected $table = "employees";
    protected $fillable = [
        'company_id',
        'department_id',
        'position_id',
        'full_name',
        'first_name',
        'last_name',
        'first_name_kh',
        'last_name_kh',
        'gender',
        'age',
        'dob',
        'user_id',
        'branch_id',
        'contract',
        'official_userid',
        'official_pass',
        'group_domain',
        's_user_id',
        's_pass',
        'm_user_id',
        'm_pass',
        'hr_user_id',
        'hr_pass',
        'rapp_user_id',
        'rapp_pass',
        'user_id_data_sheet',
        'data_sheet_pass',
        'extension_id',
        'register_pass',
        'note',
        'activate',
        'deactivate',
        'phone_number',
        'access_door',
        'status'
    ];
    public function company()
    {
        return $this->belongsTo(CompanyModel::class, 'company_id', 'id');
    }
    public function department()
    {
        return $this->belongsTo(DepartmentModel::class, 'department_id', 'id');
    }
    public function position()
    {
        return $this->belongsTo(PositionModel::class, 'position_id', 'id');
    }
    public function branch()
    {
        return $this->belongsTo(BranchModel::class, 'branch_id', 'id');
    }
}
