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
        Schema::create('tb_office_work_times', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('office_id');

            $table->time('check_in_time');
            $table->time('check_out_time');

            $table->integer('late_tolerance');
            $table->integer('early_leave_tolerance');

            $table->timestamps();

            $table->foreign('office_id')
                  ->references('id')
                  ->on('tb_office')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_office_work_times');
    }
};
