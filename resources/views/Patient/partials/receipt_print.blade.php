<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Receipt - {{ $receipt->receipt_number }}</title>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }

        body {
            font-family: 'Courier New', Arial, sans-serif;
            line-height: 1.4;
            color: #333;
            background-color: #f5f5f5;
        }

        .receipt-container {
            max-width: 80mm;
            width: 100%;
            margin: 10mm auto;
            background-color: white;
            padding: 5mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            page-break-after: always;
        }

        /* Header */
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 5mm;
            margin-bottom: 5mm;
        }

        .hospital-name {
            font-size: 14px;
            font-weight: bold;
            color: #8d2d36;
            margin-bottom: 2mm;
        }

        .hospital-info {
            font-size: 9px;
            color: #555;
            line-height: 1.3;
        }

        .divider {
            border: none;
            border-bottom: 1px dashed #999;
            margin: 3mm 0;
        }

        /* Patient Details */
        .patient-details {
            font-size: 10px;
            margin-bottom: 5mm;
        }

        .detail-row {
            display: flex;
            margin: 2mm 0;
            justify-content: space-between;
        }

        .detail-label {
            font-weight: bold;
            width: 45%;
        }

        .detail-value {
            width: 55%;
            text-align: left;
        }

        /* Tests Table */
        .tests-section {
            margin-bottom: 5mm;
        }

        .section-title {
            font-weight: bold;
            font-size: 11px;
            background-color: #f0f0f0;
            padding: 2mm;
            margin-bottom: 2mm;
            text-align: center;
        }

        .tests-table {
            width: 100%;
            font-size: 9px;
            border-collapse: collapse;
        }

        .tests-table th {
            background-color: #8d2d36;
            color: white;
            padding: 2mm;
            text-align: left;
            border: 1px solid #999;
            font-weight: bold;
        }

        .tests-table td {
            padding: 2mm;
            border: 1px solid #ddd;
        }

        .tests-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .test-name {
            flex: 1;
        }

        .test-price {
            text-align: right;
            width: 20%;
        }

        .test-status {
            text-align: center;
            width: 25%;
            font-size: 8px;
            font-weight: bold;
        }

        .status-paid {
            color: #28a745;
            background-color: #e8f5e9;
            padding: 1mm 2mm;
            border-radius: 2px;
        }

        .status-unpaid {
            color: #dc3545;
            background-color: #ffebee;
            padding: 1mm 2mm;
            border-radius: 2px;
        }

        /* Total Section */
        .total-section {
            border: 2px solid #8d2d36;
            padding: 3mm;
            margin-bottom: 5mm;
            text-align: center;
        }

        .total-label {
            font-size: 10px;
            font-weight: bold;
            color: #333;
        }

        .total-amount {
            font-size: 16px;
            font-weight: bold;
            color: #8d2d36;
            margin-top: 2mm;
        }

        .amount-in-words {
            font-size: 8px;
            color: #666;
            margin-top: 1mm;
            font-style: italic;
        }

        /* Barcode */
        .barcode-section {
            text-align: center;
            margin-bottom: 5mm;
        }

        .barcode-number {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 2mm;
        }

        .barcode-image {
            margin: 3mm 0;
        }

        .barcode-image svg {
            max-width: 100%;
            height: auto;
        }

        /* Footer */
        .receipt-footer {
            border-top: 2px solid #333;
            padding-top: 3mm;
            font-size: 9px;
            text-align: center;
            color: #666;
        }

        .footer-note {
            font-size: 8px;
            margin: 2mm 0;
            line-height: 1.3;
        }

        .printed-info {
            font-size: 8px;
            margin-top: 2mm;
            font-weight: bold;
        }

        .receipt-number-box {
            background-color: #f0f0f0;
            padding: 3mm;
            margin: 3mm 0;
            border: 1px solid #999;
            text-align: center;
        }

        .receipt-number-label {
            font-size: 8px;
            color: #666;
        }

        .receipt-number {
            font-size: 12px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
            color: #333;
            margin-top: 1mm;
        }

        /* Print Controls */
        .print-controls {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .btn {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }

        .btn-print {
            background-color: #007bff;
            color: white;
        }

        .btn-print:hover {
            background-color: #0056b3;
        }

        .btn-close {
            background-color: #6c757d;
            color: white;
        }

        .btn-close:hover {
            background-color: #5a6268;
        }

        @media (max-width: 768px) {
            .receipt-container {
                max-width: 100%;
                margin: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print print-controls">
        <button class="btn btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Print Receipt
        </button>
        <button class="btn btn-close" onclick="window.history.back()">
            <i class="fas fa-times"></i> Close
        </button>
    </div>

    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <div class="hospital-name">NEW MODERN CLINICAL LABORATORY</div>
            <div class="hospital-info">
                (KP HCC) REG: 03663 SWAT<br>
                Bacha Khan, BS Pathology (KMU)<br>
                DMLT KPK Peshawar<br>
                <strong>Tel:</strong> 0302-8080191, 0313-9797790<br>
                Kabal Road, Near Township Chowk, Kanju Swat
            </div>
        </div>

        <hr class="divider">

        <!-- Receipt Number -->
        <div class="receipt-number-box">
            <div class="receipt-number-label">RECEIPT / TOKEN NUMBER</div>
            <div class="receipt-number">{{ $receipt->getFormattedReceiptNumber() }}</div>
        </div>

        <!-- Patient Details -->
        <div class="patient-details">
            <div class="section-title">PATIENT INFORMATION</div>

            <div class="detail-row">
                <span class="detail-label">Patient ID:</span>
                <span class="detail-value">{{ $patient->patient_id }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Patient Name:</span>
                <span class="detail-value">{{ $patient->name }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Father Name:</span>
                <span class="detail-value">{{ $patient->father_name ?? 'N/A' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Age:</span>
                <span class="detail-value">{{ $patient->age }} Years</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Gender:</span>
                <span class="detail-value">{{ ucfirst($patient->gender) }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Contact:</span>
                <span class="detail-value">{{ $patient->mobile_phone }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Address:</span>
                <span class="detail-value">{{ $patient->address ?? 'N/A' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ $receipt->created_at->format('d-M-Y H:i A') }}</span>
            </div>

            @if($patient->referred_by)
                <div class="detail-row">
                    <span class="detail-label">Referred By:</span>
                    <span class="detail-value">{{ $patient->referred_by }}</span>
                </div>
            @endif
        </div>

        <hr class="divider">

        <!-- Required Tests -->
        <div class="tests-section">
            <div class="section-title">REQUIRED TESTS</div>

            <table class="tests-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Test Name</th>
                        <th style="width: 25%; text-align: right;">Price</th>
                        <th style="width: 25%; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipt->tests as $test)
                        <tr>
                            <td class="test-name">{{ $test['test_name'] ?? 'N/A' }}</td>
                            <td class="test-price">Rs. {{ number_format($test['price'] ?? 0, 2) }}</td>
                            <td class="test-status">
                                @if(strtolower($test['paid_status'] ?? 'unpaid') === 'paid')
                                    <span class="status-paid">PAID</span>
                                @else
                                    <span class="status-unpaid">UNPAID</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: #999;">No tests registered</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <hr class="divider">

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-label">TOTAL AMOUNT PAYABLE</div>
            <div class="total-amount">Rs. {{ number_format($receipt->total_amount, 2) }}</div>
            <div class="amount-in-words">
                @php
                    $amount = intval($receipt->total_amount);
                    $words = $this->numberToWords($amount);
                @endphp
                {{ $words ?? 'Amount' }} Only
            </div>
        </div>

        <!-- Barcode -->
        <div class="barcode-section">
            <div class="barcode-number">
                {{ str_split($receipt->receipt_number, 4)[0] ?? '' }}
                {{ str_split($receipt->receipt_number, 4)[1] ?? '' }}
                {{ str_split($receipt->receipt_number, 4)[2] ?? '' }}
            </div>
            <svg class="barcode-image" jsbarcode-format="CODE128" jsbarcode-value="{{ $receipt->receipt_number }}" jsbarcode-textmargin="0" jsbarcode-height="50"></svg>
        </div>

        <hr class="divider">

        <!-- Footer -->
        <div class="receipt-footer">
            <div class="footer-note">
                ✓ Please keep this receipt for your records<br>
                ✓ All tests must be completed within the reporting date<br>
                ✓ Results will be available after specified date
            </div>

            <div class="printed-info">
                Printed by: {{ $receipt->printed_by ?? 'System' }}<br>
                {{ now()->format('d-M-Y H:i A') }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        // Generate barcode
        JsBarcode(".barcode-image", "{{ $receipt->receipt_number }}", {
            format: "CODE128",
            width: 2,
            height: 50,
            displayValue: false,
            margin: 0
        });
    </script>
</body>
</html>
