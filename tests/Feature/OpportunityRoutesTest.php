<?php

namespace Tests\Feature;

use App\Models\Opening;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OpportunityRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test public opportunity routes
     *
     * @return void
     */
    public function test_public_opportunity_routes()
    {
        // Test opportunity index route
        $response = $this->get('/opportunities');
        $response->assertStatus(200);

        // Create a test opportunity
        $opportunity = Opening::factory()->create([
            'type' => Opening::OPPORTUNITY_TYPE_JOB_FULL_TIME,
            'status' => 'active',
        ]);

        // Test opportunity show route
        $response = $this->get('/opportunities/' . $opportunity->slug);
        $response->assertStatus(200);

        // Test opportunity category route
        $response = $this->get('/opportunity-category/test-category');
        $response->assertStatus(200);

        // Test opportunity service route
        $response = $this->get('/opportunity-service/test-service');
        $response->assertStatus(200);
    }

    /**
     * Test authenticated opportunity routes
     *
     * @return void
     */
    public function test_authenticated_opportunity_routes()
    {
        // Create a test user
        $user = User::factory()->create();

        // Create a test opportunity
        $opportunity = Opening::factory()->create([
            'type' => Opening::OPPORTUNITY_TYPE_JOB_FULL_TIME,
            'status' => 'active',
        ]);

        // Test opportunity bookmark route
        $response = $this->actingAs($user)
            ->post('/opportunities/' . $opportunity->id . '/bookmark');
        $response->assertStatus(200);

        // Test opportunity apply route (GET)
        $response = $this->actingAs($user)
            ->get('/opportunities/' . $opportunity->id . '/apply');
        $response->assertStatus(200);

        // Test opportunity apply route (POST)
        $response = $this->actingAs($user)
            ->post('/opportunities/' . $opportunity->id . '/apply', [
                'cover_letter' => 'Test cover letter',
            ]);
        $response->assertStatus(302); // Redirect after successful application
    }

    /**
     * Test admin opportunity routes
     *
     * @return void
     */
    public function test_admin_opportunity_routes()
    {
        // Create an admin user
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Test admin opportunity index route
        $response = $this->actingAs($admin)
            ->get('/admin/opportunity');
        $response->assertStatus(200);

        // Create a test opportunity
        $opportunity = Opening::factory()->create([
            'type' => Opening::OPPORTUNITY_TYPE_JOB_FULL_TIME,
            'status' => 'active',
        ]);

        // Test admin opportunity show route
        $response = $this->actingAs($admin)
            ->get('/admin/opportunity/' . $opportunity->id);
        $response->assertStatus(200);

        // Test admin opportunity create route
        $response = $this->actingAs($admin)
            ->get('/admin/opportunity/create');
        $response->assertStatus(200);

        // Test admin opportunity store route
        $response = $this->actingAs($admin)
            ->post('/admin/opportunity', [
                'title' => 'Test Opportunity',
                'type' => Opening::OPPORTUNITY_TYPE_JOB_FULL_TIME,
                'status' => 'active',
                // Add other required fields
            ]);
        $response->assertStatus(302); // Redirect after successful creation
    }

    /**
     * Test employer opportunity routes
     *
     * @return void
     */
    public function test_employer_opportunity_routes()
    {
        // Create an employer user
        $employer = User::factory()->create([
            'role' => 'employer',
        ]);

        // Test employer opportunity index route
        $response = $this->actingAs($employer)
            ->get('/employer/opportunities');
        $response->assertStatus(200);

        // Create a test opportunity
        $opportunity = Opening::factory()->create([
            'type' => Opening::OPPORTUNITY_TYPE_JOB_FULL_TIME,
            'status' => 'active',
            'user_id' => $employer->id,
        ]);

        // Test employer opportunity show route
        $response = $this->actingAs($employer)
            ->get('/employer/opportunities/' . $opportunity->id);
        $response->assertStatus(200);

        // Test employer opportunity create route
        $response = $this->actingAs($employer)
            ->get('/employer/opportunities/create');
        $response->assertStatus(200);

        // Test employer opportunity applicants route
        $response = $this->actingAs($employer)
            ->get('/employer/opportunity-applicants');
        $response->assertStatus(200);

        // Test employer opportunity reviews route
        $response = $this->actingAs($employer)
            ->get('/employer/opportunity-reviews');
        $response->assertStatus(200);
    }

    /**
     * Test terminology routes
     *
     * @return void
     */
    public function test_terminology_routes()
    {
        // Create a test opportunity
        $opportunity = Opening::factory()->create([
            'type' => Opening::OPPORTUNITY_TYPE_JOB_FULL_TIME,
            'status' => 'active',
        ]);

        // Test opportunity index route with new terminology
        $response = $this->get('/opportunities');
        $response->assertStatus(200);

        // Test opportunity show route with new terminology
        $response = $this->get('/opportunities/' . $opportunity->slug);
        $response->assertStatus(200);

        // Create a test user
        $user = User::factory()->create();

        // Test opportunity bookmark route with new terminology
        $response = $this->actingAs($user)
            ->post('/opportunities/' . $opportunity->id . '/bookmark');
        $response->assertStatus(200);

        // Test opportunity apply route with new terminology
        $response = $this->actingAs($user)
            ->get('/opportunities/' . $opportunity->id . '/apply');
        $response->assertStatus(200);
    }
}
