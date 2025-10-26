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
        Schema::create('machine_indices', function (Blueprint $table) {
            // Old Column Name: ID, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: ResID, New Column Name: resource_id, Data Type: unsignedBigInteger, Description: The ID of the resource. Foreign key to the resources table.
            //if one 1 then it means it is applicable to all labor resources if for a particular resource no value is avilable
            $table->unsignedBigInteger('resource_id');
            // Old Column Name: RateCardID, New Column Name: rate_card_id, Data Type: unsignedBigInteger, Description: The ID of the rate card. Foreign key to the rate_cards table.
            $table->unsignedBigInteger('rate_card_id');
            // Old Column Name: perIndex, New Column Name: index_value, Data Type: decimal, Description: The index value as a percentage.
            $table->decimal('index_value', 5, 2);
            // Old Column Name: predate, New Column Name: valid_from, Data Type: date, Description: The start date for the validity of this index.
            $table->date('valid_from')->nullable();
            // Old Column Name: postdate, New Column Name: valid_to, Data Type: date, Description: The end date for the validity of this index.
            $table->date('valid_to')->nullable();
            // Old Column Name: locked, New Column Name: is_locked, Data Type: boolean, Description: A flag to indicate if the index is locked.
            $table->boolean('is_locked')->default(false);
            // Old Column Name: canceled, New Column Name: is_canceled, Data Type: boolean, Description: A flag to indicate if the index is canceled.
            $table->boolean('is_canceled')->default(false);
            $table->timestamps();
            // Old Column Name: created_by, New Column Name: created_by, Data Type: unsignedBigInteger, Description: Foreign key to the users table.
            $table->unsignedBigInteger('created_by')->nullable();

            // Foreign key constraints
            $table->foreign('rate_card_id')->references('id')->on('rate_cards')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_indices');
    }
};
