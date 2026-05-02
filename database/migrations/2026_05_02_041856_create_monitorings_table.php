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
        Schema::create('monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kolam_id')->constrained('kolams')->onDelete('cascade');
            $table->float('ph');
            $table->float('ketinggian_air');
            $table->float('suhu_air');
            $table->float('salinitas');
            $table->integer('rssi');
            $table->integer('delay');
            $table->timestamp('device_timestamp');
            $table->timestamp('waktu_monitoring')->useCurrent();
            $table->timestamps();

            // Indexing for performance as requested in 10.6
            $table->index('kolam_id');
            $table->index('waktu_monitoring');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitorings');
    }
};
