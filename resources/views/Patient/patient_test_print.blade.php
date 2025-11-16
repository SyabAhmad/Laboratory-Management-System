
@extends('Layout.print')
@section('title', isset($testEntry['name']) ? $testEntry['name'] . ' - Test Report' : 'Test Report')
@section('content')
    <div class="print-body">
        @include('Patient.partials.test_report', [ 'patient' => $patient, 'testEntry' => $testEntry ])
    </div>
    <script>
        window.onload = function () {
            window.print();
        };
    </script>
@endsection
