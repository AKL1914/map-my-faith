<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pin;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PinControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user and generate a Sanctum token
        $this->user = User::factory()->create([
            'email' => 'leeann@example.com',
            'name' => 'Leeann Cawley',
        ]);
        $this->token = $this->user->createToken('test-token')->plainTextToken;

        // Seed test pins to mimic existing data
        Pin::create([
            'user_id' => $this->user->id,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'notes' => 'New York pin',
            'is_accepted' => true,
            'campaign_id' => null,
        ]);
        Pin::create([
            'user_id' => $this->user->id,
            'latitude' => 51.5074,
            'longitude' => -0.1278,
            'notes' => 'London pin',
            'is_accepted' => false,
            'campaign_id' => null,
        ]);
    }

    public function test_get_pins_with_sanctum_token_returns_existing_pins()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/pins');

        $response->assertStatus(200);
        $response->assertJsonCount(2); // Expect 2 pins from setup
        $response->assertJsonStructure([[
            'id',
            'latitude',
            'longitude',
            'notes',
            'is_accepted',
            'user_id',
            'campaign_id',
        ]]);
    }

    public function test_get_pins_without_sanctum_token_fails()
    {
        $response = $this->getJson('/api/v1/pins');

        $response->assertStatus(401);
    }
}
