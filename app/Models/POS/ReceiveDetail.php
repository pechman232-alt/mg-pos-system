<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\POS\ReceiveHeader; // 👈 ត្រូវប្រាកដថាបានហៅ Model នេះមកប្រើ
use App\Models\POS\Product;       // 👈 ត្រូវប្រាកដថាបានហៅ Model នេះមកប្រើ

class ReceiveDetail extends Model
{
    use HasFactory;

    protected $table = 'receive_details';

    protected $fillable = [
        'receive_header_id', 
        'product_id', 
        'qty', 
        'cost', 
        'total_cost'
    ];

   // ✅ ថែម Function មួយនេះ ដើម្បីភ្ជាប់ទៅកាន់ក្បាលវិក្កយបត្រមេ
   public function header()
   {
       return $this->belongsTo(ReceiveHeader::class, 'receive_header_id');
   }

   // (បើបងមាន Function product() រួចហើយ ទុកវានៅដដែល)
   public function product()
   {
       return $this->belongsTo(Product::class, 'product_id');
   }
   public function receiveDetails()
   {
       return $this->hasMany(\App\Models\POS\ReceiveDetail::class, 'product_id');
   }
}