@extends('Layout.master')
@section('title', 'Dashboard')

@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">BKLT</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Dashboard</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->



        @if (Auth::user()->user_type == 'Admin' || Auth::user()->user_type == 'Super Admin')
            <!-- Quick Links Section -->
            <div class="row mb-4">
                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                    <a href="{{ route('patients.create') }}" class="text-decoration-none">
                        <div class="card dashboard-card h-100">
                            <div class="card-body dashboard-card-body-2">
                                <div class="d-flex align-items-center">
                                    <div class="icon-section">
                                        <i class="fas fa-user-plus dashboard-card-icon"></i>
                                    </div>
                                    <div class="content-section">
                                        <h5 class="card-title dashboard-card-title">Add Patient</h5>
                                        <p class="dashboard-card-text">Create new patient</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                    <a href="{{ route('allbills') }}" class="text-decoration-none">
                        <div class="card dashboard-card h-100">
                            <div class="card-body dashboard-card-body-1">
                                <div class="d-flex align-items-center">
                                    <div class="icon-section">
                                        <i class="fas fa-file-invoice-dollar dashboard-card-icon"></i>
                                    </div>
                                    <div class="content-section">
                                        <h5 class="card-title dashboard-card-title">All Bills</h5>
                                        <p class="dashboard-card-text1">View all bills</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <!-- End Quick Links Section -->

            <!-- Stats Cards Section -->
            <div class="row mb-4">
                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                    <a href="{{ route('patients.list') }}" class="text-decoration-none">
                        <div class="card dashboard-card h-100">
                            <div class="card-body dashboard-card-body-4">
                                <div class="d-flex align-items-center">
                                    <div class="icon-section">
                                        <i class="fas fa-user-injured dashboard-card-icon"></i>
                                    </div>
                                    <div class="content-section">
                                        <h5 class="card-title dashboard-card-title">Patients</h5>
                                        <p class="dashboard-card-text1">{{ App\Models\Patients::get()->count() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                    <a href="{{ route('balance.index') }}" class="text-decoration-none" aria-label="View balance details">
                        <div class="card dashboard-card balance-card-accent h-100 clickable-card" role="link">
                            <div class="card-body dashboard-card-body-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-section">
                                        <i class="fas fa-balance-scale dashboard-card-icon"></i>
                                    </div>
                                    <div class="content-section">
                                        <h5 class="card-title dashboard-card-title">Company Total Balance</h5>
                                        <p class="dashboard-card-text1">{{ isset($totalBalance) ? number_format($totalBalance, 2) : '0.00' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                    <a href="{{ route('referrals.patients') }}" class="text-decoration-none">
                        <div class="card dashboard-card h-100">
                            <div class="card-body dashboard-card-body-5">
                                <div class="d-flex align-items-center">
                                    <div class="icon-section">
                                        <i class="fas fa-handshake dashboard-card-icon"></i>
                                    </div>
                                    <div class="content-section">
                                        <h5 class="card-title dashboard-card-title">Referrals</h5>
                                        <p class="dashboard-card-text1">{{ App\Models\Referrals::get()->count() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <!-- End Stats Cards Section -->
        @else
            <div class="card">
                <div class="card-body bg-dark">
                    <div class="text-center">
                        <div id="DisplayDate" class="clock mt-5 mb-5" onload="showTime()"></div>
                        <div id="MyClockDisplay" class="clock mt-5 mb-5" onload="showTime()"></div>
                    </div>
                </div>
            </div>
        @endif
        <!-- Charts Row -->
        <div class="row mt-4">
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5 class="card-title dashboard-card-title">Monthly Billed vs Paid</h5>
                        <canvas id="chartRevenue" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5 class="card-title dashboard-card-title">Monthly Paid (Payments)</h5>
                        <canvas id="chartPayments" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->


    </div> <!-- container -->

    <script>
        function showTime() {
            var date = new Date();
            // var day = date.getDay(); // 0 - 23
            // var month = date.getMonth(); // 0 - 23
            // var year = date.getYear(); // 0 - 23
            var h = date.getHours(); // 0 - 23
            var m = date.getMinutes(); // 0 - 59
            var s = date.getSeconds(); // 0 - 59
            // var session = "AM";

            // if (h == 0) {
            //     h = 12;
            // }

            // if (h > 12) {
            //     h = h - 12;
            //     session = "PM";
            // }

            h = (h < 10) ? "0" + h : h;
            m = (m < 10) ? "0" + m : m;
            s = (s < 10) ? "0" + s : s;

            // var date1 = day+"-"+month+"-"+year;
            var time = h + ":" + m + ":" + s;
            document.getElementById("MyClockDisplay").innerText = time;
            document.getElementById("MyClockDisplay").textContent = time;
            // document.getElementById("DisplayDate").innerText = date1;
            // document.getElementById("DisplayDate").textContent = date1;


            setTimeout(showTime, 1000);

        }

        showTime();
    </script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        (function() {
            // Data passed from controller
            const labels = @json($chartLabels ?? []);
            const billed = @json($chartBilled ?? []);
            const paid = @json($chartPaid ?? []);

            // Combined chart: billed vs paid
            const ctx = document.getElementById('chartRevenue');
            if (ctx) {
                new Chart(ctx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Billed',
                                data: billed,
                                borderColor: 'rgba(54, 162, 235, 1)',
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                fill: true,
                                tension: 0.3
                            },
                            {
                                label: 'Paid',
                                data: paid,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                fill: true,
                                tension: 0.3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Payments bar chart
            const ctx2 = document.getElementById('chartPayments');
            if (ctx2) {
                new Chart(ctx2.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Payments',
                            data: paid,
                            backgroundColor: 'rgba(75, 192, 192, 0.6)'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        })();
    </script>
@endsection
