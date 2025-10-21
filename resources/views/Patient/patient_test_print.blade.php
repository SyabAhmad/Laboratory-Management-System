
@extends('Layout.master')
@section('title', 'Test Report')
@section('content')
    @include('Patient.partials.test_report', [ 'patient' => $patient, 'testEntry' => $testEntry ])
    <script>
        window.onload = function () {
            window.print();
        };
    </script>
@endsection
