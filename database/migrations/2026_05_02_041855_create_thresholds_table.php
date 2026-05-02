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
        Schema::create('thresholds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kolam_id')->constrained('kolams')->onDelete('cascade');
            $table->float('ph_bawah')->default(0);
            $table->float('ph_atas')->default(14);
            $table->float('ketinggian_batas_bawah')->default(0);
            $table->float('ketinggian_batas_atas')->default(100);
            $table->float('suhu_bawah')->default(0);
            $table->float('suhu_atas')->default(50);
            $table->float('salinitas_bawah')->default(0);
            $table->float('salinitas_atas')->default(100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thresholds');
    }
};
