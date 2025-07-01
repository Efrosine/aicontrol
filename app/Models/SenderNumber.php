<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SenderNumber extends Model
{
    protected $fillable = ['name', 'api_key', 'number_key'];
}
