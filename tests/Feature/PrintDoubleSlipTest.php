<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Patients;
use App\Models\PatientReceipt;

class PrintDoubleSlipTest extends TestCase
{

	public function test_receipt_print_renders_two_slips_and_font_size()
	{
		// Create a patient and a receipt
		$patient = Patients::create([
			'patient_id' => 'PT' . time(),
			'name' => 'John Doe',
			'mobile_phone' => '03123456789'
		]);
		$receipt = PatientReceipt::createFromPatient($patient, ['Test A', 'Test B']);
		$receipt->save();

		// Act as admin
		$user = \App\Models\User::factory()->create(['user_type' => 'Admin']);
		$this->actingAs($user);

		// Call the print route
		$response = $this->get(route('patients.print-receipt', ['receiptId' => $receipt->id]));

		$response->assertStatus(200);
		// Ensure both copies are present
		$response->assertSee('Customer Copy');
		$response->assertSee('Office Copy');
		// Ensure barcode ids exist
		$response->assertSee('id="barcode-1"', false);
		$response->assertSee('id="barcode-2"', false);
		// Check that bigger font CSS is present (example token number size)
		$response->assertSee('font-size: 18px', false);
	}
}
