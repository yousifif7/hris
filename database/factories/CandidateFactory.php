<?php

namespace Database\Factories;

use App\Enums\CandidateStatus;
use App\Models\Candidate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidateFactory extends Factory
{
    protected $model = Candidate::class;

    public function definition(): array
    {
        return [
            'first_name'  => fake()->firstName(),
            'last_name'   => fake()->lastName(),
            'email'       => fake()->unique()->safeEmail(),
            'phone'       => fake()->phoneNumber(),
            'source'      => fake()->randomElement(['Indeed', 'LinkedIn', 'Referral', 'Website']),
            'status'      => CandidateStatus::NEEDS_REVIEW,
            'resume_text' => fake()->paragraphs(3, true),
        ];
    }
}
