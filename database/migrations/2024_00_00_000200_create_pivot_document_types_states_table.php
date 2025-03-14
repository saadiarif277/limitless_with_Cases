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
        Schema::create('pivot_document_types_states', function (Blueprint $table) {
            $table->id('pivot_document_types_states_id');
            $table->foreignId('document_type_id')->references('document_type_id')->on('document_types');
            $table->foreignId('state_id')->references('state_id')->on('states');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_document_types_states');
    }
};
