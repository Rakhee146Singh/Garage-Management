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
        Schema::create('customer_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('garage_id');
            $table->unsignedBigInteger('car_service_job_id');
            $table->char('service_num', 6);
            $table->char('extra_charges', 10)->nullable();
            $table->char('total_amount', 10);
            $table->timestamps();

            $table->foreign('garage_id')->references('id')->on('garages')->onDelete('cascade');
            $table->foreign('car_service_job_id')->references('id')->on('car_service_jobs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_invoices');
    }
};
