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
        Schema::create('medical_specialties', function (Blueprint $table) {
            $table->id('medical_specialty_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('medical_specialty_id')->nullable()->references('medical_specialty_id')->on('medical_specialties');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_specialties');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('medical_specialty_id');
        });
    }
};
