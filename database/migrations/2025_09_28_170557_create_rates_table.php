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
        Schema::create('rates', function (Blueprint $table) {
            // Old Column Name: ID, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: resourceid, New Column Name: resource_id, Data Type: unsignedBigInteger, Description: The ID of the resource. Foreign key to the resources table.
            $table->unsignedBigInteger('resource_id');
            // Old Column Name: ratecard, New Column Name: rate_card_id, Data Type: unsignedBigInteger, Description: The ID of the rate card. Foreign key to the rate_cards table.
            $table->unsignedBigInteger('rate_card_id');
            // Old Column Name: appdate, New Column Name: applicable_date, Data Type: date, Description: The application date of the rate.
            $table->date('applicable_date');
            // Old Column Name: unit, New Column Name: unit_id, Data Type: unsignedBigInteger, Description: The unit of measurement for the rate. Foreign key to the units table.
            $table->unsignedBigInteger('unit_id')->nullable();
            // Old Column Name: rate, New Column Name: rate, Data Type: decimal, Description: The base rate of the resource.
            $table->decimal('rate', 10, 4);
            // Old Column Name: predate, New Column Name: valid_from, Data Type: date, Description: The start date for the rate's validity.
            $table->date('valid_from')->nullable();
            // Old Column Name: postdate, New Column Name: valid_to, Data Type: date, Description: The end date for the rate's validity.
            $table->date('valid_to')->nullable();
            // Old Column Name: remark, New Column Name: remarks, Data Type: text, Description: A remark or description for the rate.
            $table->text('remarks')->nullable();
            // Old Column Name: locked, New Column Name: is_locked, Data Type: boolean, Description: A flag to indicate if the rate is locked.
            $table->boolean('is_locked')->default(false);
            // Old Column Name: publish_date, New Column Name: published_at, Data Type: timestamp, Description: The date the rate was published.
            $table->timestamp('published_at')->nullable();
            // Old Column Name: tax, New Column Name: tax, Data Type: decimal, Description: The tax amount included in the rate.
            $table->decimal('tax', 8, 4)->nullable();
            $table->timestamps();
            // Old Column Name: created_by, New Column Name: created_by, Data Type: unsignedBigInteger, Description: Foreign key to the users table.
            $table->unsignedBigInteger('created_by')->nullable();
            // Old Column Name: modify_by, New Column Name: updated_by, Data Type: unsignedBigInteger, Description: Foreign key to the users table.
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign key constraints
            $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');
            $table->foreign('rate_card_id')->references('id')->on('rate_cards')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};