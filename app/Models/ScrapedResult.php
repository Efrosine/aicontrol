<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScrapedResult extends Model
{
    protected $fillable = ['data', 'account', 'url'];
}
