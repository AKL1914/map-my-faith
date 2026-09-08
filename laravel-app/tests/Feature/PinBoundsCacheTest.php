<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Pin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PinBoundsCacheTest extends TestCase
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

    protected function fetchBounds(array $bounds)
    {
        return $this->actingAs($this->user)->getJson('/api/pins/bounds?'.http_build_query($bounds));
    }

    public function test_nearby_pans_reuse_the_same_cached_result()
    {
        // Values sit safely mid-grid-cell (3dp grid) so a tiny pan doesn't
        // coincidentally cross a cell boundary and defeat the test.
        $bounds1 = ['north' => -36.8034, 'south' => -36.8474, 'east' => 174.7654, 'west' => 174.7574];
        // A viewport shifted by a few meters — should round to the same grid cell.
        $bounds2 = ['north' => -36.80345, 'south' => -36.84745, 'east' => 174.76545, 'west' => 174.75745];

        $this->fetchBounds($bounds1)->assertStatus(200);

        DB::enableQueryLog();
        $this->fetchBounds($bounds2)->assertStatus(200);
        $queries = DB::getQueryLog();

        $pinQueries = array_filter($queries, fn ($q) => str_contains($q['query'], 'from "pins"'));
        $this->assertCount(0, $pinQueries, 'Expected the second nearby pan to be served from cache without hitting the database.');
    }

    public function test_bounds_cache_does_not_go_stale_forever_after_a_new_pin()
    {
        $bounds = ['north' => -36.8000, 'south' => -36.8500, 'east' => 174.7700, 'west' => 174.7600];

        $this->fetchBounds($bounds)->assertJsonCount(0);

        Pin::create([
            'user_id' => $this->user->id,
            'campaign_id' => $this->campaign->id,
            'latitude' => -36.8250,
            'longitude' => 174.7650,
            'is_accepted' => true,
        ]);

        $this->fetchBounds($bounds)->assertJsonCount(1);
    }

    public function test_pin_write_does_not_populate_an_unbounded_bounds_key_registry()
    {
        $bounds = ['north' => -36.8000, 'south' => -36.8500, 'east' => 174.7700, 'west' => 174.7600];
        $this->fetchBounds($bounds);

        Pin::create([
            'user_id' => $this->user->id,
            'campaign_id' => $this->campaign->id,
            'latitude' => -36.8250,
            'longitude' => 174.7650,
            'is_accepted' => true,
        ]);

        $this->assertNull(Cache::get('pins_bounds_keys'), 'The old unbounded bounds-key registry should no longer be used.');
    }
}
