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
        Schema::create('pivot_referrals_referral_reasons', function (Blueprint $table) {
            $table->id('pivot_referrals_referral_reasons_id');
            $table->foreignId('referral_id')->references('referral_id')->on('referrals');
            $table->foreignId('referral_reason_id')->references('referral_reason_id')->on('referral_reasons');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_referrals_referral_reasons');
    }
};
