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
        Schema::create('resources', function (Blueprint $table) {
            // Old Column Name: ID, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: name, New Column Name: name, Data Type: string, Description: The name of the resource.
            $table->string('name');
            // Old Column Name: code, New Column Name: resource_code, Data Type: string, Description: A unique code for the resource.
            $table->string('resource_code')->unique();
            // Old Column Name: resgr, New Column Name: group_id, Data Type: unsignedBigInteger, Description: The resource group.
            $table->unsignedBigInteger('group_id')->nullable();
            // Old Column Name: resCode, New Column Name: secondary_code, Data Type: string, Description: Another code for the resource.
            $table->string('secondary_code')->nullable();
            // Old Column Name: UnitGrpId, New Column Name: unit_group_id, Data Type: unsignedBigInteger, Description: The ID of the unit group.
            $table->unsignedBigInteger('unit_group_id')->nullable();
            // Old Column Name: TechUnitID, New Column Name: unit_id, Data Type: unsignedBigInteger, Description: The ID of the technical unit.
            $table->unsignedBigInteger('unit_id')->nullable();
            // Old Column Name: description, New Column Name: description, Data Type: text, Description: A description of the resource.
            $table->text('description')->nullable();
            // Old Column Name: numItemUsed, New Column Name: items_using_count, Data Type: integer, Description: The number of items that use this resource.
            $table->integer('items_using_count')->default(0);
            // Old Column Name: resCapacityGr, New Column Name: capacity_group, Data Type: integer, Description: The resource capacity group.
            $table->integer('capacity_group')->nullable();
            // Old Column Name: resCapacityGrId, New Column Name: capacity_group_id, Data Type: unsignedBigInteger, Description: The ID of the resource capacity group.
            $table->unsignedBigInteger('capacity_group_id')->nullable();
            // Old Column Name: dsrcode, New Column Name: dsr_code, Data Type: string, Description: DSR code for historical data.
            $table->string('dsr_code')->nullable();
            // Old Column Name: canceled, New Column Name: is_canceled, Data Type: boolean, Description: A flag to indicate if the resource is canceled.
            $table->boolean('is_canceled')->default(false);
            $table->timestamps();
            // Old Column Name: created_by, New Column Name: created_by, Data Type: unsignedBigInteger, Description: Foreign key to the users table.
            $table->unsignedBigInteger('created_by')->nullable();
            // Old Column Name: modify_by, New Column Name: updated_by, Data Type: unsignedBigInteger, Description: Foreign key to the users table.
            $table->unsignedBigInteger('updated_by')->nullable();


            // Foreign key constraints
            $table->foreign('unit_group_id')->references('id')->on('unit_groups')->onDelete('set null');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
            $table->foreign('capacity_group_id')->references('id')->on('resource_capacity_groups')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
