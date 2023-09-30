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
        Schema::create('current_prices', function (Blueprint $table) {
            $table->id();
            $table->string('item');
            $table->string('icon')->nullable()->default('fa-solid fa-wheat-awn');
            $table->string('unit')->nullable()->default('bags');
            $table->decimal('price')->default(0.0);
            $table->integer('available_qty')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('current_prices');
    }
};