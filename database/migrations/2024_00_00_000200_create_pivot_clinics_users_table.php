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
        Schema::create('pivot_clinics_users', function (Blueprint $table) {
            $table->id('pivot_clinics_users_id');
            $table->foreignId('clinic_id')->references('clinic_id')->on('clinics');
            $table->foreignId('user_id')->references('user_id')->on('users');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_clinics_users');
    }
};
