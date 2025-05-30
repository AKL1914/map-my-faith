<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Campaign;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Campaign::create(['name' => 'AUCKLAND PROPAGATION' , 'description' => 'Auckland Propagation' , 'is_active' => true]);
    }
}
