# Referral Commission System - Technical Architecture

## System Overview

```
┌─────────────────────────────────────────────────────────────┐
│                 REFERRAL COMMISSION SYSTEM                   │
└─────────────────────────────────────────────────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
    ┌───▼───┐          ┌───▼────┐         ┌───▼──┐
    │Models │          │Routes  │         │Views │
    └───┬───┘          └───┬────┘         └───┬──┘
        │                  │                   │
    ┌───▼──────────────────▼───────────────────▼───┐
    │         COMMISSION PROCESSING LAYER          │
    └───┬──────────────────┬───────────────────────┘
        │                  │
    ┌───▼────┐        ┌───▼─────┐
    │Referral│        │ Bill    │
    │CommCtrl │        │ CommCtrl│
    └────┬────┘        └──┬──────┘
         │                │
         └────┬───────────┘
              │
    ┌─────────▼──────────────┐
    │   DATABASE LAYER       │
    │  ┌────────────────┐    │
    │  │ referral_      │    │
    │  │ commissions ◄──┼────┼─ Commission Records
    │  ├────────────────┤    │
    │  │ referrals      │    │
    │  │ (+ commission_ │    │
    │  │  percentage)   │    │
    │  └────────────────┘    │
    └────────────────────────┘
```

---

## Data Flow Diagram

```
USER CREATES REFERRAL
        │
        ├─ Input: Name, Email, Phone, Commission%
        │
        ▼
    ReferralController@store()
        │
        ├─ Validate input
        ├─ Save to referrals table
        └─ commission_percentage = input value

        ▼
USER CREATES PATIENT
        │
        ├─ Input: Patient details, Referred By
        │
        ▼
    PatientsController@store()
        │
        ├─ Set patient.referred_by = "Dr. Smith"
        │
        ▼
USER CREATES BILL
        │
        ├─ Input: Patient, Tests, Amount
        │
        ▼
    BillsController@store()
        │
        ├─ Save bill to bills table
        │
        ├─ Call: createOrUpdateReferralCommission()
        │
        ▼
    COMMISSION CALCULATION
        │
        ├─ Find referral by patient.referred_by
        │
        ├─ Get commission_percentage from referrals
        │
        ├─ Calculate:
        │   commission_amount = bill_amount × (commission_% / 100)
        │
        ├─ Create record in referral_commissions:
        │   {
        │     referral_id: 1,
        │     bill_id: 42,
        │     patient_id: 10,
        │     bill_amount: 10000,
        │     commission_percentage: 5,
        │     commission_amount: 500,
        │     status: 'pending'
        │   }
        │
        ▼
USER VIEWS COMMISSION
        │
        ├─ Path: /commissions/dashboard
        │    or: /referrals/{id}/commissions
        │
        ▼
    ReferralController@commissions()
        │
        ├─ Query referral_commissions table
        ├─ Group by status (pending/paid)
        ├─ Calculate totals
        │
        ▼
    Display commission details with stats

```

---

## Database Schema

### referrals table

```sql
+------------------------+
|      referrals         |
+------------------------+
| id (PK)               |
| name                  |
| email                 |
| phone                 |
| commission_percentage | ← NEW
| created_at            |
| updated_at            |
+------------------------+
```

### referral_commissions table

```sql
+---------------------------+
|  referral_commissions    |
+---------------------------+
| id (PK)                 |
| referral_id (FK)        |
| bill_id (FK)            |
| patient_id (FK)         |
| bill_amount             |
| commission_percentage   |
| commission_amount       |
| status                  |
| notes                   |
| created_at              |
| updated_at              |
+---------------------------+

Indexes:
  - referral_id
  - bill_id
  - patient_id
  - status
  - created_at
```

### Relationships

```
referrals (1)
    │
    ├─────► (M) referral_commissions
    │

bills (1)
    │
    ├─────► (1) referral_commissions
    │

patients (1)
    │
    ├─────► (M) referral_commissions
```

---

## Model Architecture

### Referrals Model

