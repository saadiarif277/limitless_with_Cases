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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id('referral_id');

            $table->foreignId('state_id')->references('state_id')->on('states')->after('city');
            $table->foreignId('appointment_id')->nullable()->references('appointment_id')->on('appointments');
            $table->foreignId('referral_status_id')->references('referral_status_id')->on('referral_statuses');
            $table->foreignId('clinic_id')->nullable()->references('clinic_id')->on('clinics');
            $table->foreignId('source_user_id')->nullable()->references('user_id')->on('users');
            $table->foreignId('patient_user_id')->nullable()->references('user_id')->on('users');
            $table->foreignId('attorney_user_id')->nullable()->references('user_id')->on('users');
            $table->foreignId('doctor_user_id')->nullable()->references('user_id')->on('users');
            
            $table->dateTime('referral_date')->nullable();
            $table->dateTime('injury_date')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
