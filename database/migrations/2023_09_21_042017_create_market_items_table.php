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
        Schema::create('market_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('slug')->nullable();
            $table->string('image')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('grade')->nullable()->default('D');
            $table->integer('quantity')->nullable();
            $table->decimal('price')->default(0.0);
            $table->string('quantity_unit')->nullable()->default('KG');
            $table->string('location')->nullable();
            $table->string('address')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->boolean('active')->default(false);
            $table->boolean('approved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_items');
    }
};
