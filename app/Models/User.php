<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 🌟 កុំភ្លេចថែមបន្ទាត់នេះ ដើម្បីប្រើប្រាស់ Token សម្រាប់ App

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable; // 🌟 ថែម HasApiTokens ត្រង់នេះ

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'full_name', // បន្ថែមតាមកូដ Header បង
        'email',
        'password',
        'profile_pic',
        
        // 🌟 សម្រាប់ប្រព័ន្ធ Login តាមទូរស័ព្ទ (OTP)
        'phone',
        'otp_code',
        'otp_expires_at',

        // 🌟 ថែម Columns ថ្មីដែល Database របស់អ្នកទាមទារ
        'user_id',
        'username',
        'gender',
        'branch_id',

        'department_id',
        'position_id',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}