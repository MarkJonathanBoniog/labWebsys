<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\AuditTracingController;

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
    Route::get('/admin/records', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/record/{record_id}', [AdminController::class, 'showRecordView'])->name('admin.record.view');
    Route::get('/admin/settings', [AdminController::class, 'editProfile'])->name('admin.settings');
    Route::post('/admin/settings', [AdminController::class, 'updateProfile'])->name('admin.settings.update');
    Route::get('/admin/audit', [AdminController::class, 'auditAdmin'])->name('admin.audit');
    Route::get('/admin/audit-tracing/result', [AdminController::class, 'generateAuditReport'])->name('admin.auditTracing.result');
    Route::get('/admin/system/settings', [AdminController::class, 'systemSettings'])->name('admin.systemSettings');
    Route::post('/admin/program/store', [AdminController::class, 'storeProgram'])->name('admin.program.store');
    Route::put('/admin/program/update/{id}', [AdminController::class, 'updateProgram'])->name('admin.program.update');
    Route::delete('/admin/program/delete/{id}', [AdminController::class, 'destroyProgram'])->name('admin.program.destroy');
    Route::post('/admin/system/settings/upload-images', [AdminController::class, 'uploadImages'])->name('admin.systemSettings.uploadImages');

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
    Route::get('/staff/audit-tracing/result', [StaffController::class, 'generateAuditReport'])->name('staff.auditTracing.result');
    Route::get('/record/{id}/certificate/preview', [RecordController::class, 'previewCertificate'])->name('record.certificate.preview');

    //audit and certificates
    Route::get('/audit-tracing/download', [AuditTracingController::class, 'downloadAuditPdf'])->name('staff.auditTracing.download');
    Route::get('/records/{id}/certificate/preview', [RecordController::class, 'previewCertificate'])->name('certificate.preview');
    Route::get('/records/{id}/certificate/print', [RecordController::class, 'printCertificate'])->name('certificate.print');
});

// Route::get('/certGrad', function(){
//     return view("staff.certGraduate");
// });
