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
Route::get('/master/education',[MasterDataController::class, 'getEducationMaster'])->name('master.education');
Route::get('/master/employment', [MasterDataController::class, 'getEmploymentMaster'])->name('master.employment');
Route::get('/master/employment-position',[MasterDataController::class, 'getPositionByEmployment'])->name('master.position');
Route::get('/master/employment-businessline',[MasterDataController::class, 'getBusinesslineByEmployment'])->name('master.businessline');
Route::get('/master/income-range',[MasterDataController::class, 'getIncomeRangeMaster'])->name('master.incomeRange');
Route::get('/master/primary-fund-source',[MasterDataController::class, 'getPrimaryFundMaster'])->name('master.primaryFundSOurce');
Route::get('/master/investment-objective',[MasterDataController::class, 'getInvestmentObjective'])->name('master.investmentObjective');
Route::get('/master/bank',[MasterDataController::class, 'getBankMaster'])->name('master.bank');
Route::get('/master/reference-relation',[MasterDataController::class, 'getReferenceRelationMaster'])->name('master.referenceRelation');
Route::get('/master/city',[MasterDataController::class, 'getCityMaster'])->name('master.city');
Route::get('/master/kecamatan',[MasterDataController::class, 'getKecamatanMaster'])->name('master.kecamatan');
Route::get('/master/kelurahan',[MasterDataController::class, 'getKelurahanMaster'])->name('master.kelurahan');

// Check 
Route::post('/check-nik',[NIK_UsernameCheckController::class, 'nikCheck'])->name('check.nik');
Route::post('/check-username',[NIK_UsernameCheckController::class, 'usernameCheck'])->name('check.username');

// Home
Route::view('/', 'auth.home');

// Verifikasi Email
Route::view('/email', 'email')->name('email');
Route::post('/check-email',[AuthController::class,'checkEmail'])->name('check.email');
Route::post('/send-otp',[AuthController::class,'sendOtpMail'])->name('send.otp');
Route::post('/verify-otp',[AuthController::class,'verifyOtp']);
Route::get('/otp', function () {return view('otp');})->name('otp');

// Verifikasi Whatsapp
Route::view('/mobile', 'mobile')->name('mobile');
Route::view('/otp-mobile', 'otp-mobile')->name('otp-mobile');
Route::post('/save-phone',[AuthController::class,'savePhone']);
Route::post('/verify-otp-mobile',[AuthController::class,'verifyOtpMobile']);

// Create Account
Route::view('/create-account', 'create-account')->name('create-account');
Route::post('/create-account', [AuthController::class, 'createAccount'])->name('create.account');

// Create PIN
Route::view('/create-pin', 'create-pin')->name('create-pin');
Route::post('/create-pin', [CreateAccountController::class,'createPin']);

// Account Type
Route::view('/account-type', 'account-type')->name('account-type');
Route::post('/account-type',[CreateAccountController::class,'createAccountType']);

Route::middleware(['ensure.login','step.guard'])->group(function () {
    // OCR
    Route::get('/verifikasi-ktp',[Verifikasi_KTPController::class,'index'])->name('verifikasi.ktp');
    Route::post('/verifikasi-ktp/process', [Verifikasi_KTPController::class, 'process'])->name('verifikasi.ktp.process');

    // Liveness
    Route::get('/verifikasi-wajah',[Verifikasi_WajahController::class,'index'])->name('verifikasi.wajah');
    Route::post('/verifikasi-wajah/process',[Verifikasi_WajahController::class, 'process'])->name('verifikasi.wajah.process');

    // Step 1
    Route::get('/data-personal',[CreateAccountController::class, 'showPersonal'])->name('data.personal');
    Route::post('/data-personal/submit',[CreateAccountController::class, 'savePersonal'])->name('data.personal.submit');
    // Step 2
    Route::get('/data-pekerjaan', [CreateAccountController::class, 'showEmployment'])->name('data.pekerjaan');
    Route::post('/data-pekerjaan/submit',[CreateAccountController::class, 'saveEmployment'])->name('data.pekerjaan.submit');
    // Step 3
    Route::get('/data-penghasilan', [CreateAccountController::class, 'showFinancial'])->name('data.penghasilan');
    Route::post('/data-penghasilan/submit',[CreateAccountController::class, 'saveFinancial'])->name('data.penghasilan.submit');
    // Step 4
    Route::view('/data-referensi-perseorangan', 'data-referensi-perseorangan')->name('data.referensi.perseorangan');
    // Step 5
    Route::view('/data-bank', 'data-bank')->name('data.bank');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login/process', [AuthController::class, 'loginNewRegistration'])->name('login.process');

Route::get('/data-referensi-perseorangan', function () {return view('data-referensi-perseorangan');})->middleware('step.guard')->name('data.referensi.perseorangan');
Route::post('/data-referensi-perseorangan/submit',[CreateAccountController::class, 'saveReferensiPerseorangan'])->name('data.referensi.perseorangan.submit');