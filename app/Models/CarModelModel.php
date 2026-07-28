<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModelModel extends Model
{
    protected $table = "car_models";

    // 🌟 បងត្រូវប្រាកដថាឈ្មោះ Column ក្នុង $fillable គឺដូចគ្នា 100% នឹងក្នុង Database
    // ខ្ញុំបានបន្ថែម model_name ដែលជាឈ្មោះពិតប្រាកដ
    protected $fillable = [
        'model_name', 
        'color_id'
    ];

    public function color()
    {
        return $this->belongsTo(ColorModel::class, 'color_id', 'id');
    }

    public function logistics()
    {
        return $this->hasMany(LogisticsModel::class, 'model_name_id', 'id');
    }

    // 🌟 កែតម្រូវ Accessor ដើម្បីការពារកំហុសពេលទិន្នន័យទទេរ
    public function getColorNameAttribute()
    {
        return $this->color ? ($this->color->name ?? 'N/A') : 'N/A';
    }

    // 🌟 បន្ថែម Accessor សម្រាប់យកឈ្មោះឡាន (ជួយឱ្យហៅងាយក្នុង View)
    public function getModelNameDisplayAttribute()
    {
        return $this->model_name ?? 'N/A';
    }
}