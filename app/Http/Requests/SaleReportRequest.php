<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'required',
            'branch_id' => 'required',
            'member_card' => 'required',
            'brand_name' => 'required',
            'customer_name' => 'required',
            'phone_number' => 'required',
            'gender' => 'required',
            'dob' => 'required',
            'source_id' => 'required',
            'logistics_id' => 'required',
            'plate_number' => 'required',
            'invoice_no' => 'required',
            'invoice_date' => 'required',
            'delivery_date' => 'required',
            'follow_up' => 'required',
            'warranty_id' => 'required',
            'employee_id' => 'required',
            'province_id' => 'required',
            'contract_no' => 'required',
            'contract_date' => 'required',
            'vpc_vdr' => 'required',
            'offer_license_plate' => 'required',
            'offer_uv_film' => 'required',
            'offer_floor_mat' => 'required',
            'side_step' => 'required',
            'auto_tailgate' => 'required',
            'rear_spoiler' => 'required',
            'certificate_engine_oil' => 'required',
            'pro_offer_oil' => 'required',
            'pro_offer_oil_date' => 'required',
            'vihicle_battery' => 'required',
            'price' => 'required'
        ];
    }
}
