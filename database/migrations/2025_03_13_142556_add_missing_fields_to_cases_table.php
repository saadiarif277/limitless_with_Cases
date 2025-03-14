<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingFieldsToCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cases', function (Blueprint $table) {
            // Add new fields
            $table->text('referral_notes')->nullable()->after('referral_ids');
            $table->tinyInteger('lop_selected')->default(0)->after('referral_notes');
            $table->tinyInteger('insurance_selected')->default(0)->after('lop_selected');
            $table->text('cpt_code_values')->nullable()->after('cpt_codes');
            $table->decimal('lop_deduction_amount', 10, 2)->default(0.00)->after('cpt_code_values');
            $table->tinyInteger('reduction_requested')->default(0)->after('lop_deduction_amount');
            $table->decimal('reduction_amount', 10, 2)->default(0.00)->after('reduction_requested');
            $table->decimal('patient_reimbursement_amount', 10, 2)->default(0.00)->after('reduction_amount');
            $table->tinyInteger('is_1500_form_generated')->default(0)->after('patient_reimbursement_amount');
            $table->timestamp('1500_form_generated_at')->nullable()->after('is_1500_form_generated');
            $table->timestamp('referral_accepted_at')->nullable()->after('1500_form_generated_at');
            $table->timestamp('referral_completed_at')->nullable()->after('referral_accepted_at');
            $table->text('patient_info')->nullable()->after('referral_completed_at');
            $table->text('attorney_notes')->nullable()->after('patient_info');
            $table->text('piloting_physician_notes')->nullable()->after('attorney_notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cases', function (Blueprint $table) {
            // Drop the added fields
            $table->dropColumn([
                'referral_notes',
                'lop_selected',
                'insurance_selected',
                'cpt_code_values',
                'lop_deduction_amount',
                'reduction_requested',
                'reduction_amount',
                'patient_reimbursement_amount',
                'is_1500_form_generated',
                '1500_form_generated_at',
                'referral_accepted_at',
                'referral_completed_at',
                'patient_info',
                'attorney_notes',
                'piloting_physician_notes',
            ]);
        });
    }
}
