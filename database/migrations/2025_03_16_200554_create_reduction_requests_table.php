<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReductionRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('reduction_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_id'); // Foreign key to cases table
            $table->unsignedBigInteger('referral_id'); // Foreign key to referrals table
            $table->decimal('amount', 10, 2); // Reduction amount
            $table->string('file_path')->nullable(); // Path to the uploaded file
            $table->string('referral_status')->default('pending'); // Status of the referral
            $table->string('doctor_decision')->default('pending'); // Doctor's decision (accepted, rejected, pending)
            $table->decimal('counter_offer', 10, 2)->nullable(); // Doctor's counter offer
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('case_id')->references('case_id')->on('cases')->onDelete('cascade');
            $table->foreign('referral_id')->references('referral_id')->on('referrals')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reduction_requests');
    }
}
