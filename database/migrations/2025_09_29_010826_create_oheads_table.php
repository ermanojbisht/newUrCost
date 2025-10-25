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
        Schema::create('oheads', function (Blueprint $table) {
            // Old Column Name: ID, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: raitemid, New Column Name: item_id, Data Type: unsignedBigInteger, Description: Foreign key to the items table.
            $table->unsignedBigInteger('item_id');
            // Old Column Name: oheadid, New Column Name: overhead_id, Data Type: unsignedBigInteger, Description: The ID of the overhead.
            $table->unsignedBigInteger('overhead_id');
            // Old Column Name: oon, New Column Name: calculation_type, Data Type: integer, Description: The type of overhead calculation.
            $table->integer('calculation_type');
            // Old Column Name: paramtr, New Column Name: parameter, Data Type: decimal, Description: The parameter for the overhead calculation.
            $table->decimal('parameter', 10, 4);
            // Old Column Name: sorder, New Column Name: sort_order, Data Type: integer, Description: A serial number for ordering the overheads.
            $table->integer('sort_order')->nullable();
            // Old Column Name: onitm, New Column Name: applicable_items, Data Type: string, Description: Specifies which items the overhead is applied to.
            $table->string('applicable_items')->nullable();
            // Old Column Name: ohdesc, New Column Name: description, Data Type: text, Description: A description of the overhead.
            $table->text('description')->nullable();
            // Old Column Name: BasedonID, New Column Name: based_on_id, Data Type: unsignedBigInteger, Description: Specifies what the overhead is based on.
            $table->unsignedBigInteger('based_on_id')->nullable(); // Assuming foreign key to overhead_masters table
            // Old Column Name: predate, New Column Name: valid_from, Data Type: date, Description: The start date for the validity of this overhead entry.
            $table->date('valid_from')->nullable();
            // Old Column Name: postdate, New Column Name: valid_to, Data Type: date, Description: The end date for the validity of this overhead entry.
            $table->date('valid_to')->nullable();
            // Old Column Name: canceled, New Column Name: is_canceled, Data Type: boolean, Description: A flag to indicate if the overhead entry is canceled.
            $table->boolean('is_canceled')->default(false);
            // Old Column Name: furtherOhead, New Column Name: allow_further_overhead, Data Type: boolean, Description: A flag to indicate if further overheads can be applied.
            $table->boolean('allow_further_overhead')->default(false);
            $table->timestamps();
            // Old Column Name: created_by, New Column Name: created_by, Data Type: unsignedBigInteger, Description: Foreign key to the users table.
            $table->unsignedBigInteger('created_by')->nullable();
            // Old Column Name: modify_by, New Column Name: updated_by, Data Type: unsignedBigInteger, Description: Foreign key to the users table.
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign key constraints
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('overhead_id')->references('id')->on('overhead_masters')->onDelete('cascade');
            $table->foreign('based_on_id')->references('id')->on('overhead_masters')->onDelete('set null'); // Assuming foreign key to overhead_masters table
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oheads');
    }
};
