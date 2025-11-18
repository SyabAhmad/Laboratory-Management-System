@extends('Layout.print')
@section('title', 'Multiple Test Reports - Print')
@section('content')
    <div class="print-body">
        @foreach($testEntries as $testEntry)
            @include('Patient.partials.test_report', ['patient' => $patient, 'testEntry' => $testEntry])
            <div style="height:12mm; width:100%;"></div>
        @endforeach
    </div>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
@endsection
