<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk generate data Company untuk testing
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $industries = [
            'Teknologi Informasi',
            'E-Commerce',
            'Fintech',
            'Healthcare',
            'Pendidikan',
            'Manufaktur',
            'Retail',
            'Hospitality',
            'Konstruksi',
            'Logistik',
            'Media & Entertainment',
            'Real Estate',
            'Pertanian',
            'Energi',
            'Otomotif'
        ];

        $companySizes = [10, 50, 200, 500, 1000];

        return [
            'user_id' => User::factory()->create(['user_type' => 'company']),
            'name' => fake()->company(),
            'industry' => fake()->randomElement($industries),
            'description' => fake()->paragraph(3),
            'website' => fake()->url(),
            'logo' => null, // akan diisi saat testing dengan upload
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'province_id' => Province::inRandomOrder()->first()?->id ?? 1,
            'phone' => '+62' . fake()->numerify('##########'),
            'employee_count' => fake()->randomElement($companySizes),
            'founded_year' => fake()->numberBetween(1980, 2023),
            'verification_status' => fake()->randomElement(['pending_verification', 'verified', 'rejected']),
            'verified_at' => fake()->optional(0.6)->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * State untuk company yang sudah verified
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);
    }

    /**
     * State untuk company yang masih pending
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'pending_verification',
            'verified_at' => null,
        ]);
    }

    /**
     * State untuk tech company
     */
    public function tech(): static
    {
        return $this->state(fn (array $attributes) => [
            'industry' => 'Teknologi Informasi',
        ]);
    }
}
