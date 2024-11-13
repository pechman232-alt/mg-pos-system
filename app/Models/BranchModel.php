<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchModel extends Model
{
    protected $table = "branches";
    protected $fillable = [
        'branch_name'
    ];
}
