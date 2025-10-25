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
        Schema::create('skeletons', function (Blueprint $table) {
            // Old Column: ID (int unsigned)
            $table->id();

            // Old Column: sorid (int)
            $table->unsignedBigInteger('sor_id');
            $table->foreign('sor_id')->references('id')->on('sors')->onDelete('cascade');

            // Old Column: resourceid (int)
            $table->unsignedBigInteger('resource_id');
            $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');

            // Old Column: quantity (double)
            $table->decimal('quantity', 10, 4); // Assuming 10 total digits and 4 decimal places

            // Old Column: unit (int)
            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');

            // Old Column: raitemid (int)
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');

            // Old Column: res_desc (text)
            $table->text('resource_description')->nullable();

            // Old Column: SrNo (int)
            $table->integer('sort_order')->nullable();

            // Old Column: predate (bigint)
            $table->date('valid_from')->nullable();

            // Old Column: postdate (int)
            $table->date('valid_to')->nullable();

            // Old Column: canceled (tinyint(1))
            $table->boolean('is_canceled')->default(false);

            // Old Column: locked (tinyint(1))
            $table->boolean('is_locked')->default(false);

            // Old Column: factor (double)
            $table->decimal('factor', 10, 4)->default(1.0000); // Assuming 10 total digits and 4 decimal places, default 1.0

            // Old Column: insert_date, created_by, modify_date, modify_by
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skeletons');
    }
};