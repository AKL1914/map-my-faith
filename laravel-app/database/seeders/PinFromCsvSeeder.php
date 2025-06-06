<?php

namespace Database\Seeders;

use App\Jobs\FetchSuburbFromCoordinates;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PinFromCsvSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/data.csv');

        if (! File::exists($path)) {
            $this->command->error("CSV file not found: $path");

            return;
        }

        $file = fopen($path, 'r');

        // Get campaign_id and user_id from DB
        $campaignId = DB::table('campaigns')->where('is_active', true)->value('id');
        $userId = DB::table('users')->where('email', config('app.admin_email'))->value('id');

        if (! $campaignId || ! $userId) {
            $this->command->error('Missing active campaign or admin user.');

            return;
        }

        $rowsInserted = 0;

        while (($row = fgetcsv($file)) !== false) {
            $wkt = $row[0]; // e.g. POINT (174.7633 -36.8485)
            if (preg_match('/POINT\s*\(([-\d.]+)\s+([-\d.]+)\)/', $wkt, $matches)) {
                $longitude = (float) $matches[1];
                $latitude = (float) $matches[2];
            } else {
                continue; // Skip invalid points
            }
            $pinId = DB::table('pins')->insertGetId([
                'user_id' => $userId,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'notes' => $row[2] ?? null,
                'is_accepted' => true,
                'campaign_id' => $campaignId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            FetchSuburbFromCoordinates::dispatch($pinId);

            $rowsInserted++;
        }

        fclose($file);
        $this->command->info("Seeded {$rowsInserted} pins from CSV.");
    }
}
