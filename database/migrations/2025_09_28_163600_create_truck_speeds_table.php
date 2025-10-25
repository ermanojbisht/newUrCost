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
        Schema::create('truck_speeds', function (Blueprint $table) {
            // Old Column Name: LeadKm, New Column Name: lead_distance, Data Type: decimal, Description: The lead distance in kilometers.
            $table->decimal('lead_distance', 8, 2)->primary(); // Assuming lead_distance is unique and serves as a primary key
            // Old Column Name: AverageSpeed, New Column Name: average_speed, Data Type: decimal, Description: The average speed of the truck.
            $table->decimal('average_speed', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('truck_speeds');
    }
};
