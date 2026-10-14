<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class PosCompany extends Model
{
    use HasFactory;
    use Auditable;
    protected $table = 'pos_companies';

    protected $fillable = [
        'company_name_kh',
        'company_name_en',
        'logo',
        'vat_tin',
        'phone',
        'email',
        'address',
        'website'
    ];
}