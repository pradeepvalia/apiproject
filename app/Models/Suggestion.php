<?php

namespace App\Models;

use App\Traits\HasFormattedDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    use HasFactory, HasFormattedDates;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'description',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean'
    ];
}
