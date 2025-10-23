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
            'member_card' => 'required|max:255|unique:sale_reports,member_card',
            'brand_name' => 'required',
            'customer_name' => 'required',
            'phone_number' => 'required',
            'gender' => 'required',
            'dob' => 'required',
            'source_id' => 'required',
            'logistics_id' => 'required|max:255|unique:sale_reports,logistics_id',
            'plate_number' => 'required|max:255|unique:sale_reports,plate_number',
            'invoice_no' => 'required|max:255|unique:sale_reports,invoice_no',
            'invoice_date' => 'required',
            'delivery_date' => 'required',
            'warranty_id' => 'required',
            'warranty_leap_motor' => 'required',
            'employee_id' => 'required',
            'province_id' => 'required',
            'contract_no' => 'required',
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
            'vihicle_battery' => 'required'
        ];
    }
}
