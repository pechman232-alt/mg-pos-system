<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $fillable = [
        'issue_id', 'subject', 'category', 'department', 
        'description', 'reported_by', 'priority', 'status', 'admin_remark',
        'manager_comment'
    ];
}
