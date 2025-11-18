<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Patients;

class PrintTestReportDoubleSlipTest extends TestCase
{

	public function test_test_report_print_renders_two_copies_with_font_size()
	{
		$patient = Patients::create([
			'patient_id' => 'PT' . time(),
			'name' => 'Jane Doe',
			'mobile_phone' => '03123456780'
		]);

		// Prepare sample test_entry data
		$testName = 'CBC';
		$patient->test_report = json_encode([$testName => ['analytes' => [['name' => 'WBC', 'value' => '5.5', 'units' => 'x10^9/L', 'ref_range' => '4.0-11.0']]]]);
		$patient->save();

		$user = \App\Models\User::factory()->create(['user_type' => 'Admin']);
		$this->actingAs($user);
		$response = $this->get(route('patients.printTest', ['patient' => $patient->id, 'testName' => $testName]));
		$response->assertStatus(200);
		$response->assertSee(strtoupper($testName));
		// Should include larger font sizes used in test report partial
		$response->assertSee('font-size: 18px', false);
	}
}
