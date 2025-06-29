<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'page',
        'user_agent',
        'screen_resolution',
        'language',
        'timezone',
        'referrer',
        'session_id',
        'ip_address',
        'created_at',
    ];

    public $timestamps = true;
}
