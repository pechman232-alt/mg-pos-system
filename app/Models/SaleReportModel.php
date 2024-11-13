<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReportModel extends Model
{
    protected $table ="sale_reports";
    protected $fillable = [
        'image',
        'branch_id',
        'member_card',
        'brand_name',
        'customer_name',
        'phone_number',
        'gender',
        'dob',
        'age',
        'source_id',
        'logistics_id',
        'plate_number',
        'invoice_no',
        'invoice_date',
        'delivery_date',
        'follow_up',
        'warranty_id',
        'employee_id',
        'province_id',
        'contract_no',
        'contract_date',
        'vpc_vdr',
        'offer_license_plate',
        'offer_uv_film',
        'offer_floor_mat',
        'side_step',
        'auto_tailgate',
        'rear_spoiler',
        'certificate_engine_oil',
        'pro_offer_oil',
        'pro_offer_oil_date',
        'vihicle_battery',
        'remark_sa',
        'attach_file'
    ];
    protected $dates = ['created_at', 'updated_at'];

    public function branch()
    {
        return $this->belongsTo(BranchModel::class, 'branch_id', 'id');
    }
    public function source()
    {
        return $this->belongsTo(SourceModel::class, 'source_id', 'id');
    }
    public function logistics()
    {
        return $this->belongsTo(LogisticsModel::class, 'logistics_id', 'id');
    }
    public function warranty()
    {
        return $this->belongsTo(WarrantyModel::class, 'warranty_id', 'id');
    }
    public function employee()
    {
        return $this->belongsTo(EmployeeModel::class, 'employee_id', 'id');
    }
    public function province()
    {
        return $this->belongsTo(ProvinceModel::class, 'province_id', 'id');
    }
}
