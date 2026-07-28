<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;
use App\Models\BranchModel;

class SalesHeader extends Model
{
    protected $table = 'sales_headers';
    protected $fillable = [
        'invoice_no', 
        'sale_date', 
        'customer_name', 
        'location_id', 
        'car_invoice_no',      // ✅ ថែមថ្មី
        'reason',              // ✅ ថែមថ្មី
        'purchase_invoice_id', // ✅ ថែមថ្មី
        'subtotal', 
        'discount_amount', 
        'tax_percent', 
        'tax_amount', 
        'grand_total'
    ];

    public function branch() {
        return $this->belongsTo(BranchModel::class, 'location_id');
    }

    public function details() {
        return $this->hasMany(SalesDetail::class, 'sales_header_id');
    }
}