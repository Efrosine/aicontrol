<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialDetectionResult extends Model
{
    protected $fillable = ['scraped_data_id', 'data'];
    protected $casts = [
        'data' => 'array',
    ];
}
