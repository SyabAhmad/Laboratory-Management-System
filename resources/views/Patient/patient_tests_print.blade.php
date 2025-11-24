@extends('Layout.print')
@section('title', 'Multiple Test Reports - Print')
@section('content')
    <div class="print-body {{ count($testEntries) > 1 ? 'multiple-tests' : '' }}">
        {{-- Patient info once at top --}}
        @if(!empty($testEntries))
            @php $firstTest = $testEntries[0]; @endphp
            @include('Patient.partials.test_report', ['patient' => $patient, 'testEntry' => $firstTest, 'skipPatientInfo' => false])
        @endif

        {{-- Then each additional test data without patient info --}}
        @foreach($testEntries as $index => $testEntry)
            @if($index > 0)
                <div style="height:6mm; width:100%;"></div> <!-- reduced separation -->
                @include('Patient.partials.test_report', ['patient' => $patient, 'testEntry' => $testEntry, 'skipPatientInfo' => true])
            @endif
        @endforeach
    </div>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
@endsection
