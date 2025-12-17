<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    protected $fillable = [
        'temperature',
        'bat_v',
        'panel_v',
        'panel_power',
        'charging_power',
        'bat_percent',
        'bat_wh',
    ];

    protected $casts = [
        'temperature' => 'float',
        'bat_v' => 'float',
        'panel_v' => 'float',
        'panel_power' => 'float',
        'charging_power' => 'float',
        'bat_percent' => 'float',
        'bat_wh' => 'float',
    ];
}
