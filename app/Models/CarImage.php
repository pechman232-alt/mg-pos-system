<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarImage extends Model
{
    protected $table ="car_images";
    protected $fillable = [
        'model_id',
        'color_id',
        'image_name'

    ];
}
