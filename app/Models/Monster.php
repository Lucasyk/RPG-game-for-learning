<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Monster extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'hp',
        'attack',
        'defense',
        'speed',
    ];

    public function player():BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
