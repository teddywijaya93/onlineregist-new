<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\KtpVerificationController;

Route::get('/', [RegisterController::class, 'index']);
Route::post('/start-registration', [RegisterController::class, 'submit']);

Route::get('/Customer-Type', [RegisterController::class, 'customerType']);
Route::post('/Customer-Type', [RegisterController::class, 'selectCustomerType']);

Route::get('/Email', [RegisterController::class, 'email']);
Route::post('/Email', [RegisterController::class, 'submitEmail']);

Route::get('/OTP', [RegisterController::class, 'otp']);
Route::post('/OTP', [RegisterController::class, 'verifyOtp']);

Route::get('/verifikasi_ktp', [KtpVerificationController::class, 'index']);
Route::post('/verifikasi_ktp', [KtpVerificationController::class, 'process']);
Route::post('/verifikasi_ktp/ocr', [KtpVerificationController::class, 'ajaxOcr']);
