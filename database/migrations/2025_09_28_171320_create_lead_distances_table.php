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
        Schema::create('lead_distances', function (Blueprint $table) {
            // Old Column Name: id, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: ResID, New Column Name: resource_id, Data Type: unsignedBigInteger, Description: The ID of the resource. Foreign key to the resources table.
            $table->unsignedBigInteger('resource_id');
            // Old Column Name: RateCardID, New Column Name: rate_card_id, Data Type: unsignedBigInteger, Description: The ID of the rate card. Foreign key to the rate_cards table.
            $table->unsignedBigInteger('rate_card_id');
            // Old Column Name: Lead, New Column Name: distance, Data Type: decimal, Description: The lead distance in kilometers.
            $table->decimal('distance', 8, 2);
            // Old Column Name: leadType, New Column Name: type, Data Type: integer, Description: The type of lead (1: Mechanical, 2: Manual, 3: Mule).
            $table->integer('type');
            // Old Column Name: appdate, New Column Name: applicable_date, Data Type: date, Description: The application date of the lead distance.
            $table->date('applicable_date')->nullable();
            // Old Column Name: predate, New Column Name: valid_from, Data Type: date, Description: The start date for the validity of this lead distance.
            $table->date('valid_from')->nullable();
            // Old Column Name: postdate, New Column Name: valid_to, Data Type: date, Description: The end date for the validity of this lead distance.
            $table->date('valid_to')->nullable();
            // Old Column Name: locked, New Column Name: is_locked, Data Type: boolean, Description: A flag to indicate if the lead distance is locked.
            $table->boolean('is_locked')->default(false);
            // Old Column Name: canceled, New Column Name: is_canceled, Data Type: boolean, Description: A flag to indicate if the lead distance is canceled.
            $table->boolean('is_canceled')->default(false);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');
            $table->foreign('rate_card_id')->references('id')->on('rate_cards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_distances');
    }
};