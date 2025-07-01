<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CctvDetectionResult extends Model
{
    protected $fillable = ['cctv_id', 'data', 'snapshoot_url'];

    protected $casts = [
        'data' => 'array',
    ];

    public function cctv()
    {
        return $this->belongsTo(Cctv::class);
    }
}
