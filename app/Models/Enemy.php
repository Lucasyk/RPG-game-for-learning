<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enemy extends Model
{
    protected $fillable = [
        'name',
        'max_hp',
        'max_mp',
        'attack',
        'defense',
        'speed',
        'fire_res',
        'water_res',
        'electric_res',
        'exp_reward',
        "gold",
        'image',
        'description',
        "icon"
    ];


}
