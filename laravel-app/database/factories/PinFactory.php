<?php

namespace Database\Factories;

use App\Models\Pin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pin>
 */
class PinFactory extends Factory
{
    protected $model = Pin::class;

    public function definition(): array
    {
        return [
            'url' => $this->faker->url,
            'user_id' => User::factory(),
        ];
    }
}
