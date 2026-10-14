<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Product extends Model
{
    // ✅ បន្ថែម Auditable នៅទីនេះ
    use HasFactory, Auditable;

    protected $table = 'products'; 

    protected $fillable = [
        'item_code',
        'item_name',
        'category',
        'location_id',
        'cost',
        'price',
        'stock_qty',
        'description',
        'user_id'
    ];

    // =========================================
    // ✅ Relationships
    // =========================================
    
    /**
     * ភ្ជាប់ទៅកាន់សាខា (Branch)
     */
    public function branch()
    {
        return $this->belongsTo(\App\Models\BranchModel::class, 'location_id', 'id')
                    ->withDefault([
                        'branch_name' => 'N/A'
                    ]);
    }

    /**
     * ភ្ជាប់ទៅកាន់អ្នកប្រើប្រាស់ (User)
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id')
                    ->withDefault([
                        'username' => 'System/Admin',
                        'name'     => 'System/Admin'
                    ]);
    }
}