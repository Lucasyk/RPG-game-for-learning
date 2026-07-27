<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('character_class');
            $table->string("description")->nullable();
            $table->unsignedInteger("level")->default(1);
            $table->unsignedInteger("exp")->default(0);
            $table->unsignedInteger("exp_to_next_level")->default(0);
            $table->unsignedInteger("gold")->default(0);
            $table->integer('max_hp')->default(10);
            $table->integer('max_mp')->default(10);
            $table->integer('attack')->default(3);
            $table->integer('defense')->default(1);
            $table->integer('speed')->default(1);
            $table->integer("fire_res")->default(0);
            $table->integer("water_res")->default(0);
            $table->integer("electric_res")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player');
    }
};
