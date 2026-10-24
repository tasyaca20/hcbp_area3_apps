<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluasiController;
use App\Http\Controllers\IdpController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\SettingRoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');
    Route::post('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'baca'])->name('notifikasi.baca');
});

Route::middleware(['auth', 'role:admin_master'])->prefix('admin-master')->name('admin-master.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/idp/daftar', [IdpController::class, 'daftar'])->name('idp.daftar');
    Route::get('/idp/daftar/template', [IdpController::class, 'downloadTemplateImportMaster'])->name('idp.template');
    Route::post('/idp/daftar/import', [IdpController::class, 'importMaster'])->name('idp.import');
    Route::post('/idp/daftar', [IdpController::class, 'storeMaster'])->name('idp.store');
    Route::put('/idp/daftar/{idp}', [IdpController::class, 'updateMaster'])->name('idp.update');
    Route::delete('/idp/daftar/{idp}', [IdpController::class, 'destroyMaster'])->name('idp.destroy');
    Route::get('/idp/penetapan', [IdpController::class, 'penetapan'])->name('idp.penetapan');
    Route::get('/idp/pemantauan', [IdpController::class, 'pemantauan'])->name('idp.pemantauan');
    Route::get('/coaching/pemantauan', [IdpController::class, 'pemantauanCoaching'])->name('coaching.pemantauan');
    Route::get('/coaching/{idp}/download/{type}/{idRencana?}', [IdpController::class, 'downloadCoachingBukti'])->name('coaching.download');
    Route::get('/idp/evaluasi', [EvaluasiController::class, 'indexAdminMaster'])->name('idp.evaluasi');
    Route::get('/idp/evaluasi/{idp}/download/{type}/{evaluasi?}', [EvaluasiController::class, 'download'])->name('idp.evaluasi.download');
    Route::get('/setting-role', [SettingRoleController::class, 'index'])->name('setting-role');
    Route::post('/setting-role', [SettingRoleController::class, 'store'])->name('setting-role.store');
    Route::put('/setting-role/{pengguna}', [SettingRoleController::class, 'update'])->name('setting-role.update');
    Route::delete('/setting-role/{pengguna}', [SettingRoleController::class, 'destroy'])->name('setting-role.destroy');
});

Route::middleware(['auth', 'role:admin_area'])->prefix('admin-area')->name('admin-area.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'indexArea'])->name('dashboard');
    Route::get('/idp/daftar', [IdpController::class, 'daftarArea'])->name('idp.daftar');
    Route::get('/idp/penetapan', [IdpController::class, 'penetapanArea'])->name('idp.penetapan');
    Route::get('/idp/pemantauan', [IdpController::class, 'pemantauanArea'])->name('idp.pemantauan');
    Route::get('/coaching/pemantauan', [IdpController::class, 'pemantauanCoachingArea'])->name('coaching.pemantauan');
    Route::get('/coaching/{idp}/download/{type}/{idRencana?}', [IdpController::class, 'downloadCoachingBukti'])->name('coaching.download');
    Route::get('/idp/evaluasi', [EvaluasiController::class, 'indexAdminArea'])->name('idp.evaluasi');
    Route::get('/idp/evaluasi/{idp}/download/{type}/{evaluasi?}', [EvaluasiController::class, 'download'])->name('idp.evaluasi.download');
});

Route::middleware(['auth', 'role:atasan'])->prefix('atasan')->name('atasan.')->group(function () {
    Route::get('/idp/daftar', [IdpController::class, 'daftarAtasan'])->name('idp.daftar');
    Route::get('/idp/penetapan', [IdpController::class, 'penetapanAtasan'])->name('idp.penetapan');
    Route::put('/idp/penetapan/{rencana}', [IdpController::class, 'reviewRencanaAtasan'])->name('idp.penetapan.review');
    Route::get('/idp/pemantauan', [IdpController::class, 'pemantauanAtasan'])->name('idp.pemantauan');
    Route::get('/idp/evaluasi', [EvaluasiController::class, 'indexAtasan'])->name('idp.evaluasi');
    Route::post('/idp/evaluasi/{idp}/nilai', [EvaluasiController::class, 'storeNilai'])->name('idp.evaluasi.nilai');
    Route::put('/idp/evaluasi/{idp}/bukti/{bukti}/review', [EvaluasiController::class, 'reviewAtasan'])->name('idp.evaluasi.review');
    Route::get('/idp/evaluasi/{idp}/download/{type}', [EvaluasiController::class, 'download'])->name('idp.evaluasi.download');
    Route::get('/coaching', [IdpController::class, 'coachingAtasan'])->name('coaching.index');
    Route::get('/coaching/{idp}/download/{type}/{idRencana?}', [IdpController::class, 'downloadCoachingBukti'])->name('coaching.download');
    Route::put('/coaching/{idp}/bukti/{coachingBukti}/review', [IdpController::class, 'reviewCoachingAtasan'])->name('coaching.review');
});

Route::middleware(['auth', 'role:bawahan'])->prefix('bawahan')->name('bawahan.')->group(function () {
    Route::get('/idp/daftar', [IdpController::class, 'daftarBawahan'])->name('idp.daftar');
    Route::post('/idp/daftar/{idp}/rencana', [IdpController::class, 'storeRencanaBawahan'])->name('idp.rencana.store');
    Route::get('/idp/penetapan', [IdpController::class, 'penetapanBawahan'])->name('idp.penetapan');
    Route::get('/idp/pemantauan', [IdpController::class, 'pemantauanBawahan'])->name('idp.pemantauan');
    Route::get('/idp/evaluasi', [EvaluasiController::class, 'indexBawahan'])->name('idp.evaluasi');
    Route::post('/idp/evaluasi/{idp}/bukti', [EvaluasiController::class, 'store'])->name('idp.evaluasi.store');
    Route::get('/idp/evaluasi/{idp}/download/{type}', [EvaluasiController::class, 'download'])->name('idp.evaluasi.download');
    Route::get('/coaching', [IdpController::class, 'coachingBawahan'])->name('coaching.index');
    Route::post('/coaching/{idp}/bukti', [IdpController::class, 'uploadBuktiCoaching'])->name('coaching.bukti');
    Route::get('/coaching/{idp}/download/{type}/{idRencana?}', [IdpController::class, 'downloadCoachingBukti'])->name('coaching.download');
});
