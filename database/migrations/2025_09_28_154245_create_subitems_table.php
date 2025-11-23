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
        Schema::create('subitems', function (Blueprint $table) {
            // Old Column Name: ID, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: raitemid, New Column Name: item_id, Data Type: unsignedBigInteger, Description: Foreign key to the items table (the main item).
            $table->unsignedBigInteger('item_code');
            // Old Column Name: subraitem, New Column Name: sub_item_id, Data Type: unsignedBigInteger, Description: Foreign key to the items table (the sub-item).
            $table->unsignedBigInteger('sub_item_code');
            // Old Column Name: dResQty, New Column Name: quantity, Data Type: decimal, Description: The quantity of the sub-item required.
            $table->decimal('quantity', 10, 4);
            // Old Column Name: Percentage, New Column Name: is_oh_applicable, Data Type: integer, Description: in subitem further oh is applicable or not.
            $table->integer('is_oh_applicable')->default(0);
            // Old Column Name: BasedonID, New Column Name: is_overhead, Data Type: integer, Description: subitem oh will be taken or not.
            $table->integer('is_overhead')->default(1);
            // Old Column Name: SrNo, New Column Name: sort_order, Data Type: integer, Description: A serial number for ordering the sub-items.
            $table->integer('sort_order')->nullable();
            // Old Column Name: UnitID, New Column Name: unit_id, Data Type: unsignedBigInteger, Description: Foreign key to the units table.
            $table->unsignedBigInteger('unit_id')->nullable();
            // Old Column Name: Remark, New Column Name: remarks, Data Type: text, Description: A remark or description for the sub-item.
            $table->text('remarks')->nullable();
            // Old Column Name: predate, New Column Name: valid_from, Data Type: date, Description: The start date for the validity of this relationship.
            $table->date('valid_from')->nullable();
            // Old Column Name: postdate, New Column Name: valid_to, Data Type: date, Description: The end date for the validity of this relationship.
            $table->date('valid_to')->nullable();
            // Old Column Name: factor, New Column Name: factor, Data Type: decimal, Description: A multiplication factor for the quantity.
            $table->decimal('factor', 8, 4)->nullable();
            $table->timestamps();
            // Old Column Name: created_by, New Column Name: created_by, Data Type: unsignedBigInteger, Description: Foreign key to the users table.
            $table->unsignedBigInteger('created_by')->nullable();
            // Old Column Name: modify_by, New Column Name: updated_by, Data Type: unsignedBigInteger, Description: Foreign key to the users table.
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign key constraints
           /* $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('sub_item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subitems');
    }
};
