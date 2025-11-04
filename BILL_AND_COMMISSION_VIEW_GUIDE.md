# 💰 Bill & Commission Data Viewing Guide

Complete guide to viewing all bill data and commission information in the Laboratory Management System.

---

## 📊 Key Pages Overview

### 1. **All Bills Page** - View all lab bills

**URL:** `/billing` (or click Billing → All Bills from sidebar)

**What You See:**

-   Patient ID
-   Patient Name
-   Billing Date
-   Payment Status (Paid/Unpaid)
-   Paid Amount
-   Action button to view details

**Purpose:** Quick overview of all patient bills in the system

---

### 2. **Bill Details Page** - View complete bill with commission

**URL:** `/billing/details/{billId}`

**What You See:**

#### A) Patient Information

-   Patient ID
-   Patient Name
-   Age & Gender
-   Phone Number
-   Lab Information

#### B) Test Details Table

-   All tests in the bill
-   Test prices
-   Total test amount

#### C) **✨ NEW: Referral Commission Section** (Most Important!)

When a patient is referred, this section shows:

-   **Referral Name:** Who referred this patient
-   **Commission Percentage:** The agreed percentage (e.g., 10%)
-   **Commission Amount:** Calculated commission in PKR
    -   Formula: `Bill Amount × (Commission % ÷ 100)`

#### D) Bill Summary

-   Total Amount
-   Discount
-   Net Amount
-   Payment Method
-   Paid Amount
-   Due/Return Amount

**Example Calculation:**

```
Bill Amount: 5,000 PKR
Commission Rate: 15%
Commission Amount = 5,000 × (15 ÷ 100) = 750 PKR ← Lab owes referral this amount
```

---

### 3. **Commission Dashboard** - System-wide commission overview

**URL:** `/commissions/dashboard`

**What You See:**

#### A) Overall Statistics (4 Cards)

```
┌─────────────────────────────────────────────────┐
│  Total Earned    │  Pending      │  Paid       │
│  ₹150,000        │  ₹45,000      │  ₹105,000   │
│  (All-time)      │  (Not yet paid)│(Already paid)│
└─────────────────────────────────────────────────┘
```

#### B) Top 10 Referrals by Commission Table

Shows your best-performing referrals:

-   Referral Name
-   Commission Rate (%)
-   Total Commissions Earned
-   Number of Transactions
-   Email
-   Action: "View Details" button

#### C) All Referrals Overview Table

Complete list of all referrals with:

-   Name
-   Commission Rate
-   **Total Earned:** All commissions from this referral
-   **Pending:** Commissions not yet paid
-   **Paid:** Already paid commissions
-   Number of Commissions
-   Phone
-   Action: "View" button for details

**Purpose:** Track all commission earnings at a glance

---

### 4. **Individual Referral Commission Page** - Detailed commission tracking

**URL:** `/referrals/{referralId}/commissions`

**How to Access:**

1. Go to Commission Dashboard (`/commissions/dashboard`)
2. Find the referral in either table
3. Click "View Details" or "View" button

**What You See:**

#### A) Commission Summary Cards (4 Cards)

-   **Total Earned:** All money earned from this referral
-   **Pending:** Commissions awaiting payment
-   **Paid:** Already compensated commissions
-   **Total Commissions:** Number of transactions

#### B) Referral Information

-   Name
-   Email
-   Phone
-   Commission Rate (%)

#### C) Detailed Commission Table

Shows every commission transaction for this referral:

| Date       | Patient    | Bill #  | Bill Amount | Commission % | Commission Amount | Status     | Action    |
| ---------- | ---------- | ------- | ----------- | ------------ | ----------------- | ---------- | --------- |
| 04-11-2025 | Ahmed Khan | #000001 | ₹5,000      | 15%          | ₹750              | ⚠️ Pending | Mark Paid |
| 03-11-2025 | Fatima Ali | #000002 | ₹3,500      | 15%          | ₹525              | ✅ Paid    | -         |

**Features:**

-   Click on Patient Name → View patient details
-   Click on Bill # → View full bill details
-   Click "Mark Paid" → Mark commission as paid (for pending only)
-   Status shows: Pending (Yellow) / Paid (Green) / Cancelled (Red)

**Purpose:** Track every commission for a specific referral

---

## 🚀 How Commission Data Flows

### Commission Creation Process:

```
1. Patient created with "Referred By" field
   ↓
2. Bill created for referred patient
   ↓
3. System auto-calculates commission:
   Commission = Bill Amount × (Referral Commission % ÷ 100)
   ↓
4. Commission record created with:
   - Status: "Pending"
   - Amount: Calculated value
   - Bill ID, Patient ID, Referral ID linked
   ↓
5. Visible in:
   - Bill Details Page (commission section)
   - Commission Dashboard
   - Individual Referral Commission Page
```

### Marking Commission as Paid:

```
1. Go to Individual Referral Commission Page
   ↓
2. Find commission row with status "Pending"
   ↓
3. Click "Mark Paid" button
   ↓
4. Status changes: Pending → Paid
   ↓
5. Amount moves from "Pending" to "Paid" totals
```

---

## 📈 Viewing Bill Money for Lab and Referral

