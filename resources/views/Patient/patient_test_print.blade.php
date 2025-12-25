@extends('Layout.print')
@section('title', isset($testEntry['name']) ? $testEntry['name'] . ' - Test Report' : 'Test Report')

@push('print-styles')
    <style>
        @page {
            margin-top: 40mm !important;
            margin-bottom: 50mm !important;
            margin-left: 50px !important;
            margin-right: 50px !important;
        }
    </style>
@endpush

@section('content')
    <table class="report-table">
        <tbody>
            <tr>
                <td style="padding: 0;">
                    <div class="print-body">
                        @include('Patient.partials.test_report', [ 'patient' => $patient, 'testEntry' => $testEntry ])
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <script>
        window.onload = function () {
            window.print();
        };
    </script>
@endsection
