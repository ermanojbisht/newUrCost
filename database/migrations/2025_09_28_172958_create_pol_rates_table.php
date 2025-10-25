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
        Schema::create('pol_rates', function (Blueprint $table) {
            // Old Column Name: ID, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: Ddate, New Column Name: rate_date, Data Type: date, Description: The date of the rate.
            $table->date('rate_date');
            // Old Column Name: DesilRate, New Column Name: diesel_rate, Data Type: decimal, Description: The rate of diesel.
            $table->decimal('diesel_rate', 8, 2);
            // Old Column Name: MobileRate, New Column Name: mobile_oil_rate, Data Type: decimal, Description: The rate of mobile oil.
            $table->decimal('mobile_oil_rate', 8, 2);
            // Old Column Name: MazdoorCharges, New Column Name: laborer_charges, Data Type: decimal, Description: The charges for laborers.
            $table->decimal('laborer_charges', 8, 2);
            // Old Column Name: HiringCharges, New Column Name: hiring_charges, Data Type: decimal, Description: The hiring charges.
            $table->decimal('hiring_charges', 8, 2);
            // Old Column Name: OHCharges, New Column Name: overhead_charges, Data Type: decimal, Description: The overhead charges.
            $table->decimal('overhead_charges', 8, 2);
            // Old Column Name: MuleRate, New Column Name: mule_rate, Data Type: decimal, Description: The rate for mules.
            $table->decimal('mule_rate', 8, 2);
            // Old Column Name: predate, New Column Name: valid_from, Data Type: date, Description: The start date for the rate's validity.
            $table->date('valid_from')->nullable();
            // Old Column Name: postdate, New Column Name: valid_to, Data Type: date, Description: The end date for the rate's validity.
            $table->date('valid_to')->nullable();
            // Old Column Name: locked, New Column Name: is_locked, Data Type: boolean, Description: A flag to indicate if the rate is locked.
            $table->boolean('is_locked')->default(false);
            // Old Column Name: publish_date, New Column Name: published_at, Data Type: timestamp, Description: The date the rate was published.
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pol_rates');
    }
};