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
        Schema::table('registers', function (Blueprint $table) {

            if (!Schema::hasColumn('registers', 'selfie_photo')) {
                $table->string('selfie_photo', 255)
                    ->nullable()
                    ->after('photo4_uploaded_on');
            }

            if (!Schema::hasColumn('registers', 'selfie_photo_status')) {
                $table->enum('selfie_photo_status', ['APPROVED', 'UNAPPROVED'])
                    ->default('UNAPPROVED')
                    ->after('selfie_photo');
            }

            if (!Schema::hasColumn('registers', 'selfie_photo_uploaded_on')) {
                $table->dateTime('selfie_photo_uploaded_on')
                    ->nullable()
                    ->after('selfie_photo_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registers', function (Blueprint $table) {

            if (Schema::hasColumn('registers', 'selfie_photo_uploaded_on')) {
                $table->dropColumn('selfie_photo_uploaded_on');
            }

            if (Schema::hasColumn('registers', 'selfie_photo_status')) {
                $table->dropColumn('selfie_photo_status');
            }

            if (Schema::hasColumn('registers', 'selfie_photo')) {
                $table->dropColumn('selfie_photo');
            }
        });
    }
};
