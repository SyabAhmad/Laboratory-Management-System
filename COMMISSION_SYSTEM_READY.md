# ✅ Referral Commission System - Implementation Complete

**Status**: Ready to Use! Migrations have been successfully applied.

---

## 🎯 Overview

A complete commission-based tracking system has been implemented for referrals. Each referral can now have a commission percentage (0-100%) that is automatically calculated and tracked when bills are created for patients referred by them.

### Key Features:

✅ Commission percentage assigned per referral  
✅ Automatic commission calculation on bill creation  
✅ Commission status tracking (pending/paid)  
✅ Commission dashboard with analytics  
✅ Per-referral commission details view  
✅ Mark commissions as paid  
✅ Linked to referrals, bills, and patients

---

## 📊 Database Structure

### New Columns Added

#### `referrals` table

```sql
commission_percentage DECIMAL(5,2) DEFAULT 0
-- Stores the commission percentage (0-100) for each referral
```

### New Table Created

#### `referral_commissions` table

```sql
CREATE TABLE referral_commissions (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  referral_id BIGINT UNSIGNED,         -- Link to referral
  bill_id BIGINT UNSIGNED,             -- Link to bill
  patient_id BIGINT UNSIGNED,          -- Link to patient
  bill_amount DECIMAL(12,2),           -- Original bill amount
  commission_percentage DECIMAL(5,2),  -- Percentage applied
  commission_amount DECIMAL(12,2),     -- Calculated commission
  status VARCHAR(255) DEFAULT 'pending', -- pending/paid/cancelled
  notes TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

---

## 🔧 How It Works

```
1. Create Referral with Commission %
   ↓
   Dr. Smith (5% commission)
   ↓
2. Create Patient → Referred By: Dr. Smith
   ↓
3. Create Bill for Patient (₹1000)
   ↓
4. System Automatically:
   ✅ Calculates: ₹1000 × 5% = ₹50
   ✅ Creates commission record
   ✅ Status: Pending
   ↓
5. View Commission:
   ✅ Commission Dashboard
   ✅ Referral Commission Details
   ✅ Mark as Paid
```

---

## 📁 Models

### 1. **Referrals** (`app/Models/Referrals.php`)

-   ✅ Added `commission_percentage` field
-   ✅ New relationship: `commissions()`
-   ✅ New attributes:
    -   `total_commission` - Total earned
    -   `pending_commission` - Pending commissions
    -   `paid_commission` - Paid commissions

### 2. **Bills** (`app/Models/Bills.php`)

-   ✅ New relationship: `referralCommission()`
-   ✅ Methods:
    -   `calculateReferralCommission()` - Calculate commission
    -   `getReferralCommissionDetails()` - Get full details

### 3. **ReferralCommission** (`app/Models/ReferralCommission.php`)

-   ✅ Tracks all commissions
-   ✅ Relationships to Referrals, Bills, Patients
-   ✅ Scopes: `pending()`, `paid()`, `forReferral()`
-   ✅ Static methods for statistics

---

## 🎮 Controllers

### ReferralController Updates

-   **store()** - Saves commission percentage when creating referral
-   **update()** - Updates commission percentage when editing referral
-   **commissions($referralId)** - Display commissions for specific referral
-   **markCommissionPaid($commissionId)** - Mark commission as paid
-   **commissionDashboard()** - System-wide commission statistics

### BillsController Updates

-   **store()** - Automatically creates commission when bill is created
-   **createOrUpdateReferralCommission()** - Helper method

---

## 🎨 Views

### Updated Views

1. **add_referral.blade.php** - Commission % field in add form
2. **referrel.blade.php** - Commission % field in add/edit modals

### New Views

1. **commissions.blade.php** - View all commissions for a referral

    - Summary cards (Total, Pending, Paid)
    - Detailed commission table
    - Mark as Paid buttons
    - Pagination support

2. **commission_dashboard.blade.php** - System-wide dashboard
    - Overall statistics
    - Top 10 referrals by commission
    - Complete referral list with stats

---

## 🛣️ Routes

```php
// Commission Routes
GET  /referrals/{referralId}/commissions          → View commissions for referral
POST /referrals/commission/{commissionId}/mark-paid → Mark commission as paid
GET  /commissions/dashboard                       → View all commissions dashboard
```

---

## 📝 Usage Examples

### Example 1: Create Referral with Commission

1. Go to **Referrals** page
2. Click **Add Referral** button
3. Fill in:
    - Name: Dr. Smith
    - Email: dr.smith@hospital.com
    - Phone: 9876543210
    - Commission: 5 (for 5%)
4. Click Register

### Example 2: Bill Creation Triggers Commission

1. Create Patient → Select "Dr. Smith" as Referred By
2. Create Bill → Amount: ₹10,000
3. Commission automatically created:
    - Amount: ₹500 (10,000 × 5%)
    - Status: Pending

### Example 3: View Commission Details

1. Go to **Commissions Dashboard**
    - See total system commissions
    - View top earners
2. Click on referral → **View Commission**
    - See all bills and commissions
    - Mark commissions as paid
    - Track pending vs paid

---

## 🧮 Commission Calculation

```
Formula: Commission Amount = Bill Amount × (Commission Percentage / 100)

