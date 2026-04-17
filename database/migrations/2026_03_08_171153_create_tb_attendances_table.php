<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_attendances', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('id_users');
            $table->unsignedBigInteger('office_id');

            $table->unsignedBigInteger('device_id')->nullable();

            $table->date('attendance_date');

            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();

            $table->enum('status',['hadir','telat','izin','sakit','alpha']);

            $table->text('description')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_users')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('office_id')
                  ->references('id')
                  ->on('tb_office')
                  ->onDelete('cascade');

            $table->foreign('device_id')
                  ->references('id')
                  ->on('tb_devices')
                  ->nullOnDelete(); // Device dihapus -> device_id null
        });
    }

    public function down(): void
    {
        Schema::table('tb_attendances', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->dropForeign(['id_users']);
            $table->dropForeign(['office_id']);
        });

        Schema::dropIfExists('tb_attendances');
    }
};
