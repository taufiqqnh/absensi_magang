<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_devices', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('device_name',255);
            $table->string('ip_address',100);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_devices');
    }
};
