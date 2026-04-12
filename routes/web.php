<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/suppliers/{supplierId}', function ($supplierId) {
    return view('suppliers.show', ['supplierId' => $supplierId]);
})->name('suppliers.show');

Route::get('/suppliers/{supplierId}/layups/{layupId}', function ($supplierId, $layupId) {
    return view('layups.show', ['supplierId' => $supplierId, 'layupId' => $layupId]);
})->name('layups.show');

