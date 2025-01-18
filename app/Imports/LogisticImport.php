<?php

namespace App\Imports;

use App\Models\LogisticsModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LogisticImport implements ToModel, WithHeadingRow ,WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // dd($row);

        return new LogisticsModel([
            'model_name' => $row['model_name'],
            'exterior_color' => $row['exterior_color'],
            'interior_color' => $row['interior_color'],
            'model_year' => $row['model_year'],
            'vin_number' => $row['vin_number'],
            'engine_number' => $row['engine_number'],
            'remark' => $row['remark'],
        ]);
    }

    public function rules(): array
    {
        return [
            'model_name' => 'required',
            'exterior_color' => 'required',
            'interior_color' => 'required',
            'model_year' => 'required',
            // 'vin_number' => 'required',
            // 'engine_number' => 'required',
            'vin_number' => 'required|string|max:255|unique:logistics,vin_number',
            'engine_number' => 'required|string|max:255|unique:logistics,engine_number',
        ];
    }
}
