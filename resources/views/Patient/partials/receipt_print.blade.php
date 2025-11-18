<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Slip - {{ $receipt->receipt_number }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
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
            line-height: 1.2;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        /* Thermal Printer Slip - 80 mm width (common thermal receipt width) */
        @media print {
            @page { size: 80mm auto; margin: 0; }
        }
        .slip-wrapper {
            width: 80mm;
            margin: 0 auto;
            background-color: white;
            padding: 0;
            page-break-after: avoid;
            break-inside: avoid;
        }

        .slip {
            width: 100%;
            padding: 3mm;
            font-size: 11px; /* increased baseline font size */
            line-height: 1.15;
            border: 1px dashed #999;
            margin-bottom: 6mm;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .slip-header {
            text-align: center;
            border-bottom: 1px solid #333;
            margin-bottom: 2mm;
            padding-bottom: 1mm;
        }

        .slip-title {
            font-weight: bold;
            font-size: 14px; /* made title bigger for readability */
            color: #8d2d36;
            margin-bottom: 0.2mm;
        }

        .slip-subtitle {
            font-size: 10px;
            color: #666;
            margin-bottom: 0.2mm;
        }

        .slip-marker {
            font-size: 9px;
            color: #999;
        }

        /* Patient Information Section */
        .slip-patient {
            margin-bottom: 2mm;
            padding-bottom: 1mm;
            border-bottom: 1px dashed #ddd;
        }

        .patient-row {
            display: flex;
            justify-content: space-between;
            margin: 0.6mm 0;
            font-size: 11px;
        }

        .patient-label {
            font-weight: bold;
            width: 30%;
        }

        .patient-value {
            width: 70%;
            text-align: left;
            word-break: break-word;
        }

        /* Token/Slip Number */
        .slip-token {
            text-align: center;
            border: 1px solid #8d2d36;
            padding: 2mm;
            margin: 1mm 0;
            background-color: #fafafa;
        }

        .token-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 0.3mm;
        }

        .token-number {
            font-size: 18px;
            font-weight: 800;
            font-family: 'Courier New', monospace;
            letter-spacing: 0.5px;
            color: #333;
        }

        /* Tests Section with Prices */
        .slip-tests {
            border-top: 1px dashed #ddd;
            border-bottom: 1px dashed #ddd;
            padding: 1.2mm 0;
            margin: 1.5mm 0;
            font-size: 10px;
        }

        .tests-header {
            font-weight: bold;
            margin-bottom: 0.6mm;
            font-size: 12px;
        }

        .test-item {
            display: flex;
            justify-content: space-between;
            margin: 0.5mm 0;
            padding: 0.2mm 0;
        }

        .test-name {
            flex: 1;
            margin-right: 1mm;
            font-size: 10px;
        }

        .test-price {
            text-align: right;
            font-weight: bold;
            min-width: 18mm;
            font-size: 10px;
        }

        /* Total Amount */
        .slip-total {
            display: flex;
            justify-content: space-between;
            padding: 1.6mm 0;
            margin-top: 1.2mm;
            font-weight: bold;
            font-size: 12px;
        }

        .total-label {
            font-weight: bold;
        }

        .total-amount {
            font-size: 14px;
            color: #8d2d36;
        }

        /* Barcode Section */
        .slip-barcode {
            text-align: center;
            margin-top: 2mm;
            padding-top: 1.0mm;
            border-top: 1px dashed #ddd;
        }

        #barcode {
            max-width: 100%;
            height: auto;
        }

        .barcode-text {
            font-size: 10px;
            color: #666;
            margin-top: 0.3mm;
            font-family: 'Courier New', monospace;
        }

        /* Footer */
        .slip-footer {
            padding-top: 1mm;
            margin-top: 1mm;
            font-size: 10px;
            text-align: center;
            color: #666;
        }

        /* Print Controls */
        .print-controls {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .btn {
            padding: 8px 16px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
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
    </style>
</head>

<body>
    <div class="no-print print-controls">
        <button class="btn btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Print Slip
        </button>
        <!-- <button class="btn btn-close" onclick="universalClose()">
            <i class="fas fa-times"></i> Close
        </button> -->
    </div>

    <div class="slip-wrapper">
        @include('Patient.partials.receipt_single', ['patient' => $patient, 'receipt' => $receipt, 'copyLabel' => 'Customer Copy', 'barcodeId' => 'barcode-1'])
                @php
                    $displayTests = [];

                    // Use receipt tests if available
                    if (!empty($receipt->tests) && is_array($receipt->tests)) {
                        $displayTests = $receipt->tests;
                    } else {
                        // Fallback: get from patient's test_category JSON
    $patientTests = json_decode($patient->test_category ?? '[]', true);
    if (!empty($patientTests)) {
        foreach ($patientTests as $testName) {
            $displayTests[] = [
                'test_name' => $testName,
                'price' => 0,
                                ];
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
        <!-- Divider (visual) -->
        <div style="height:4mm; width:100%;"></div>
        @include('Patient.partials.receipt_single', ['patient' => $patient, 'receipt' => $receipt, 'copyLabel' => 'Office Copy', 'barcodeId' => 'barcode-2'])


    <script>
        // Generate barcode(s) for both copies
        JsBarcode("#barcode-1", "{{ $receipt->receipt_number }}", {
            format: "CODE128",
            width: 1.2,
            height: 20,
            displayValue: false,
            margin: 0
        });
        JsBarcode("#barcode-2", "{{ $receipt->receipt_number }}", {
            format: "CODE128",
            width: 1.2,
            height: 20,
            displayValue: false,
            margin: 0
        });

        // Universal close handler
        function universalClose() {
            try {
                // Try jQuery first (most common)
                if (typeof jQuery !== 'undefined') {
                    jQuery('.modal').modal('hide');
                    jQuery('.modal-backdrop').remove();
                    jQuery('body').removeClass('modal-open');
                    setTimeout(() => {
                        window.location.href = '{{ route('patients.list') }}';
                    }, 300);
                    return;
                }

                // Try Bootstrap 5
                if (window.bootstrap && window.bootstrap.Modal) {
                    const modals = document.querySelectorAll('.modal.show');
                    modals.forEach(modal => {
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) {
                            bsModal.hide();
                        }
                    });
                }

                // Remove modal manually
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => modal.style.display = 'none');

                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());

                document.body.classList.remove('modal-open');

                // Send message to parent if in iframe
                if (window.parent && window.parent !== window) {
                    window.parent.postMessage({
                        action: 'closeModal'
                    }, '*');
                }

            } catch (e) {
                console.log('Close error:', e);
            }

            // Always redirect as fallback
            setTimeout(() => {
                window.location.href = '{{ route('patients.list') }}';
            }, 500);
        }

        // ESC key handler
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                universalClose();
            }
        });

        // Auto-close when modal hidden
        document.addEventListener('hidden.bs.modal', function() {
            setTimeout(() => {
                window.location.href = '{{ route('patients.list') }}';
            }, 300);
        });

        if (typeof jQuery !== 'undefined') {
            jQuery(document).on('hidden.bs.modal', function() {
                setTimeout(() => {
                    window.location.href = '{{ route('patients.list') }}';
                }, 300);
            });
        }
    </script>
</body>

</html>
