<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReportModel extends Model
{
    protected $table = "sale_reports";
    
    protected $fillable = [
        'image', 'branch_id', 'member_card_id', 'brand_name', 'customer_name',
        'phone_number', 'gender', 'dob', 'source_id', 'logistics_id',
        'plate_number', 'invoice_no', 'invoice_date', 'delivery_date',
        'follow_up', 'feedback_command', 'warranty_id', 'warranty_leap_motor',
        'employee_id', 'province_id', 'contract_no', 'contract_date',
        'vpc_vdr', 'offer_license_plate', 'offer_uv_film', 'offer_floor_mat',
        'side_step', 'auto_tailgate', 'rear_spoiler', 'certificate_engine_oil',
        'pro_offer_oil', 'pro_offer_oil_date', 'vihicle_battery', 'price',
        'remark_sa', 'attach_file', 'status', 'created_by',
    ];
    
    protected $dates = [
        'created_at', 'updated_at', 'invoice_date', 'delivery_date', 
        'dob', 'contract_date', 'pro_offer_oil_date', 'vihicle_battery'
    ];

    protected static function booted()
    {
        static::saved(function ($saleReport) {
            if ($saleReport->logistics_id) {
                \App\Models\LogisticsModel::where('id', $saleReport->logistics_id)->update(['is_sale' => 0]);
            }
            if ($saleReport->member_card_id) {
                \App\Models\MemberCard::where('id', $saleReport->member_card_id)->update(['is_sale' => 0]);
            }
        });

        static::deleted(function ($saleReport) {
            if ($saleReport->logistics_id) {
                \App\Models\LogisticsModel::where('id', $saleReport->logistics_id)->update(['is_sale' => 1]);
            }
            if ($saleReport->member_card_id) {
                \App\Models\MemberCard::where('id', $saleReport->member_card_id)->update(['is_sale' => 1]);
            }
        });
    }

    // =========================================================
    // 🌟 RELATIONSHIPS (មានទាំង ២ ការពារកូដចាស់ និងកូដថ្មីហៅរក)
    // =========================================================

    // ១. សម្រាប់ AuthController ថ្មីប្រើប្រាស់ (អត់មានអក្សរ s)
    public function logistic()
    {
        return $this->belongsTo(\App\Models\LogisticsModel::class, 'logistics_id', 'id');
    }

    // ២. សម្រាប់ការពារកូដចាស់ៗនៅផ្នែកផ្សេងៗហៅរក (មានអក្សរ s)
    public function logistics()
    {
        return $this->belongsTo(\App\Models\LogisticsModel::class, 'logistics_id', 'id');
    }

    public function branch() { 
        return $this->belongsTo(\App\Models\BranchModel::class, 'branch_id', 'id'); 
    }
    
    public function source() { 
        return $this->belongsTo(\App\Models\SourceModel::class, 'source_id', 'id'); 
    }
    
    public function warranty()
    {
        return $this->belongsTo(\App\Models\WarrantyModel::class, 'warranty_id', 'id');
    }
    
    public function employee() { 
        return $this->belongsTo(\App\Models\EmployeeModel::class, 'employee_id', 'id'); 
    }
    
    public function province() { 
        return $this->belongsTo(\App\Models\ProvinceModel::class, 'province_id', 'id'); 
    }
    
    public function memberCard() { 
        return $this->belongsTo(\App\Models\MemberCard::class, 'member_card_id', 'id'); 
    }
    
    public function engineOil() {
        return $this->belongsTo(\App\Models\EngineOilModel::class, 'certificate_engine_oil', 'id');
    }

    public function car_model() {
        return $this->belongsTo(\App\Models\CarModelModel::class, 'car_model_id', 'id');
    }

    public function getFullModelNameAttribute()
{
    // ទាញឈ្មោះពីតារាង logistics តាម relationship 
    if ($this->logistic) {
        return $this->logistic->model_name_id; // កន្លែងនេះបងមើលតាមឈ្មោះ Column ក្នុងតារាង logistics របស់បង
    }
    return 'N/A';
}
    public function getFinalModelNameAttribute()
    {
        // ប្រើ $this->model_name បើសិនជាបងមាន Column ឈ្មោះនេះ 
        // (ឬប្តូរទៅ $this->car_model_id វិញ បើសិនជាក្នុង Table ប្រើ car_model_id)
        $modelVal = $this->model_name;

        // បើវាជាលេខ (ID) ឱ្យវាទៅទាញឈ្មោះពីតារាង car_models
        if (is_numeric($modelVal)) {
            $carModelObj = \App\Models\CarModelModel::find($modelVal);
            return $carModelObj ? $carModelObj->model_name : 'N/A';
        }

        // បើវាជាអក្សរស្រាប់ (ឈ្មោះឡាន) គឺបោះវាទៅយកតែម្ដង
        return $modelVal ?: 'N/A';
    }
    
}