<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AddCbcDefaultParameters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Find or create the CBC test category
        $cat = DB::table('labtest_cat')->where('cat_name', 'CBC')->first();

        if (! $cat) {
            $catId = DB::table('labtest_cat')->insertGetId([
                'cat_name' => 'CBC',
                'department' => 'Hematology',
                'price' => 0,
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } else {
            $catId = $cat->id;
        }

        // Common CBC parameters (from HL7 sample)
        $params = [
            ['parameter_name' => 'WBC', 'unit' => '10^9/L', 'reference_range' => '4.0-10.0'],
            ['parameter_name' => 'RBC', 'unit' => '10^12/L', 'reference_range' => '4.5-6.0'],
            ['parameter_name' => 'HGB', 'unit' => 'g/dL', 'reference_range' => '13.0-17.0'],
            ['parameter_name' => 'HCT', 'unit' => '%', 'reference_range' => '40-50'],
            ['parameter_name' => 'MCV', 'unit' => 'fL', 'reference_range' => '80-100'],
            ['parameter_name' => 'MCH', 'unit' => 'pg', 'reference_range' => '27-33'],
            ['parameter_name' => 'MCHC', 'unit' => 'g/dL', 'reference_range' => '32-36'],
            ['parameter_name' => 'PLT', 'unit' => '10^9/L', 'reference_range' => '150-400'],
        ];

        foreach ($params as $p) {
            $exists = DB::table('lab_test_parameters')
                ->where('lab_test_cat_id', $catId)
                ->where('parameter_name', $p['parameter_name'])
                ->exists();

            if (! $exists) {
                DB::table('lab_test_parameters')->insert([
                    'lab_test_cat_id' => $catId,
                    'parameter_name' => $p['parameter_name'],
                    'unit' => $p['unit'],
                    'reference_range' => $p['reference_range'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $cat = DB::table('labtest_cat')->where('cat_name', 'CBC')->first();
        if (! $cat) {
            return;
        }

        $catId = $cat->id;

        $names = ['WBC','RBC','HGB','HCT','MCV','MCH','MCHC','PLT'];

        DB::table('lab_test_parameters')
            ->where('lab_test_cat_id', $catId)
            ->whereIn('parameter_name', $names)
            ->delete();

        // If the category now has no parameters, remove it too (only if empty)
        $remaining = DB::table('lab_test_parameters')->where('lab_test_cat_id', $catId)->count();
        if ($remaining == 0) {
            DB::table('labtest_cat')->where('id', $catId)->delete();
        }
    }
}
