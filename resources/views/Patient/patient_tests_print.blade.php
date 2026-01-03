@extends('Layout.print')
@section('title', 'Multiple Test Reports - Print')

@push('print-styles')
    <style>
        @media print {
            :root {
                --print-header-height: 50mm;
                --print-footer-height: 40mm;
            }
            
            /* Override all global styles */
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: auto !important;
                background-color: white;
            }
            
            .report-container, .report-inner {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box !important;
            }
            
            .report-content {
                padding-left: 50px;
                padding-right: 50px;
            }
            
            .report-table {
                width: 100%;
                border-collapse: collapse;
                page-break-inside: auto;
            }
            
            .report-table thead {
                display: table-header-group;
                page-break-after: avoid;
            }
            
            .report-table tfoot {
                display: table-footer-group;
                page-break-before: avoid;
            }
            
            .report-table tr {
                page-break-inside: avoid;
            }
        }
        
        @page {
            margin: 0 !important;
        }
    </style>
@endpush

@section('content')
    <div class="report-content">
        <table class="report-table">
            <thead>
                <tr>
                    <td style="height: 50mm; padding: 0; border: none; vertical-align: bottom;">
                        <!-- Empty header space -->
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 0; border: none;">
                    <div style="height: 0mm;"></div> <!-- Spacer between header and content -->
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
        <tfoot>
            <tr>
                <td style="height: 40mm; padding: 0; border: none; vertical-align: top;">
                    <!-- Empty footer space -->
                </td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection