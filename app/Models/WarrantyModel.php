<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarrantyModel extends Model
{
    protected $table ="warranty";
    protected $fillable = [
        'name'
    ];
}
