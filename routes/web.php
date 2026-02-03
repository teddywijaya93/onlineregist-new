<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NIK_UsernameCheckController;
use App\Http\Controllers\CreateAccountController;

// Views
Route::view('/', 'auth.home');
Route::view('/referral-code', 'referral-code')->name('referral-form');
Route::view('/customer-type', 'customer-type')->name('customer-type');
Route::view('/check-nik-name', 'check-nik-name')->name('check-nik-name');
Route::view('/create-account', 'create-account')->name('create-account');

// Step API
Route::post('/step/account-type',   [CreateAccountController::class, 'saveAccountType'])->name('step.account-type');
Route::post('/step/identity',       [CreateAccountController::class, 'saveIdentity'])->name('step.identity');

// Check 
Route::post('/check-nik',      [NIK_UsernameCheckController::class, 'nikCheck'])->name('check.nik');
Route::post('/check-username', [NIK_UsernameCheckController::class, 'usernameCheck'])->name('check.username');

// Submit
Route::post('/create-account-submit', [CreateAccountController::class, 'createAccount'])->name('create.account.submit');