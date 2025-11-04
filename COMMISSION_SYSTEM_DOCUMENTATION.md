# Referral Commission System - Implementation Complete

## Overview

A comprehensive commission tracking system has been implemented for referrals based on tests. Each referral now has a commission percentage that is applied to bills created for patients referred by them.

---

## Database Changes

### 1. Migrations Created

#### `2025_11_04_add_commission_to_referrals_table.php`

-   Adds `commission_percentage` decimal field to the `referrals` table
-   Stores the commission percentage (0-100) for each referral

#### `2025_11_04_create_referral_commissions_table.php`

-   New table: `referral_commissions`
-   Tracks all commissions earned from referrals
-   Fields:
    -   `referral_id` - Link to referral
    -   `bill_id` - Link to bill
    -   `patient_id` - Link to patient
    -   `bill_amount` - Original bill amount
    -   `commission_percentage` - Percentage applied
    -   `commission_amount` - Calculated commission
    -   `status` - pending/paid/cancelled
    -   `notes` - Commission details

---

## Models Updated

### 1. **Referrals Model** (`app/Models/Referrals.php`)

-   Added `commission_percentage` field to `$fillable`
-   New relationship: `commissions()` - Get all commissions for this referral
-   New attributes:
    -   `total_commission` - Total earned
    -   `pending_commission` - Pending commissions
    -   `paid_commission` - Paid commissions

### 2. **Bills Model** (`app/Models/Bills.php`)

-   New relationship: `referralCommission()` - Get commission for this bill
-   New methods:
    -   `calculateReferralCommission()` - Calculate commission amount
    -   `getReferralCommissionDetails()` - Get full commission info

### 3. **ReferralCommission Model** (`app/Models/ReferralCommission.php`)

-   New model for tracking commissions
-   Relationships to Referrals, Bills, and Patients
-   Scopes: `pending()`, `paid()`, `forReferral()`
-   Static methods for statistics

---

## Controller Updates

### 1. **ReferralController** (`app/Http/Controllers/ReferralController.php`)

#### Updated Methods:

-   `store()` - Now accepts and saves `commission_percentage`
-   `update()` - Updates commission percentage when editing referral

#### New Methods:

-   `createCommissionFromBill(Bills $bill)` - Creates commission records when bills are created
-   `commissions($referralId)` - Display all commissions for a referral with pagination
-   `markCommissionPaid($commissionId)` - Mark a commission as paid
-   `commissionDashboard()` - Dashboard showing all commission statistics and top referrals

### 2. **BillsController** (`app/Http/Controllers/BillsController.php`)

-   Enhanced `store()` method to automatically create commission records
-   New private method: `createOrUpdateReferralCommission(Bills $bill)`
-   Automatically called when bills are created/updated
-   Handles both new commission creation and updates

---

## Routes Added (`routes/web.php`)

```php
// Commission-specific routes
Route::get('/referrals/{referralId}/commissions', 'ReferralController@commissions')
    ->name('referrals.commissions');

Route::post('/referrals/commission/{commissionId}/mark-paid', 'ReferralController@markCommissionPaid')
    ->name('referrals.mark-commission-paid');

Route::get('/commissions/dashboard', 'ReferralController@commissionDashboard')
    ->name('commissions.dashboard');
```

---

## Views Updated/Created

### 1. **Updated: `add_referral.blade.php`**

-   Added commission percentage input field
-   Accepts values from 0-100
-   Marked as required field

### 2. **Updated: `referrel.blade.php`**

-   Added commission percentage field to add modal
-   Added commission percentage field to edit modal
-   Both fields support decimal values (e.g., 5.50%)

### 3. **Created: `commissions.blade.php`**

-   Displays all commissions for a specific referral
-   Summary cards showing:
    -   Total earned
    -   Pending commissions
    -   Paid commissions
    -   Total commission count
-   Detailed table with:
    -   Commission date
    -   Patient name (linked)
    -   Bill number (linked)
    -   Bill amount
    -   Commission percentage
    -   Commission amount
    -   Status badge
    -   "Mark as Paid" button for pending commissions
-   Pagination support

### 4. **Created: `commission_dashboard.blade.php`**

