<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    use HasFactory;

    // 💡 ប្រាប់ Laravel ឱ្យស្គាល់ឈ្មោះ Table ឱ្យច្បាស់
    protected $table = 'lead_activities';

    protected $fillable = [
        'lead_id', 
        'user_id', 
        'status', 
        'comment'
    ];

    /**
     * 🔗 ទំនាក់ទំនងត្រឡប់ទៅកាន់ Lead (Belongs To)
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    /**
     * 🔗 ទំនាក់ទំនងជាមួយ User/Staff (Belongs To)
     * ប្រើសម្រាប់ទាញឈ្មោះអ្នកដែលបាន Update Status មកបង្ហាញ
     */
    public function user(): BelongsTo
    {
        // កំណត់ឱ្យទៅរក Table 'users' (ឬថែម 'users' បើបងប្រើ Custom Guard)
        return $this->belongsTo(User::class, 'user_id');
    }
}