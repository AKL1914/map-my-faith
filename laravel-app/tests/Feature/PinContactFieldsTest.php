<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Pin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PinContactFieldsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Campaign $campaign;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->campaign = Campaign::create(['name' => 'Test Campaign', 'is_active' => true]);
    }

    public function test_pin_can_be_created_with_optional_contact_fields()
    {
        $response = $this->actingAs($this->user)->postJson('/api/pin', [
            'latitude' => -36.8485,
            'longitude' => 174.7633,
            'campaign_id' => $this->campaign->id,
            'contact_name' => 'Jane Doe',
            'contact_phone' => '021 234 5678',
            'contact_email' => 'jane@example.com',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('pins', [
            'contact_name' => 'Jane Doe',
            'contact_phone' => '021 234 5678',
            'contact_email' => 'jane@example.com',
        ]);
    }

    public function test_pin_can_be_created_without_contact_fields()
    {
        $response = $this->actingAs($this->user)->postJson('/api/pin', [
            'latitude' => -36.8485,
            'longitude' => 174.7633,
            'campaign_id' => $this->campaign->id,
        ]);

        $response->assertStatus(201);
        $pin = Pin::first();
        $this->assertNull($pin->contact_name);
        $this->assertNull($pin->contact_phone);
        $this->assertNull($pin->contact_email);
    }

    public function test_pin_creation_rejects_invalid_email()
    {
        $response = $this->actingAs($this->user)->postJson('/api/pin', [
            'latitude' => -36.8485,
            'longitude' => 174.7633,
            'campaign_id' => $this->campaign->id,
            'contact_email' => 'not-an-email',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['contact_email']);
    }

    public function test_pin_creation_rejects_invalid_nz_phone()
    {
        $response = $this->actingAs($this->user)->postJson('/api/pin', [
            'latitude' => -36.8485,
            'longitude' => 174.7633,
            'campaign_id' => $this->campaign->id,
            'contact_phone' => '12345',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['contact_phone']);
    }

    public function test_pin_creation_accepts_nz_landline_with_formatting()
    {
        $response = $this->actingAs($this->user)->postJson('/api/pin', [
            'latitude' => -36.8485,
            'longitude' => 174.7633,
            'campaign_id' => $this->campaign->id,
            'contact_phone' => '(09) 123-4567',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('pins', ['contact_phone' => '(09) 123-4567']);
    }

    public function test_bounds_endpoint_does_not_expose_contact_fields()
    {
        Pin::create([
            'user_id' => $this->user->id,
            'campaign_id' => $this->campaign->id,
            'latitude' => -36.8485,
            'longitude' => 174.7633,
            'is_accepted' => true,
            'contact_name' => 'Jane Doe',
            'contact_phone' => '021 234 5678',
            'contact_email' => 'jane@example.com',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/pins/bounds?'.http_build_query([
            'north' => -36.0,
            'south' => -37.0,
            'east' => 175.0,
            'west' => 174.0,
        ]));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonMissingPath('0.contact_name');
        $response->assertJsonMissingPath('0.contact_phone');
        $response->assertJsonMissingPath('0.contact_email');
    }

    public function test_update_endpoint_ignores_contact_field_changes()
    {
        $pin = Pin::create([
            'user_id' => $this->user->id,
            'campaign_id' => $this->campaign->id,
            'latitude' => -36.8485,
            'longitude' => 174.7633,
            'is_accepted' => false,
            'contact_name' => 'Original Name',
        ]);

        $response = $this->actingAs($this->user)->patchJson("/api/pins/{$pin->id}", [
            'is_accepted' => true,
            'contact_name' => 'Hijacked Name',
        ]);

        $response->assertStatus(200);
        $pin->refresh();
        $this->assertTrue((bool) $pin->is_accepted);
        $this->assertSame('Original Name', $pin->contact_name);
    }
}
