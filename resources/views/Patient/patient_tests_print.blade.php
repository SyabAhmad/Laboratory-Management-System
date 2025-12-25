@extends('Layout.print')
@section('title', 'Multiple Test Reports - Print')

@push('print-styles')
    <style>
        @page {
            margin-top: 50mm !important;
            margin-bottom: 50mm !important;
            margin-left: 20px !important;
            margin-right: 20px !important;
        }
    </style>
@endpush

@section('content')
    <table class="report-table">
        <tbody>
            <tr>
                <td style="padding: 0;">
                    <div class="print-body {{ count($testEntries) > 1 ? 'multiple-tests' : '' }}">
                        {{-- Patient info once at top --}}
                        @if (!empty($testEntries))
                            @php $firstTest = $testEntries[0]; @endphp
                            @include('Patient.partials.test_report', [
                                'patient' => $patient,
                                'testEntry' => $firstTest,
                                'skipPatientInfo' => false,
                            ])
                        @endif

                        {{-- Then each additional test data without patient info --}}
                        @foreach ($testEntries as $index => $testEntry)
                            @if ($index > 0)
                                <div style="height:6mm; width:100%;"></div> <!-- reduced separation -->
                                @include('Patient.partials.test_report', [
                                    'patient' => $patient,
                                    'testEntry' => $testEntry,
                                    'skipPatientInfo' => true,
                                ])
                            @endif
                        @endforeach
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
@endsection
