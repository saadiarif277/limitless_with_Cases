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
        Schema::create('pivot_documents_referrals', function (Blueprint $table) {
            $table->id('pivot_documents_referrals_id');
            $table->foreignId('document_id')->references('document_id')->on('documents');
            $table->foreignId('referral_id')->references('referral_id')->on('referrals');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_documents_referrals');
    }
};
