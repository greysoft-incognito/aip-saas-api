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
        Schema::create('soil_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('crop');
            $table->string('water')->nullable();
            $table->string('temperature')->nullable();
            $table->string('period')->nullable();
            $table->string('details')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soil_requirements');
    }
};
