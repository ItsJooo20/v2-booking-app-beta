<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->Integer('user_id'); // INT UNSIGNED biar sama dengan users.id
            $table->string('device_token', 500);
            $table->string('device_type')->default('android');
            $table->timestamps();

            $table->unique(['user_id', 'device_token']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_tokens');
    }
};