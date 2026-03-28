<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; // 🚩 ថែមជួរទី ១៖ Import Trait

class UsersModel extends Authenticatable
{
    use Notifiable; // 🚩 ថែមជួរទី ២៖ ប្រើប្រាស់ Trait ដើម្បីឱ្យស្គាល់ unreadNotifications()

    protected $table = 'users';
    protected $fillable = [
        'user_id',
        'username',
        'full_name',
        'password',
        'remember_token',
        'gender',
        'email',
        'branch_id',
        'department_id',
        'position_id',
        'role_id',
        'phone_number',
        'profile_pic',
        'start_work_date',
        'created_by',
        'updated_by',
        'status'
    ];
}