<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\RecordController;

Route::get('/', function () {
    return redirect()->route("login");
});

//Staff Registration
Route::get('/register-staff', [UserController::class, 'create'])->name('register.staff');
Route::post('/register-staff', [UserController::class, 'store'])->name('register.staff.store');
//Staff Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    //admin side
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    //staff side
    Route::get('/staff/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
    Route::get('/staff/generating-record', [StaffController::class, 'generateRecord'])->name('staff.record.generate');
    Route::get('/staff/record/{record_id}', [StaffController::class, 'showRecordView'])->name('staff.record.view');
    Route::post('/record/{id}/update-basic', [RecordController::class, 'updateBasic'])->name('record.update.basic');
    Route::post('/record/{id}/update-documents', [RecordController::class, 'updateDocuments'])->name('record.update.documents');
    Route::post('/record/{id}/update-transfer-info', [RecordController::class, 'updateTransferInfo'])->name('record.update.transfer_info');
    Route::get('/records', [RecordController::class, 'index'])->name('records.index');
    Route::put('/staff/record/{id}/complete', [StaffController::class, 'markAsCompleted'])->name('record.complete');
    Route::put('/staff/record/{id}/invalidate', [StaffController::class, 'markAsFailed'])->name('record.invalidate');
    Route::get('/staff/settings', [StaffController::class, 'editProfile'])->name('staff.settings');
    Route::post('/staff/settings', [StaffController::class, 'updateProfile'])->name('staff.settings.update');
    Route::get('/staff/audit', [StaffController::class, 'auditStaff'])->name('staff.audit');
});

Route::get('/certGrad', function(){
    return view("staff.certGraduate");
});
