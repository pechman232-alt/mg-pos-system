<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogisticsModel extends Model
{
    protected $table ="logistics";
    protected $fillable = [
        'model_name',
        'exterior_color',
        'interior_color',
        'model_year',
        'vin_number',
        'engine_number',
        'remark',
        'status'
    ];
}
