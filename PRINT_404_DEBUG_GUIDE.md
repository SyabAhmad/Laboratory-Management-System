# Print 404 Error Diagnosis and Resolution Guide

## Problem Overview

You're experiencing intermittent 404 Not Found errors when trying to print test reports in your Laboratory Management System. This guide provides debugging tools and solutions to identify and resolve the underlying causes.

## Common Causes

### 1. **URL Encoding Issues**

-   Test names with spaces, special characters, or non-ASCII characters
-   Improper URL encoding/decoding in JavaScript

### 2. **Missing Test Data**

-   Patient records without saved test results
-   Test names that don't match database records exactly

### 3. **Database Query Failures**

-   Template parameters not configured for specific tests
-   Missing test categories in the database

### 4. **Authentication/Session Issues**

-   Expired sessions affecting print requests
-   Missing CSRF tokens or authentication cookies

## Debugging Tools Created

### 1. **PrintDebugController** (`app/Http/Controllers/PrintDebugController.php`)

Provides detailed debugging information for print requests.

**Debug Routes Added:**

-   `GET /print/debug/{patient}/{testName}` - Detailed debug info for specific test
-   `GET /print/test-urls/{patient}` - Generate all print URLs for a patient

### 2. **ImprovedPrintController** (`app/Http/Controllers/ImprovedPrintController.php`)

Enhanced print functions with better error handling and fallback strategies.

### 3. **Custom Error View** (`resources/views/errors/print_error.blade.php`)

User-friendly error page with debugging information and suggestions.

## Step-by-Step Diagnosis

### Step 1: Enable Laravel Logging

Add this to your `.env` file:

```env
LOG_LEVEL=debug
```

### Step 2: Test Print URLs

1. Go to a patient's profile page
2. Open browser Developer Tools (F12)
3. Try to print a test report
4. Check the Console tab for any JavaScript errors
5. Check the Network tab for failed requests

### Step 3: Use Debug Routes

#### Get Patient's Available Tests:

```
GET /print/test-urls/{patient_id}
```

This returns JSON with all available tests and their print URLs.

#### Debug Specific Print Error:

```
GET /print/debug/{patient_id}/{encoded_test_name}
```

This provides detailed information about why a specific print is failing.

### Step 4: Check Log Files

After attempting prints, check Laravel logs:

```bash
tail -f storage/logs/laravel.log
```

Look for entries like:

-   "PRINT DEBUG START"
-   "Print test error"
-   Database query errors
-   Authentication issues

## Common Solutions

### Solution 1: Fix Test Name Encoding

**Problem:** Test names with special characters cause URL issues
**Solution:** Update JavaScript to properly encode test names

In your Blade files, change:

```javascript
// OLD (problematic)
printTest(event, '{{ route('patients.printTest', ['patient' => $patient->id, 'testName' => $testName]) }}')

// NEW (fixed)
printTest(event, '{{ route('patients.printTest', ['patient' => $patient->id, 'testName' => urlencode($testName)]) }}')
```

### Solution 2: Add Test Data Validation

Ensure test data exists before allowing print:

```php
// In your print function
if (empty($testData) && empty($templateFields)) {
    return response()->json([
        'error' => 'No test data found',
        'available_tests' => array_keys($existingTestReports)
    ], 404);
}
```

### Solution 3: Improve Error Handling

Use the `ImprovedPrintController` which includes:

-   Multiple fallback strategies for finding test data
-   Better error messages
-   Comprehensive logging

### Solution 4: Database Cleanup

Check and fix test names in the database:

```sql
-- Find patients with test data issues
SELECT id, name, test_report
FROM patients
WHERE test_report IS NOT NULL
AND test_report != '{}';

-- Check test categories
SELECT id, cat_name FROM labtest_cat ORDER BY cat_name;
```

## Implementation Steps

### Step 1: Clear Laravel Cache

```bash
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
```

### Step 2: Test Debug Routes

1. Navigate to: `/print/test-urls/1` (replace 1 with actual patient ID)
2. Try debug routes for specific failing prints

### Step 3: Monitor Logs

After implementing changes, monitor the logs during print attempts:

```bash
tail -f storage/logs/laravel.log | grep -E "(PRINT|print)"
```

### Step 4: Update Print Functions (Optional)

Replace existing print functions with improved versions:

```php
// In routes/web.php, update routes to use ImprovedPrintController
Route::get('/patients/{patient}/tests/{testName}/print',
    'App\Http\Controllers\ImprovedPrintController@improvedPrintTestReport')
    ->name('patients.printTest');
```

## JavaScript Improvements

### Add Error Handling to Print Function

```javascript
function printTest(e, url) {
    if (e && e.preventDefault) e.preventDefault();

    fetch(url, {
        credentials: "include",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
        },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(
                    `HTTP ${response.status}: ${response.statusText}`
                );
            }
            return response;
        })
        .then((resp) => {
            // ... existing print logic
        })
        .catch((error) => {
            console.error("Print failed:", error);
            alert(
                "Print failed: " +
                    error.message +
                    ". Please try again or contact support."
            );
        });
}
```

## Quick Fixes for Common Issues

### Fix 1: Spaces in Test Names

```php
// Ensure test names are properly encoded
$testName = str_replace(' ', '%20', $testName);
// or
$testName = rawurlencode($testName);
```

### Fix 2: Case Sensitivity

```php
// Use case-insensitive matching
$test = Test::whereRaw('LOWER(name) = ?', [strtolower($testName)])->first();
```

### Fix 3: Missing Test Data

```php
// Check if test data exists before processing
if (!$patient->test_report || $patient->test_report === '{}') {
    return redirect()->back()->with('error', 'No test data found for this patient.');
}
```

## When to Contact Support

Contact technical support if:

1. Debug routes show no obvious issues
2. Database queries are failing unexpectedly
3. Authentication issues persist after clearing cache
4. Custom error pages aren't displaying properly

## Prevention Tips

1. **Regular Database Cleanup:** Remove orphaned test data
2. **Test Name Standardization:** Use consistent naming conventions
3. **Monitor Logs:** Set up log monitoring for print errors
4. **User Training:** Ensure staff understand proper test data entry
5. **Regular Backups:** Regular database backups before major changes

---

**Note:** This debugging system is designed to be temporary. Once issues are identified and resolved, debug routes can be removed from production for security reasons.
