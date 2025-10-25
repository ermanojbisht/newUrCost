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
        Schema::create('nested_table_items', function (Blueprint $table) {
            // Old Column Name: id, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: lft, New Column Name: _lft, Data Type: integer, Description: The left value of the nested set model.
            $table->integer('_lft');
            // Old Column Name: rgt, New Column Name: _rgt, Data Type: integer, Description: The right value of the nested set model.
            $table->integer('_rgt');
            // Old Column Name: ItemNo, New Column Name: item_number, Data Type: string, Description: The item number.
            $table->string('item_number')->nullable();
            // Old Column Name: sorId, New Column Name: sor_id, Data Type: unsignedBigInteger, Description: Foreign key to the sors table.
            $table->unsignedBigInteger('sor_id')->nullable();
            // Old Column Name: itemOrChapter, New Column Name: is_chapter, Data Type: boolean, Description: A flag to indicate if the node is a chapter.
            $table->boolean('is_chapter')->default(false);
            // Old Column Name: parent_id, New Column Name: parent_id, Data Type: unsignedBigInteger, Description: The ID of the parent node.
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('sor_id')->references('id')->on('sors')->onDelete('set null');
            $table->foreign('parent_id')->references('id')->on('nested_table_items')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nested_table_items');
    }
};