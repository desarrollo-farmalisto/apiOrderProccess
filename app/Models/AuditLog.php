<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'model', 'model_id',
        'description', 'ip_address', 'user_agent', 'payload', 'level'
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
