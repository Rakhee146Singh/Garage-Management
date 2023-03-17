<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            DB::statement("ALTER TABLE `garage`.`users`
                CHANGE COLUMN `type` `type` ENUM('mechanic', 'customer', 'owner', 'admin') NOT NULL ");
            $table->char('created_by', 4)->nullable();
            $table->char('updated_by', 4)->nullable();
            $table->char('deleted_by', 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
