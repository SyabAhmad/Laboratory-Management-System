<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Auth::routes();
Route::middleware(['auth:sanctum', 'verified'])
    ->group(function () {
        Route::get('/', 'App\Http\Controllers\DashboardController@index')->name('dashboard');
        Route::get('/dashboard', 'App\Http\Controllers\DashboardController@index')->name('dashboard');
        Route::post('/labdetails/add', 'App\Http\Controllers\DashboardController@store')->name('labdetails.add');
        Route::get('/labdetails/show', 'App\Http\Controllers\DashboardController@details')->name('labdetails.show');
        Route::get('/labdetails/edit/{id}', 'App\Http\Controllers\MainCompanysController@edit')->name('labdetails.edit');
        Route::post('/labdetails/update', 'App\Http\Controllers\MainCompanysController@update')->name('labdetails.update');

        // Users Route
        Route::get('user', 'App\Http\Controllers\UserController@index')->name('user');
        Route::get('/users/edit/{id}', "App\Http\Controllers\UserController@edit")->name('user.edit');
        Route::put('/users/update', 'App\Http\Controllers\UserController@update')->name('user.update');
        Route::put('/users/pass/update', 'App\Http\Controllers\UserController@updatepass')->name('user.pass.update');
        Route::get('/users/status/{id}', 'App\Http\Controllers\UserController@statuschange')->name('user.status');
        Route::delete('/user/{id}', 'App\Http\Controllers\UserController@destroy')->name('user.delete');

        Route::get('/users/employees/{id}', 'App\Http\Controllers\UserController@employeeschange')->name('user.employees');
        Route::get('/users/patitents/{id}', 'App\Http\Controllers\UserController@patitentschange')->name('user.patitents');
        Route::get('/users/testcategory/{id}', 'App\Http\Controllers\UserController@testcategory')->name('user.testcategory');
        Route::get('/users/referral/{id}', 'App\Http\Controllers\UserController@referral')->name('user.referral');
        Route::get('/users/billing/{id}', 'App\Http\Controllers\UserController@billing')->name('user.billing');

        Route::get('/users/pathology/{id}', 'App\Http\Controllers\UserController@pathology')->name('user.pathology');
        Route::get('/users/radiology/{id}', 'App\Http\Controllers\UserController@radiology')->name('user.radiology');
        Route::get('/users/electrocardiography/{id}', 'App\Http\Controllers\UserController@electrocardiography')->name('user.electrocardiography');
        Route::get('/users/ultrasonography/{id}', 'App\Http\Controllers\UserController@ultrasonography')->name('user.ultrasonography');
        Route::get('/users/reportbooth/{id}', 'App\Http\Controllers\UserController@reportbooth')->name('user.reportbooth');
        Route::get('/users/financial/{id}', 'App\Http\Controllers\UserController@financial')->name('user.financial');
        Route::get('/users/report_g/{id}', 'App\Http\Controllers\UserController@report_g')->name('user.report_g');
        Route::get('/users/inventory/{id}', 'App\Http\Controllers\UserController@inventory')->name('user.inventory');

        // Employees Route
        Route::get('/employees', 'App\Http\Controllers\EmployeesController@index')->name('employees');
        Route::post('/employees/add', 'App\Http\Controllers\EmployeesController@store')->name('employees.add');
        Route::get('/employees/details/{id}', 'App\Http\Controllers\EmployeesController@show')->name('employees.profile');
        Route::get('/employees/edit/{id}', 'App\Http\Controllers\EmployeesController@edit')->name('employees.edit');
        Route::put('/employees/edit/{id}', 'App\Http\Controllers\EmployeesController@update')->name('employees.update');
        Route::delete('/employees/{id}', 'App\Http\Controllers\EmployeesController@destroy')->name('employees.destroy');

        // Patients Routes
        Route::get('/patients', 'App\Http\Controllers\PatientsController@index')->name('patients.list');
    Route::get('/patients/completed', 'App\Http\Controllers\PatientsController@completedList')->name('patients.completed');
        Route::get('/new/patients', 'App\Http\Controllers\PatientsController@create')->name('patients.create');
        Route::post('/new/patients/store', 'App\Http\Controllers\PatientsController@store')->name('patients.store');
        Route::get('/patients/status/{id}', 'App\Http\Controllers\PatientsController@statuschange')->name('patients.status');
        Route::get('/patients/{id}/edit', 'App\Http\Controllers\PatientsController@edit')->name('patients.edit');
        // Print friendly patient test report (per-test)
        Route::get('/patients/{patient}/tests/{testName}/print', [App\Http\Controllers\PatientsController::class, 'printTestReport'])->name('patients.printTest');
        // Save-to-system routes removed

        Route::put('/patients/{id}', 'App\Http\Controllers\PatientsController@update')->name('patients.update');
        Route::post('/patients/test-data', 'App\Http\Controllers\PatientsController@storeTestData')->name('patients.test.data.store');
        Route::get('/patients/details/{id}', 'App\Http\Controllers\PatientsController@show')->name('patients.profile');
        Route::delete('/patients/{id}', 'App\Http\Controllers\PatientsController@destroy')->name('patients.destroy');
        Route::get('/patients/{id}/registered-tests', 'App\Http\Controllers\PatientsController@registeredTests')->name('patients.registered_tests');
        Route::get('/patients/{id}/fetch-cbc-results', [App\Http\Controllers\PatientsController::class, 'fetchCBCResults'])->name('patients.fetchCBCResults');

        // Patient Receipt / Token Routes
        Route::get('/patients/{patientId}/receipt', 'App\Http\Controllers\PatientsController@viewReceipt')->name('patients.receipt');
        Route::get('/receipt/{receiptId}/print', 'App\Http\Controllers\PatientsController@printReceipt')->name('patients.print-receipt');
        Route::get('/patients/{patientId}/receipt/latest', 'App\Http\Controllers\PatientsController@getLatestReceipt')->name('patients.receipt.latest');

        // Referrals Route
        Route::get('/referrals', 'App\Http\Controllers\ReferralController@index')->name('referrels.list');
        Route::get('/referrals/create', 'App\Http\Controllers\ReferralController@create')->name('referrals.create');
        Route::post('/referrals/add', 'App\Http\Controllers\ReferralController@store')->name('referrals.store');
        Route::get('/referrals/edit/{id}', "App\Http\Controllers\ReferralController@edit")->name('referrals.edit');
        Route::put('/referrals/update', "App\Http\Controllers\ReferralController@update")->name('referrals.update');
        Route::delete('/referrals/{id}', 'App\Http\Controllers\ReferralController@destroy')->name('referrals.destroy');
        Route::get('/referrals/patients', 'App\Http\Controllers\ReferralController@patients')->name('referrals.patients');
        
        // Referral Commission Routes
        Route::get('/referrals/{referralId}/commissions', 'App\Http\Controllers\ReferralController@commissions')->name('referrals.commissions');
        Route::get('/referrals/{referralId}/commissions/monthly', 'App\Http\Controllers\ReferralController@commissionsMonthly')->name('referrals.commissions-monthly');
        Route::post('/referrals/{referralId}/commissions/month/{monthKey}/mark-paid', 'App\Http\Controllers\ReferralController@markMonthPaid')->name('referrals.mark-month-paid');
        Route::post('/referrals/commission/{commission}/mark-paid', 'App\Http\Controllers\ReferralController@markCommissionPaid')->name('referrals.mark-commission-paid');
        Route::get('/commissions/dashboard', 'App\Http\Controllers\ReferralController@commissionDashboard')->name('commissions.dashboard');

        // Lab Test Category Routes
        Route::get('/labtest', 'App\Http\Controllers\LabTestCatController@index')->name('labtest.index');


        // Lab Test Parameters Routes
        Route::get('/labtest/{id}/parameters', 'App\Http\Controllers\LabTestParameterController@create')->name('labtest.parameters.create');
        Route::post('/labtest/{id}/parameters', 'App\Http\Controllers\LabTestParameterController@store')->name('labtest.parameters.store');
    Route::delete('/labtest/parameters/{id}', 'App\Http\Controllers\LabTestParameterController@destroy')->name('labtest.parameters.destroy');
        Route::put('/labtest/parameters/{id}', 'App\Http\Controllers\LabTestParameterController@update')->name('labtest.parameters.update');

        Route::post('/labtest/add', 'App\Http\Controllers\LabTestCatController@store')->name('labtest.add');
        Route::delete('/labtest/{id}', 'App\Http\Controllers\LabTestCatController@destroy')->name('labtest.destroy');
        Route::get('/labtest/edit/{id}', 'App\Http\Controllers\LabTestCatController@edit')->name('labtest.edit');
        Route::put('/labtest/update', 'App\Http\Controllers\LabTestCatController@update')->name('labtest.update');

        // Department Routes
        Route::get('/departments', 'App\Http\Controllers\DepartmentController@index')->name('departments.index');
        Route::post('/departments', 'App\Http\Controllers\DepartmentController@store')->name('departments.store');
        Route::get('/departments/{id}/edit', 'App\Http\Controllers\DepartmentController@edit')->name('departments.edit');
        Route::put('/departments/{id}', 'App\Http\Controllers\DepartmentController@update')->name('departments.update');
        Route::delete('/departments/{id}', 'App\Http\Controllers\DepartmentController@destroy')->name('departments.destroy');
        Route::get('/departments/get', 'App\Http\Controllers\DepartmentController@getDepartments')->name('departments.get');

        // Billing System Routes - MUST come before /billing
        Route::get('/billing/create/{id}', 'App\Http\Controllers\BillsController@create')->name('billing.create');
        Route::get('/billing/registered-tests/{id}', 'App\Http\Controllers\BillsController@getRegisteredTests')->name('billing.get-registered-tests');
        Route::post('/billing/add', 'App\Http\Controllers\BillsController@store')->name('billing.add');
        Route::get('/billing/details/{id}', 'App\Http\Controllers\BillsController@show')->name('billing.details');
        Route::put('/billing/{bill}', 'App\Http\Controllers\BillsController@update')->name('bills.update');
    // Mark a bill as fully paid (records delta payment and updates status)
    Route::post('/billing/{bill}/mark-paid', 'App\Http\Controllers\BillsController@markAsPaid')->name('bills.markPaid');
        Route::get('/allbilling', 'App\Http\Controllers\BillsController@allbills')->name('allbills');
        Route::get('/all/billing', 'App\Http\Controllers\BillsController@allbills')->name('all.bills');
        Route::get('/billing', 'App\Http\Controllers\BillsController@index')->name('billing');
        Route::get('/billing/{patient}/registered-tests', [App\Http\Controllers\BillsController::class, 'getRegisteredTests'])
            ->name('billing.registeredTests');

        // Payments Route
        Route::get('/transection/record', 'App\Http\Controllers\PaymentsController@index')->name('transection.record');
        Route::get('/transection/other', 'App\Http\Controllers\PaymentsController@create')->name('other.transection');
        Route::post('/transection/other/post', 'App\Http\Controllers\PaymentsController@store')->name('other.transection.store');

    // Balance overview
    Route::get('/balance', 'App\Http\Controllers\BalanceController@index')->name('balance.index');

        //Report Genarate Route
        Route::get('/patientreport', 'App\Http\Controllers\ReportGenarationController@patientindex')->name('patientreport');
        Route::get('/ledger', 'App\Http\Controllers\ReportGenarationController@ledger')->name('ledger');
        Route::get('/ledger/details', 'App\Http\Controllers\ReportGenarationController@ledgerdetails')->name('ledger.details');
        Route::get('/referralreport', 'App\Http\Controllers\ReportGenarationController@referrallist')->name('referralreport');
        Route::get('/reportbooth', 'App\Http\Controllers\ReportGenarationController@reportbooth')->name('reportbooth');
        Route::get('/reportbooth/status/{id}/{status}', 'App\Http\Controllers\ReportGenarationController@report_statuschange');
        Route::get('/report/details/{id}', 'App\Http\Controllers\ReportGenarationController@report_details');

        //Test Report Route
        Route::get('/pathology', 'App\Http\Controllers\XrayReportController@pathology')->name('pathology');
        Route::get('/pathology/testresult/{id}', 'App\Http\Controllers\XrayReportController@pathologyedit')->name('pathologyedit');
        Route::get('/pathology/inventory/{id}', 'App\Http\Controllers\XrayReportController@pathologyinstrument')->name('pathologyinstrument');
        Route::put('/pathology/inventory/{id}', 'App\Http\Controllers\XrayReportController@pathologyinstrumentupdate');
        Route::put('/pathology/result/{id}', 'App\Http\Controllers\XrayReportController@pathologyreport');

        Route::get('/radiology', 'App\Http\Controllers\XrayReportController@radiology')->name('radiology');
        Route::get('/radiology/testresult/{id}', 'App\Http\Controllers\XrayReportController@radiologyedit')->name('radiologyedit');
        Route::put('/radiology/result/{id}', 'App\Http\Controllers\XrayReportController@radiologyreport');

        Route::get('/ultrasonography', 'App\Http\Controllers\XrayReportController@ultrasonography')->name('ultrasonography');
        Route::get('/ultrasonography/testresult/{id}', 'App\Http\Controllers\XrayReportController@ultrasonographyedit')->name('ultrasonographyedit');
        Route::put('/ultrasonography/result/{id}', 'App\Http\Controllers\XrayReportController@ultrasonographyreport');

        Route::get('/Electrocardiography', 'App\Http\Controllers\XrayReportController@Electrocardiography')->name('Electrocardiography');
        Route::get('/Electrocardiography/testresult/{id}', 'App\Http\Controllers\XrayReportController@Electrocardiographyedit');
        Route::put('/Electrocardiography/result/{id}', 'App\Http\Controllers\XrayReportController@Electrocardiographyreport');

        //Inventories Route
        Route::get('/inventories', 'App\Http\Controllers\InventoriesController@index')->name('inventories');
        Route::get('/inventories/history', 'App\Http\Controllers\InventoriesController@getInventories')->name('inventories.history');
        Route::post('/inventories/add', 'App\Http\Controllers\InventoriesController@store')->name('inventories.add');
        Route::post('/inventories/update', 'App\Http\Controllers\InventoriesController@storeinventoryhistory')->name('inventories.update');
        Route::delete('/inventories/{id}', 'App\Http\Controllers\InventoriesController@destroy')->name('inventories.destroy');
        Route::delete('/inventories/history/{id}', 'App\Http\Controllers\InventoriesController@historydestroy')->name('inventories.history.destroy');

        //Attendance Route
        Route::get('/Attendance', 'App\Http\Controllers\AttendancesController@index')->name('Attendance');
        Route::post('/Attendance/add', 'App\Http\Controllers\AttendancesController@store')->name('Attendance.add');
        Route::put('/Attendance/update', 'App\Http\Controllers\AttendancesController@update')->name('Attendance.update');

        //Activities Route
        Route::get('/activities', 'App\Http\Controllers\DaityActivitiesController@index')->name('activities');
        Route::post('/activities/add', 'App\Http\Controllers\DaityActivitiesController@store')->name('activities.add');
        Route::put('/activities/update', 'App\Http\Controllers\DaityActivitiesController@update')->name('activities.update');
    });

// User Profile Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('user.profile');
    Route::put('/profile/update', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('/patients/store-test-data', [App\Http\Controllers\PatientsController::class, 'storeTestData'])->name('patients.storeTestData');
});
