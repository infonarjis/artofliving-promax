<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamilyStatusMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('family_status_masters', function (Blueprint $table) {
            $table->id();
            $table->string('family_status_name')->nullable();
            $table->enum('status', ['APPROVED', 'UNAPPROVED'])->default('APPROVED');
            $table->timestamps();
            $table->enum('is_deleted', ['Yes', 'No'])->default('No');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('family_status_masters');
    }
}
