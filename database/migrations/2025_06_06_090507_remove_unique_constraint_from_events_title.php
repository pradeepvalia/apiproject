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
        Schema::table('events', function (Blueprint $table) {
            // Check if the index exists before trying to drop it
            if (Schema::hasIndex('events', 'events_title_unique')) {
                $table->dropUnique(['title']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Only add the unique constraint if it doesn't exist
            if (!Schema::hasIndex('events', 'events_title_unique')) {
                $table->unique('title');
            }
        });
    }
};
