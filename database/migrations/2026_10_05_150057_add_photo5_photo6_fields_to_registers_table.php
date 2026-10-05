<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registers', function (Blueprint $table) {
            $table->string('photo5')->nullable()->default(null)->after('photo4_uploaded_on');
            $table->tinyInteger('photo5_status')->nullable()->default(0)->after('photo5');
            $table->dateTime('photo5_uploaded_on')->nullable()->default(null)->after('photo5_status');

            $table->string('photo6')->nullable()->default(null)->after('photo5_uploaded_on');
            $table->tinyInteger('photo6_status')->nullable()->default(0)->after('photo6');
            $table->dateTime('photo6_uploaded_on')->nullable()->default(null)->after('photo6_status');
        });
    }

    public function down(): void
    {
        Schema::table('registers', function (Blueprint $table) {
            $table->dropColumn([
                'photo5',
                'photo5_status',
                'photo5_uploaded_on',
                'photo6',
                'photo6_status',
                'photo6_uploaded_on',
            ]);
        });
    }
};