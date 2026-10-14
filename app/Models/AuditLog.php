<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'module', 'action', 'description', 
        'old_values', 'new_values', 'ip_address'
    ];

    // =========================================
    // ✅ នេះគឺជាកូដដែលខ្វះ (Relationship ភ្ជាប់ទៅ User)
    // =========================================
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Global helper to record an action.
     */
    public static function record($module, $action, $description, $oldValues = null, $newValues = null)
    {
        self::create([
            'user_id'    => Auth::check() ? Auth::id() : null,
            'module'     => $module,
            'action'     => $action,
            'description'=> $description,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => Request::ip(),
        ]);
    }
}