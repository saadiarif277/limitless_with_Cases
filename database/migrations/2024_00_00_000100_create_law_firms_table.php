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
        Schema::create('law_firms', function (Blueprint $table) {
            $table->id('law_firm_id');
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone_number')->nullable();

            // Address
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->foreignId('state_id')->references('state_id')->on('states')->after('city')->nullable();
            $table->string('zip_code')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Modify Users Table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('law_firm_id')->nullable()->references('law_firm_id')->on('law_firms')->after('weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('law_firms');
    }
};
