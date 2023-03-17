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
        Schema::create('car_service_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_service_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('service_type_id');
            $table->enum('status', ['Pending', 'In-Progress', 'Complete']);
            $table->timestamps();

            $table->foreign('car_service_id')->references('id')->on('car_services')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('service_type_id')->references('id')->on('service_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_service_jobs');
    }
};
