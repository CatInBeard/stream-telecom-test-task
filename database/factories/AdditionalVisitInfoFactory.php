<?php

namespace Database\Factories;

use App\Models\VisitUserData;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdditionalVisitInfo>
 */
class AdditionalVisitInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'userAgent' => $this->faker->userAgent,
            'language' => $this->faker->languageCode,
            'platform' => $this->faker->word,
            'screenResolution' => $this->faker->randomElement(['1920x1080', '1366x768', '1280x720']),
            'colorDepth' => $this->faker->randomElement(['8', '16', '24', '32']),
            'timezone' => $this->faker->timezone,
            'plugins' => $this->faker->words(3, true),
            'cookiesEnabled' => $this->faker->boolean,
            'hardwareConcurrency' => $this->faker->numberBetween(1, 16),
            'onlineStatus' => $this->faker->boolean,
            'viewportSize' => json_encode(['width' => $this->faker->numberBetween(300, 1920), 'height' => $this->faker->numberBetween(300, 1080)]),
            'canvasFingerprint' => $this->faker->uuid,
            'installedFonts' => json_encode($this->faker->words(3)),
            'browserName' => $this->faker->word,
            'browserVersion' => $this->faker->word,
            'windowSize' => json_encode(['width' => $this->faker->numberBetween(300, 1920), 'height' => $this->faker->numberBetween(300, 1080)]),
            'localStorageAvailable' => $this->faker->boolean,
            'sessionStorageAvailable' => $this->faker->boolean,
            'cssProperties' => json_encode(['property' => $this->faker->word]),
            'visit_user_data_id' => VisitUserData::inRandomOrder()->first()->id,
        ];
    }
}
