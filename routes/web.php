<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubUserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\GuardController;
use App\Http\Controllers\QRScannerController;
use App\Http\Controllers\AdvanceController;

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

Route::get('/', function () {
    //return view('welcome');
    // return view('auth.registration');
    return view('auth.login');
});

// Registration
Route::get('registration', [CustomAuthController::class, 'registration'])->name('register');

Route::post('custom-registration', [CustomAuthController::class, 'custom_registration'])->name('register.custom');

// Login
Route::get('login', [CustomAuthController::class, 'index'])->name('login');

Route::post('custom-login', [CustomAuthController::class, 'custom_login'])->name('login.custom');

// Dashboard
Route::get('dashboard', [CustomAuthController::class, 'dashboard'])->name('dashboard');

Route::get('logout', [CustomAuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

// Profile
Route::get('profile', [ProfileController::class, 'index'])->name('profile');

Route::post('profile/edit_validation', [ProfileController::class, 'edit_validation'])->name('profile.edit_validation');

// Sub-Users
Route::get('sub_user', [SubUserController::class, 'index'])->name('sub_user');

Route::get('sub_user/fetchall', [SubUserController::class, 'fetch_all'])->name('sub_user.fetchall');

Route::get('sub_user/add', [SubUserController::class, 'add'])->name('sub_user.add');

Route::post('sub_user/add_validation', [SubUserController::class, 'add_validation'])->name('sub_user.add_validation');

Route::get('sub_user/edit/{id}', [SubUserController::class, 'edit'])->name('edit');

Route::post('sub_user/edit_validation', [SubUserController::class, 'edit_validation'])->name('sub_user.edit_validation');


Route::get('sub_user/delete/{id}', [SubUserController::class, 'delete'])->name('delete');

// Departments
Route::get('department', [DepartmentController::class, 'index'])->name('department');

Route::get('department/fetch_all', [DepartmentController::class, 'fetch_all'])->name('department.fetch_all');

Route::get('department/add', [DepartmentController::class, 'add'])->name('add');

Route::post('department/add_validation', [DepartmentController::class, 'add_validation'])->name('department.add_validation');

Route::get('department/edit/{id}', [DepartmentController::class, 'edit'])->name('edit');

Route::post('department/edit_validation', [DepartmentController::class, 'edit_validation'])->name('department.edit_validation');

Route::get('department/delete/{id}', [DepartmentController::class, 'delete'])->name('delete');

// Visitors

Route::get('visitor', [VisitorController::class, 'index'])->name('visitor.index');
// 
Route::get('gate1_visitor', [VisitorController::class, 'add'])->name('gate1_visitor.add');

Route::get('gate2_visitor', [VisitorController::class, 'add'])->name('gate2_visitor.add');

Route::get('gate3_visitor', [VisitorController::class, 'add'])->name('gate3_visitor.add');

Route::get('visitor/fetchall', [VisitorController::class, 'fetch_all'])->name('visitor.fetchall');

Route::get('visitor/add', [VisitorController::class, 'add'])->name('visitor.add');

Route::post('visitor/add_validation', [VisitorController::class, 'add_validation'])->name('visitor.add_validation');

Route::post('visitor/add_visitor_validation', [VisitorController::class, 'add_visitor_validation'])->name('visitor.add_visitor_validation');

Route::get('visitor/edit/{id}', [VisitorController::class, 'edit'])->name('edit');

Route::post('visitor/edit_validation', [VisitorController::class, 'edit_validation'])->name('visitor.edit_validation');

Route::get('visitor/delete/{id}', [VisitorController::class, 'delete'])->name('delete');

Route::delete('/visitor/delete/{id}', [VisitorController::class, 'delete'])->name('visitor.delete');

Route::get('visitor/view/{id}', [visitorController::class, 'view'])->name('visitor.view');

Route::get('/visitor/print/{id}', [VisitorController::class, 'print'])->name('visitor.print');


// advance Schedule
Route::get('/advance_sched', [AdvanceController::class, 'advance_sched'])->name('advance_sched');

Route::post('advancevisitor/add_visitor_validation', [AdvanceController::class, 'add_visitor_validation'])->name('advancevisitor.add_visitor_validation');

Route::get('/advancevisitor/print/{id}', [AdvanceController::class, 'print'])->name('advance.print');


// Students

Route::get('/student', [StudentController::class, 'index'])->name('student.index');

Route::get('/student/edit/{id}', [StudentController::class, 'edit'])->name('student.edit');

Route::post('/student/update/{id}', [StudentController::class, 'update'])->name('student.update');

Route::get('/student/fetchall', [StudentController::class, 'fetchAll'])->name('student.fetchall');

Route::get('/student/add', [StudentController::class, 'add'])->name('student.add');

Route::post('/student/add', [StudentController::class, 'add_validation'])->name('student.add_validation');

Route::post('/student/verify', [StudentController::class, 'verify'])->name('student.verify');

Route::get('/student/print/{id}', [StudentController::class, 'printStudent'])->name('student.print');

Route::delete('/student/delete/{id}', [StudentController::class, 'destroy'])->name('student.delete');

// OTP Routes

Route::get('otp_form', [OtpController::class, 'showOtpForm'])->name('otp_form');

Route::get('gate1_visitor/view', [OtpController::class, 'gate1'])->name('gate1_visitor.gate1');

Route::get('gate1_visitor/verify', [OtpController::class, 'addverify'])->name('gate1_visitor.addverify');

Route::get('gate2_visitor/view', [OtpController::class, 'gate2'])->name('gate2_visitor.gate2');

Route::get('gate2_visitor/verify', [OtpController::class, 'addverify'])->name('gate2_visitor.addverify');

Route::get('gate3_visitor/view', [OtpController::class, 'gate3'])->name('gate3_visitor.gate3');

Route::get('gate3_visitor/verify', [OtpController::class, 'addverify'])->name('gate3_visitor.addverify');

Route::get('gate3_visitor', [OtpController::class, 'addverify'])->name('gate3_visitor.addverify');

Route::post('guards', [OtpController::class, 'store'])->name('store');

Route::get('/otp_form', [OtpController::class, 'sendOtp'])->name('sendOtp');

Route::post('add_validation', [OtpController::class, 'add_validation'])->name('add_validation');

Route::post('validate-otp', [OtpController::class, 'validateOtp'])->name('validate-otp');

Route::get('gate1_visitor/add', [OtpController::class, 'add'])->name('gate1_visitor.add');

Route::post('otp_verify', [OtpController::class, 'verify'])->name('verifyOtp');

// Status Guard
Route::post('/update-status', [OtpController::class, 'updateStatus'])->name('updateStatus');

// Resend OTP 

Route::post('/resend-otp', [OtpController::class, 'resendOtp'])->name('resendOtp');

// routes/web.php

Route::delete('/otp/{id}', [OtpController::class, 'destroy'])->name('otp.delete');


// update status
Route::post('/update-status', [OtpController::class, 'updateStatus'])->name('updateStatus');


// QR Scanner
Route::get('/handle-scan', [QRScannerController::class, 'handleScan']);

Route::post('/verify-token', [QRScannerController::class, 'verifyAndUpdateToken'])->name('verify.token');

Route::post('/verify-token', [VisitorController::class, 'verifyToken'])->name('verify.token');

Route::post('/update-visitor-status', [VisitorController::class, 'updateVisitorStatus'])->name('update.visitor.status');

Route::post('/send-otp', [OtpController::class, 'sendOtp'])->name('sendOtp');

Route::post('/generate-and-send-otp', [OtpController::class, 'generateAndSendOtp'])->name('generateAndSendOtp');

Route::delete('/student/delete/{id}', [StudentController::class, 'destroy'])->name('student.delete');

Route::get('/student/get-token/{id}', [StudentController::class, 'getToken']);