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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('garage_id')->nullable();
            $table->string('name', 50);
            $table->string('description', 100);
            $table->char('price', 10);
            $table->char('quantity', 3);
            $table->boolean('is_available')->default(true);
            $table->date('manufacture_date');
            $table->timestamps();
            $table->char('created_by', 4)->nullable();
            $table->char('updated_by', 4)->nullable();
            $table->char('deleted_by', 4)->nullable();

            $table->foreign('garage_id')->references('id')->on('garages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
