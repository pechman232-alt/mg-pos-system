<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionModel extends Model
{
    protected $table ="positions";
    protected $fillable = [
        'name',
        'status'
    ];
}
