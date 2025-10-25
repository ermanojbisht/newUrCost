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
        Schema::create('overhead_masters', function (Blueprint $table) {
            // Old Column Name: ID, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: vOHCode, New Column Name: code, Data Type: string, Description: The code of the overhead.
            $table->string('code')->unique();
            // Old Column Name: Flag, New Column Name: flag, Data Type: boolean, Description: Not clear from the code.
            $table->boolean('flag')->default(false);
            // Old Column Name: vdescription, New Column Name: description, Data Type: text, Description: The description of the overhead.
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overhead_masters');
    }
};
