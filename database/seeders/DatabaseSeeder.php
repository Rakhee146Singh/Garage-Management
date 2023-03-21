<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /**
         * Seeder For User Admin
         */
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        \App\Models\User::factory()->create([
            'first_name'        => 'Admin',
            'last_name'         => 'admin',
            'email'             => 'admin@gmail.com',
            'password'          => bcrypt('password'),
            'type'              => 'admin',
            'address1'          => 'Ahmedabad',
            'address2'          => 'Surat',
            'zipcode'           => 785462,
            'phone'             => 9876543210,
        ]);
    }
}
