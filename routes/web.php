<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NIK_UsernameCheckController;
use App\Http\Controllers\CreateAccountController;
use App\Http\Controllers\MasterDataController;

// API Master Data
Route::get('/master/employment', [MasterDataController::class, 'getEmploymentMaster'])->name('master.employment');
Route::get('/master/employment-position',[MasterDataController::class, 'getPositionByEmployment'])->name('master.position');
Route::get('/master/employment-businessline',[MasterDataController::class, 'getBusinesslineByEmployment'])->name('master.businessline');
Route::get('/master/income-range',[MasterDataController::class, 'getIncomeRangeMaster'])->name('master.incomeRange');
Route::get('/master/primary-fund-source',[MasterDataController::class, 'getPrimaryFundMaster'])->name('master.primaryFundSOurce');
Route::get('/master/investment-objective',[MasterDataController::class, 'getInvestmentObjective'])->name('master.investmentObjective');

// Views
Route::view('/', 'auth.home');
Route::view('/referral-code', 'referral-code')->name('referral-form');
Route::view('/customer-type', 'customer-type')->name('customer-type');
Route::view('/check-nik-name', 'check-nik-name')->name('check-nik-name');
Route::view('/create-account', 'create-account')->name('create-account');
Route::view('/login', 'login')->name('login');
// Step 2
Route::view('/pekerjaan', 'pekerjaan')->name('pekerjaan');
// Step 3
Route::view('/penghasilan', 'penghasilan')->name('penghasilan');

// Step API
Route::post('/step/account-type',[CreateAccountController::class, 'saveAccountType'])->name('step.account-type');
Route::post('/step/identity',[CreateAccountController::class, 'saveIdentity'])->name('step.identity');
Route::post('/create-account-submit',[CreateAccountController::class, 'createAccount'])->name('create.account.submit');

// Check 
Route::post('/check-nik',[NIK_UsernameCheckController::class, 'nikCheck'])->name('check.nik');
Route::post('/check-username',[NIK_UsernameCheckController::class, 'usernameCheck'])->name('check.username');