<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\MemberCard;

class SalesContract extends Model {
    
    // 🌟 កំណត់កឡោនដែលអនុញ្ញាតឱ្យបញ្ចូល (Mass Assignment)
    protected $fillable = [
        'customer_name', 
        'customer_name_kh',  // 🌟 ត្រូវប្រាកដថាមានពាក្យនេះ
        'phone_number', 
        'id_card_number', 
        'membership_number',
        'customer_id_card', 
        'address', 
        'branch_name', 
        'sale_person', 
        'contract_number', 
        'order_date', 
        'source_id',            // 🌟 ត្រូវប្រាកដថាមានពាក្យនេះ
        'model', 
        'color', 
        'vin_number', 
        'engine_number', 
        'offers', 
        'sub_total', 
        'discount', 
        'deposit', 
        'payment_type', 
        'status',
        'note',
        'grand_total'
        
    ];
    
    // 🌟 កំណត់ប្រភេទទិន្នន័យឱ្យត្រូវពេលទាញយកមកប្រើ
    protected $casts = [
        'order_date'  => 'date',
        'offers'      => 'array',
        'sub_total'   => 'decimal:2',
        'discount'    => 'decimal:2',
        'deposit'     => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    /**
     * 🌟 1. Logic បញ្ជាអូតូ (Lifecycle Hooks)
     */
    protected static function boot() {
        parent::boot();
        
        // --- មុននឹង Save៖ គណនាលុយសរុបឱ្យត្រូវជានិច្ច ---
        static::saving(function ($contract) {
            $sub = (float)($contract->sub_total ?? 0);
            $dis = (float)($contract->discount ?? 0);
            $dep = (float)($contract->deposit ?? 0);
            $contract->grand_total = $sub - $dis - $dep;
        });

        // --- ក្រោយបង្កើតថ្មី៖ កាត់ស្តុកកាតសមាជិក (is_sale = 0) ---
        static::created(function ($contract) {
            if ($contract->membership_number) {
                MemberCard::where('member_card', $contract->membership_number)
                          ->update(['is_sale' => 0]);
            }
        });

        // --- ពេលលុបកិច្ចសន្យា៖ ដោះលែងកាតឱ្យទំនេរវិញ (Restock) ---
        static::deleted(function ($contract) {
            if ($contract->membership_number) {
                MemberCard::where('member_card', $contract->membership_number)
                          ->update(['is_sale' => 1]);
            }
        });

        // --- ពេល Edit ប្តូរលេខកាត៖ ប្តូរស្ថានភាពក្នុងស្តុកឱ្យត្រូវតាមកាតថ្មី ---
        static::updating(function ($contract) {
            if ($contract->isDirty('membership_number')) {
                $oldCard = $contract->getOriginal('membership_number');
                $newCard = $contract->membership_number;

                if ($oldCard) {
                    MemberCard::where('member_card', $oldCard)->update(['is_sale' => 1]);
                }
                if ($newCard) {
                    MemberCard::where('member_card', $newCard)->update(['is_sale' => 0]);
                }
            }
        });
    }

    /**
     * 🌟 2. មុខងារបែងចែកសិទ្ធិមើលទិន្នន័យ (Data Visibility Scope)
     * ប្រើក្នុង Controller៖ SalesContract::byRole()->get();
     */
    public function scopeByRole($query)
{
    $user = auth('users')->user();
    if (!$user) return $query;

    $roleId = $user->role_id;

    // --- ១. Full Access: Admin, Director, GM (Roles: 1, 2, 6) ---
    if (in_array($roleId, [1, 2, 6])) {
        return $query;
    }

    // --- ២. Branch Access: Managers/IT/Marketing (Roles: 3, 4, 5, 7, 8) ---
    if (in_array($roleId, [3, 4, 5, 7, 8])) {
        // 🌟 ដំណោះស្រាយ៖ ទាញឈ្មោះសាខាចេញពី Branch Model តាមរយៈ branch_id របស់ User
        $userBranchName = \App\Models\BranchModel::where('id', $user->branch_id)->value('branch_name');
        
        return $query->where('branch_name', $userBranchName);
    }

    // --- ៣. Personal Access: Sale Consultant (Other Roles) ---
    return $query->where('sale_person', $user->full_name);
}

    /**
     * 🌟 3. Accessors & Helpers
     */
    public function getFormattedGrandTotalAttribute() {
        return '$' . number_format($this->grand_total, 2);
    }

    public function isBooking() { return $this->status === 'Booking'; }
    public function isPayoff() { return $this->status === 'Payoff'; }
}