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
        Schema::create('rate_cards', function (Blueprint $table) {
            // Old Column Name: id, New Column Name: droped as not needed

            // Old Column Name: ratecardid, New Column Name: rate_card_code, Data Type: bigIncrements, Description: Primary key for the table. The unique code for the rate card. id,
            $table->bigIncrements('id')->unique();
            // Old Column Name: ratecardname, New Column Name: name, Data Type: string, Description: The name of the rate card (region).
            $table->string('name');
            // Old Column Name: ratecardgrpid, removed as not needed

            // Old Column Name: description, New Column Name: description, Data Type: text, Description: A description of the rate card.
            $table->text('description')->nullable();
            // Old Column Name: created_by, New Column Name: created_by, Data Type: unsignedBigInteger, Description: Foreign key to the users table.
            $table->unsignedBigInteger('created_by')->nullable();
            // Old Column Name: modify_by, New Column Name: updated_by, Data Type: unsignedBigInteger, Description: Foreign key to the users table.
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('rate_cards');
        Schema::enableForeignKeyConstraints();
    }
};
