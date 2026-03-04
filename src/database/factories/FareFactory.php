<?php

namespace Database\Factories;

use App\Models\fare;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\fare>
 */
class FareFactory extends Factory
{
    protected $model = fare::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'year' => 2024,
            'month' => 7,
            'data' => json_encode([
                [
                    'date' => '2024-06-11',
                    'detail' => [
                        [
                            'traffic' => 'JR',
                            'fare' => 500,
                            'route' => '東京-品川',
                        ]
                    ],
                    'sub_total' => 500,
                ]
            ]),
            'first_total' => 500,
            'second_total' => 0,
            'osaka_total' => null,
        ];
    }
}
