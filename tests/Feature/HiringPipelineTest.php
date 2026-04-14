<?php

namespace Tests\Feature;

use App\Enums\CandidateStatus;
use App\Models\Candidate;
use App\Models\JobCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HiringPipelineTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_login(): void
    {
        $this->postJson('/api/login', ['email' => $this->admin->email, 'password' => 'password'])
            ->assertOk()
            ->assertJsonStructure(['user', 'token']);
    }

    public function test_dashboard(): void
    {
        $this->actingAs($this->admin)
            ->getJson('/api/dashboard')
            ->assertOk()
            ->assertJsonStructure(['stats', 'pipeline', 'recent_candidates']);
    }

    public function test_create_candidate(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/api/candidates', [
                'first_name' => 'Jane', 'last_name' => 'Doe',
                'email' => 'jane@test.com', 'source' => 'Indeed',
            ])
            ->assertCreated();

        $c = Candidate::where('email', 'jane@test.com')->first();
        $this->assertEquals(CandidateStatus::NEEDS_REVIEW, $c->status);
        $this->assertNotNull($c->assigned_to);
    }

    public function test_review_queue(): void
    {
        $this->actingAs($this->admin)
            ->getJson('/api/candidates-review-queue')
            ->assertOk();
    }

    public function test_change_status(): void
    {
        $c = Candidate::where('status', CandidateStatus::NEEDS_REVIEW)->first();
        $this->actingAs($this->admin)
            ->patchJson("/api/candidates/{$c->id}/status", ['status' => 'invite_sent'])
            ->assertOk()
            ->assertJsonFragment(['status' => 'invite_sent']);
    }

    public function test_schedule_interview(): void
    {
        $c = Candidate::where('status', CandidateStatus::NEEDS_REVIEW)->first();
        $this->actingAs($this->admin)
            ->postJson('/api/interviews', [
                'candidate_id' => $c->id,
                'scheduled_at' => now()->addDays(3)->toISOString(),
            ])
            ->assertCreated();
    }

    public function test_create_offer(): void
    {
        $c = Candidate::factory()->create(['status' => CandidateStatus::AWAITING_BACKGROUND_CHECK, 'assigned_to' => $this->admin->id]);
        $this->actingAs($this->admin)
            ->postJson('/api/offers', [
                'candidate_id' => $c->id, 'pay_rate' => 25,
                'employment_type' => 'Full-Time',
            ])
            ->assertCreated();
        $this->assertEquals(CandidateStatus::OFFER_SENT, $c->fresh()->status);
    }

    public function test_public_apply(): void
    {
        $this->postJson('/api/public/apply', [
            'first_name' => 'Public', 'last_name' => 'Applicant',
            'email' => 'pub@test.com', 'source' => 'Website',
        ])->assertCreated();
    }

    public function test_unauthenticated_blocked(): void
    {
        $this->getJson('/api/dashboard')->assertUnauthorized();
    }
}
