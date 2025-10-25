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
        Schema::create('files', function (Blueprint $table) {
            // Old Column Name: id, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: title, New Column Name: title, Data Type: string, Description: The title of the file.
            $table->string('title');
            // Old Column Name: file_name, New Column Name: filename, Data Type: string, Description: The name of the file.
            $table->string('filename');
            // Old Column Name: status, New Column Name: status, Data Type: enum, Description: The status of the file ('active', 'inactive', 'deleted').
            $table->enum('status', ['active', 'inactive', 'deleted'])->default('active');
            // Old Column Name: typeofdoc, New Column Name: document_type, Data Type: string, Description: The type of the document.
            $table->string('document_type');
            // Old Column Name: ratecard, New Column Name: rate_card_id, Data Type: unsignedBigInteger, Description: Foreign key to the rate_cards table.
            $table->unsignedBigInteger('rate_card_id')->nullable();
            // Old Column Name: sorid, New Column Name: sor_id, Data Type: unsignedBigInteger, Description: Foreign key to the sors table.
            $table->unsignedBigInteger('sor_id')->nullable();
            // Old Column Name: created_by, New Column Name: created_by, Data Type: unsignedBigInteger, Description: Foreign key to the users table.
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('rate_card_id')->references('id')->on('rate_cards')->onDelete('set null');
            $table->foreign('sor_id')->references('id')->on('sors')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};