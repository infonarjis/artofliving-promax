<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master registry of every homepage design available in the system.
     * 'design1' (the one already wired via HomePageSectionController) is
     * inserted by the seeder with controller_type = 'legacy', is_default = 1,
     * is_deletable = 0. Every design added afterwards is controller_type =
     * 'dynamic' and is driven entirely by the `schema` json column below —
     * no new PHP controller code, no new migration, no new columns needed
     * to add a homepage or add a field to one.
     */
    public function up(): void
    {
        Schema::create('home_page_designs', function (Blueprint $table) {
            $table->id();
            $table->string('design_key')->unique();               // e.g. 'design1', 'home2', 'home3'...
            $table->string('design_name');                        // Admin-facing label, e.g. "Classic Matrimony Home"
            $table->string('thumbnail')->nullable();               // Screenshot shown in the design picker list
            $table->string('view_folder')->nullable();             // resources/views/home/designs/{view_folder}/index.blade.php
            $table->string('asset_path')->nullable();              // public/{asset_path}/css, /js, /images
            $table->enum('controller_type', ['legacy', 'dynamic'])->default('dynamic');
            $table->json('schema')->nullable();                    // Tabs + fields definition (null for legacy design1)
            $table->boolean('is_active')->default(false);          // Only ONE row may be 1 at a time (enforced in controller)
            $table->boolean('is_default')->default(false);         // design1 only — cannot be deleted
            $table->boolean('is_deletable')->default(true);
            $table->string('status')->default('ACTIVE');           // ACTIVE / INACTIVE (soft toggle, separate from is_active)
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_page_designs');
    }
};
