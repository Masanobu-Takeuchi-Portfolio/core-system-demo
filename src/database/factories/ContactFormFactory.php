<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactForm>
 */
class ContactFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(20),
            'title' => $this->faker->realText(50), // 日本語テキスト
            'email' => $this->faker->email(),
            'url' => $this->faker->url(),
            'gender' => $this->faker->boolean(), // 0 or 1の2値
            'age' => $this->faker->numberBetween(1, 6), // 数字の１～６までの間をランダムで取得したいので
            'contact' => $this->faker->realText(200),
        ];
    }
}