### **For Laboratory (Revenue)**

Go to **All Bills** → See total paid amounts per patient

-   This is the lab's revenue from tests

### **For Referral Commissions (Owed)**

1. Go to **Commission Dashboard**
2. Look for your referral in the "All Referrals" table
3. Three key columns show:
    - **Total Earned:** Lab paid this much in commissions
    - **Pending:** Still owes this amount
    - **Paid:** Already compensated this amount

### Example Scenario:

```
Patient: John Doe
Bill Amount: 10,000 PKR
Referral: Dr. Smith (Commission Rate: 20%)

Lab Revenue: 10,000 PKR
Dr. Smith Commission: 10,000 × (20 ÷ 100) = 2,000 PKR

Before Payment:
- Lab Net Revenue: 10,000 - 2,000 = 8,000 PKR
- Dr. Smith Pending: 2,000 PKR

After Payment:
- Lab Net Revenue: 8,000 PKR (unchanged)
- Dr. Smith Paid: 2,000 PKR
```

---

## 🎯 Quick Access Guide

### View All Bills

```
Sidebar → Billing System → All Bills
or direct: /billing
```

### View Specific Bill with Commission

```
All Bills → Click 👁️ (Eye icon) on any bill
or direct: /billing/details/{billId}
```

### View All Commissions

```
Sidebar → Referral Management → Commission Dashboard
or direct: /commissions/dashboard
```

### View Commissions for One Referral

```
Commission Dashboard → Find referral → Click "View Details/View"
or direct: /referrals/{referralId}/commissions
```

---

## 📊 Key Metrics to Track

### For Lab Management:

-   **Total Bill Amount:** Revenue source
-   **Paid Amount:** Cash received
-   **Due Amount:** Outstanding payments

### For Referral Management:

-   **Total Commission Earned:** Total owed to referrals
-   **Pending Commissions:** Money still owed
-   **Paid Commissions:** Already compensated

### Formula Reference:

```
Bill Amount: The total cost of tests
Commission Percentage: Agreed rate per referral (0-100%)
Commission Amount = Bill Amount × (Commission % ÷ 100)

Lab keeps: Bill Amount - Commission Amount
Referral gets: Commission Amount
```

---

## 💡 Features & Actions Available

### On Bill Details Page:

-   ✅ View referral commission section
-   ✅ Edit bill payment details (Payment button)
-   ✅ Mark bill as paid
-   ✅ Print bill

### On Commission Dashboard:

-   ✅ View system-wide statistics
-   ✅ See top performing referrals
-   ✅ View all referral commissions
-   ✅ Click through to individual referral details

### On Individual Referral Commission Page:

-   ✅ View all transactions for this referral
-   ✅ See pending vs paid commissions
-   ✅ Mark commissions as paid
-   ✅ View patient details
-   ✅ View full bill details

---

## 🔍 Troubleshooting

### Q: Commission not showing on bill details?

**A:**

-   Check if patient has "Referred By" field filled
-   Check if referral exists in the system with commission percentage > 0
-   Make sure bill has been saved

### Q: Commission Dashboard shows no data?

**A:**

-   No commissions created yet
-   Check if any bills exist for referred patients
-   Try creating a test bill for a referred patient first

### Q: How to verify commission calculation?

**A:**

1. Note the Bill Amount and Referral Commission %
2. Calculate: `Bill Amount × (Commission % ÷ 100)`
3. Check against displayed Commission Amount
4. Should match exactly

### Q: Want to see bill history for a referral?

**A:**
Go to Individual Referral Commission Page → View all bills in the Commission Details table

---

## 📌 Summary Table

| What to View                | Where to Go                      | URL                           |
| --------------------------- | -------------------------------- | ----------------------------- |
| All lab bills               | Billing → All Bills              | `/billing`                    |
| Single bill with commission | All Bills → Click eye icon       | `/billing/details/{id}`       |
| System commission overview  | Referral → Commission Dashboard  | `/commissions/dashboard`      |
| One referral's commissions  | Dashboard → Click "View Details" | `/referrals/{id}/commissions` |
| Referral details            | Referral Management → List       | `/referrels/list`             |

---

## 🎓 Best Practices

1. **Regular Commission Review:**

    - Check Commission Dashboard weekly
    - Mark commissions as paid promptly
    - Keep records updated

2. **Bill Creation:**

    - Ensure referral is properly recorded when creating patient
    - Verify commission % is set for referral
    - Bill commission auto-calculates on save

3. **Payment Tracking:**
    - Use "Mark Paid" button after paying referral
    - Keep Pending commissions list updated
    - Review Paid list monthly for audit trail

---

## ✨ New Features in This Update

✅ **Commission Display in Bill Details**

-   Shows referral name, rate, and amount on every bill

✅ **Referral Info on Bill Creation Form**

-   Shows which referral the patient comes from
-   Displays commission rate
-   Reminds about auto-calculation

✅ **Cleaner Bill Form**

-   Removed "Add Additional Tests" section
-   Focuses on pre-registered tests only
-   Easier to use

---

**Last Updated:** November 4, 2025  
**System:** Laboratory Management System v1.0  
**Feature:** Referral Commission System
