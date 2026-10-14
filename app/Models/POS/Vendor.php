<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\POS\ReceiveHeader; // ហៅមកប្រើដើម្បីធ្វើ Relationship
use App\Traits\Auditable;
class Vendor extends Model
{
    use HasFactory;
    use Auditable;
    protected $table = 'vendors';

    // អនុញ្ញាតឱ្យបញ្ចូលទិន្នន័យគ្រប់ Column
    protected $guarded = [];

    /**
     * អ្នកផ្គត់ផ្គង់ម្នាក់ អាចមានវិក្កយបត្រទិញចូល (Receive Headers) ច្រើន
     */
    public function receiveHeaders()
    {
        return $this->hasMany(ReceiveHeader::class, 'vendor_id');
    }
}