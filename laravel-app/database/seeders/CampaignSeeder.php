<?php

namespace Database\Seeders;

use App\Models\Campaign;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Campaign::create(['name' => 'AUCKLAND PROPAGATION', 'description' => 'Auckland Propagation', 'is_active' => true]);
    }
}
