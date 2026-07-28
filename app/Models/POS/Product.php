<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products'; 

    protected $fillable = [
        'item_code', 
        'item_name', 
        'category', 
        'stock_qty', 
        'cost', 
        'selling_price',
        'description',
        'location_id',
        'user_id',     
        'updated_by'   
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
        // ការប្រើ withDefault ជួយការពារកុំឱ្យ Error "Attempt to read property username on null" 
        // ក្នុងករណីទំនិញនោះអត់មាន user_id
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id')
                    ->withDefault([
                        'username' => 'System/Admin',
                        'name'     => 'System/Admin'
                    ]);
    }
}