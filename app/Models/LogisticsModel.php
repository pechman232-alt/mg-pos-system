<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CarModelModel;
use App\Models\ServiceHistoryModel;

class LogisticsModel extends Model
{
    protected $table = "logistics";
    
    protected $fillable = [
        'model_name_id',
        'exterior_color',
        'interior_color',
        'model_year',
        'vin_number',
        'engine_number',
        'remark',
        'status',
        'is_sale'
    ];

    /**
     * Relationship: ទៅកាន់តារាង CarModels
     * ចំណាំ៖ ប្រសិនបើ model_name_id គឺជាឈ្មោះឡាន (String) មិនមែនជាលេខ ID ទេនោះ
     * ត្រូវប្រាកដថា column ក្នុង CarModelModel ក៏មាន value ដូចគ្នានេះដែរ។
     */
    public function carModel() {
        return $this->belongsTo(CarModelModel::class, 'model_name_id', 'model_name');
    }
    
    /**
     * មុខងារសម្រាប់ហៅយកឈ្មោះ Model ទៅប្រើ (Accessor)
     */
    public function getModelNameAttribute() {
        // បើមានទិន្នន័យក្នុងតារាង car_models គឺយកឈ្មោះចេញពីទីនោះ
        if ($this->carModel) {
            return $this->carModel->model_name;
        }
        
        // បើគ្មានទេ គឺយកអក្សរដែលស្រាប់ក្នុង column model_name_id យកទៅប្រើតែម្ដង
        // នេះជាកូដដែលធ្វើឱ្យវាលែងចេញ N/A
        return $this->model_name_id ?? 'N/A';
    }

    /**
     * Relationship: ទៅកាន់ Service History
     */
    public function serviceHistories()
    {
        return $this->hasMany(ServiceHistoryModel::class, 'logistics_id', 'id');
    }
}