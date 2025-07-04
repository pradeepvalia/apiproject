<?php

namespace App\Traits;

use DateTimeInterface;

trait HasFormattedDates
{
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s.00');
    }
}
