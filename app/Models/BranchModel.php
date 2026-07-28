<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchModel extends Model
{
    use HasFactory;

    protected $table = 'branches'; 
    
    // ប្រសិនបើអ្នកប្រើ $fillable ត្រូវប្រាកដថាឈ្មោះ Column ក្នុង DB គឺ 'branch_name' ពិតមែន
    protected $fillable = ['branch_name']; 

    public function company()
    {
        return $this->belongsTo(\App\Models\POS\PosCompany::class, 'company_id');
    }
}

