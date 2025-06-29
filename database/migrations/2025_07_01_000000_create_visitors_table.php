<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('page');
            $table->text('user_agent')->nullable();
            $table->string('screen_resolution', 50)->nullable();
            $table->string('language', 10)->nullable();
            $table->string('timezone', 50)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->string('session_id', 100);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('session_id');
            $table->index('page');
            $table->index('created_at');
            $table->index(['session_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('visitors');
    }
};
