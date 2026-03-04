<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

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
                    'type' => 'ゴミ袋',
                    'num' => 10,
                    'destination' => '倉庫A',
                    'deadline' => '2024-06-15',
                    'note' => 'テスト備考',
                ]
            ]),
            'notes' => null,
        ];
    }
}
