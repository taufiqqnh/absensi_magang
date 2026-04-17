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
        Schema::create('tb_face_data', function (Blueprint $table) {
            $table->id();

            // Relasi ke user
            $table->unsignedBigInteger('id_users');

            // Face descriptor & image
            $table->longText('face_descriptor');
            $table->longText('face_image')->nullable(); // ubah dari string 255 ke longText

            $table->timestamps();

            // Optionally, buat foreign key manual jika mau
            // $table->foreign('id_users')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_face_data');
    }
};
