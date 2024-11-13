<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingModel extends Model
{
    protected $table = "bookings";

    protected $fillable = [
        'name',
        'status'
    ];
}
