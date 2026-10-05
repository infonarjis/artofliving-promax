<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    /**
     * Format all model dates when converting to JSON/API response.
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date
            ->setTimezone(new \DateTimeZone('Asia/Kolkata'))
            ->format('Y-m-d H:i:s');
    }
}
