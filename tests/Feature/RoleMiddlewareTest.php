<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class RoleMiddlewareTest extends TestCase
{

	public function test_guest_is_redirected_to_login()
	{
		$response = $this->get(route('user'));
		$response->assertRedirect('/login');
	}

	public function test_non_admin_cannot_access_admin_routes()
	{
		$user = User::factory()->create(['user_type' => 'Receptionist']);
		$this->actingAs($user);
		$response = $this->get(route('user'));
		$response->assertStatus(403);
	}

	public function test_admin_can_access_admin_routes()
	{
		$user = User::factory()->create(['user_type' => 'Admin']);
		$this->actingAs($user);
		$response = $this->get(route('user'));
		$response->assertStatus(200);
	}
}
