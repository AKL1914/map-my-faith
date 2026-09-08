<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PinAdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_list_all_pins()
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/api/pins');

        $response->assertRedirect('/');
    }

    public function test_admin_can_list_all_pins()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->getJson('/api/pins');

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_view_pins_by_user_id()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $other = User::factory()->create();

        $response = $this->actingAs($user)->get("/api/pins/user/{$other->id}");

        $response->assertRedirect('/');
    }

    public function test_admin_can_view_pins_by_user_id()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $other = User::factory()->create();

        $response = $this->actingAs($admin)->getJson("/api/pins/user/{$other->id}");

        $response->assertStatus(200);
    }

    public function test_non_owner_non_admin_cannot_view_another_users_pin_list()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $other = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/user/{$other->id}/pins");

        $response->assertStatus(403);
    }

    public function test_user_can_view_their_own_pin_list()
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->getJson("/api/user/{$user->id}/pins");

        $response->assertStatus(200);
    }

    public function test_admin_can_view_any_users_pin_list()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $other = User::factory()->create();

        $response = $this->actingAs($admin)->getJson("/api/user/{$other->id}/pins");

        $response->assertStatus(200);
    }
}
