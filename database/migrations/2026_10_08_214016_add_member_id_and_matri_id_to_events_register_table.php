<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events_register', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id')->nullable()->after('event_id');
            $table->string('matri_id')->nullable()->after('member_id');
        });
    }

    public function down(): void
    {
        Schema::table('events_register', function (Blueprint $table) {
            $table->dropColumn(['member_id', 'matri_id']);
        });
    }
};