# Laboratory Management System (LMS)

**Proprietary Software - All Rights Reserved**

## Overview

A comprehensive laboratory management system built with Laravel, designed for modern clinical laboratory operations including patient management, billing, test reporting, and HL7 integration.

## ⚠️ License & Usage

This software is **PROPRIETARY AND CONFIDENTIAL**.

**UNAUTHORIZED COPYING, MODIFICATION, OR DISTRIBUTION IS STRICTLY PROHIBITED.**

### License Type: Proprietary Commercial

-   **Status**: Restricted Use Only
-   **Copyright**: © 2025 Team MenteE
-   **Permission**: Internal use only - Limited, non-exclusive, non-transferable license

See [LICENSE](./LICENSE) file for complete legal terms.

---

## Features

✅ **Patient Management**

-   Patient registration and demographics
-   Medical history tracking
-   Referral management

✅ **Laboratory Tests**

-   Test registration and templates
-   CBC, HBA1C, and custom test support
-   HL7 MLLP integration with instruments

✅ **Billing & Payments**

-   Automatic bill generation
-   Payment tracking
-   Balance calculation
-   Invoice management

✅ **Test Reports**

-   Professional formatted reports
-   PDF generation and printing
-   HL7 machine integration
-   Digital signatures
-   Reference ranges and interpretation

✅ **Dashboard**

-   Real-time statistics
-   Patient count tracking
-   Revenue monitoring
-   Referral tracking

---

## Technology Stack

-   **Backend**: Laravel 8.x, PHP 8.0+
-   **Database**: MySQL 5.7+
-   **Frontend**: Blade Templates, Bootstrap 4
-   **Integration**: HL7 MLLP Protocol
-   **Python Middleware**: HL7 Data Parser (cbc_mllp_to_db.py)
-   **Security**: ionCube Encoder Ready

---

## Installation (Authorized Users Only)

### Prerequisites

-   PHP 8.0+
-   MySQL 5.7+
-   Composer
-   Node.js & npm

### Setup

```bash
# 1. Install dependencies
composer install
npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Setup database
php artisan migrate
php artisan db:seed

# 4. Start HL7 middleware
cd instrument_middleware
python cbc_mllp_to_db.py

# 5. Run server
php artisan serve
```

---

## Compliance & Security

⚠️ **Healthcare Data Protection**

-   This system processes sensitive patient and medical information
-   Users MUST comply with HIPAA, GDPR, and local healthcare regulations
-   Adequate security measures must be implemented
-   Audit logs and access controls are required
-   Data encryption is mandatory

---

## File Structure

```
app/
├── Http/Controllers/     # API & Web controllers
├── Models/              # Database models
└── Helpers/             # Utility functions

routes/
├── api.php             # API routes
└── web.php             # Web routes

resources/views/
├── Patient/            # Patient management views
├── Bill/               # Billing views
└── dashboard.blade.php # Main dashboard

database/
├── migrations/         # Database schema
└── seeders/           # Sample data

instrument_middleware/  # HL7 MLLP integration
├── cbc_mllp_to_db.py
├── send_sample_hl7.py
└── requirements.txt
```

---

## API Endpoints

### Patients

-   `GET /patients` - List all patients
-   `POST /patients` - Create patient
-   `GET /patients/{id}` - Get patient details
-   `PUT /patients/{id}` - Update patient
-   `DELETE /patients/{id}` - Delete patient

### Billing

-   `GET /bills` - List all bills
-   `POST /bills` - Create bill
-   `GET /bills/{id}` - Get bill details
-   `PUT /bills/{id}` - Update bill

### Test Reports

-   `GET /patients/{patient}/tests/{testName}/print` - Print test report

---

## Configuration

### Environment Variables (.env)

```env
APP_NAME="Laboratory Management System"
APP_DEBUG=false
APP_ENV=production

DB_HOST=127.0.0.1
DB_PORT=3336
DB_DATABASE=nahhah
DB_USERNAME=r88t
DB_PASSWORD=

MLLP_HOST=0.0.0.0
MLLP_PORT=2575
CBC_INSTRUMENT_NAME=M32M
```

### Database Configuration

-   Supports MySQL 5.7+
-   Automatic migrations included
-   Seed data available

---

## Support & Maintenance

For authorized users only:

-   Bug reports to: Syed Syab Ahmad Shah
-   Feature requests email: Syedsyabahmadshah@gmail.com
-   Maintenance email: Syedsyabahmadshah@gmail.com

---

## Disclaimer

THE SOFTWARE IS PROVIDED "AS IS" WITHOUT WARRANTY. THE AUTHOR ASSUMES NO
LIABILITY FOR DATA LOSS, SYSTEM FAILURES, OR IMPROPER USE.

Users are solely responsible for:

-   Data backup and recovery
-   Security implementation
-   Regulatory compliance
-   System monitoring

---

## Legal Notice

**VIOLATION OF THIS LICENSE MAY RESULT IN:**

-   Legal action and prosecution
-   Civil penalties and damages
-   Injunctive relief
-   Recovery of development costs

**DO NOT:**

-   Share source code with unauthorized parties
-   Modify code for redistribution
-   Reverse engineer or decompile
-   Use for competing purposes
-   Remove copyright notices

---

**Last Updated**: October 23, 2025
**Version**: 1.0.0
**License Type**: Proprietary - All Rights Reserved

---

For licensing inquiries or commercial use:
📧 Contact: Syedsyabahmadshah@gmail.com
