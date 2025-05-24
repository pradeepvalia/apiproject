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
        'event_date',
        'event_time',
        'venue',
        'status',
        'featured'
    ];

    protected $casts = [
        'event_date' => 'date',
        'featured' => 'boolean',
        'status' => 'boolean'
    ];
}
