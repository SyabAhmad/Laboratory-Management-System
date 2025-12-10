<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MigratePatientDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-patient-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing patient dates to datetime fields';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting patient date migration...');

        try {
            // Get all patients with dates
            $patients = DB::table('patients')->whereNotNull('receiving_date')->get();

            $this->info("Found {$patients->count()} patients to migrate");

            $bar = $this->output->createProgressBar($patients->count());

            foreach ($patients as $patient) {
                try {
                    // Convert receiving_date to datetime
                    if ($patient->receiving_date) {
                        $receivingDate = Carbon::parse($patient->receiving_date);
                        DB::table('patients')
                            ->where('id', $patient->id)
                            ->update([
                                'receiving_datetime' => $receivingDate
                            ]);
                    }

                    // Convert reporting_date to datetime
                    if ($patient->reporting_date) {
                        $reportingDate = Carbon::parse($patient->reporting_date);
                        DB::table('patients')
                            ->where('id', $patient->id)
                            ->update([
                                'reporting_datetime' => $reportingDate
                            ]);
                    }

                    $bar->advance();
                } catch (\Exception $e) {
                    $this->error("Error migrating patient {$patient->id}: {$e->getMessage()}");
                }
            }

            $bar->finish();
            $this->info("\nMigration completed successfully!");

            return 0;
        } catch (\Exception $e) {
            $this->error("Migration failed: {$e->getMessage()}");
            return 1;
        }
    }
}
