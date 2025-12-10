<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LabSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if settings already exist
        $existingSettings = DB::table('lab_settings')->first();

        if (!$existingSettings) {
            // Get system timezone as default
            $timezone = date_default_timezone_get();

            // Insert default settings
            DB::table('lab_settings')->insert([
                'settings' => json_encode([
                    'timezone' => $timezone,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('Laboratory settings initialized with timezone: ' . $timezone);
        } else {
            $this->command->info('Laboratory settings already exist. Skipping initialization.');
        }
    }
}
