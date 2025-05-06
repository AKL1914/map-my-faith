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
        Campaign::create(['name' => 'EVM 2024' , 'description' => 'Old Data From Old Map' , 'is_active' => true]);
        Campaign::create(['name' => 'EVM 2025' , 'description' => 'Description' , 'is_active' => false]);
        Campaign::create(['name' => 'JUNE EVM 2025' , 'description' => 'Description' , 'is_active' => false]);
    }
}
