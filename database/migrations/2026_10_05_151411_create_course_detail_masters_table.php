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
        Schema::create('course_detail_masters', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment
            $table->string('course_name', 255)->nullable();
            $table->string('lang_code', 10)->nullable()->default('en');
            $table->bigInteger('lang_id')->nullable()->default(1);
            $table->enum('status', ['APPROVED', 'UNAPPROVED'])->default('APPROVED');
            $table->timestamps();   // created_at, updated_at (nullable)
            $table->softDeletes();  // deleted_at (replaces old is_deleted)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_detail_masters');
    }
};
