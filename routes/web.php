<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController as AuthRegisteredUserController;
use Illuminate\Support\Facades\Route;
use App\Models\Job;


// Route::get('/', function () {
//     return view('home');
    
// }); short hand for this in below 

Route::get('/', function () {
    return redirect()->route('login');
});

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
Route::get('/transactions/export/csv', [TransactionController::class, 'exportCsv'])->name('transactions.export.csv');
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/settings', function() { return view('settings.index'); })->name('settings.index');

// Authentication Routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::get('/register', [AuthRegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [AuthRegisteredUserController::class, 'store']);

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/data', [App\Http\Controllers\Admin\UserManagementController::class, 'getUsersData'])->name('users.data');
    Route::post('/users/{user}/approve', [App\Http\Controllers\Admin\UserManagementController::class, 'approve'])->name('users.approve');
    Route::post('/users/{user}/reject', [App\Http\Controllers\Admin\UserManagementController::class, 'reject'])->name('users.reject');
    Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])->name('users.destroy');
});

// Additional Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications/clear-all', [App\Http\Controllers\NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    Route::get('/faq', [App\Http\Controllers\FaqController::class, 'index'])->name('faq.index');
    Route::get('/faq/search', [App\Http\Controllers\FaqController::class, 'search'])->name('faq.search');
    
    // Audit Logs Routes
    Route::get('/audit-logs', [App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{id}', [App\Http\Controllers\AuditLogController::class, 'show'])->name('audit-logs.show');
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/export', [App\Http\Controllers\ProfileController::class, 'exportData'])->name('profile.export');
    Route::get('/profile/security', [App\Http\Controllers\ProfileController::class, 'security'])->name('profile.security');
    Route::get('/profile/help', [App\Http\Controllers\ProfileController::class, 'help'])->name('profile.help');
    Route::get('/profile/login-activity', [App\Http\Controllers\ProfileController::class, 'loginActivity'])->name('profile.login-activity');
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/reset', [App\Http\Controllers\SettingsController::class, 'reset'])->name('settings.reset');
    // Transactions Import/Export Routes
    Route::post('/transactions/import', [TransactionController::class, 'import'])->name('transactions.import');
    // Reports Routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/annual-performance', [ReportController::class, 'annualPerformance'])->name('reports.annual-performance');
    Route::get('/reports/yearly-comparison', [ReportController::class, 'yearlyComparison'])->name('reports.yearly-comparison');
    Route::get('/reports/detailed-statement', [ReportController::class, 'detailedStatement'])->name('reports.detailed-statement');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
    
    // Budget Routes (Admin only for CRUD, users can view their own)
    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::get('/budgets/create', [BudgetController::class, 'create'])->name('budgets.create');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
    Route::get('/budgets/{budget}/edit', [BudgetController::class, 'edit'])->name('budgets.edit');
    Route::put('/budgets/{budget}', [BudgetController::class, 'update'])->name('budgets.update');
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');
    Route::post('/budgets/{budget}/toggle', [BudgetController::class, 'toggleStatus'])->name('budgets.toggle');
    Route::post('/budgets/{budget}/update-spent', [BudgetController::class, 'updateSpent'])->name('budgets.update-spent');
    
    // Admin Budget Pool Routes (Admin only)
    Route::get('/budgets/add-funds', [BudgetController::class, 'showAddFunds'])->name('budgets.add-funds');
    Route::post('/budgets/add-funds', [BudgetController::class, 'addFunds'])->name('budgets.add-funds.store');
});