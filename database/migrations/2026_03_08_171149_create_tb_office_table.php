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
        Schema::create('tb_office', function (Blueprint $table) {
            $table->id();

            $table->string('office_name',100);
            $table->text('address');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_office');
    }
};
