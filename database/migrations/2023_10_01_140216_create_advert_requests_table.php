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
        Schema::create('advert_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('image')->nullable();
            $table->string('title')->nullable();
            $table->text('line1')->nullable();
            $table->text('line2')->nullable();
            $table->text('line3')->nullable();
            $table->integer('duration')->default(24)->nullable();
            $table->string('status')->nullable()->default('draft');
            $table->timestamps();
        });

        Schema::table('slides', function (Blueprint $table) {
            if (!Schema::hasColumn('slides', 'advert_request_id')) {
                $table->foreignId('advert_request_id')->after('id')->nullable()->constrained('advert_requests');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('slides', 'advert_request_id')) {
            Schema::table('slides', function (Blueprint $table) {
                $table->dropForeign(['advert_request_id']);
                $table->dropColumn('advert_request_id');
            });
        }

        Schema::dropIfExists('advert_requests');
    }
};