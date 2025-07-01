<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastRecipient extends Model
{
    protected $fillable = ['name', 'phone_no', 'receive_cctv', 'receive_social'];

    protected $casts = [
        'receive_cctv' => 'boolean',
        'receive_social' => 'boolean',
    ];
}
