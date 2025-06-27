<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cctv extends Model
{
    protected $fillable = [
        'name',
        'origin_url',
        'stream_url',
        'location',
    ];

    public function detectionResults(): HasMany
    {
        return $this->hasMany(CctvDetectionResult::class);
    }
}