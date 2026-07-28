<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\POS\Vendor;
use App\Models\BranchModel;
use App\Models\POS\ReceiveDetail;

class ReceiveHeader extends Model
{
    use HasFactory;

    protected $table = 'receive_headers';

    protected $fillable = [
        'document_no', 
        'date', 
        'location_id', 
        'vendor_id', 
        'purchase_invoice_no', 
        'tran_vendor', // ✅ ត្រូវមានជួរនេះ
        'description',
    ];

    /**
     * កំណត់ Casts ដើម្បីឱ្យ Laravel ស្គាល់ប្រភេទកាលបរិច្ឆេទអូតូ
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    // =========================================
    // ទំនាក់ទំនង (Relationships)
    // =========================================

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function branch()
    {
        return $this->belongsTo(BranchModel::class, 'location_id');
    }

    public function details()
    {
        return $this->hasMany(ReceiveDetail::class, 'receive_header_id');
    }

    // =========================================
    // មុខងារបន្ថែម (Accessors) សម្រាប់ហៅប្រើក្នុង Blade
    // =========================================

    /**
     * មុខងារគណនាតម្លៃសរុបនៃវិក្កយបត្រទាំងមូលអូតូ
     * ហៅប្រើ: $item->total_amount
     */
    public function getTotalAmountAttribute()
    {
        return $this->details->sum('total_cost');
    }

    /**
     * មុខងារគណនាចំនួនទំនិញសរុបក្នុងវិក្កយបត្រ
     * ហៅប្រើ: $item->total_qty
     */
    public function getTotalQtyAttribute()
    {
        return $this->details->sum('qty');
    }
}