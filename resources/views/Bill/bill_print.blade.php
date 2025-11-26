<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Print - {{ $bills->bill_no }}</title>
    <style>
        @media print {
            @page { size: A4; margin: 6mm 0mm 10mm 0mm; }
        }
        :root {
            --print-page-width: 210mm;
            --print-side-padding: 0mm;
            --print-inner-width-mm: calc(var(--print-page-width) - (var(--print-side-padding) * 2));
        }
        html, body { margin: 0; padding: 0; width: var(--print-page-width); height: 297mm; }
        .report-container { width: var(--print-page-width); margin: 0; padding: 0; box-sizing: border-box; }
        .report-inner { width: var(--print-inner-width-mm); margin: 0 auto; padding: 0; box-sizing: border-box; }
        @media print {
            .no-print { display: none !important; }
            .print-header, .print-footer { display: block !important; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
</head>
<body>
    {{-- Include header/footer partial --}}
    @include('Patient.partials.print_header_footer')
    <div class="report-container">
        <div class="report-inner">
            <div class="print-body" style="margin-top: 50mm; width: 160mm; margin-left: auto; margin-right: auto; margin-bottom: 0;">
        <!-- Bill Header -->
        <div style="text-align: center; margin: 20px 0; padding: 10px; border-bottom: 2px solid #8d2d36;">
            <h2 style="color: #8d2d36; margin: 0; font-size: 24px;">BILL RECEIPT</h2>
            <p style="margin: 5px 0; font-size: 16px; color: #666;">Invoice Number: {{ $bills->bill_no }}</p>
            <p style="margin: 5px 0; font-size: 14px; color: #666;">Date: {{ $bills->created_at ? $bills->created_at->format('d-M-Y H:i') : 'N/A' }}</p>
        </div>

        <!-- Patient Information -->
        <div style="margin: 15px 0;">
            <h4 style="color: #8d2d36; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Patient Information</h4>
            <table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="width: 30%; font-weight: bold;">Patient ID:</td>
                    <td>{{ optional($bills->patient)->patient_id ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Name:</td>
                    <td>{{ optional($bills->patient)->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Mobile:</td>
                    <td>{{ optional($bills->patient)->mobile_phone ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- Test Details -->
        <div style="margin: 15px 0;">
            <h4 style="color: #8d2d36; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Test Details</h4>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">S/N</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Test Name</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Price (PKR)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @if (isset($tests) && count($tests))
                        @foreach ($tests as $test)
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;">{{ $loop->iteration }}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">{{ $test->cat_name }}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($test->price ?? 0, 2) }}</td>
                            </tr>
                            @php $total += $test->price ?? 0; @endphp
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" style="border: 1px solid #ddd; padding: 8px; text-align: center;">No test data available</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Bill Summary -->
        <div style="margin: 15px 0;">
            <h4 style="color: #8d2d36; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Bill Summary</h4>
            <table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="font-weight: bold;">Total Amount:</td>
                    <td style="text-align: right; font-weight: bold; color: #8d2d36;">{{ number_format($total, 2) }} PKR</td>
                </tr>
                <tr>
                    <td>In Words:</td>
                    <td style="text-align: right;">{{ ucwords(\App\Helpers\NumberToWords::convert($total)) }} Rupees Only</td>
                </tr>
                <tr>
                    <td>Discount:</td>
                    <td style="text-align: right;">{{ number_format($bills->discount, 2) }} PKR</td>
                </tr>
                <tr style="border-top: 1px solid #ddd;">
                    <td style="font-weight: bold;">Net Amount:</td>
                    <td style="text-align: right; font-weight: bold; color: #28a745;">{{ number_format($bills->total_price, 2) }} PKR</td>
                </tr>
                <tr>
                    <td>Payment Method:</td>
                    <td style="text-align: right;">{{ $bills->payment_type }}</td>
                </tr>
                <tr>
                    <td>Paid Amount:</td>
                    <td style="text-align: right;">{{ number_format($bills->paid_amount, 2) }} PKR</td>
                </tr>
                <tr>
                    <td>Due/Return Amount:</td>
                    <td style="text-align: right;">{{ number_format($bills->due_amount, 2) }} PKR</td>
                </tr>
            </table>
        </div>

        <!-- Bill Status -->
        <div style="margin: 20px 0; text-align: center;">
            <div style="display: inline-block; padding: 10px 20px; border: 2px solid {{ strtolower($bills->status ?? '') === 'paid' ? '#28a745' : '#ffc107' }}; border-radius: 5px;">
                <h4 style="margin: 0; color: {{ strtolower($bills->status ?? '') === 'paid' ? '#28a745' : '#ffc107' }};">
                    <i class="fas fa-{{ strtolower($bills->status ?? '') === 'paid' ? 'check-circle' : 'clock' }}"></i>
                    Bill Status: {{ ucfirst($bills->status ?? 'unpaid') }}
                </h4>
            </div>
        </div>

        <!-- Thank You Note -->
        <div style="text-align: center; margin: 20px 0; padding: 10px; background-color: #f8f9fa; border-radius: 5px;">
            <p style="margin: 0; font-size: 14px; color: #666;">Thank you for choosing New Modern Clinical Laboratory</p>
            <p style="margin: 5px 0 0 0; font-size: 12px; color: #999;">For any queries, please contact us at: 0302-8080191 | 0313-9797790</p>
        </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>