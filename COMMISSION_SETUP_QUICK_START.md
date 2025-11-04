# Quick Setup Guide - Referral Commission System

## Step 1: Run Migrations

```bash
php artisan migrate
```

This will:

-   Add `commission_percentage` column to `referrals` table
-   Create `referral_commissions` table for tracking

## Step 2: Test the System

### Create a Referral with Commission

1. Go to Referrals page
2. Click "Add Referral" button
3. Fill in details:
    - **Full Name**: Dr. Smith (example)
    - **Email**: dr.smith@example.com
    - **Phone**: 9876543210
    - **Commission Percentage**: 5 (for 5% commission)
4. Click "Register"

### Create a Patient Referred by This Referral

1. Go to Patients → New Patient
2. Fill patient details
3. In "Referred By" field, select: **Dr. Smith** (the referral just created)
4. Complete and save

### Create a Bill for the Patient

1. Go to Billing → Create Bill
2. Select the patient you just created
3. Add tests (e.g., CBC, Pathology tests)
4. Set bill amount: ₹1000 (example)
5. Submit the bill

### View the Commission

1. Go to **Commissions Dashboard** (new menu item)

    - Or navigate to: `/commissions/dashboard`

2. Or go to **Referrals** → Click on referral → **View Commissions**
    - Or navigate to: `/referrals/{id}/commissions`

### Expected Result

-   A commission record should appear showing:
    -   **Bill Amount**: ₹1000
    -   **Commission %**: 5%
    -   **Commission Amount**: ₹50 (calculated as 1000 × 5%)
    -   **Status**: Pending

## Step 3: Mark Commission as Paid

1. In the commissions table, find the commission record
2. Click "Mark Paid" button
3. Status will change from "Pending" to "Paid"

## Navigation Guide

| Feature                    | URL/Location                                 |
| -------------------------- | -------------------------------------------- |
| Commissions Dashboard      | `/commissions/dashboard`                     |
| Referral Commissions       | `/referrals/{id}/commissions`                |
| Edit Referral Commission % | Referrals page → Click referral → Edit modal |
| Add Referral               | Referrals page → Add modal                   |

## Database Schema

### referrals table (updated)

-   `id` - Primary Key
-   `name` - Referral name
-   `email` - Email
-   `phone` - Phone
-   **`commission_percentage`** - ✨ NEW (0-100)
-   `created_at`, `updated_at`

### referral_commissions table (new)

-   `id` - Primary Key
-   `referral_id` - Foreign Key to referrals
-   `bill_id` - Foreign Key to bills
-   `patient_id` - Foreign Key to patients
-   `bill_amount` - Amount of bill
-   `commission_percentage` - Applied percentage
-   `commission_amount` - Calculated commission
-   `status` - pending/paid/cancelled
-   `notes` - Details
-   `created_at`, `updated_at`

## Key Points

✅ Commission is **automatic** - no manual entry needed
✅ Commission is calculated when bill is **created**
✅ Commission amount = Bill Amount × (Commission % / 100)
✅ Supports **decimal percentages** (e.g., 5.50%)
✅ **Track** pending and paid commissions separately
✅ **Per-referral** commission dashboard
✅ **System-wide** commission dashboard

## Examples

### Example 1: Simple Commission

-   Bill: ₹10,000
-   Commission %: 5%
-   Commission Amount: ₹500 (10,000 × 0.05)

### Example 2: Decimal Commission

-   Bill: ₹10,000
-   Commission %: 5.5%
-   Commission Amount: ₹550 (10,000 × 0.055)

### Example 3: Multiple Bills from One Referral

-   Dr. Smith (5% commission)
-   Bill 1: ₹5,000 → Commission: ₹250
-   Bill 2: ₹8,000 → Commission: ₹400
-   Total Earned: ₹650
-   Status: Shows both pending and paid

## Troubleshooting

### Commission not appearing after creating bill?

1. Check if patient has "referred_by" field filled
2. Check if referral name matches exactly
3. Check if referral has commission_percentage > 0
4. Check logs: `storage/logs/laravel.log`

### Migration errors?

1. Ensure database is accessible: `php artisan migrate --path=database/migrations`
2. Check for SQL syntax errors in logs

### Referral not showing in "Referred By" dropdown?

1. Verify referral was created successfully
2. Verify referral name in database
3. Clear cache: `php artisan cache:clear`

## Next Steps (Optional)

1. **Generate Commission Reports** - Add export to PDF/Excel
2. **Commission Payout History** - Track when commissions were paid out
3. **Automated Commission Notifications** - Email referrals about earnings
4. **Commission Adjustments** - Manual commission modifications with notes
5. **Commission Tier System** - Different percentages based on bill amount

---

For detailed information, see: `COMMISSION_SYSTEM_DOCUMENTATION.md`
