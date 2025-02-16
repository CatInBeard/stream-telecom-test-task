<?php

namespace Database\Factories;

use App\Models\ShortLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VisitUserData>
 */
class VisitUserDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'short_link_id' => ShortLink::inRandomOrder()->first()->id,
            'user_agent' => $this->faker->userAgent,
            'session_uuid' => $this->faker->uuid,
            'ip_address' => $this->faker->ipv4,
        ];
    }
}
