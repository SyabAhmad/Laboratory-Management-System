# Commission System - All Views & URLs

## Dashboard & Navigation

### System-Wide Commission Dashboard
**URL:** `/commissions/dashboard`
**Purpose:** Overview of all referrals and their commissions
**Shows:**
- System-wide statistics (Total Earned, Pending, Paid)
- Top referrals by commission
- All referrals summary table

---

## Referral-Specific Views

### Individual Commission Records
**URL:** `/referrals/{referral_id}/commissions`
**Purpose:** View detailed commission records for a specific referral
**Shows:**
- Referral details and commission rate
- Individual commission records with pagination (15 per page)
- Columns: Date, Patient, Bill #, Bill Amount, Commission %, Commission Amount, Status, Action
- Individual "Mark Paid" button per commission
- Navigation to monthly view

**Access Path:**
1. Go to Referral Management → Referrals
2. Click on a referral
3. Click "View Commissions"

---

### Monthly Commission Summary
**URL:** `/referrals/{referral_id}/commissions/monthly`
**Purpose:** View commissions grouped by month with bulk payment options
**Shows:**
- Referral details and commission rate
- Monthly summary table with:
  - Month label (e.g., "November 2025")
  - Total commission for month
  - Pending amount & count
  - Paid amount & count
  - Month status (Pending/Paid/Partial)
  - "Show Invoices" button to expand details
  - "Mark Paid" button for pending months
- Expandable detail sections showing individual commissions per month

**Key Feature:** Mark all pending commissions for a month as paid with one click

**Access Path:**
1. Go to Individual Commission Records (`/referrals/{id}/commissions`)
2. Click "Monthly View" button
3. OR go directly to `/referrals/{id}/commissions/monthly`

---

## Navigation Overview

```
Referrals Menu
├── Referral Management
│   └── Commission Dashboard
│       └── /commissions/dashboard
│
├── Individual Referral View
│   ├── View Commissions (Individual)
│   │   └── /referrals/{id}/commissions
│   │       ├── Button: "Monthly View"
│   │       └── Button: "View Monthly Summary"
│   │
│   └── View Commissions (Monthly)
│       └── /referrals/{id}/commissions/monthly
│           ├── Button: "View Individual Records"
│           └── Button: "Show Invoices" (per month)
```

---

## Quick URL Reference

| View | URL | Method | Purpose |
|------|-----|--------|---------|
| Dashboard | `/commissions/dashboard` | GET | System-wide overview |
| Individual Records | `/referrals/{id}/commissions` | GET | Detailed commission list |
| Monthly Summary | `/referrals/{id}/commissions/monthly` | GET | Monthly grouped view |
| Mark Paid (Individual) | `/referrals/commission/{id}/mark-paid` | POST | Mark single commission paid |
| Mark Paid (Monthly) | `/referrals/{id}/commissions/month/{YYYY-MM}/mark-paid` | POST | Mark all month's commissions paid |

---

## Feature Comparison

### Individual Commission Records
**Best For:**
- Detailed audit trails
- Specific commission verification
- One-by-one payment processing
- Viewing individual bill details

**Advantages:**
- See all details for each commission
- Mark individual commissions as needed
- Link directly to patient profiles
- Link directly to bill records

**Limitations:**
- More time-consuming for bulk payments
- Requires many clicks to mark multiple commissions
- Less organized view for large datasets

---

### Monthly Commission Summary
**Best For:**
- Quick monthly payment processing
- Summary reporting
- Bulk payment operations
- Month-end reconciliation

**Advantages:**
- Fast bulk mark paid (one click per month)
- Clean summary with totals
- Easy to see month status
- Better for financial reporting
- Shows cumulative amounts

**Limitations:**
- Requires expanding to see individual records
- Less granular control
- Invoice details less prominent

---

## Payment Workflow Comparison

### Individual Payment Workflow
```
1. Go to /referrals/{id}/commissions
2. Find commission in list
3. Click "Mark Paid" button
4. Page refreshes
5. Repeat for each commission
```
**Time:** ~30 seconds per commission

---

### Monthly Payment Workflow
```
1. Go to /referrals/{id}/commissions/monthly
2. Find month row
3. Click "Mark Paid" button
4. Confirm in dialog (shows total amount)
5. All pending commissions in month marked at once
```
**Time:** ~10 seconds per month

---

## Statistics & Summary Cards

### Available Statistics

**Total Earned**
- Sum of all commission amounts (pending + paid)
- Across all referrals or per referral

**Pending**
- Sum of commissions with status = 'pending'
- Awaiting payment

**Paid**
- Sum of commissions with status = 'paid'
- Already paid out

**Total Commissions** / **Total Months**
- Count of individual commission records (Individual view)
- Count of months with commissions (Monthly view)

---

## Data Display Examples

### Individual View Record Example
```
Date: 04-11-2025
Patient: Syed Ahmed
Bill #: INV-00123
Bill Amount: PKR 5,000
Commission %: 15%
Commission Amount: PKR 750
Status: Pending
Action: [Mark Paid Button]
```

### Monthly View Summary Example
```
Month: November 2025
Total Commission: PKR 15,750
Pending: PKR 5,250 (2 commissions)
Paid: PKR 10,500 (4 commissions)
Status: Partial
Details: [Show Invoices] [Mark Paid]
```

---

## Recommended Usage Guide

### For Daily Operations
- Use **Individual Commission Records** view
- Quick verification of individual commissions
- Link to patients and bills
- Mark one-off commissions as paid

### For End-of-Month Processing
- Use **Monthly Commission Summary** view
- Quick overview of all pending commissions
- Bulk mark entire months as paid
- Verify monthly totals before payment

### For System Monitoring
- Use **Commission Dashboard**
- Track overall commission statistics
- Monitor referral performance
- Quick health check

### For Reporting
- Use **Monthly Summary** for monthly reports
- Use **Individual Records** for detailed audits
- Use **Dashboard** for management overview

---

## Tips & Best Practices

1. **Always verify amount in confirmation dialog** before marking paid
2. **Use monthly view for faster bulk payments** if processing multiple commissions
3. **Keep individual view open** for quick customer verification
4. **Check dashboard monthly** to ensure payment tracking
5. **Use monthly summary** for reconciliation with accounting

---

## Troubleshooting

### Issue: "Mark Paid" button not working
- Check browser console (F12) for errors
- Verify commission status is "pending"
- Try refreshing page
- Check server logs

### Issue: Monthly total doesn't match
- Expand invoices to see individual amounts
- Verify bill amounts
- Check commission percentage
- Formula: Bill Amount × (Commission % ÷ 100)

### Issue: Commission not appearing
- Check if bill has an associated referral
- Verify referral has commission_percentage set
- Check if commission was auto-created when bill was saved

---

## Related Features

- **Bill Details**: View commission breakdown when viewing bill
- **Patient Profile**: See which referral referred the patient
- **Referral Management**: Edit referral commission percentage
- **Dashboard**: System-wide commission overview
