<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Player extends Model
{
    protected $fillable = [
        "name", "character_class", "lvl", "exp", "max_hp", "max_mp", "attack", "defense", "speed", "fire_res", "water_res", "electric_res", "gold", "icon"
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function monster():HasOne
    {
        return $this->hasOne(Monster::class);
    }
}
