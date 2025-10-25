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
        Schema::create('pol_skeletons', function (Blueprint $table) {
            // Old Column Name: ID, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: Ddate, New Column Name: date, Data Type: date, Description: The date of the data.
            $table->date('date');
            // Old Column Name: DesilMailage, New Column Name: diesel_mileage, Data Type: decimal, Description: The mileage of diesel.
            $table->decimal('diesel_mileage', 8, 2);
            // Old Column Name: MobileMailage, New Column Name: mobile_oil_mileage, Data Type: decimal, Description: The mileage of mobile oil.
            $table->decimal('mobile_oil_mileage', 8, 2);
            // Old Column Name: NoofMazdoors, New Column Name: number_of_laborers, Data Type: integer, Description: The number of laborers.
            $table->integer('number_of_laborers');
            // Old Column Name: predate, New Column Name: valid_from, Data Type: date, Description: The start date for the data's validity.
            $table->date('valid_from')->nullable();
            // Old Column Name: postdate, New Column Name: valid_to, Data Type: date, Description: The end date for the data's validity.
            $table->date('valid_to')->nullable();
            // Old Column Name: locked, New Column Name: is_locked, Data Type: boolean, Description: A flag to indicate if the data is locked.
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pol_skeletons');
    }
};
