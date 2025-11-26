<?php

namespace App\Http\Controllers;

use App\Models\Patients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PrintDebugController extends Controller
{
    /**
     * Debug print function with enhanced error handling and logging
     */
    public function debugPrintTest($patientId, $testName)
    {
        try {
            Log::info('=== PRINT DEBUG START ===', [
                'user_id' => Auth::id(),
                'patient_id' => $patientId,
                'test_name_raw' => $testName,
                'request_url' => request()->fullUrl(),
                'user_agent' => request()->header('User-Agent'),
            ]);

            // Validate and decode test name
            $testName = rawurldecode($testName);
            Log::info('Test name after decoding', ['test_name' => $testName]);

            // Find patient with better error handling
            $patient = Patients::find($patientId);
            if (!$patient) {
                Log::error('Patient not found', ['patient_id' => $patientId]);
                return response()->json([
                    'error' => 'Patient not found',
                    'patient_id' => $patientId,
                    'debug_info' => 'The specified patient ID does not exist in the database.'
                ], 404);
            }

            Log::info('Patient found', [
                'patient_id' => $patient->id,
                'patient_name' => $patient->name,
                'patient_has_test_report' => !empty($patient->test_report)
            ]);

            // Decode and validate test reports
            $existingTestReports = json_decode($patient->test_report ?? '{}', true) ?? [];
            Log::info('Test reports decoded', [
                'test_reports_keys' => array_keys($existingTestReports),
                'test_reports_count' => count($existingTestReports)
            ]);

            // Find test data with flexible matching
            $foundKey = null;
            $testData = null;
            
            // Try exact case-insensitive match first
            foreach ($existingTestReports as $k => $v) {
                if (is_string($k) && strtolower($k) === strtolower($testName)) {
                    $foundKey = $k;
                    $testData = $v;
                    Log::info('Found exact case-insensitive match', ['found_key' => $k]);
                    break;
                }
            }

            // If no exact match, try partial matching
            if ($foundKey === null) {
                foreach ($existingTestReports as $k => $v) {
                    if (is_array($v) && isset($v['test']) && strtolower($v['test']) === strtolower($testName)) {
                        $foundKey = $k;
                        $testData = $v;
                        Log::info('Found partial match via test field', ['found_key' => $k]);
                        break;
                    }
                }
            }

            // If still no match, try contains matching
            if ($foundKey === null) {
                foreach ($existingTestReports as $k => $v) {
                    if (is_string($k) && stripos($k, $testName) !== false) {
                        $foundKey = $k;
                        $testData = $v;
                        Log::info('Found contains match', ['found_key' => $k]);
                        break;
                    }
                }
            }

            Log::info('Test search completed', [
                'searched_for' => $testName,
                'found_key' => $foundKey,
                'has_test_data' => !empty($testData)
            ]);

            // Try to find DB template
            $templateFields = [];
            $departmentName = null;
            $cat = null;

            try {
                // Try exact case-insensitive match first
                $cat = DB::table('labtest_cat')
                    ->whereRaw('LOWER(cat_name) = ?', [strtolower($testName)])
                    ->first();
                    
                if (!$cat) {
                    // Try partial match (contains)
                    $cat = DB::table('labtest_cat')
                        ->whereRaw('LOWER(cat_name) LIKE ?', ['%' . strtolower($testName) . '%'])
                        ->first();
                }

                if ($cat) {
                    $departmentName = $cat->department ?? (isset($cat->department_id) ? 
                        DB::table('departments')->where('id', $cat->department_id)->value('name') : null);
                    
                    $params = DB::table('lab_test_parameters')
                        ->where('lab_test_cat_id', $cat->id)
                        ->orderBy('id')
                        ->get();

                    foreach ($params as $p) {
                        $fieldName = \Str::slug($p->parameter_name, '_');
                        $templateFields[] = [
                            'name' => $fieldName,
                            'label' => $p->parameter_name,
                            'unit' => $p->unit ?? '',
                            'ref' => $p->reference_range ?? '',
                            'required' => false,
                        ];
                    }
                    Log::info('DB template found', [
                        'category_id' => $cat->id,
                        'category_name' => $cat->cat_name,
                        'parameters_count' => count($templateFields)
                    ]);
                } else {
                    Log::warning('No DB template found for test', ['test_name' => $testName]);
                }
            } catch (\Exception $e) {
                Log::error('DB query error while finding template', [
                    'test_name' => $testName,
                    'error' => $e->getMessage()
                ]);
            }

            // Check if we have data or template
            if (empty($testData) && empty($templateFields)) {
                $debugInfo = [
                    'patient_id' => $patientId,
                    'patient_name' => $patient->name,
                    'test_name' => $testName,
                    'available_tests' => array_keys($existingTestReports),
                    'search_methods' => [
                        'exact_case_insensitive' => 'checked',
                        'partial_via_test_field' => 'checked', 
                        'contains_matching' => 'checked'
                    ],
                    'has_template_in_db' => !empty($templateFields),
                    'has_saved_data' => !empty($testData)
                ];

                Log::warning('No test data or template found', $debugInfo);

                return response()->json([
                    'error' => 'Test data not found',
                    'message' => "No test data found for '{$testName}' or template not configured.",
                    'debug_info' => $debugInfo,
                    'suggestions' => [
                        'Check if test name matches exactly (case-insensitive)',
                        'Ensure test data is saved for this patient',
                        'Verify test category exists in database',
                        'Check if test parameters are configured'
                    ]
                ], 404);
            }

            // Build response
            $result = [
                'success' => true,
                'patient' => [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'patient_id' => $patient->patient_id
                ],
                'test' => [
                    'name' => $testName,
                    'found_key' => $foundKey,
                    'has_data' => !empty($testData),
                    'has_template' => !empty($templateFields),
                    'department' => $departmentName
                ],
                'debug_info' => [
                    'search_performed' => true,
                    'data_found' => !empty($testData),
                    'template_found' => !empty($templateFields),
                    'category_id' => $cat->id ?? null
                ]
            ];

            Log::info('=== PRINT DEBUG SUCCESS ===', $result);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('=== PRINT DEBUG ERROR ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'patient_id' => $patientId ?? null,
                'test_name' => $testName ?? null
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage(),
                'debug_info' => [
                    'error_class' => get_class($e),
                    'error_line' => $e->getLine(),
                    'error_file' => $e->getFile()
                ]
            ], 500);
        }
    }

    /**
     * Test URL generation for a specific patient and test
     */
    public function testUrlGeneration($patientId)
    {
        try {
            $patient = Patients::find($patientId);
            if (!$patient) {
                return response()->json(['error' => 'Patient not found'], 404);
            }

            $testReports = json_decode($patient->test_report ?? '{}', true) ?? [];
            $testNames = array_keys($testReports);
            
            $urls = [];
            foreach ($testNames as $testName) {
                $encodedName = rawurlencode($testName);
                $urls[] = [
                    'test_name' => $testName,
                    'encoded_name' => $encodedName,
                    'print_url' => route('patients.printTest', ['patient' => $patientId, 'testName' => $encodedName]),
                    'debug_url' => route('print.debug.test', ['patient' => $patientId, 'testName' => $encodedName])
                ];
            }

            return response()->json([
                'patient' => [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'patient_id' => $patient->patient_id
                ],
                'available_tests' => $testNames,
                'generated_urls' => $urls
            ]);

        } catch (\Exception $e) {
            Log::error('URL generation test error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}