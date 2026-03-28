<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SalesContract extends Model {
    protected $fillable = [
        'contract_number', 
        'order_date', 
        'sale_person',
        'branch_name', 
        'source', 
        'customer_name', 
        'phone_number', 
        'email', 
        'address', 
        'location_province', 
        'customer_id_card', // សម្រាប់រក្សាទុកឈ្មោះ File រូបភាពអត្តសញ្ញាណប័ណ្ណ
        'membership_number', 
        'model', 
        'color', 
        'offers',
        'qty', 
        'vin_number', 
        'engine_number', 
        'plate_no', 
        'miles', 
        'sub_total', 
        'discount', 
        'deposit', 
        'grand_total', 
        'payment_type', 
        'status'
    ];
    /**
     * Casting ensures that currency values are handled as decimals, 
     * not strings, avoiding math errors in Chrome/PHP.
     */
    protected $casts = [
        'order_date' => 'date',
        'sub_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'deposit' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    protected static function boot() {
        parent::boot();
        
        static::saving(function ($contract) {
            // 1. Calculate Balance Due
            $subTotal = (float) ($contract->sub_total ?? 0);
            $discount = (float) ($contract->discount ?? 0);
            $deposit  = (float) ($contract->deposit ?? 0);

            // Grand Total represents the "Balance Due"
            $contract->grand_total = $subTotal - $discount - $deposit;

            // 2. Logic: If status is 'Payoff', ensure balance is recorded correctly
            // You might want to force the balance to 0 if the status is Payoff
            if ($contract->status === 'Payoff') {
                // Logic check: If they select Payoff, the deposit effectively 
                // becomes the full price minus discount.
            }

            // 3. Auto-generate Membership Number if empty
            if (empty($contract->membership_number)) {
                $contract->membership_number = 'MB-' . strtoupper(Str::random(6));
            }
        });
    }

    /* ================= HELPER METHODS ================= */

    /**
     * Check if the contract is a Booking
     */
    public function isBooking() {
        return $this->status === 'Booking';
    }

    /**
     * Check if the contract is fully Paid Off
     */
    public function isPayoff() {
        return $this->status === 'Payoff';
    }

    /**
     * Formatted Balance for UI
     */
    public function getFormattedBalanceAttribute() {
        return '$' . number_format($this->grand_total, 2);
    }
    
}