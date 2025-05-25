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
        Schema::table('users', function (Blueprint $table) {
            // Drop the existing address column
            $table->dropColumn('address');
        });

        Schema::table('users', function (Blueprint $table) {
            // Re-create the address column as nullable
            $table->string('address', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the nullable address column
            $table->dropColumn('address');
        });

        Schema::table('users', function (Blueprint $table) {
            // Re-create the address column as required
            $table->string('address', 255);
        });
    }
};
