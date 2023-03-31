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
        Schema::create('car_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('garage_id');
            $table->unsignedBigInteger('car_id');
            $table->enum('status', ['I', 'IP', 'DE', 'C', 'D'])->comment('I:Initiated', 'IP:In-Progress', 'DE:Delay', 'C:Completed', 'D:Delivered');
            $table->timestamps();

            $table->foreign('garage_id')->references('id')->on('garages')->onDelete('cascade');
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_services');
    }
};
