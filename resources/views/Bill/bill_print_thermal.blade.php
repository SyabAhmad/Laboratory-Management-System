<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Receipt - Thermal Print</title>
    <style>
        @media print {
            @page {
                size: 80mm auto; /* Thermal printer width */
                margin: 0;
            }
            body {
                width: 80mm;
                margin: 0;
                padding: 2mm;
                font-family: 'Courier New', monospace;
                font-size: 12px;
                line-height: 1.2;
            }
        }
        body {
            width: 80mm;
            margin: 0 auto;
            padding: 2mm;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
            background: white;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        .double-divider { border-top: 2px solid #000; margin: 5px 0; }
        .status-paid { color: green; }
        .status-unpaid { color: orange; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; }
        .small { font-size: 10px; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="center bold">
        NEW MODERN CLINICAL LABORATORY<br>
        (KP HCC) REG: 03663 SWAT<br>
        Bacha Khan, BS Pathology (KMU)<br>
        DMLT KPK Peshawar<br>
        CT Pathology Department<br>
        Saidu Medical College/SGTH Swat<br>
    </div>
    <div class="divider"></div>

    <!-- Bill Info -->
    <div class="center bold">
        BILL RECEIPT<br>
        Invoice: {{ $bills->bill_no }}<br>
        Date: {{ $bills->created_at ? $bills->created_at->format('d-M-Y H:i') : 'N/A' }}<br>
    </div>
    <div class="divider"></div>

    <!-- Patient Info -->
    <div>
        <strong>Patient ID:</strong> {{ optional($bills->patient)->patient_id ?? 'N/A' }}<br>
        <strong>Name:</strong> {{ optional($bills->patient)->name ?? 'N/A' }}<br>
        <strong>Mobile:</strong> {{ optional($bills->patient)->mobile_phone ?? 'N/A' }}<br>
    </div>
    <div class="divider"></div>

    <!-- Tests -->
    <div class="center bold">TEST DETAILS</div>
    <table>
        <thead>
            <tr>
                <td class="bold">#</td>
                <td class="bold">Test</td>
                <td class="bold right">Price</td>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @if (isset($tests) && count($tests))
                @foreach ($tests as $test)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $test->cat_name }}</td>
                        <td class="right">{{ number_format($test->price ?? 0, 2) }}</td>
                    </tr>
                    @php $total += $test->price ?? 0; @endphp
                @endforeach
            @else
                <tr>
                    <td colspan="3" class="center">No tests</td>
                </tr>
            @endif
        </tbody>
    </table>
    <div class="divider"></div>

    <!-- Summary -->
    <table>
        <tr>
            <td><strong>Total:</strong></td>
            <td class="right">{{ number_format($total, 2) }}</td>
        </tr>
        <tr>
            <td>Discount:</td>
            <td class="right">{{ number_format($bills->discount, 2) }}</td>
        </tr>
        <tr>
            <td class="bold">Net Amount:</td>
            <td class="right bold">{{ number_format($bills->total_price, 2) }}</td>
        </tr>
        <tr>
            <td>Payment:</td>
            <td class="right">{{ $bills->payment_type }}</td>
        </tr>
        <tr>
            <td>Paid:</td>
            <td class="right">{{ number_format($bills->paid_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Due:</td>
            <td class="right">{{ number_format($bills->due_amount, 2) }}</td>
        </tr>
    </table>
    <div class="divider"></div>

    <!-- In Words -->
    <div class="small">
        <strong>In Words:</strong><br>
        {{ ucwords(\App\Helpers\NumberToWords::convert($bills->total_price)) }} Rupees Only
    </div>
    <div class="divider"></div>

    <!-- Status -->
    <div class="center bold {{ strtolower($bills->status ?? '') === 'paid' ? 'status-paid' : 'status-unpaid' }}">
        STATUS: {{ strtoupper($bills->status ?? 'unpaid') }}
    </div>
    <div class="double-divider"></div>

    <!-- Footer -->
    <div class="center small">
        Thank you for choosing us!<br>
        Contact: 0302-8080191 | 0313-9797790<br>
        Email: bachakhanacl@gmail.com<br>
        Kabal Road, Near Township Chowk, Kanju Swat
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>