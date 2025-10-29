# Laboratory Management System

A comprehensive web-based application designed to streamline laboratory operations, including patient management, test reporting, billing, inventory tracking, and more. Built with Laravel, this system supports diagnostic centers in managing daily activities efficiently.

## Features

- **User Management**: Create users, assign roles and permissions (e.g., admin, employees, patients).
- **Employee Management**: Add, update, and track laboratory staff information and daily attendance.
- **Patient Management**: Register patients, manage test data, view profiles, and generate printable reports.
- **Referral System**: Manage referrals and associated patient data.
- **Lab Test Categories and Parameters**: Define test categories, add parameters, and manage test configurations (e.g., CBC parameters).
- **Billing and Payments**: Create bills, track payments, mark bills as paid, and view balance overviews.
- **Test Reporting**: Generate and manage reports for pathology, radiology, ultrasonography, and electrocardiography.
- **Inventory Management**: Track inventories, stock levels, and history.
- **Daily Activities and Attendance**: Record and monitor daily activities and employee attendance.
- **Dashboard**: Overview with charts for billed and paid amounts over time.
- **Patient Portal**: Patients can view their test reports online and in printable format.
- **DataTables Integration**: Server-side data tables for efficient data handling.
- **Instrument Middleware**: Support for HL7 integration with lab instruments (e.g., CBC data parsing).
- **Reporting**: Generate ledgers, referral reports, and other analytics.

## Technologies Used

- **Backend**: Laravel 8.x (PHP Framework)
- **Frontend**: Blade Templates, Bootstrap, jQuery, Ajax
- **Database**: MySQL (Relational Database)
- **Additional Libraries**:
  - Yajra Laravel DataTables for data tables
  - mPDF for PDF generation
  - Twilio SDK for SMS integration
  - Laravel Sanctum for API authentication
  - Laravel UI for authentication scaffolding
- **Other Tools**: Composer for dependency management, NPM for frontend assets, PHPUnit for testing

## Prerequisites

- PHP 7.3 or 8.0+
- Composer
- Node.js and NPM (for frontend assets)
- MySQL or compatible database
- Web server (e.g., Apache or Nginx)

## Installation

1. **Clone the Repository**:

   ```bash
   git clone https://github.com/your-username/laboratory-management-system.git
   cd laboratory-management-system
   ```

2. **Install PHP Dependencies**:

   ```bash
   composer install
   ```

3. **Install Node.js Dependencies**:

   ```bash
   npm install
   ```

4. **Environment Setup**:

   - Copy the `.env.example` file to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Update the `.env` file with your database credentials and other settings (e.g., APP_NAME, DB_CONNECTION, etc.).

5. **Generate Application Key**:

   ```bash
   php artisan key:generate
   ```

6. **Run Migrations** (Note: Some migrations may require payment for full database setup as per the original note):

   ```bash
   php artisan migrate
   ```

7. **Seed the Database** (if applicable):

   ```bash
   php artisan db:seed
   ```

8. **Build Frontend Assets**:

   ```bash
   npm run dev
   # or for production
   npm run prod
   ```

9. **Start the Server**:

   ```bash
   php artisan serve
   ```

10. **Access the Application**:
    - Open your browser and go to `http://127.0.0.1:8000/`
    - Log in with admin credentials or sign up as an admin user.

## Usage

- **Login**: Use admin or user credentials to access the system.
- **Dashboard**: View overview, charts, and navigate to different sections.
- **Manage Entities**: Use the sidebar to access Users, Employees, Patients, Referrals, Lab Tests, Billing, etc.
- **Reports**: Generate and view reports from the Report Generation section.
- **Patient Portal**: Patients can log in to view their reports.
- **Billing**: Create bills for patients, track payments, and view balances.
- **Inventory**: Add and manage lab inventories and track usage.

## API and Middleware

- **Instrument Middleware**: Located in `instrument_middleware/`, handles HL7 messages for lab instruments (e.g., CBC data). Run scripts like `python cbc_mllp_to_db.py` for integration.
- **API Routes**: Defined in `routes/api.php` for potential external integrations.

## Testing

Run the test suite with PHPUnit:

```bash
./vendor/bin/phpunit
```

## Contributing

1. Fork the repository.
2. Create a feature branch: `git checkout -b feature-name`
3. Commit changes: `git commit -am 'Add feature'`
4. Push to the branch: `git push origin feature-name`
5. Submit a pull request.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contact

For questions, suggestions, or support, contact:

- Email: iamtalhakhn@gmail.com
- GitHub: [Talha Khan](https://github.com/Talhakhan-Developer)

## Acknowledgments

- Built with Laravel Framework.
- Thanks to the open-source community for libraries and tools used.
