<?php

namespace App\Models;

use App\Traits\HasFormattedDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory, HasFormattedDates;

    protected $fillable = [
        'full_name',
        'email',
        'mobile_number',
        'address',
        'amount',
        'payment_method',
        'status',
        'transaction_id'
    ];
}