Examples:
- Bill: ₹5,000, Commission: 5% → Commission: ₹250
- Bill: ₹10,000, Commission: 5.5% → Commission: ₹550
- Bill: ₹7,500, Commission: 3% → Commission: ₹225
```

---

## ✨ Key Capabilities

| Feature               | Details                                      |
| --------------------- | -------------------------------------------- |
| **Auto Calculation**  | Commission calculated when bill created      |
| **Percentage Ranges** | 0-100% supported, including decimals (5.5%)  |
| **Status Tracking**   | Pending → Paid → Cancelled                   |
| **Per-Referral View** | See all commissions for one referral         |
| **System Dashboard**  | See all commissions system-wide              |
| **Linked Records**    | Quick navigation to bills/patients/referrals |
| **Audit Trail**       | All transactions tracked with timestamps     |
| **Mark as Paid**      | Update commission payment status             |

---

## 🚀 Next Steps (Optional Enhancements)

1. **Commission Reports**

    - Export to PDF/Excel
    - Date range filtering
    - Per-referral reports

2. **Automated Notifications**

    - Email referrals about new commissions
    - Monthly commission summaries

3. **Commission Payouts**

    - Track when commissions were paid out
    - Payout history

4. **Advanced Tiers**

    - Different commission % based on bill amount
    - Volume-based bonuses

5. **Commission Adjustments**
    - Manual adjustments with notes
    - Reversal/cancellation tracking

---

## 🐛 Troubleshooting

### Commission not appearing after bill creation?

✅ Check if patient has "referred_by" field filled  
✅ Check if referral name matches exactly  
✅ Check if referral has commission_percentage > 0  
✅ Check logs: `storage/logs/laravel.log`

### Can't find referral in dropdown?

✅ Verify referral was created successfully  
✅ Verify referral name in database  
✅ Clear cache: `php artisan cache:clear`

### Database migration issues?

✅ Run: `php artisan migrate:refresh`  
✅ Check database connection in `.env`

---

## 📋 Migration Files

✅ **2025_11_04_180111_add_commission_percentage_to_referrals_table.php**

-   Adds commission_percentage column to referrals table

✅ **2025_11_04_180732_create_referral_commissions_table.php**

-   Creates referral_commissions table with all fields

---

## 🔗 File Changes Summary

### Created:

-   `app/Models/ReferralCommission.php`
-   `resources/views/referrel/commissions.blade.php`
-   `resources/views/referrel/commission_dashboard.blade.php`
-   `database/migrations/2025_11_04_180111_add_commission_percentage_to_referrals_table.php`
-   `database/migrations/2025_11_04_180732_create_referral_commissions_table.php`

### Modified:

-   `app/Models/Referrals.php` - Added commission field & relationships
-   `app/Models/Bills.php` - Added commission methods
-   `app/Http/Controllers/ReferralController.php` - Added commission methods
-   `app/Http/Controllers/BillsController.php` - Added auto commission creation
-   `resources/views/referrel/add_referral.blade.php` - Added commission field
-   `resources/views/referrel/referrel.blade.php` - Added commission field to modals
-   `routes/web.php` - Added commission routes

---

## ✅ Status Checklist

-   ✅ Database migrations created and applied
-   ✅ Models updated with commission fields and relationships
-   ✅ Controllers updated to handle commission creation
-   ✅ Views updated with commission percentage fields
-   ✅ Commission dashboard created
-   ✅ Commission details view created
-   ✅ Routes configured
-   ✅ Auto-calculation implemented
-   ✅ Status tracking implemented
-   ✅ Ready for production use

---

## 🎓 Quick Reference

| Action                    | URL                           |
| ------------------------- | ----------------------------- |
| Add Referral              | `/referrals` → Add modal      |
| Edit Referral             | `/referrals` → Edit modal     |
| View All Commissions      | `/commissions/dashboard`      |
| View Referral Commissions | `/referrals/{id}/commissions` |
| Mark Commission Paid      | Commission details page       |

---

**The system is now fully operational and ready to use!**

For questions or issues, check:

-   Model documentation in code comments
-   Controller method documentation
-   Database schema structure

Enjoy tracking referral commissions! 🎉
