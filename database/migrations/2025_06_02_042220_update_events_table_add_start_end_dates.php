<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('event_date');
            $table->date('start_date')->nullable()->after('content');
            $table->date('end_date')->nullable()->after('start_date');
        });

        // Update existing records with a default date
        DB::table('events')->update([
            'start_date' => now(),
            'end_date' => now()->addDays(1)
        ]);

        // Make the columns required
        Schema::table('events', function (Blueprint $table) {
            $table->date('start_date')->nullable(false)->change();
            $table->date('end_date')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
            $table->date('event_date')->after('content');
        });
    }
};
