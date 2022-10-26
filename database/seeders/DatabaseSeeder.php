<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('data_tables')->insert([
            'date' => "13.09.2022 15:14:27",
            'call' => '10 секунд',
            'commentManager' => '',
        ]);
    }
}
