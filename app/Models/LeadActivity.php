<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    use HasFactory;

    // កំណត់ Column ណាខ្លះដែលអនុញ្ញាតឱ្យបញ្ចូលទិន្នន័យបាន (Mass Assignment)
    protected $fillable = [
        'lead_id', 
        'user_id', 
        'status', 
        'comment'
    ];

    /**
     * 🔗 តភ្ជាប់ទៅកាន់ Table Users
     * សកម្មភាពនីមួយៗ គឺធ្វើឡើងដោយបុគ្គលិកម្នាក់ (User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 🔗 តភ្ជាប់ត្រឡប់ទៅកាន់ Table Leads វិញ
     * សកម្មភាពនីមួយៗ គឺជារបស់ Lead តែមួយប៉ុណ្ណោះ
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}