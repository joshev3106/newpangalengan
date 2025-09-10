<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotspot extends Model
{
    protected $fillable = ['name','lat','lng','confidence','cases'];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'confidence' => 'integer',
        'cases' => 'integer',
    ];
}
