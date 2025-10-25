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
        Schema::create('old_jobs', function (Blueprint $table) {
            // Old Column Name: jobid, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table. Renamed to old_jobs table.
            $table->bigIncrements('id');
            // Old Column Name: job_title, New Column Name: title, Data Type: string, Description: The title of the job. Renamed for clarity.
            $table->string('title');
            // Old Column Name: job_page, New Column Name: page, Data Type: string, Description: The page associated with the job. Renamed for clarity.
            $table->string('page');
            // Old Column Name: job_type, New Column Name: type, Data Type: integer, Description: The type of the job. Renamed for clarity.
            $table->integer('type');
            // Old Column Name: sorder, New Column Name: sort_order, Data Type: integer, Description: The sort order of the job. Renamed for clarity.
            $table->integer('sort_order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('old_jobs');
    }
};