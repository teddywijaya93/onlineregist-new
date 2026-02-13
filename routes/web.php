<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NIK_UsernameCheckController;
use App\Http\Controllers\CreateAccountController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\Verifikasi_KTPController;
use App\Http\Controllers\Verifikasi_WajahController;
// use App\Http\Controllers\ProfilResikoController;
use App\Http\Controllers\AuthController;

// API Master Data
Route::get('/master/gender',[MasterDataController::class, 'getGenderMaster'])->name('master.gender');
Route::get('/master/religion',[MasterDataController::class, 'getReligionMaster'])->name('master.religion');
Route::get('/master/marital',[MasterDataController::class, 'getMaritalMaster'])->name('master.marital');
Route::get('/master/employment', [MasterDataController::class, 'getEmploymentMaster'])->name('master.employment');
Route::get('/master/employment-position',[MasterDataController::class, 'getPositionByEmployment'])->name('master.position');
Route::get('/master/employment-businessline',[MasterDataController::class, 'getBusinesslineByEmployment'])->name('master.businessline');
Route::get('/master/income-range',[MasterDataController::class, 'getIncomeRangeMaster'])->name('master.incomeRange');
Route::get('/master/primary-fund-source',[MasterDataController::class, 'getPrimaryFundMaster'])->name('master.primaryFundSOurce');
Route::get('/master/investment-objective',[MasterDataController::class, 'getInvestmentObjective'])->name('master.investmentObjective');
Route::get('/master/bank',[MasterDataController::class, 'getBankMaster'])->name('master.bank');
Route::get('/master/reference-relation',[MasterDataController::class, 'getReferenceRelationMaster'])->name('master.referenceRelation');

// Step API
Route::post('/step/account-type',[CreateAccountController::class, 'saveAccountType'])->name('step.account-type');
Route::post('/step/identity',[CreateAccountController::class, 'saveIdentity'])->name('step.identity');
Route::post('/create-account-submit',[CreateAccountController::class, 'createAccount'])->name('create.account.submit');

// Check 
Route::post('/check-nik',[NIK_UsernameCheckController::class, 'nikCheck'])->name('check.nik');
Route::post('/check-username',[NIK_UsernameCheckController::class, 'usernameCheck'])->name('check.username');

// Views
Route::view('/', 'auth.home');
Route::view('/referral-code', 'referral-code')->name('referral-form');
Route::view('/customer-type', 'customer-type')->name('customer-type');
Route::view('/check-nik-name', 'check-nik-name')->name('check-nik-name');
Route::view('/create-account', 'create-account')->name('create-account');

Route::view('/login', 'login')->name('login');
Route::post('/login/process', [AuthController::class, 'loginNewRegistration'])->name('login.process');
Route::view('/otp', 'otp')->name('otp');
Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('otp.verify');
Route::post('/otp/resend', [AuthController::class, 'resendOtp'])->name('otp.resend');

// OCR 
Route::view('/verifikasi-ktp', 'verifikasi-ocr-ktp')->name('verifikasi.ktp');
Route::post('/verifikasi-ktp/process', [Verifikasi_KTPController::class, 'process'])->name('verifikasi.ktp.process');

// Liveness
Route::view('/verifikasi-wajah', 'verifikasi-liveness-wajah')->name('verifikasi.wajah');
Route::post('/verifikasi-wajah/process',[Verifikasi_WajahController::class, 'process'])->name('verifikasi.wajah.process');

// Step 1
Route::get('/data-personal',[Verifikasi_KTPController::class, 'dataPersonal'])->middleware('check.step')->name('data.personal');
Route::post('/data-personal/submit', [CreateAccountController::class, 'savePersonal'])->name('data.personal.submit');

// Step 2
Route::view('/data-pekerjaan', 'data-pekerjaan')->middleware('check.step')->name('data.pekerjaan');
Route::post('/data-pekerjaan/submit',[CreateAccountController::class, 'saveEmployment'])->name('data.pekerjaan.submit');

// Step 3
Route::view('/data-penghasilan', 'data-penghasilan')->middleware('check.step')->name('data.penghasilan');
Route::post('/data-penghasilan/submit',[CreateAccountController::class, 'saveFinancial'])->name('data.penghasilan.submit');

// Step 4
Route::view('/data-referensi-perseorangan', 'data-referensi-perseorangan')->middleware('check.step')->name('data.referensi.perseorangan');
Route::get('/data-referensi-perseorangan', function () {return view('data-referensi-perseorangan');})->middleware('check.step')->name('data.referensi.perseorangan');
Route::post('/data-referensi-perseorangan/submit',[CreateAccountController::class, 'saveReferensiPerseorangan'])->name('data.referensi.perseorangan.submit');

// Step 5
Route::view('/profil-resiko', 'profil-resiko')->middleware('check.step')->name('data.profil.resiko');
Route::post('/profil-resiko/submit',[CreateAccountController::class, 'saveProfilResiko'])->name('profil.resiko.submit');
Route::get('/profil-resiko-result',[CreateAccountController::class, 'resultProfilResiko'])->name('profil.resiko.result');

// Step 6
Route::view('/data-bank', 'data-bank')->middleware('check.step')->name('data.bank');