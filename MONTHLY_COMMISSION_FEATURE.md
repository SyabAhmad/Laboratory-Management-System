# Monthly Commission Summary Feature

## Overview

Added a monthly-based commission grouping and payment system to simplify commission tracking and payments for referrals.

## Feature Description

### What's New

1. **Monthly View**: Groups all commissions by month and displays summary statistics
2. **Monthly Mark Paid**: Single button to mark all pending commissions for a month as paid
3. **Expandable Details**: Click "Show Invoices" to see individual commission records for each month
4. **Better Reporting**: Statistics cards show pending and paid amounts separately
5. **Easy Navigation**: Switch between individual view and monthly summary view

### Key Benefits

-   **Simplified Payments**: Mark entire months as paid with one click instead of marking individual commissions
-   **Better Analytics**: See cumulative commission amounts per month
-   **Audit Trail**: Maintains all individual commission records for reference
-   **Clean UI**: Collapsible invoice details keep the view organized

## How to Use

### Accessing Monthly Commission View

1. Navigate to any referral's commission page: `/referrals/{id}/commissions`
2. Click the **"Monthly View"** button in the top right
3. Or go directly to: `/referrals/{id}/commissions/monthly`

### Mark Entire Month as Paid

1. Find the month you want to mark as paid
2. Click the **"Mark Paid"** button on the right
3. A confirmation dialog will show:
    - Month name
    - Total commission amount for that month
4. Click **"Yes, Mark Paid"** to confirm
5. All pending commissions for that month will be marked as paid
6. Page will automatically reload to show updated status

### View Individual Commissions

1. In monthly view, click **"Show Invoices"** for any month
2. Table expands to show all individual commission records for that month
3. Shows: Date, Patient, Bill #, Bill Amount, Commission %, Commission Amount, Status
4. Click **"Hide Invoices"** to collapse the details

### Switch Between Views

-   **Monthly View**: `/referrals/{id}/commissions/monthly` (Summary view)
-   **Individual View**: `/referrals/{id}/commissions` (Detailed records)
-   Both views have navigation buttons to switch between them

## Technical Implementation

### Controller Methods Added

#### 1. `commissionsMonthly($referralId)`

-   Groups all commissions by month (YYYY-MM format)
-   Calculates totals, pending, and paid amounts per month
-   Determines month status (pending, paid, or partial)
-   Returns data sorted with newest months first

**Data Structure:**

```php
$monthlyData = [
    '2025-11' => [
        'month_key' => '2025-11',
        'month_label' => 'November 2025',
        'commissions' => [...], // array of commission objects
        'total_amount' => 50000,
        'pending_amount' => 20000,
        'paid_amount' => 30000,
        'pending_count' => 3,
        'paid_count' => 2,
        'status' => 'partial' // pending, paid, or mixed
    ],
    // ... more months
]
```

#### 2. `markMonthPaid($referralId, $monthKey)`

-   Accepts referral ID and month key (YYYY-MM format)
-   Finds all pending commissions in that month
-   Updates all of them to 'paid' status
-   Returns JSON response with:
    -   `success`: boolean
    -   `message`: Description of action taken
    -   `total_amount`: Total commission amount marked as paid
    -   `commission_count`: Number of commissions updated

### Routes Added

```php
Route::get('/referrals/{referralId}/commissions/monthly', 'ReferralController@commissionsMonthly')
    ->name('referrals.commissions-monthly');

Route::post('/referrals/{referralId}/commissions/month/{monthKey}/mark-paid', 'ReferralController@markMonthPaid')
    ->name('referrals.mark-month-paid');
```

### Views Updated/Created

#### `resources/views/referrel/commissions_monthly.blade.php` (NEW)

-   Monthly summary table with statistics
-   Expandable invoice details
-   Mark paid buttons per month
-   Navigation buttons

#### `resources/views/referrel/commissions.blade.php` (UPDATED)

-   Added "Monthly View" button
-   Added navigation links to switch views
-   Enhanced header with view switcher

## Database Structure

