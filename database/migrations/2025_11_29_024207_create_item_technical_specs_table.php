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
        Schema::create('item_technical_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->text('introduction')->nullable();
            $table->json('specifications')->nullable();
            $table->json('tests_frequency')->nullable();
            $table->json('dos_donts')->nullable();
            $table->json('execution_sequence')->nullable();
            $table->json('precautionary_measures')->nullable();
            $table->json('reference_links')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_technical_specs');
    }
};
