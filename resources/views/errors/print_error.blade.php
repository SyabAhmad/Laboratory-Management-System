<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Error - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px;
        }
        .error-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .error-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .error-header i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        .error-content {
            padding: 40px;
        }
        .error-message {
            background: #f8f9fa;
            border-left: 4px solid #dc3545;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .debug-info {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .debug-info h5 {
            color: #1976d2;
            margin-bottom: 15px;
        }
        .info-item {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #333;
            display: inline-block;
            min-width: 120px;
        }
        .info-value {
            color: #666;
            word-break: break-all;
        }
        .test-list {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .test-item {
            background: white;
            padding: 8px 12px;
            margin: 5px 0;
            border-radius: 5px;
            border-left: 3px solid #28a745;
            font-family: monospace;
            font-size: 0.9em;
        }
        .suggestions {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .suggestions h5 {
            color: #856404;
            margin-bottom: 15px;
        }
        .suggestions ul {
            margin: 0;
            padding-left: 20px;
        }
        .suggestions li {
            color: #856404;
            margin: 8px 0;
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-header">
            <i class="fas fa-print"></i>
            <h2>Print Error</h2>
            <p>Unable to generate test report for printing</p>
        </div>
        
        <div class="error-content">
            <div class="error-message">
                <h4><i class="fas fa-exclamation-triangle text-danger"></i> Error Details</h4>
                <p><strong>Message:</strong> {{ $message ?? 'Unknown error occurred' }}</p>
                
                @isset($testName)
                    <p><strong>Test Name:</strong> <code>{{ $testName }}</code></p>
                @endisset
            </div>

            @isset($patient)
                <div class="debug-info">
                    <h5><i class="fas fa-info-circle"></i> Patient Information</h5>
                    <div class="info-item">
                        <span class="info-label">Patient ID:</span>
                        <span class="info-value">{{ $patient->id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Patient Name:</span>
                        <span class="info-value">{{ $patient->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Lab ID:</span>
                        <span class="info-value">{{ $patient->patient_id ?? 'Not assigned' }}</span>
                    </div>
                </div>
            @endisset

            @isset($availableTests)
                @if(count($availableTests) > 0)
                    <div class="debug-info">
                        <h5><i class="fas fa-list"></i> Available Tests for this Patient</h5>
                        <p>The following tests have data available for printing:</p>
                        <div class="test-list">
                            @foreach($availableTests as $test)
                                <div class="test-item">{{ $test }}</div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="debug-info">
                        <h5><i class="fas fa-exclamation-circle"></i> No Test Data Found</h5>
                        <p>This patient doesn't have any test data saved yet.</p>
                    </div>
                @endif
            @endisset

            <div class="suggestions">
                <h5><i class="fas fa-lightbulb"></i> Possible Solutions</h5>
                <ul>
                    <li><strong>Check test name spelling:</strong> Ensure the test name matches exactly (case-insensitive)</li>
                    <li><strong>Verify data exists:</strong> Make sure test results have been saved for this patient</li>
                    <li><strong>Check test configuration:</strong> Ensure the test category exists in the database</li>
                    <li><strong>Try different test:</strong> Select a different test that has complete data</li>
                    <li><strong>Contact support:</strong> If the issue persists, contact system administrator</li>
                </ul>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ url()->previous() }}" class="btn-custom">
                    <i class="fas fa-arrow-left"></i> Go Back
                </a>
                
                @isset($patient)
                    <a href="{{ route('patients.profile', $patient->id) }}" class="btn-custom">
                        <i class="fas fa-user"></i> View Patient
                    </a>
                @endisset
                
                <a href="{{ route('patients.list') }}" class="btn-custom">
                    <i class="fas fa-list"></i> All Patients
                </a>
            </div>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>