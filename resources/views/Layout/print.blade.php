<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>@yield('title', 'Print') - New Modern Clinical Laboratory</title>
    <style>
            /* A4 printer settings */
            @page { size: A4; margin: 10mm; }
        :root {
            --print-page-width: 210mm; /* A4 width */
            --print-side-padding: 10mm;
            --print-inner-width-mm: calc(var(--print-page-width) - (var(--print-side-padding) * 2));
        }
        html, body { margin: 0; padding: 0; width: 100%; height: auto; }
        .report-container { width: 100%; margin: 0; padding: 0; box-sizing: border-box; }
           /* report-inner should be the single source of truth for inner width and should not apply horizontal padding
              so that header/footer inner wrappers and body content can match exactly. */
           .report-inner { width: 100%; margin: 0 auto; padding: 0; box-sizing: border-box; }
        @media print {
            .no-print { display: none !important; }
            /* Table-based layout for repeating headers */
            table.report-table { width: 100%; border-collapse: collapse; border: none; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            tr { page-break-inside: avoid; }
            /* Ensure content flows correctly */
            .report-table td { vertical-align: top; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/notes-markdown.css') }}">
    @stack('print-styles')
</head>
<body>
    {{-- Header/Footer are now handled via table structure in the content view --}}
    <div class="report-container">
        <div class="report-inner">
                    @yield('content')
        </div>
    </div>
    @stack('print-scripts')
</body>
</html>
