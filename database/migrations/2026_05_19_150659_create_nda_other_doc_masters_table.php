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
        Schema::create('nda_other_doc_masters', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('doc_image')
                ->nullable();
            $table->integer('staff_id')
                ->nullable();
            $table->enum('type', ['staff', 'admin'])
                ->nullable();
            $table->enum('status', ['APPROVED', 'UNAPPROVED'])
                ->default('APPROVED');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nda_other_doc_masters');
    }
};
