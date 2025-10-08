<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('departments')->insert([
            [
                'id' => 1,
                'name' => '大阪',
                'created_at' => Now()
            ],
            [
                'id' => 2,
                'name' => '東京',
                'created_at' => Now()
            ]
        ]);
    }
}
