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
Route::get('/master/all-kelurahan',[MasterDataController::class, 'getallKelurahanMaster'])->name('master.all.kelurahan');

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

// Route::get('/get-registration-status', function () {
//     return response()->json([
//         'registrationStatus' => session('registrationStatus'),
//         'registrationStep'   => session('registrationStep'),
//         'redirect' => \App\Services\StepRedirectService::routeByStep(session('registrationStep'))
//     ]);
// });

Route::middleware(['step.guard'])->group(function () {
    Route::get('/dashboard', [CreateAccountController::class, 'index'])->name('dashboard');

    // Create PIN
    Route::view('/create-pin', 'create-pin')->name('create.pin');
    Route::post('/create-pin', [CreateAccountController::class,'createPin']);

    // Account Type
    Route::view('/account-type', 'account-type')->name('account.type');
    Route::post('/account-type',[CreateAccountController::class,'createAccountType']);

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
    Route::get('/data-penghasilan', [CreateAccountController::class, 'showFinancial'])->name('data.penghasilan');
    Route::post('/data-penghasilan/submit',[CreateAccountController::class, 'saveFinancial'])->name('data.penghasilan.submit');
  
    // Step 3
    Route::get('/data-pekerjaan', [CreateAccountController::class, 'showEmployment'])->name('data.pekerjaan');
    Route::post('/data-pekerjaan/submit',[CreateAccountController::class, 'saveEmployment'])->name('data.pekerjaan.submit');

    // Step 3 If Mahasiswa
    Route::get('/data-universitas', [CreateAccountController::class, 'showUniversitas'])->name('data.universitas');
    Route::post('/data-universitas/submit',[CreateAccountController::class, 'saveUniversitas'])->name('data.universitas.submit');

    Route::get('/data-relation', [CreateAccountController::class, 'showRelation'])->name('data.relation');
    Route::post('/data-relation/submit',[CreateAccountController::class, 'saveRelation'])->name('data.relation.submit');

    // Step 4
    Route::get('/data-bank', [CreateAccountController::class, 'showBank'])->name('data.bank');
    Route::post('/data-bank/submit',[CreateAccountController::class, 'saveBank'])->name('data.bank.submit');

    // Step 5
    Route::view('/syarat-ketentuan', 'syarat-ketentuan')->name('syarat.ketentuan');
    Route::post('/syarat-ketentuan/agree', [CreateAccountController::class, 'agreeAgreement'])->name('syarat.ketentuan.agree');

    Route::get('/data-signature', [CreateAccountController::class, 'showSignature'])->name('data.signature');
    Route::post('/data-signature/submit', [CreateAccountController::class, 'saveSignature'])->name('data.signature.submit');

    Route::view('/sukses', 'success')->name('success');
});