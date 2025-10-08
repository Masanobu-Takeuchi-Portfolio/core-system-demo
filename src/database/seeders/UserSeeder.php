<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name_last' => '一般',
                'name_first' => 'ユーザー',
                'email' => 'teset@example.co.jp',
                'password' => Hash::make('test'),
                'staff_number' => 'Test01',
                'department_id' => 2, // 東京スタッフ
                'url_transportation_expenses' => 'https://www.yahoo.co.jp/',
                'url_schedule' => 'https://www.google.co.jp/',
                'created_at' => Now(),
                'dummy' => 0
            ]
        ]);
    }
}
