<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController as AuthRegisteredUserController;
use Illuminate\Support\Facades\Route;
use App\Models\Job;


// Route::get('/', function () {
//     return view('home');
    
// }); short hand for this in below 

Route::view('/' , 'home');

// Index

// Route::controller(JobController::class)->group(function(){
//     Route::get('/jobs' , 'index');
//     Route::get('/jobs/create' , 'create');
//     Route::get('/jobs/{job}' , 'show');
//     Route::post('/jobs' , 'store');
//     Route::get('/jobs/{job}/edit' , 'edit');
//     Route::patch('/jobs/{job}' , 'update');
//     Route::delete('/jobs/{job}', 'destroy');
// });

// Route::resource('jobs',JobController::class , [
//     'only'=>['index','show','create','store']
// ]);

Route::resource('jobs',JobController::class);

Route::get('/register' ,[RegisteredUserController::class , 'create']);


Route::get('/about', function () {
    return view('about');
});
Route::get('/students', [StudentController::class, 'index'])->name('students.index');
Route::post('/students/create' , [StudentController::class, 'store'])->name('students.store');
Route::put('/students/update/{id}', [StudentController::class, 'update'])->name('students.update');
Route::delete('/students/delete/{id}', [StudentController::class, 'delete'])->name('students.delete');
// put, delete, get, patch, post

// Financial Management Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('categories', CategoryController::class);
Route::resource('accounts', AccountController::class);
Route::resource('transactions', TransactionController::class);
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/settings', function() { return view('settings.index'); })->name('settings.index');

// Authentication Routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::get('/register', [AuthRegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [AuthRegisteredUserController::class, 'store']);