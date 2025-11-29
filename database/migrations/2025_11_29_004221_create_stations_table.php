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
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('nodal_rate_card_id')->nullable();
            $table->unsignedBigInteger('nodal_resource_id')->nullable();
            $table->json('resources')->nullable(); // JSON array of resource_ids
            $table->json('rate_card_ids')->nullable(); // JSON array of rate_card_ids
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};
