<div class="slip">
    <!-- Header -->
    <div class="slip-header">
        <div class="slip-title">NEW MODERN LAB</div>
        <div class="slip-subtitle">Patient Registration Slip</div>
        <div class="slip-marker" style="font-size:11px;">{{ $copyLabel ?? '' }}</div>
    </div>

    <!-- Patient Information (from database) -->
    <div class="slip-patient">
        <div class="patient-row">
            <span class="patient-label">Patient #:</span>
            <span class="patient-value">{{ $patient->patient_id ?? 'N/A' }}</span>
        </div>
        <div class="patient-row">
            <span class="patient-label">Name:</span>
            <span class="patient-value">{{ substr($patient->name ?? 'N/A', 0, 18) }}</span>
        </div>
        <div class="patient-row">
            <span class="patient-label">Age:</span>
            <span class="patient-value">{{ $patient->age ?? 'N/A' }}</span>
        </div>
        <div class="patient-row">
            <span class="patient-label">Date:</span>
            <span class="patient-value">{{ $receipt->created_at->format('d-M-Y') }}</span>
        </div>
    </div>

    <!-- Token/Slip Number -->
    <div class="slip-token">
        <div class="token-label">SLIP / TOKEN NUMBER</div>
        <div class="token-number">{{ $receipt->receipt_number }}</div>
    </div>

    <!-- Tests with Prices (from database) -->
    <div class="slip-tests">
        <div class="tests-header">Tests Registered:</div>
        @php
            $displayTests = [];
            if (!empty($receipt->tests) && is_array($receipt->tests)) {
                $displayTests = $receipt->tests;
            } else {
                $patientTests = json_decode($patient->test_category ?? '[]', true);
                if (!empty($patientTests)) {
                    foreach ($patientTests as $testName) {
                        $displayTests[] = [ 'test_name' => $testName, 'price' => 0 ];
                    }
                }
            }
        @endphp

        @forelse($displayTests as $test)
            <div class="test-item">
                @if (is_array($test))
                    <span class="test-name">{{ substr($test['test_name'] ?? '', 0, 14) }}</span>
                    <span class="test-price">
                        @if (isset($test['price']) && $test['price'] > 0)
                            Rs. {{ number_format($test['price'], 0) }}
                        @else
                            -
                        @endif
                    </span>
                @else
                    <span class="test-name">{{ substr($test, 0, 14) }}</span>
                    <span class="test-price">-</span>
                @endif
            </div>
        @empty
            <div class="test-item">
                <span class="test-name">No tests</span>
                <span class="test-price">-</span>
            </div>
        @endforelse
    </div>

    <!-- Total Amount -->
    <div class="slip-total">
        <span class="total-label">TOTAL AMOUNT:</span>
        <span class="total-amount">Rs. {{ number_format($receipt->total_amount ?? 0, 0) }}</span>
    </div>

    <!-- Barcode (generates from receipt number) -->
    <div class="slip-barcode">
        <svg id="{{ $barcodeId ?? 'barcode' }}"></svg>
        <div class="barcode-text">{{ $receipt->receipt_number }}</div>
    </div>

    <!-- Footer -->
    <div class="slip-footer">
        Keep this slip for your records
    </div>
</div>
