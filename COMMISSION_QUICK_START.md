# Referral Commission System - Quick Start Guide

## ✅ System Status: READY TO USE

Migrations completed successfully. All database changes applied.

---

## 🚀 Quick Start (5 Minutes)

### Step 1: Create a Referral with Commission

```
Path: Referrals → Add Referral Modal
Fields:
  - Full Name: Dr. Smith
  - Email: dr.smith@hospital.com
  - Phone: 9876543210
  - Commission Percentage: 5
Click: Register
```

### Step 2: Create a Patient with Referral

```
Path: Patients → New Patient
Fields:
  - Patient details...
  - Referred By: Dr. Smith (dropdown)
Click: Save
```

### Step 3: Create a Bill

```
Path: Billing → Create Bill
Steps:
  1. Select patient
  2. Add tests
  3. Set amount: ₹10,000
  4. Submit
```

### Step 4: View Commission

```
System automatically:
  ✅ Calculates: ₹10,000 × 5% = ₹500
  ✅ Creates commission record
  ✅ Status: Pending

View at:
  - Commissions Dashboard: /commissions/dashboard
  - Referral Commissions: /referrals/{id}/commissions
```

---

## 📊 Commission Examples

| Bill Amount | Commission % | Commission Amount |
| ----------- | ------------ | ----------------- |
| ₹5,000      | 5%           | ₹250              |
| ₹10,000     | 5%           | ₹500              |
| ₹10,000     | 5.5%         | ₹550              |
| ₹7,500      | 3%           | ₹225              |
| ₹20,000     | 10%          | ₹2,000            |

---

## 🎯 Key Navigation

| What                      | Where                             |
| ------------------------- | --------------------------------- |
| Add/Edit Referral         | `/referrals` page                 |
| View All Commissions      | `/commissions/dashboard`          |
| View Referral Commissions | Click referral → View Commissions |
| Mark Commission Paid      | Commission detail page            |
| Create Bill               | `/billing/create/{patient-id}`    |

---

## 💡 How Commission Works

1. **Referral Setup**
    - Referral created with 5% commission
2. **Patient Referred**
    - Patient linked to referral (Dr. Smith)
3. **Bill Created**
    - Bill for ₹10,000 created for patient
    - System calculates: ₹10,000 × 5% = ₹500
    - Commission record created
4. **Commission Tracking**
    - Status: Pending
    - Can mark as Paid when payment received
    - Dashboard shows all commissions

---

## 📋 Commissioning Scenarios

### Scenario 1: Simple Referral

-   Dr. Smith refers patient
-   Bill: ₹5,000
-   Commission rate: 5%
-   Earned: ₹250

### Scenario 2: Multiple Bills from One Referral

-   Dr. Smith (5% commission)
-   Bill 1: ₹5,000 → ₹250
-   Bill 2: ₹8,000 → ₹400
-   Total Earned: ₹650

### Scenario 3: Different Commission Rates

-   Dr. Smith: 5%
-   Dr. Johnson: 8%
-   Dr. Patel: 10%
-   Each referral tracks own commissions

---

## 🔄 Commission Status Flow

```
Bill Created
    ↓
Commission Calculated (Pending)
    ↓
Commission Tracked in DB
    ↓
View in Dashboard
    ↓
Mark as Paid (Status: Paid)
```

---

## 🎓 Field Reference

### Referral Fields

-   **name** - Referral name
-   **email** - Email address
-   **phone** - Phone number
-   **commission_percentage** - ✨ NEW (0-100%)

### Commission Record Fields

-   **referral_id** - Which referral
-   **bill_id** - Which bill
-   **patient_id** - Which patient
-   **bill_amount** - Original bill amount
-   **commission_percentage** - % applied
-   **commission_amount** - Calculated amount
-   **status** - pending/paid/cancelled

---

## ✨ Features

✅ **Auto Calculation** - No manual entry needed  
✅ **Flexible %** - 0-100%, supports decimals  
✅ **Status Tracking** - Pending → Paid  
✅ **Dashboard** - System-wide view  
✅ **Per-Referral** - Individual tracking  
✅ **Linked Data** - Quick navigation  
✅ **Audit Trail** - All transactions tracked

---

## 🐛 Common Issues & Solutions

### Commission not showing?

```
✅ Check: Patient has "referred_by" filled
✅ Check: Referral name matches exactly
✅ Check: Referral has commission_percentage > 0
✅ Check: Bill was created successfully
```

### Can't find referral in dropdown?

```
✅ Verify referral exists in database
✅ Try: php artisan cache:clear
✅ Refresh browser
```

### Wrong commission amount?

```
Formula: Bill Amount × (Commission % / 100)
Example: ₹10,000 × (5 / 100) = ₹500
Check: Commission percentage is correct
```

---

## 📊 Dashboard Overview

### Commissions Dashboard

Shows:

-   Total commissions earned (all referrals)
-   Pending commissions
-   Paid commissions
-   Total transactions
-   Top 10 referrals by earnings
-   Complete referral list with stats

### Referral Commission View

Shows:

-   Commission summary (earned, pending, paid)
-   Referral details
-   All commission transactions
-   Date, patient, bill, amount, status
-   Mark as Paid options

---

## 🔐 Data Security

-   Commission records linked to referrals
-   Foreign keys ensure data integrity
-   Automatic cascade delete (if referral deleted)
-   Timestamp tracking (created_at, updated_at)
-   Status audit trail

---

## 📞 Support

For issues or questions:

1. Check `COMMISSION_SYSTEM_READY.md` for detailed info
2. Review database logs: `storage/logs/laravel.log`
3. Verify database tables: `referral_commissions` exists

---

## 🎉 You're All Set!

Start using the commission system:

1. Create referrals with commission %
2. Link patients to referrals
3. Create bills
4. Watch commissions calculate automatically
5. Track and manage in the dashboard

**Happy tracking!**
