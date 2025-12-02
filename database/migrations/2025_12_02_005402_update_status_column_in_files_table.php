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
        Schema::table('files', function (Blueprint $table) {
            $table->string('status')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            // We can't easily revert to the exact enum without raw SQL or redefining it
            // For now, let's just revert to a string or leave it
            // Ideally: $table->enum('status', ['active', 'inactive', 'deleted'])->default('active')->change();
        });
    }
};
