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
        Schema::create('tb_office_location', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('office_id');

            $table->decimal('latitude',10,7);
            $table->decimal('longitude',10,7);
            $table->integer('radius');

            $table->timestamps();

            $table->foreign('office_id')
                  ->references('id')
                  ->on('tb_office')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_office_location');
    }
};
