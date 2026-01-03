<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('condition_logs', function (Blueprint $table) {
            $table->id();
            $table->string('status')->index(); // NORMAL / WARMING / WARNING / DANGER / OTHER
            $table->float('temperature')->nullable();
            $table->float('bat_v')->nullable();
            $table->float('panel_v')->nullable();
            $table->float('panel_power')->nullable();
            $table->float('charging_power')->nullable();
            $table->float('bat_percent')->nullable();
            $table->float('bat_wh')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('condition_logs');
    }
};


