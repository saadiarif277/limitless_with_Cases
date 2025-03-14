<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cases', function (Blueprint $table) {
            $table->id('case_id'); // Primary key for the case
            $table->unsignedBigInteger('patient_id'); // Links to a single patient
            $table->unsignedBigInteger('attorney_id')->nullable(); // Case managing attorney
            $table->unsignedBigInteger('piloting_physician_id')->nullable(); // Piloting physician
            $table->text('policy_limit_info')->nullable(); // Policy limit details
            $table->unsignedBigInteger('primary_referral_id'); // Main referral for the case
            $table->text('referral_ids')->nullable(); // Additional referrals linked to the case (JSON or comma-separated)
            $table->text('icd10_codes')->nullable(); // ICD-10 diagnosis codes
            $table->text('cpt_codes')->nullable(); // CPT codes for services
            $table->decimal('service_billed', 10, 2)->default(0.00); // Total amount billed for services
            $table->string('billing_type')->nullable(); // Billing type (Insurance or LOP)
            $table->boolean('is_cms1500_generated')->default(false); // Tracks if CMS-1500 form has been generated
            $table->boolean('case_won')->nullable(); // True if case won, false if lost
            $table->string('outcome')->nullable(); // Detailed case outcome
            $table->boolean('reduction_accepted')->nullable(); // Indicates if a reduction was accepted
            $table->boolean('is_closed')->default(false); // Marks the case as closed
            $table->timestamp('closed_at')->nullable(); // Date and time of case closure
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('patient_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('attorney_id')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('piloting_physician_id')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('primary_referral_id')->references('referral_id')->on('referrals')->onDelete('cascade');
        });

        Schema::create('bills', function (Blueprint $table) {
            $table->id('bill_id'); // Primary key for bills
            $table->unsignedBigInteger('case_id'); // Links the bill to the case
            $table->text('icd10_codes'); // ICD-10 codes for diagnosis
            $table->text('cpt_codes'); // CPT codes for services
            $table->decimal('amount', 10, 2); // Amount billed
            $table->string('billing_type'); // Insurance or LOP
            $table->boolean('reduction_requested')->default(false); // Whether reduction has been requested
            $table->boolean('reduction_approved')->nullable(); // Indicates if reduction was approved
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('case_id')->references('case_id')->on('cases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bills');
        Schema::dropIfExists('cases');
    }
};
