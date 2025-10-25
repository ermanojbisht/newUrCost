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
        Schema::create('job_groups', function (Blueprint $table) {
            // Old Column Name: grId, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: grtitle, New Column Name: title, Data Type: string, Description: The title of the job group.
            $table->string('title');
            // Old Column Name: parentGrId, New Column Name: parent_id, Data Type: unsignedBigInteger, Description: The ID of the parent job group.
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('parent_id')->references('id')->on('job_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_groups');
    }
};