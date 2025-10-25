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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Old: id
            $table->string('name'); // Old: name
            $table->string('email')->unique(); // New: email for Laravel authentication
            $table->timestamp('email_verified_at')->nullable(); // New: email verification for Laravel
            $table->string('password'); // New: password for Laravel authentication
            $table->rememberToken(); // New: remember_token for Laravel authentication
            $table->integer('status')->default(1); // Old: status
            $table->timestamps(); // Old: created -> created_at, modified -> updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
