<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>@yield('title', 'Print') - New Modern Clinical Laboratory</title>
    <style>
            /* left/right set to 0mm to minimize side white area - browsers/printers may still enforce a non-printable edge */
            @page { size: A4; margin: 6mm 0mm 10mm 0mm; }
        :root {
            --print-page-width: 210mm;
            --print-side-padding: 0mm; /* use zero side padding so inner content fills more of the page; printers may still enforce a safe margin */
            --print-inner-width-mm: calc(var(--print-page-width) - (var(--print-side-padding) * 2)); /* dynamic inner width */
        }
        html, body { margin: 0; padding: 0; width: var(--print-page-width); height: 297mm; }
        .report-container { width: var(--print-page-width); margin: 0; padding: 0; box-sizing: border-box; }
          /* report-inner should be the single source of truth for inner width and should not apply horizontal padding
              so that header/footer inner wrappers and body content can match exactly. */
          .report-inner { width: var(--print-inner-width-mm); margin: 0 auto; padding: 0; box-sizing: border-box; }
        @media print {
            .no-print { display: none !important; }
            .print-header, .print-footer { display: block !important; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    @stack('print-styles')
</head>
<body>
    {{-- Include header/footer partial (centered inner) --}}
    @includeWhen(View::exists('Patient.partials.print_header_footer'), 'Patient.partials.print_header_footer', ['patient' => $patient ?? null, 'testEntry' => $testEntry ?? null])
    <div class="report-container">
        <div class="report-inner">
                    @yield('content')
        </div>
    </div>
    @stack('print-scripts')
</body>
</html>