```php
class Referrals extends Model {
    // Fields
    protected $fillable = [
        'name',
        'email',
        'phone',
        'commission_percentage'  // NEW
    ];

    // Relationships
    public function commissions() {
        return $this->hasMany(ReferralCommission::class);
    }

    // Accessors
    public function getTotalCommissionAttribute() { ... }
    public function getPendingCommissionAttribute() { ... }
    public function getPaidCommissionAttribute() { ... }
}
```

### Bills Model

```php
class Bills extends Model {
    // New Relationship
    public function referralCommission() {
        return $this->hasOne(ReferralCommission::class);
    }

    // New Methods
    public function calculateReferralCommission() { ... }
    public function getReferralCommissionDetails() { ... }
}
```

### ReferralCommission Model (NEW)

```php
class ReferralCommission extends Model {
    protected $table = 'referral_commissions';

    protected $fillable = [
        'referral_id',
        'bill_id',
        'patient_id',
        'bill_amount',
        'commission_percentage',
        'commission_amount',
        'status',
        'notes'
    ];

    // Relationships
    public function referral() { ... }
    public function bill() { ... }
    public function patient() { ... }

    // Scopes
    public function scopePending($query) { ... }
    public function scopePaid($query) { ... }
    public function scopeForReferral($query, $id) { ... }

    // Statistics
    public static function getTotalCommissionForReferral($id, $status) { ... }
    public static function getCommissionStats($id) { ... }
}
```

---

## Controller Methods

### ReferralController

#### store()

-   **Purpose**: Create referral with commission
-   **Input**: name, email, phone, commission_percentage
-   **Output**: JSON response with referral data

#### update()

-   **Purpose**: Update referral including commission %
-   **Input**: id, name1, email1, phone1, commission_percentage1
-   **Output**: JSON response

#### createCommissionFromBill(Bills $bill)

-   **Purpose**: Calculate and create commission for a bill
-   **Called**: From BillsController@store()
-   **Logic**:
    ```
    1. Get patient's referred_by name
    2. Find referral by name
    3. Get commission_percentage
    4. Calculate: bill_amount × (commission_% / 100)
    5. Create/update referral_commissions record
    ```

#### commissions($referralId)

-   **Purpose**: Display all commissions for a referral
-   **View**: referrel.commissions
-   **Data**: Commission records with pagination

#### markCommissionPaid($commissionId)

-   **Purpose**: Update commission status to paid
-   **Response**: JSON with success/error

#### commissionDashboard()

-   **Purpose**: System-wide commission statistics
-   **View**: referrel.commission_dashboard
-   **Data**: All referrals, top earners, statistics

### BillsController

#### store()

-   **Modified**: Calls createOrUpdateReferralCommission()
-   **Flow**:
    ```
    1. Validate bill data
    2. Save bill to database
    3. Create commission record via ReferralController
    4. Return success response
    ```

---

## Route Map

```
/referrals                              → ReferralController@index
/referrals/create                       → ReferralController@create
/referrals/add [POST]                   → ReferralController@store
/referrals/edit/{id}                    → ReferralController@edit
/referrals/update [PUT]                 → ReferralController@update
/referrals/{id} [DELETE]                → ReferralController@destroy

NEW COMMISSION ROUTES:
/referrals/{referralId}/commissions     → ReferralController@commissions
/referrals/commission/{id}/mark-paid    → ReferralController@markCommissionPaid
/commissions/dashboard                  → ReferralController@commissionDashboard
```

---

## View Architecture

### Form: add_referral.blade.php

```html
<form action="/referrals/add" method="POST">
    <input name="name" required />
    <input name="email" required />
    <input name="phone" />
    <input name="commission_percentage" min="0" max="100" required /> ← NEW
    <button>Register</button>
</form>
```

### Form: referrel.blade.php (modals)

```html
<!-- Add Modal -->
<input name="commission_percentage" min="0" max="100" /> ← NEW

<!-- Edit Modal -->
<input name="commission_percentage1" min="0" max="100" /> ← NEW
```

### View: commissions.blade.php

```
├─ Summary Cards
│  ├─ Total Earned
│  ├─ Pending
│  ├─ Paid
│  └─ Count
├─ Referral Info
└─ Commission Table
   ├─ Date
   ├─ Patient
   ├─ Bill
   ├─ Amount
   ├─ Commission %
   ├─ Commission Amount
   ├─ Status
   └─ Action (Mark as Paid)
```

