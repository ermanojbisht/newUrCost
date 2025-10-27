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
        Schema::create('items', function (Blueprint $table) {
            // Old Column Name: chId, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the items table.
            $table->bigIncrements('id')->unique();
            // Old Column Name: sorId, New Column Name: sor_id, Data Type: unsignedBigInteger, Description: Foreign key to the sors table.
            $table->unsignedBigInteger('sor_id');
            // Old Column Name: chParentId, New Column Name: parent_id, Data Type: unsignedBigInteger, Description: Foreign key to the same table for self-referencing.
            $table->unsignedBigInteger('parent_id')->nullable();
            // Old Column Name: itemcode, New Column Name: item_code, Data Type: string, Description: The unique code for the item.
            $table->string('item_code');
            // Old Column Name: itemname, New Column Name: name, Data Type: text, Description: The full, hierarchical name of the item.
            $table->text('name');
            // Old Column Name: orderInParent, New Column Name: order_in_parent, Data Type: integer, Description: The order of the item within its parent chapter.
            $table->integer('order_in_parent')->nullable();
            // Old Column Name: SpcCode, New Column Name: specification_code, Data Type: text, Description: The specification code.
            $table->text('specification_code')->nullable();
            // Old Column Name: SpcPageNO, New Column Name: specification_page_number, Data Type: text, Description: The specification page number.
            $table->text('specification_page_number')->nullable();
            // Old Column Name: chIdtype, New Column Name: item_type, Data Type: string, Description: The type of the item (e.g., chapter, item).
            $table->string('item_type')->nullable();
            // Old Column Name: maybeusedforsorting, New Column Name: sort_order, Data Type: text, Description: A field that might be used for sorting.
            $table->text('sort_order')->nullable();
            // Old Column Name: ItemNo, New Column Name: item_number, Data Type: text, Description: The item number (e.g., "1.1", "1.2a").
            $table->text('item_number')->nullable();
            // Old Column Name: ItemDesc, New Column Name: description, Data Type: text, Description: The detailed description of the item.
            $table->text('description')->nullable();
            // Old Column Name: ItemShortDesc, New Column Name: short_description, Data Type: text, Description: A shorter description of the item.
            $table->text('short_description')->nullable();
            // Old Column Name: TurnOutQuantity, New Column Name: turnout_quantity, Data Type: decimal, Description: The quantity of the item produced by the given resources.
            $table->decimal('turnout_quantity', 10, 4)->nullable();
            // Old Column Name: Assumption, New Column Name: assumptions, Data Type: text, Description: Assumptions made for the item.
            $table->text('assumptions')->nullable();
            // Old Column Name: FootNote, New Column Name: footnotes, Data Type: text, Description: Footnotes for the item.
            $table->text('footnotes')->nullable();
            // Old Column Name: UnitID, New Column Name: unit_id, Data Type: unsignedBigInteger, Description: Foreign key to the units table.
            $table->unsignedBigInteger('unit_id')->nullable();
            // Old Column Name: canceled, New Column Name: is_canceled, Data Type: boolean, Description: A flag to indicate if the item is canceled.
            $table->boolean('is_canceled')->default(false);
            // Old Column Name: orderFromNestedList, New Column Name: nested_list_order, Data Type: integer, Description: The order of the item from a nested list.
            $table->integer('nested_list_order')->nullable();
            // Old Column Name: subitemlvl, New Column Name: sub_item_level, Data Type: integer, Description: The level of the item in the sub-item hierarchy.
            $table->integer('sub_item_level')->nullable();
            // Old Column Name: noOfSubitem, New Column Name: sub_item_count, Data Type: integer, Description: The number of sub-items that this item contains.
            $table->integer('sub_item_count')->default(0);
            // Old Column Name: olditemcode, New Column Name: old_item_code, Data Type: string, Description: The old item code.
            $table->string('old_item_code')->nullable();
            // Old Column Name: dsr16id, New Column Name: dsr_16_id, Data Type: string, Description: The DSR 16 ID.
            $table->string('dsr_16_id')->nullable();
            // Old Column Name: locked, New Column Name: is_locked, Data Type: boolean, Description: A flag to indicate if the item is locked.
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
            // Old Column Name: created_by, New Column Name: created_by, Data Type: unsignedBigInteger, Description: Foreign key to the users table.
            $table->unsignedBigInteger('created_by')->nullable();
            // Old Column Name: modify_by, New Column Name: updated_by, Data Type: unsignedBigInteger, Description: Foreign key to the users table.
            $table->unsignedBigInteger('updated_by')->nullable();
            // Old Column Name: ref_from, New Column Name: reference_from, Data Type: unsignedBigInteger, Description: The reference from which the item was created.
            $table->unsignedBigInteger('reference_from')->nullable();

            // Foreign key constraints
            $table->foreign('sor_id')->references('id')->on('sors')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('reference_from')->references('id')->on('items')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('items');
        Schema::enableForeignKeyConstraints();
    }
};
