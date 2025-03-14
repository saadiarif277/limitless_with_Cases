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
        Schema::create('clinics', function (Blueprint $table) {
            $table->id('clinic_id');
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone_number')->nullable();

            // Address
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->foreignId('state_id')->references('state_id')->on('states')->after('city')->nullable();
            $table->string('zip_code')->nullable();

            // Prices
            $table->string('price_read')->nullable();
            $table->string('price_scan')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};
