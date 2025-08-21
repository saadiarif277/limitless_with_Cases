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
        Schema::table('cases', function (Blueprint $table) {
            // LOP Fields
            $table->date('lop_date')->nullable();
            $table->date('lop_expiration_date')->nullable();
            $table->enum('lop_status', ['active', 'expired', 'revoked'])->default('active');
            $table->enum('lop_acknowledgment_received', ['yes', 'no'])->default('no');
            $table->date('lop_acknowledgment_date')->nullable();
            $table->enum('lop_verification_status', ['verified', 'unverified'])->default('unverified');
            $table->date('lop_verification_date')->nullable();
            $table->string('lop_document')->nullable();
            
            // Attorney/Law Firm Information
            $table->string('law_firm_name')->nullable();
            $table->string('attorney_contact_person')->nullable();
            $table->string('attorney_phone', 20)->nullable();
            $table->string('attorney_fax', 20)->nullable();
            $table->string('attorney_bar_number', 50)->nullable();
            $table->string('attorney_file_number', 100)->nullable();
            
            // Case/Litigation Information
            $table->string('case_number', 100)->nullable();
            $table->string('court_jurisdiction')->nullable();
            $table->string('insurance_company_name')->nullable();
            $table->string('insurance_claim_number', 100)->nullable();
            $table->date('accident_date')->nullable();
            $table->text('accident_description')->nullable();
            
            // Financial Information
            $table->decimal('estimated_case_value', 15, 2)->nullable();
            $table->decimal('contingency_percentage', 5, 2)->nullable();
            $table->decimal('current_medical_specials', 15, 2)->nullable();
            $table->decimal('outstanding_balance', 15, 2)->nullable();
            
            // Treatment Authorization
            $table->text('authorized_treatment_types')->nullable();
            $table->text('treatment_limitations')->nullable();
            $table->date('authorization_expiration_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            // Drop LOP Fields
            $table->dropColumn([
                'lop_date',
                'lop_expiration_date',
                'lop_status',
                'lop_acknowledgment_received',
                'lop_acknowledgment_date',
                'lop_verification_status',
                'lop_verification_date',
                'lop_document',
                
                // Attorney/Law Firm Information
                'law_firm_name',
                'attorney_contact_person',
                'attorney_phone',
                'attorney_fax',
                'attorney_bar_number',
                'attorney_file_number',
                
                // Case/Litigation Information
                'case_number',
                'court_jurisdiction',
                'insurance_company_name',
                'insurance_claim_number',
                'accident_date',
                'accident_description',
                
                // Financial Information
                'estimated_case_value',
                'contingency_percentage',
                'current_medical_specials',
                'outstanding_balance',
                
                // Treatment Authorization
                'authorized_treatment_types',
                'treatment_limitations',
                'authorization_expiration_date',
            ]);
        });
    }
};
