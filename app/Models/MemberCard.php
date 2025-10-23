<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberCard extends Model
{
    protected $table ="member_cards";
    protected $fillable = [
        'member_card',
        'is_sale'
    ];
}
