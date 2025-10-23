<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProvinceModel extends Model
{
    protected $table ="provinces";
    protected $fillable = [
        'name'
    ];
}
