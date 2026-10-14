<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmployeeModel extends Model
{
    protected $table = 'employees';
    
    protected $fillable = [
        'company_id', 'department_id', 'position_id', 'full_name', 'first_name', 
        'last_name', 'first_name_kh', 'last_name_kh', 'gender', 'age', 'dob', 
        'user_id', 'branch_id', 'contract', 'official_userid', 'official_pass', 
        'group_domain', 's_user_id', 's_pass', 'm_user_id', 'm_pass', 
        'hr_user_id', 'hr_pass', 'rapp_user_id', 'rapp_pass', 
        'user_id_data_sheet', 'data_sheet_pass', 'extension_id', 
        'register_pass', 'note', 'activate', 'deactivate', 
        'phone_number', 'access_door', 'status', 'image_employee'
    ];

    protected $casts = [
        'dob'        => 'date',
        'activate   ' => 'date',
        'deactivate ' => 'date',
        'contract   ' => 'date',
        'status     ' => 'integer',
    ];

    // ==========================================
    // 1. ACCESSORS (Computed Properties)
    // ==========================================

    /**
     * Calculate age dynamically based on DOB.
     * Usage in Blade: $employee->age
     */
    public function getAgeAttribute()
    {
        if (!$this->dob) {
            return 'N/A';
        }

        try {
            $dob = Carbon::parse($this->dob);
            
            // Prevent unrealistic years (e.g., 9188 or before 1900)
            if ($dob->year > Carbon::now()->year || $dob->year < 1900) {
                return 'Invalid Year';
            }

            return $dob->age; 
        } catch (\Exception $e) {
            return 'Error';
        }
    }

    /**
     * Automatically return correct avatar URL based on gender or uploaded file.
     * Usage in Blade: $employee->profile_photo_url
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->image_employee && file_exists(public_path('uploads/employees/' . $this->image_employee))) {
            return asset('uploads/employees/' . $this->image_employee);
        }
        
        if (strtolower($this->gender) === 'female') {
            return asset('assets/img/avatars/default-female.png'); 
        }
        
        return asset('assets/img/avatars/default-male.png');
    }

    // ==========================================
    // 2. SCOPES (Query Helpers)
    // ==========================================

    /**
     * Only get active employees.
     * Usage: EmployeeModel::active()->get();
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // ==========================================
    // 3. RELATIONSHIPS
    // ==========================================

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

    /**
     * 🌟 ADDED: Relationship to Sale Reports
     * This is required for the Follow-Up Tracking to work correctly.
     */
    public function saleReports()
    {
        // Assuming your SaleReport model is named 'SaleReportModel'.
        // If it's named 'SaleReport', change the class name below.
        // Assuming the foreign key in sale_reports table is 'employee_id' (or 'sales_person_id').
        return $this->hasMany(SaleReportModel::class, 'employee_id', 'id');
    }
}