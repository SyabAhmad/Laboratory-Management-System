# Print Button Fix Plan

## Problem Analysis

The print buttons in `resources/views/Patient/patient_edit.blade.php` are not working due to JavaScript errors in the `printTest()` function and button handlers.

## Root Causes Identified

1. **Function Parameter Error**: The `printTest(e, url)` function expects an event parameter `e` as the first argument, but the button handlers are calling it incorrectly.

2. **Event Handling Issue**: In the button handlers, `printTest(e, target)` is called with the event `e` as the first parameter, but the function expects the URL as the first parameter.

3. **URL Construction**: The URL for the "Print Selected" button was using the wrong route (with-header instead of without-header).

## Solution

### 1. Fix the `printTest()` Function

**Current Code (Lines 707-761):**

```javascript
function printTest(e, url) {
    if (e && e.preventDefault) e.preventDefault();
    // ... rest of function
}
```

**Fixed Code:**

```javascript
function printTest(url) {
    console.log("Print request:", url);
    // ... rest of function (remove e.preventDefault() call)
}
```

### 2. Fix Button Handlers

**Current Code (Lines 765-790):**

```javascript
// Print Selected button handler
$("#btn-print-selected").on("click", function (e) {
    e.preventDefault();
    // ... code ...
    printTest(e, target); // ❌ Wrong: passing event as first parameter
});

// Print Selected with Header button handler
$("#btn-print-selected-with-header").on("click", function (e) {
    e.preventDefault();
    // ... code ...
    printTest(e, target); // ❌ Wrong: passing event as first parameter
});
```

**Fixed Code:**

```javascript
// Print Selected button handler
$("#btn-print-selected").on("click", function (e) {
    e.preventDefault();
    // ... code ...
    printTest(target); // ✅ Correct: passing URL as first parameter
});

// Print Selected with Header button handler
$("#btn-print-selected-with-header").on("click", function (e) {
    e.preventDefault();
    // ... code ...
    printTest(target); // ✅ Correct: passing URL as first parameter
});
```

### 3. Fix URL Construction

**Current Code:**

```javascript
const urlBase =
    '{{ url("/patients/" . $patient->id . "/tests/print-multiple-with-header") }}';
```

**Fixed Code:**

```javascript
// For "Print Selected" button (without header)
const urlBase =
    '{{ url("/patients/" . $patient->id . "/tests/print-multiple") }}';

// For "Print Selected with Header" button (with header)
const urlBase =
    '{{ url("/patients/" . $patient->id . "/tests/print-multiple-with-header") }}';
```

## Implementation Steps

1. **Switch to Code mode** to edit the Blade template file
2. **Apply the JavaScript fixes** to the `printTest()` function
3. **Update the button handlers** to pass parameters correctly
4. **Fix the URL construction** for both buttons
5. **Test the functionality** to ensure print buttons work correctly

## Expected Outcome

After implementing these fixes:

-   The "Print Selected" button will work and print selected tests without headers
-   The "Print Selected with Header" button will work and print selected tests with headers
-   The individual test print buttons in the table will continue to work as before
-   No JavaScript errors will occur in the browser console

## Files to Modify

-   `resources/views/Patient/patient_edit.blade.php` (Lines 706-790)
