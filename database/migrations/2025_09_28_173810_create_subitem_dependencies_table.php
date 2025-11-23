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
        Schema::create('subitem_dependencies', function (Blueprint $table) {
            // Old Column Name: ID, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: raitemid, New Column Name: item_id, Data Type: unsignedBigInteger, Description: Foreign key to the items table (the main item).
            $table->string('item_code');
            // Old Column Name: subitem, New Column Name: sub_item_id, Data Type: unsignedBigInteger, Description: Foreign key to the items table (the sub-item).
            $table->string('sub_item_code');
            // Old Column Name: lvl, New Column Name: level, Data Type: integer, Description: The level of the sub-item in the dependency tree.
            $table->integer('level');
            // Old Column Name: pos, New Column Name: position, Data Type: integer, Description: The position of the sub-item at its level.
            $table->integer('position');
            // Old Column Name: dResQty, New Column Name: quantity, Data Type: decimal, Description: The quantity of the sub-item required.
            $table->decimal('quantity', 10, 4);
            // Old Column Name: UnitID, New Column Name: unit_id, Data Type: unsignedBigInteger, Description: Foreign key to the units table.
            $table->unsignedBigInteger('unit_id')->nullable();
            // Old Column Name: PturnOutQty, New Column Name: parent_turnout_quantity, Data Type: decimal, Description: The turnout quantity of the parent item.
            $table->decimal('parent_turnout_quantity', 10, 4);
            // Old Column Name: PitemCarryOH, New Column Name: parent_carries_overhead, Data Type: boolean, Description: A flag to indicate if the parent item carries overhead.
            $table->boolean('parent_carries_overhead')->default(false);
            // Old Column Name: Pohapplicability, New Column Name: parent_overhead_applicability, Data Type: boolean, Description: A flag to indicate if overhead is applicable to the parent item.
            $table->boolean('parent_overhead_applicability')->default(false);
            // Old Column Name: predate, New Column Name: valid_from, Data Type: date, Description: The start date for the validity of this dependency.
            $table->date('valid_from')->nullable();
            // Old Column Name: postdate, New Column Name: valid_to, Data Type: date, Description: The end date for the validity of this dependency.
            $table->date('valid_to')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subitem_dependencies');
    }
};
