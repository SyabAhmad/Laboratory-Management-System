<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get current timezone from config
        $currentTimezone = config('app.timezone');

        // Get all available timezones
        $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);

        // Get lab settings from database if they exist
        $settings = $this->getSettings();

        return view('settings.index', compact('currentTimezone', 'timezones', 'settings'));
    }

    /**
     * Update lab settings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'timezone' => 'required|string|timezone',
        ]);

        try {
            // Update the .env file with new timezone
            $this->updateEnvFile('APP_TIMEZONE', $request->timezone);

            // Clear config cache to apply changes
            Artisan::call('config:clear');

            // Update settings in database if needed
            $this->saveSettings($request->all());

            return redirect()->route('settings.index')
                ->with('success', 'Settings updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating settings: ' . $e->getMessage());
            return redirect()->route('settings.index')
                ->with('error', 'Error updating settings: ' . $e->getMessage());
        }
    }

    /**
     * Get settings from database
     *
     * @return array
     */
    private function getSettings()
    {
        // Try to get settings from database
        try {
            $settings = DB::table('lab_settings')->first();
            if ($settings) {
                return json_decode($settings->settings, true);
            }
        } catch (\Exception $e) {
            Log::warning('Could not retrieve lab settings from database: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Save settings to database
     *
     * @param array $settings
     */
    private function saveSettings($settings)
    {
        try {
            // Prepare settings data
            $settingsData = [
                'timezone' => $settings['timezone'] ?? config('app.timezone'),
                'updated_at' => now(),
            ];

            // Try to update or create settings in database
            $existingSettings = DB::table('lab_settings')->first();

            if ($existingSettings) {
                DB::table('lab_settings')
                    ->where('id', $existingSettings->id)
                    ->update([
                        'settings' => json_encode($settingsData),
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('lab_settings')->insert([
                    'settings' => json_encode($settingsData),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Could not save lab settings to database: ' . $e->getMessage());
        }
    }

    /**
     * Update .env file with new value
     *
     * @param string $key
     * @param string $value
     */
    private function updateEnvFile($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            // Read file content
            $content = file_get_contents($path);

            // Replace the key if it exists, otherwise add it
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }

            // Write back to file
            file_put_contents($path, $content);
        }
    }
}
