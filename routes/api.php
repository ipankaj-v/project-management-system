<?php

use App\Http\Controllers\Api\Auth\OTPController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AuditController;
use App\Http\Controllers\Api\Organization\OrganizationController;
use App\Http\Controllers\Api\Project\ProjectController;
use App\Http\Controllers\Api\Task\AttachmentController;
use App\Http\Controllers\Api\Task\TaskCommentController;
use App\Http\Controllers\Api\Task\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// OTP routes
Route::post('/send-otp', [OTPController::class, 'sendOTP']);
Route::post('/verify-otp', [OTPController::class, 'verifyOTP']);

// Password reset routes
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

//Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('organizations', OrganizationController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('tasks-comments', TaskCommentController::class);
    Route::apiResource('tasks-attachments', AttachmentController::class);
    Route::get('activities', [ActivityController::class, 'index']);
    Route::get('activities/{id}', [ActivityController::class, 'show']);
    Route::get('audits', [AuditController::class, 'index']);
    Route::get('audits/{id}', [AuditController::class, 'show']);
});