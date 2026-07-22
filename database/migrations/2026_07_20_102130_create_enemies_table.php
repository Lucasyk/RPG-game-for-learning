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
        Schema::create('enemies', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->unsignedInteger("max_hp");
            $table->unsignedInteger("max_mp")->default(0);
            $table->unsignedInteger("attack");
            $table->unsignedInteger("defense");
            $table->unsignedInteger("speed");
            $table->integer("fire_res")->default(0);
            $table->integer("water_res")->default(0);
            $table->integer("electric_res")->default(0);
            $table->unsignedInteger("exp_reward")->default(10);
            $table->unsignedInteger("gold")->default(3);
            $table->string("image")->nullable();
            $table->text("description")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enemies');
    }
};
