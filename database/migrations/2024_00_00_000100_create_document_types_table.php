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
        Schema::create('document_types', function (Blueprint $table) {
            $table->id('document_type_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('document_category_id')->references('document_category_id')->on('document_categories');
            $table->boolean('is_generated')->default(false);
            $table->boolean('is_permanent')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