-   Overall system commission statistics
-   Top 10 referrals by total commission earned
-   Complete list of all referrals with:
    -   Commission rate
    -   Total earned
    -   Pending/Paid breakdown
    -   Number of transactions
    -   Quick links to view details

---

## How It Works

### Flow Diagram:

```
1. Referral Created/Updated
   ↓
   Commission % set (0-100)
   ↓
2. Patient Referred by this Referral
   ↓
3. Bill Created for Patient
   ↓
4. System Automatically:
   - Calculates: Bill Amount × (Commission % / 100)
   - Creates ReferralCommission record
   - Stores: commission_amount, commission_percentage, status=pending
   ↓
5. Commission Tracking:
   - View commission details per referral
   - Mark commissions as paid
   - Generate reports
   - Dashboard statistics
```

### Example:

-   Referral "Dr. Smith" has 5% commission
-   Bill created: ₹10,000 for patient referred by Dr. Smith
-   Commission calculated: ₹10,000 × 5% = ₹500
-   Status: Pending (can be marked as paid later)
-   Dr. Smith's total commission increases

---

## Key Features

✅ **Automatic Calculation** - Commissions calculated automatically when bills are created
✅ **Percentage-Based** - Flexible commission percentage (0-100%)
✅ **Status Tracking** - Track pending and paid commissions
✅ **Commission Dashboard** - View all commissions system-wide
✅ **Per-Referral Stats** - View commissions for each referral
✅ **Mark as Paid** - Update commission status when payments made
✅ **Audit Trail** - All commission transactions tracked
✅ **Linked Records** - Quick navigation to related bills/patients/referrals

---

## Next Steps (After Migration)

1. **Run Migrations**:

    ```bash
    php artisan migrate
    ```

2. **Test the System**:

    - Add a referral with a commission percentage (e.g., 5%)
    - Create a patient referred by this referral
    - Create a bill for the patient
    - Check if commission appears in the referral's commissions view

3. **Access New Views**:
    - Commission Dashboard: `/commissions/dashboard`
    - Referral Commissions: `/referrals/{referralId}/commissions`
    - Edit Referral: Modal in `/referrals` page

---

## Files Modified/Created

### Created:

-   `database/migrations/2025_11_04_add_commission_to_referrals_table.php`
-   `database/migrations/2025_11_04_create_referral_commissions_table.php`
-   `app/Models/ReferralCommission.php`
-   `resources/views/referrel/commissions.blade.php`
-   `resources/views/referrel/commission_dashboard.blade.php`

### Modified:

-   `app/Models/Referrals.php`
-   `app/Models/Bills.php`
-   `app/Http/Controllers/ReferralController.php`
-   `app/Http/Controllers/BillsController.php`
-   `resources/views/referrel/add_referral.blade.php`
-   `resources/views/referrel/referrel.blade.php`
-   `routes/web.php`

---

## Validation & Error Handling

-   Commission percentage validated: 0-100
-   Null checks for referrals without commission
-   Automatic bill-to-commission linking
-   Graceful handling of missing data
-   Transaction logging for auditing

---

## Database Schema Reference

### referral_commissions table structure:

```sql
CREATE TABLE referral_commissions (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  referral_id BIGINT UNSIGNED,
  bill_id BIGINT UNSIGNED,
  patient_id BIGINT UNSIGNED,
  bill_amount DECIMAL(12,2),
  commission_percentage DECIMAL(5,2),
  commission_amount DECIMAL(12,2),
  status VARCHAR(255) DEFAULT 'pending',
  notes TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (referral_id) REFERENCES referrals(id),
  FOREIGN KEY (bill_id) REFERENCES bills(id),
  FOREIGN KEY (patient_id) REFERENCES patients(id),
  INDEX (referral_id),
  INDEX (bill_id),
  INDEX (status)
);
```

---

## Notes

-   Commission is calculated when bill is created, not when payment is received
-   Status tracks commission payment status separately from bill payment
-   Commission percentage can be updated anytime; future commissions use new percentage
-   Past commissions retain original percentage for accuracy
-   Supports partial commissions (decimal percentages like 5.50%)

---

Ready to use! Run `php artisan migrate` to apply the database changes.
