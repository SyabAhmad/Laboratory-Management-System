<?php

namespace App\Http\Controllers;

use App\Models\Patients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ImprovedPrintController extends Controller
{
    /**
     * Improved print test report with better error handling
     */
    public function improvedPrintTestReport($patientId, $testName)
    {
        try {
            Log::info('Improved print test started', [
                'user_id' => Auth::id(),
                'patient_id' => $patientId,
                'test_name_raw' => $testName
            ]);

            // Validate and decode test name
            $testName = rawurldecode($testName);
            
            // Find patient with error handling
            $patient = Patients::findOrFail($patientId);

            // Get test data with multiple fallback strategies
            $testEntry = $this->findTestData($patient, $testName);
            
            if (!$testEntry['has_data'] && !$testEntry['has_template']) {
                Log::warning('No test data or template found', [
                    'patient_id' => $patientId,
                    'test_name' => $testName,
                    'available_tests' => array_keys(json_decode($patient->test_report ?? '{}', true) ?? [])
                ]);
                
                return $this->createErrorResponse($patient, $testName, 'No test data found for this test.');
            }

            Log::info('Test data found successfully', [
                'patient_id' => $patientId,
                'test_name' => $testName,
                'has_data' => $testEntry['has_data'],
                'has_template' => $testEntry['has_template']
            ]);

            return view('Patient.patient_test_print', compact('patient', 'testEntry'));
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Patient not found', ['patient_id' => $patientId]);
            return response()->view('errors.404', ['message' => 'Patient not found'], 404);
        } catch (\Exception $e) {
            Log::error('Print test error', [
                'error' => $e->getMessage(),
                'patient_id' => $patientId,
                'test_name' => $testName,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->view('errors.500', ['message' => 'An error occurred while generating the print view.'], 500);
        }
    }

    /**
     * Improved print multiple test reports
     */
    public function improvedPrintMultipleTestReports($patientId, $testNames)
    {
        try {
            Log::info('Improved print multiple tests started', [
                'user_id' => Auth::id(),
                'patient_id' => $patientId,
                'test_names_raw' => $testNames
            ]);

            $patient = Patients::findOrFail($patientId);
            
            $decoded = rawurldecode($testNames);
            $names = array_filter(array_map('trim', explode(',', $decoded)));
            
            if (empty($names)) {
                return $this->createErrorResponse($patient, '', 'No test names provided.');
            }

            $testEntries = [];
            $errors = [];
            
            foreach ($names as $name) {
                $testEntry = $this->findTestData($patient, $name);
                
                if ($testEntry['has_data'] || $testEntry['has_template']) {
                    $testEntries[] = $testEntry;
                } else {
                    $errors[] = $name;
                    Log::warning('Test not found for multiple print', [
                        'patient_id' => $patientId,
                        'missing_test' => $name
                    ]);
                }
            }

            if (empty($testEntries)) {
                return $this->createErrorResponse($patient, '', 'No valid test data found for printing.');
            }

            if (!empty($errors)) {
                Log::info('Some tests skipped due to missing data', [
                    'patient_id' => $patientId,
                    'errors' => $errors,
                    'successful_tests' => count($testEntries)
                ]);
            }

            return view('Patient.patient_tests_print', compact('patient', 'testEntries'));
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Patient not found for multiple print', ['patient_id' => $patientId]);
            return response()->view('errors.404', ['message' => 'Patient not found'], 404);
        } catch (\Exception $e) {
            Log::error('Print multiple tests error', [
                'error' => $e->getMessage(),
                'patient_id' => $patientId,
                'test_names' => $testNames,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->view('errors.500', ['message' => 'An error occurred while generating the print view.'], 500);
        }
    }

    /**
     * Find test data using multiple strategies
     */
    private function findTestData($patient, $testName)
    {
        $existingTestReports = json_decode($patient->test_report ?? '{}', true) ?? [];
        
        // Strategy 1: Exact case-insensitive match
        $foundKey = null;
        $testData = null;
        
        foreach ($existingTestReports as $k => $v) {
            if (is_string($k) && strtolower($k) === strtolower($testName)) {
                $foundKey = $k;
                $testData = $v;
                break;
            }
        }

        // Strategy 2: Match via 'test' field
        if ($foundKey === null) {
            foreach ($existingTestReports as $k => $v) {
                if (is_array($v) && isset($v['test']) && strtolower($v['test']) === strtolower($testName)) {
                    $foundKey = $k;
                    $testData = $v;
                    break;
                }
            }
        }

        // Strategy 3: Partial contains match
        if ($foundKey === null) {
            foreach ($existingTestReports as $k => $v) {
                if (is_string($k) && stripos($k, $testName) !== false) {
                    $foundKey = $k;
                    $testData = $v;
                    break;
                }
            }
        }

        // Get template from database
        $templateFields = [];
        $departmentName = null;
        
        try {
            $cat = DB::table('labtest_cat')
                ->whereRaw('LOWER(cat_name) = ?', [strtolower($testName)])
                ->first();
                
            if (!$cat) {
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
            }
        } catch (\Exception $e) {
            Log::warning('DB template query failed', [
                'test_name' => $testName,
                'error' => $e->getMessage()
            ]);
        }

        // Flatten test data for display
        $savedDataFlattened = [];
        if (is_array($testData)) {
            $savedDataFlattened = $this->flattenTestData($testData);
            $savedDataFlattened = $this->mapSavedDataToSlugKeys($savedDataFlattened);
        }

        return [
            'name' => $testName,
            'template' => ['fields' => $templateFields],
            'saved_data' => $savedDataFlattened,
            'department' => $departmentName,
            'has_template' => !empty($templateFields),
            'has_data' => !empty($testData),
        ];
    }

    /**
     * Create standardized error response
     */
    private function createErrorResponse($patient, $testName, $message)
    {
        $availableTests = array_keys(json_decode($patient->test_report ?? '{}', true) ?? []);
        
        $errorView = view()->exists('errors.print_error') 
            ? 'errors.print_error' 
            : 'errors.404';
            
        return response()->view($errorView, [
            'patient' => $patient,
            'testName' => $testName,
            'message' => $message,
            'availableTests' => $availableTests
        ], 404);
    }

    /**
     * Helper function to flatten test data
     */
    private function flattenTestData($testData)
    {
        $savedData = [];
        if (is_array($testData)) {
            foreach ($testData as $k => $v) {
                if ($k === 'analytes' && is_array($v)) {
                    foreach ($v as $analyte) {
                        if (is_array($analyte) && isset($analyte['name']) && array_key_exists('value', $analyte)) {
                            $savedData[$analyte['name']] = $analyte['value'];
                        }
                    }
                } else {
                    $savedData[$k] = $v;
                }
            }
        } else {
            $savedData = $testData;
        }
        return $savedData;
    }

    /**
     * Map saved data keys to slug format
     */
    private function mapSavedDataToSlugKeys(array $savedData)
    {
        $mapped = $savedData;
        foreach ($savedData as $k => $v) {
            $slug = \Str::slug($k, '_');
            if (!array_key_exists($slug, $mapped)) {
                $mapped[$slug] = $v;
            }
        }
        return $mapped;
    }
}