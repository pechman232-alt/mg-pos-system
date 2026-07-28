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
            // 🌟 ព័ត៌មានចាំបាច់បំផុត (Required)
            'branch_id' => 'required',
            'customer_name' => 'required',
            'phone_number' => 'required',
            'employee_id' => 'required',
            'price' => 'required|numeric',
            'invoice_no' => 'required|max:255|unique:sale_reports,invoice_no,' . $this->route('id'), // ជួយកុំឲ្យ Error ពេល Update
            'invoice_date' => 'required|date',
            
            // 🌟 ព័ត៌មានដែលអត់មានក៏បាន តែបើមានត្រូវតែ Unique
            'member_card_id' => 'nullable|max:255|unique:sale_reports,member_card_id,' . $this->route('id'),
            'logistics_id' => 'nullable|max:255|unique:sale_reports,logistics_id,' . $this->route('id'),
            'plate_number' => 'nullable|max:255|unique:sale_reports,plate_number,' . $this->route('id'),

            // 🌟 ព័ត៌មានផ្សេងៗដែលអនុញ្ញាតឲ្យទទេរបាន (Nullable)
            'image' => 'nullable',
            'brand_name' => 'nullable',
            'gender' => 'nullable',
            'dob' => 'nullable|date',
            'source_id' => 'nullable',
            'delivery_date' => 'nullable|date',
            'warranty_id' => 'nullable',
            'warranty_leap_motor' => 'nullable',
            'province_id' => 'nullable',
            'contract_no' => 'nullable',
            'contract_date' => 'nullable|date',
            
            // លក្ខខណ្ឌកាដូ (CheckBox ច្រើនតែបោះតម្លៃ Yes ឬអត់បោះសោះ)
            'offer_license_plate' => 'nullable',
            'offer_uv_film' => 'nullable',
            'offer_floor_mat' => 'nullable',
            'side_step' => 'nullable',
            'auto_tailgate' => 'nullable',
            'rear_spoiler' => 'nullable',
            
            'certificate_engine_oil' => 'nullable',
            'pro_offer_oil' => 'nullable',
            'pro_offer_oil_date' => 'nullable|date',
            
            // ទិន្នន័យដែល Form ហាក់ដូចជាអត់មាន (ដាក់ Nullable សិនដើម្បីកុំឲ្យគាំង)
            'vpc_vdr' => 'nullable',
            'vihicle_battery' => 'nullable',
        ];
    }
    
    // បងអាចបន្ថែម Function នេះដើម្បីប្តូរអត្ថបទ Error ឲ្យជាភាសាខ្មែរបាន
    public function messages(): array
    {
        return [
            'invoice_no.unique' => 'លេខវិក្កយបត្រនេះមានក្នុងប្រព័ន្ធរួចហើយ!',
            'member_card_id.unique' => 'លេខកាតនេះត្រូវបានប្រើប្រាស់រួចហើយ!',
            'logistics_id.unique' => 'រថយន្តនេះត្រូវបានលក់ចេញរួចហើយ!',
        ];
    }
}