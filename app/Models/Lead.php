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
        'branch_name', 
        'remind_date',      
        'customer_name', 
        'contact_info', 
        'telegram', 
        'location', 
        'source', 
        'model', 
        'color', 
        'status', 
        'concern', 
        'competition', 
        'comment',
        'is_transferred',
        'manager_comment',
        'is_archived', // ✅ បន្ថែមនេះដើម្បីឱ្យ Archive ដំណើរការបានជោគជ័យ
    ];

    /**
     * 💡 បន្ថែម Casting ដើម្បីឱ្យប្រព័ន្ធស្គាល់ប្រភេទកាលបរិច្ឆេទ និង Boolean
     */
    protected $casts = [
        'date' => 'date',
        'remind_date' => 'datetime', 
        'is_transferred' => 'boolean',
        'is_archived' => 'boolean', // ✅ កំណត់ជា Boolean ដើម្បីងាយស្រួលប្រើ Logic true/false
    ];

    /**
     * 🚀 មុខងារ Auto-Set Remind Date (៧ ថ្ងៃក្រោយ)
     * វានឹងដំណើរការអូតូនៅពេលបង្កើត Lead ថ្មី
     */
    protected static function booted()
    {
        static::creating(function ($lead) {
            if (!$lead->remind_date) {
                $lead->remind_date = now()->addDays(7);
            }
        });
    }

    /**
     * 🔗 ទំនាក់ទំនងជាមួយ LeadActivity (Has Many)
     * បង្ហាញ Timeline នៃ Workflow
     */
    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class, 'lead_id')->latest();
    }

    /**
     * 🔗 ទាញយក Activity ចុងក្រោយគេបង្អស់ (Has One)
     */
    public function lastActivity(): HasOne
    {
        return $this->hasOne(LeadActivity::class, 'lead_id')->latestOfMany();
    }

    /**
     * 🔗 ទំនាក់ទំនងជាមួយ ManagerInstructions (History Table)
     * ក្នុងករណីបងចង់ទុក Comment ច្រើនដងដាច់ដោយឡែក
     */
    public function managerInstructions(): HasMany
    {
        return $this->hasMany(ManagerInstruction::class, 'lead_id')->latest();
    }

    /**
     * 🔗 ទាញយក Instruction ចុងក្រោយគេបង្អស់ពី Table manager_instructions
     * (សម្រាប់បង្ហាញក្នុង Alert Bar)
     */
    public function latestInstruction(): HasOne
    {
        return $this->hasOne(ManagerInstruction::class, 'lead_id')->latestOfMany();
    }
}