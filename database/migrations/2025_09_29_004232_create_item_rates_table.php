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
        Schema::create('item_rates', function (Blueprint $table) {
            // Old Column Name: racode, New Column Name: item_id, Data Type: unsignedBigInteger, Description: Foreign key to the items table.
            $table->unsignedBigInteger('item_id');
            // Old Column Name: rate, New Column Name: rate, Data Type: decimal, Description: The calculated rate of the item.
            $table->decimal('rate', 10, 4);
            // Old Column Name: laborcost, New Column Name: labor_cost, Data Type: decimal, Description: The labor cost component of the rate.
            $table->decimal('labor_cost', 10, 4);
            // Old Column Name: materialcost, New Column Name: material_cost, Data Type: decimal, Description: The material cost component of the rate.
            $table->decimal('material_cost', 10, 4);
            // Old Column Name: machinecost, New Column Name: machine_cost, Data Type: decimal, Description: The machine cost component of the rate.
            $table->decimal('machine_cost', 10, 4);
            // Old Column Name: ocost, New Column Name: overhead_cost, Data Type: decimal, Description: The overhead cost component of the rate.
            $table->decimal('overhead_cost', 10, 4);
            // Old Column Name: ratecard, New Column Name: rate_card_id, Data Type: unsignedBigInteger, Description: Foreign key to the rate_cards table.
            $table->unsignedBigInteger('rate_card_id');
            // Old Column Name: date, New Column Name: calculation_date, Data Type: date, Description: The date when the rate was calculated.
            $table->date('calculation_date');
            // Old Column Name: predate, New Column Name: valid_from, Data Type: date, Description: The start date for the rate's validity.
            $table->date('valid_from')->nullable();
            // Old Column Name: postdate, New Column Name: valid_to, Data Type: date, Description: The end date for the rate's validity.
            $table->date('valid_to')->nullable();
            // Old Column Name: UnitID, New Column Name: unit_id, Data Type: unsignedBigInteger, Description: Foreign key to the units table.
            $table->unsignedBigInteger('unit_id')->nullable();
            // Old Column Name: locked, New Column Name: is_locked, Data Type: boolean, Description: A flag to indicate if the rate is locked.
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            // Composite primary key
            $table->primary(['item_id', 'rate_card_id', 'calculation_date']);

            // Foreign key constraints
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('rate_card_id')->references('id')->on('rate_cards')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_rates');
    }
};