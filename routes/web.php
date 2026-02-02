<?php

use Illuminate\Support\Facades\Route;   

Route::get('/', function(){return view('auth.home');});
Route::get('/referral-code', function(){return view('referral-code');})->name('referral-form');
Route::get('/customer-type', function(){return view('customer-type');})->name('customer-type');
Route::get('/check-nik-name', function(){return view('check-nik-name');})->name('check-nik-name');
Route::get('/create-account', function(){return view('create-account');})->name('create-account');