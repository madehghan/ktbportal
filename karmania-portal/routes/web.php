<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SmsLogController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectTaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // User Management Routes
    Route::resource('users', UserController::class);
    
    // Customer Management Routes
    Route::resource('customers', CustomerController::class);
    
    // Project Management Routes
    Route::resource('projects', ProjectController::class);
    
    // Project Tasks Routes
    Route::post('/projects/{project}/tasks', [ProjectTaskController::class, 'store'])->name('projects.tasks.store');
    Route::put('/projects/{project}/tasks/{task}', [ProjectTaskController::class, 'update'])->name('projects.tasks.update');
    Route::post('/projects/{project}/tasks/{task}/toggle', [ProjectTaskController::class, 'toggle'])->name('projects.tasks.toggle');
    Route::delete('/projects/{project}/tasks/{task}', [ProjectTaskController::class, 'destroy'])->name('projects.tasks.destroy');
    
    // Role Management Routes
    Route::resource('roles', RoleController::class);
    
    // SMS Logs Routes
    Route::get('/sms-logs', [SmsLogController::class, 'index'])->name('sms-logs.index');
    Route::post('/sms-logs/send', [SmsLogController::class, 'send'])->name('sms-logs.send');
    Route::get('/sms-logs/{smsLog}', [SmsLogController::class, 'show'])->name('sms-logs.show');
});

require __DIR__.'/auth.php';
