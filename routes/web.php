<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SmsLogController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectColumnController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\MessengerController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::post('/dashboard/tabs', [DashboardController::class, 'storeTab'])->middleware(['auth', 'verified'])->name('dashboard.tabs.store');
Route::put('/dashboard/tabs/{tab}', [DashboardController::class, 'updateTab'])->middleware(['auth', 'verified'])->name('dashboard.tabs.update');
Route::put('/dashboard/tabs/{tab}/content', [DashboardController::class, 'updateTabContent'])->middleware(['auth', 'verified'])->name('dashboard.tabs.update-content');
Route::put('/dashboard/tabs/{tab}/users', [DashboardController::class, 'updateTabUsers'])->middleware(['auth', 'verified'])->name('dashboard.tabs.update-users');
Route::delete('/dashboard/tabs/{tab}', [DashboardController::class, 'deleteTab'])->middleware(['auth', 'verified'])->name('dashboard.tabs.delete');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    
    // User Management Routes
    Route::resource('users', UserController::class);
    Route::post('users/{user}/send-new-password', [UserController::class, 'sendNewPassword'])->name('users.send-new-password');
    
    // Customer Management Routes
    Route::resource('customers', CustomerController::class);
    
    // Project Management Routes
    Route::resource('projects', ProjectController::class);
    
    // Project Columns Routes
    Route::get('projects/{project}/columns', [ProjectTaskController::class, 'getColumns'])->name('projects.columns.get');
    Route::post('projects/{project}/columns', [ProjectColumnController::class, 'store'])->name('projects.columns.store');
    Route::put('projects/{project}/columns/{column}', [ProjectColumnController::class, 'update'])->name('projects.columns.update');
    Route::delete('projects/{project}/columns/{column}', [ProjectColumnController::class, 'destroy'])->name('projects.columns.destroy');
    
    // Project Tasks Routes
    Route::post('columns/{column}/tasks', [ProjectTaskController::class, 'store'])->name('columns.tasks.store');
    Route::post('tasks/create', [ProjectTaskController::class, 'createFromAnywhere'])->name('tasks.create.anywhere');
    Route::put('tasks/{task}', [ProjectTaskController::class, 'update'])->name('tasks.update');
    Route::post('tasks/{task}/toggle', [ProjectTaskController::class, 'toggle'])->name('tasks.toggle');
    Route::post('tasks/reorder', [ProjectTaskController::class, 'reorder'])->name('tasks.reorder');
    Route::delete('tasks/{task}', [ProjectTaskController::class, 'destroy'])->name('tasks.destroy');
    
    // Project Accounts Routes
    Route::post('projects/{project}/accounts', [\App\Http\Controllers\ProjectAccountController::class, 'store'])->name('projects.accounts.store');
    Route::put('projects/{project}/accounts/{id}', [\App\Http\Controllers\ProjectAccountController::class, 'update'])->name('projects.accounts.update');
    Route::delete('projects/{project}/accounts/{id}', [\App\Http\Controllers\ProjectAccountController::class, 'destroy'])->name('projects.accounts.destroy');
    
    // Project Chat Routes
    Route::get('projects/{project}/chat/messages', [\App\Http\Controllers\ProjectChatController::class, 'getMessages'])->name('projects.chat.messages');
    Route::post('projects/{project}/chat/send', [\App\Http\Controllers\ProjectChatController::class, 'sendMessage'])->name('projects.chat.send');
    Route::get('projects/chat/attachment/{attachmentId}', [\App\Http\Controllers\ProjectChatController::class, 'downloadAttachment'])->name('projects.chat.download-attachment');
    
    // Project Files Routes
    Route::get('projects/{project}/files', [\App\Http\Controllers\ProjectFileController::class, 'index'])->name('projects.files.index');
    Route::post('projects/{project}/files', [\App\Http\Controllers\ProjectFileController::class, 'store'])->name('projects.files.store');
    Route::delete('projects/{project}/files/{projectFile}', [\App\Http\Controllers\ProjectFileController::class, 'destroy'])->name('projects.files.destroy');
    Route::get('projects/{project}/files/{projectFile}/download', [\App\Http\Controllers\ProjectFileController::class, 'download'])->name('projects.files.download');
    
    // Role Management Routes
    Route::resource('roles', RoleController::class);
    
    // SMS Logs Routes
    Route::get('/sms-logs', [SmsLogController::class, 'index'])->name('sms-logs.index');
    Route::post('/sms-logs/send', [SmsLogController::class, 'send'])->name('sms-logs.send');
    Route::get('/sms-logs/{smsLog}', [SmsLogController::class, 'show'])->name('sms-logs.show');
    
    // Messenger Routes
    Route::get('/messenger', [MessengerController::class, 'index'])->name('messenger.index');
    Route::get('/messenger/conversation/{userId}', [MessengerController::class, 'getConversation'])->name('messenger.conversation');
    Route::post('/messenger/send', [MessengerController::class, 'sendMessage'])->name('messenger.send');
    Route::get('/messenger/messages/{conversationId}', [MessengerController::class, 'getMessages'])->name('messenger.messages');
    Route::post('/messenger/read/{conversationId}', [MessengerController::class, 'markAsRead'])->name('messenger.mark-read');
    Route::get('/messenger/attachment/{attachmentId}', [MessengerController::class, 'downloadAttachment'])->name('messenger.download-attachment');
});

require __DIR__.'/auth.php';
