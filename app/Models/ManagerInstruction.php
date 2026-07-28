<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagerInstruction extends Model
{
    use HasFactory;

    // ✅ កំណត់ឈ្មោះ Table (ប្រសិនបើបងបង្កើត Table ឈ្មោះ manager_instructions)
    protected $table = 'manager_instructions';

    protected $fillable = [
        'lead_id',
        'user_id',
        'instruction'
    ];

    /**
     * 🔗 ទំនាក់ទំនងទៅកាន់ Lead (មតិយោបល់នេះជារបស់ Lead ណា?)
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    /**
     * 🔗 ទំនាក់ទំនងទៅកាន់ User (អ្នកណាជាអ្នកសរសេរ? Director/Manager)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}