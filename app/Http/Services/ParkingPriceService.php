<?php

namespace App\Http\Services;

use App\Models\Zone;
use Carbon\Carbon;

class ParkingPriceService
{
    public static function calculatePrice(int $zoneId, string $startTime, string $stopTime = null)
    {
        $start = new Carbon($startTime);
        $stop = is_null($stopTime) ? now() : new Carbon($stopTime);

        $startedHours = ceil($stop->diffInMinutes($start)/60);

        return $startedHours * Zone::find($zoneId)->price_per_hour;
    }
}
