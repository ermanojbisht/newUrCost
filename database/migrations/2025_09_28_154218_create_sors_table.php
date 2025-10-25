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
        Schema::create('sors', function (Blueprint $table) {
            $table->bigIncrements('id'); // Old: sorid, New: id
            $table->string('name'); // Old: sorname, New: name
            $table->boolean('is_locked')->default(false); // Old: locked, New: is_locked
            $table->boolean('display_details')->default(false); // Old: display_details, New: display_details
            $table->string('filename')->nullable(); // Old: filename
            $table->string('short_name')->nullable(); // Old: shortname, New: short_name
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable(); // Old: created_by, New: created_by
            $table->unsignedBigInteger('updated_by')->nullable(); // Old: modify_by, New: updated_by

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
        Schema::dropIfExists('sors');
    }
};