### View: commission_dashboard.blade.php

```
├─ Overall Statistics
│  ├─ Total Earned
│  ├─ Pending
│  ├─ Paid
│  └─ Transactions
├─ Top 10 Referrals
│  └─ Earnings, Rate, Count
└─ All Referrals List
   └─ Name, Rate, Totals, Stats
```

---

## Commission Calculation Engine

### Algorithm

```
FUNCTION CalculateCommission(bill_id):
    bill = Bill.find(bill_id)
    patient = bill.patient

    IF patient.referred_by IS NULL:
        RETURN None

    referral = Referral.where('name', patient.referred_by).first()

    IF referral IS NULL OR referral.commission_percentage <= 0:
        RETURN None

    bill_amount = bill.total_price ?? bill.amount
    commission_amount = bill_amount × (referral.commission_percentage / 100)

    commission_record = {
        referral_id: referral.id,
        bill_id: bill.id,
        patient_id: patient.id,
        bill_amount: bill_amount,
        commission_percentage: referral.commission_percentage,
        commission_amount: commission_amount,
        status: 'pending'
    }

    RETURN ReferralCommission.create(commission_record)
```

---

## Data Validation

### Referral Commission Percentage

-   **Type**: Decimal(5, 2)
-   **Range**: 0 - 100
-   **Examples**: 5, 5.50, 10.25
-   **Validation**: min="0", max="100", step="0.01"

### Commission Amount

-   **Type**: Decimal(12, 2)
-   **Calculation**: bill_amount × (commission_percentage / 100)
-   **Example**: 10000 × (5/100) = 500

### Status

-   **Type**: String
-   **Values**: pending, paid, cancelled
-   **Default**: pending

---

## Performance Considerations

### Indexes

```sql
CREATE INDEX idx_referral_id ON referral_commissions(referral_id);
CREATE INDEX idx_bill_id ON referral_commissions(bill_id);
CREATE INDEX idx_patient_id ON referral_commissions(patient_id);
CREATE INDEX idx_status ON referral_commissions(status);
CREATE INDEX idx_created_at ON referral_commissions(created_at);
```

### Query Optimization

-   Use eager loading: `with(['patient', 'referral', 'bill'])`
-   Paginate large commission lists
-   Cache dashboard statistics

---

## Error Handling

### Bill Creation Fails

-   Commission creation wrapped in try-catch
-   Logged to: `storage/logs/laravel.log`
-   Bill still saves, commission skipped

### Referral Not Found

-   Check if patient.referred_by matches referral.name
-   Log error with details

### Commission Already Exists

-   Check if commission exists for bill_id
-   Update existing instead of creating duplicate

---

## Migration Timeline

```
2025-11-04 18:01:11 → Add commission_percentage to referrals
2025-11-04 18:07:32 → Create referral_commissions table

Both migrations successfully applied ✓
```

---

## API Response Examples

### Create Commission (Auto)

```json
{
    "success": true,
    "message": "Bill saved successfully.",
    "commission": {
        "id": 42,
        "referral_id": 5,
        "bill_id": 100,
        "commission_amount": 500,
        "status": "pending"
    }
}
```

### Get Commission Stats

```json
{
    "total_earned": 15000,
    "pending": 5000,
    "paid": 10000,
    "count": 30
}
```

---

## Testing Checklist

-   [ ] Create referral with commission %
-   [ ] Edit referral commission %
-   [ ] Create patient with referral
-   [ ] Create bill for patient
-   [ ] Verify commission calculated correctly
-   [ ] View commission in dashboard
-   [ ] Mark commission as paid
-   [ ] Verify status updated
-   [ ] Test with different commission percentages
-   [ ] Test with decimal percentages (5.50%)
-   [ ] Verify referral without commission (0%)
-   [ ] Test with multiple referrals
-   [ ] Test pagination
-   [ ] Test linked records navigation

---

**This architecture ensures:**
✅ Data integrity with foreign keys  
✅ Performance with proper indexing  
✅ Scalability with proper design  
✅ Maintainability with clear structure  
✅ Extensibility for future enhancements
