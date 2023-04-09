<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price_per_hour'
    ];

    public function parkings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Parking::class);
    }
}
