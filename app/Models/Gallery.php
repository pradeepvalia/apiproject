<?php

namespace App\Models;

use App\Traits\HasFormattedDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory, HasFormattedDates;

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'category_id',
        'status',
        'featured'
    ];

    protected $casts = [
        'status' => 'string',
        'featured' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
