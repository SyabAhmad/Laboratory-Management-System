<?php

namespace Tests\Feature;

// RefreshDatabase removed to avoid DB reset during feature test runs as requested
use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
