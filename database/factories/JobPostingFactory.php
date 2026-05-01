<?php

namespace Database\Factories;

use App\Models\JobPosting;
use App\Models\Company;
use App\Models\JobCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk generate data JobPosting untuk testing
 */
class JobPostingFactory extends Factory
{
    protected $model = JobPosting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jobTitles = [
            'Software Engineer',
            'Frontend Developer',
            'Backend Developer',
            'Full Stack Developer',
            'Data Scientist',
            'Product Manager',
            'UI/UX Designer',
            'DevOps Engineer',
            'QA Engineer',
            'Business Analyst',
            'Digital Marketing Specialist',
            'Content Writer',
            'Graphic Designer',
            'Sales Executive',
            'Customer Success Manager'
        ];

        $employmentTypes = ['full-time', 'part-time', 'contract', 'internship', 'freelance'];
        $experienceLevels = ['entry', 'junior', 'mid', 'senior', 'lead'];
        $workLocations = ['onsite', 'remote', 'hybrid'];
        $statuses = ['draft', 'open', 'closed', 'filled'];

        return [
            'company_id' => Company::factory(),
            'job_category_id' => JobCategory::inRandomOrder()->first()?->id ?? null,
            'title' => fake()->randomElement($jobTitles),
            'slug' => fn (array $attributes) => \Illuminate\Support\Str::slug($attributes['title']) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->paragraphs(3, true),
            'requirements' => implode("\n", fake()->sentences(5)),
            'responsibilities' => implode("\n", fake()->sentences(5)),
            'employment_type' => fake()->randomElement($employmentTypes),
            'experience_level' => fake()->randomElement($experienceLevels),
            'work_location' => fake()->randomElement($workLocations),
            'city' => fake()->city(),
            'province' => fake()->state(),
            'salary_min' => $salaryMin = fake()->numberBetween(4000000, 8000000),
            'salary_max' => fake()->numberBetween($salaryMin, $salaryMin + 5000000),
            'salary_currency' => 'IDR',
            'show_salary' => fake()->boolean(70),
            'deadline' => fake()->dateTimeBetween('+1 week', '+3 months'),
            'max_applicants' => fake()->numberBetween(10, 100),
            'status' => fake()->randomElement($statuses),
            'views_count' => fake()->numberBetween(0, 1000),
            'applications_count' => fake()->numberBetween(0, 50),
            'featured' => fake()->boolean(20),
            'published_at' => fake()->optional(0.8)->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * State untuk job posting yang open/active
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open',
            'published_at' => now()->subDays(rand(1, 30)),
        ]);
    }

    /**
     * State untuk job posting yang masih draft
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    /**
     * State untuk job posting yang sudah closed
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
            'published_at' => now()->subMonths(rand(1, 3)),
        ]);
    }

    /**
     * State untuk job posting yang featured
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
        ]);
    }

    /**
     * State untuk remote job
     */
    public function remote(): static
    {
        return $this->state(fn (array $attributes) => [
            'work_location' => 'remote',
        ]);
    }
}
