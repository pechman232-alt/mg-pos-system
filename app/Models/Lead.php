<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 
        'sales_person', 
        'branch', 
        'customer_name', 
        'contact_info', 
        'telegram', 
        'location', 
        'source', 
        'model', 
        'color', 
        'status', 
        'dp_percent', 
        'mp', 
        'lp', 
        'concern', 
        'competition', 
        'comment'
    ];

    /**
     * 💡 បន្ថែម Casting ដើម្បីឱ្យប្រព័ន្ធស្គាល់ថា 'date' គឺជាប្រភេទកាលបរិច្ឆេទ
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * 🔗 ទំនាក់ទំនងជាមួយ LeadActivity (Has Many)
     * ប្រើសម្រាប់បង្ហាញ Timeline នៃការតាមដានទាំងអស់
     */
    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class, 'lead_id')->latest();
    }

    /**
     * 🔗 ទាញយក Activity ចុងក្រោយគេបង្អស់តែមួយ (Has One)
     * ប្រើសម្រាប់បង្ហាញឈ្មោះអ្នកកែចុងក្រោយនៅលើទំព័រ Index ដើម្បីឱ្យ Dashboard ដើរលឿន
     */
    public function lastActivity(): HasOne
    {
        return $this->hasOne(LeadActivity::class, 'lead_id')->latestOfMany();
    }
}