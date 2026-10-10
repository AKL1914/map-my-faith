<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Pin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserParticipationByAreaTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Campaign $campaign;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        $this->admin = User::factory()->create(['is_admin' => true, 'area' => null]);
        $this->campaign = Campaign::create(['name' => 'Test', 'is_active' => true]);
    }

    private function pinFor(User $user, string $date, ?Campaign $campaign = null): void
    {
        $pin = Pin::create([
            'user_id' => $user->id,
            'campaign_id' => ($campaign ?? $this->campaign)->id,
            'latitude' => -36.85,
            'longitude' => 174.76,
        ]);

        DB::table('pins')->where('id', $pin->id)->update(['created_at' => $date]);
    }

    private function participation(array $query = []): array
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/admin/users/participation/by-area?'.http_build_query($query))
            ->assertOk();

        return collect($response->json('participation'))->keyBy('area')->all();
    }

    public function test_unfiltered_counts_every_user_with_any_pin(): void
    {
        $a = User::factory()->create(['area' => 1]);
        $b = User::factory()->create(['area' => 1]);
        $this->pinFor($a, '2026-01-10 12:00:00');

        $row = $this->participation()[1];

        $this->assertSame(1, $row['with_pins']);
        $this->assertSame(1, $row['no_pins']);
    }

    public function test_date_filter_only_counts_pins_inside_the_range_and_includes_whole_end_day(): void
    {
        $inRange = User::factory()->create(['area' => 2]);
        $endDay = User::factory()->create(['area' => 2]);
        $outOfRange = User::factory()->create(['area' => 2]);
        $this->pinFor($inRange, '2026-02-05 09:00:00');
        $this->pinFor($endDay, '2026-02-10 23:30:00');
        $this->pinFor($outOfRange, '2026-03-01 09:00:00');

        $row = $this->participation(['date_from' => '2026-02-01', 'date_to' => '2026-02-10'])[2];

        $this->assertSame(2, $row['with_pins']);
        $this->assertSame(1, $row['no_pins']);
    }

    public function test_different_filters_are_not_served_from_one_cached_result(): void
    {
        $user = User::factory()->create(['area' => 3]);
        $this->pinFor($user, '2026-02-05 09:00:00');

        $inside = $this->participation(['date_from' => '2026-02-01', 'date_to' => '2026-02-28'])[3];
        $outside = $this->participation(['date_from' => '2026-04-01', 'date_to' => '2026-04-30'])[3];

        $this->assertSame(1, $inside['with_pins']);
        $this->assertSame(0, $outside['with_pins']);
        $this->assertSame(1, $outside['no_pins']);
    }

    public function test_campaign_filter_scopes_pins_to_that_campaign(): void
    {
        $other = Campaign::create(['name' => 'Other', 'is_active' => false]);
        $user = User::factory()->create(['area' => 4]);
        $this->pinFor($user, '2026-02-05 09:00:00', $other);

        $mine = $this->participation(['campaign_id' => $this->campaign->id])[4];
        $theirs = $this->participation(['campaign_id' => $other->id])[4];

        $this->assertSame(0, $mine['with_pins']);
        $this->assertSame(1, $theirs['with_pins']);
    }

    public function test_every_area_one_to_six_is_returned_even_when_empty(): void
    {
        $this->assertSame([1, 2, 3, 4, 5, 6], array_keys($this->participation()));
    }

    public function test_non_admin_is_rejected(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get('/admin/users/participation/by-area')->assertRedirect('/');
    }
}
