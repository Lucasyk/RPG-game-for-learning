<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Enemy;

class EnemySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Enemy::updateOrCreate(
            ["name"=>"Slime"],
            [
                "max_hp"=>7,
                "attack"=>4,
                "defense"=>4,
                "speed"=>4,
            ]
        );
        Enemy::updateOrCreate(
            ["name"=>"Goblin"],
            [
                "max_hp"=>12,
                "attack"=>6,
                "defense"=>5,
                "speed"=>5,
            ]
        );
        Enemy::updateOrCreate(
            ["name"=>"Wolf"],
            [
                "max_hp"=>18,
                "attack"=>7,
                "defense"=>7,
                "speed"=>10,
            ]
        );
    }
}
