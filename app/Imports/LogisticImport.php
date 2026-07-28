<?php

namespace App\Imports;

use App\Models\LogisticsModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LogisticImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // 🌟 យើងប្រើ (string) ដើម្បីបង្ខំទិន្នន័យឱ្យទៅជាអក្សរទាំងអស់ ទោះបីជា Excel បញ្ជូនលេខមកក៏ដោយ
        return LogisticsModel::updateOrCreate(
            [
                'vin_number' => trim((string)$row['vin_number']), 
            ],
            [
                'model_name_id'  => (string)($row['model_name_id'] ?? 'N/A'), 
                'exterior_color' => (string)($row['exterior_color'] ?? 'N/A'),
                'interior_color' => (string)($row['interior_color'] ?? 'N/A'),
                'model_year'     => $row['model_year'] ?? date('Y'),
                'engine_number'  => trim((string)$row['engine_number']),
                'remark'         => isset($row['remark']) ? (string)$row['remark'] : null,
                'is_sale'        => 1
            ]
        );
    }

    public function rules(): array
    {
        return [
            'model_name_id'  => 'required', 
            'exterior_color' => 'required',
            'vin_number'     => 'required', 
            'engine_number'  => 'required', 
            // 🌟 ខ្ញុំបានលុប '|string' ចេញ ដើម្បីកុំឱ្យវាឆែកទាស់ជាមួយ Excel ទៀត
        ];
    }
}