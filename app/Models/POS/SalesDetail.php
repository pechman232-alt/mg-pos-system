<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
    protected $table = 'sales_details';

    protected $fillable = [
        'sales_header_id', 
        'product_id', 
        'item_code', 
        'item_name', 
        'qty', 
        'unit_price', 
        'total_price'
    ];

    // ✅ បន្ថែមមុខងារនេះដើម្បីឱ្យវាស្គាល់ Relationship ជាមួយ Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // (ជម្រើស) បន្ថែមមុខងារនេះដើម្បីឱ្យវាស្គាល់ក្បាលវិក្កយបត្រវិញ
    public function salesHeader()
    {
        return $this->belongsTo(SalesHeader::class, 'sales_header_id');
    }
}