No database changes required. The feature works with existing `referral_commissions` table by:

-   Grouping by `created_at` date (using `date_format` in Laravel)
-   Updating `status` field to 'paid'
-   Creating audit trail through timestamps

## User Experience Flow

```
Referral Page
    ↓
Click "View Commissions" → Goes to Individual Commission Records
    ↓
Click "Monthly View" → Switches to Monthly Summary
    ↓
See monthly totals with "Show Invoices" collapsible sections
    ↓
Click "Mark Paid" on any month → Confirmation dialog
    ↓
Confirm → All pending commissions for that month marked as paid
    ↓
Page reloads with updated status
```

## Status Display

### Month Status Colors

-   **Yellow/Warning**: Pending - All commissions pending payment
-   **Green/Success**: Paid - All commissions paid
-   **Info/Blue**: Partial - Mix of pending and paid commissions

### Commission Status Badges

-   **Pending**: Yellow badge
-   **Paid**: Green badge
-   **Other**: Red badge

## Confirmations & Alerts

### Mark Month as Paid Dialog

Shows:

-   Month name (e.g., "November 2025")
-   Total amount to be marked as paid
-   Two buttons: "Yes, Mark Paid" and "Cancel"

### Success Message

Shows after marking month as paid:

-   Success message
-   Total amount marked as paid
-   Number of commissions updated
-   Auto-reloads page

### Error Handling

-   Server errors show in alert with error message
-   Network errors are caught and displayed
-   Console logs available for debugging

## Testing Checklist

-   [ ] Navigate to `/referrals/6/commissions/monthly`
-   [ ] Verify monthly grouping shows correct months
-   [ ] Check that totals are calculated correctly
-   [ ] Click "Show Invoices" - table should expand
-   [ ] Click "Hide Invoices" - table should collapse
-   [ ] Click "Mark Paid" on a month with pending commissions
-   [ ] Confirm in the dialog
-   [ ] Verify commissions are marked as paid
-   [ ] Verify page shows updated status
-   [ ] Switch between monthly and individual views
-   [ ] Check that paid amounts are calculated correctly
-   [ ] Verify pending amounts exclude paid commissions

## Performance Considerations

-   All commissions are loaded for a referral (no pagination on monthly view)
-   Grouping is done in PHP, not database
-   For referrals with thousands of commissions, consider adding:
    -   Date range filters
    -   Pagination by month
    -   Database-level grouping with aggregates

## Future Enhancements

1. **Date Range Filters**: Show only specific month ranges
2. **Export to Excel**: Export monthly summary to spreadsheet
3. **Payment Batch Processing**: Select multiple months to mark paid at once
4. **Payment History**: Track when payments were made
5. **Commission Notes**: Add notes when marking months as paid
6. **Email Notifications**: Send confirmation emails when months marked paid
7. **Dashboard Widget**: Show summary of pending monthly commissions

## Files Modified

1. **app/Http/Controllers/ReferralController.php**

    - Added `commissionsMonthly()` method
    - Added `markMonthPaid()` method

2. **routes/web.php**

    - Added route for `commissionsMonthly`
    - Added route for `markMonthPaid`

3. **resources/views/referrel/commissions.blade.php**

    - Added "Monthly View" button
    - Enhanced header with navigation

4. **resources/views/referrel/commissions_monthly.blade.php** (NEW)
    - Complete monthly summary view
    - Expandable details table
    - Mark paid functionality

## Debugging

### Check Console Logs

Open browser DevTools (F12) → Console tab to see:

-   Fetch requests
-   Response data
-   Any JavaScript errors

### Check Laravel Logs

View `storage/logs/laravel.log` for:

-   Controller method calls
-   Database queries
-   Any exceptions

### Verify Data

-   Check database `referral_commissions` table
-   Verify `status` field updates to 'paid'
-   Check `updated_at` timestamp

## Related Links

-   Individual Commission Records: `/referrals/{id}/commissions`
-   Commission Dashboard: `/commissions/dashboard`
-   Referral List: `/referrels`
