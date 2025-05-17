<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\GamePin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GamePinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $event = Event::where('is_active',true)->first();
        GamePin::create([
            'latitude' => '-36.9246931',
            'longitude' => '174.6940595',
            'event_id' => $event->id,
        ]);
        GamePin::create([
            'latitude' => '-36.9249858',
            'longitude' => '174.6936733',
            'event_id' => $event->id,
        ]);
        GamePin::create([
            'latitude' => '-36.9250028',
            'longitude' => '174.6940970',
            'event_id' => $event->id,
        ]);

    }
}
