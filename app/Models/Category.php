<?php

namespace App\Models;

use App\Traits\HasFormattedDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, HasFormattedDates;

    protected $fillable = [
        'name',
        'description'
    ];

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }
}
