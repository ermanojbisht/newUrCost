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
        Schema::create('assign_jobs', function (Blueprint $table) {
            // Old Column Name: sno, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: userid, New Column Name: user_id, Data Type: unsignedBigInteger, Description: Foreign key to the users table.
            $table->unsignedBigInteger('user_id');
            // Old Column Name: jobid, New Column Name: job_id, Data Type: unsignedBigInteger, Description: Foreign key to the old_jobs table.
            $table->unsignedBigInteger('job_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('job_id')->references('id')->on('old_jobs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assign_jobs');
    }
};