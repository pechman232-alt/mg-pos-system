<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModelModel extends Model
{
    protected $table = "car_models";
    protected $fillable = [
        'model_name',
        'color_id'
    ];
    public function color()
    {
        return $this->belongsTo(ColorModel::class, 'color_id', 'id');
    }
}
