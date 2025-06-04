<?php

namespace App\Models;

use App\Traits\HasFormattedDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, HasFormattedDates;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'image',
        'start_date',
        'end_date',
        'event_time',
        'venue',
        'status',
        'featured',
        'event_type'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'boolean',
        'featured' => 'boolean'
    ];
}
