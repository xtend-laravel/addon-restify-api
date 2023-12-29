<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use XtendLunar\Addons\RestifyApi\Controllers\Auth\ForgotPasswordController;
use XtendLunar\Addons\RestifyApi\Controllers\Auth\LoginController;
use XtendLunar\Addons\RestifyApi\Controllers\Auth\RegisterController;
use XtendLunar\Addons\RestifyApi\Controllers\Auth\ResetPasswordController;
use XtendLunar\Addons\RestifyApi\Controllers\Auth\VerifyController;
use XtendLunar\Addons\RestifyApi\Controllers\Auth\VerifyEmailController;
use XtendLunar\Addons\RestifyApi\Middleware\LanguageMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', RegisterController::class)
    ->name('restify.register');

Route::post('login', LoginController::class)
    //->middleware('throttle:6,1')
    ->name('restify.login');

Route::post('verify/{id}/{hash}', VerifyController::class)
    ->middleware('throttle:6,1')
    ->name('restify.verify');

Route::post('verify-email/{email}', VerifyEmailController::class)
    ->name('restify.verifyEmail');

Route::post('forgotPassword', ForgotPasswordController::class)
    ->middleware('throttle:6,1')
    ->name('restify.forgotPassword');

Route::post('resetPassword', ResetPasswordController::class)
    ->middleware('throttle:6,1')
    ->name('restify.resetPassword');

// @todo Improve this later, temporary solution to exclude auth:sanctum middleware
Route::post(
    'restify/{repository}/{repositoryId}/public-actions',
    \Binaryk\LaravelRestify\Http\Controllers\PerformRepositoryActionController::class
)
    ->middleware(LanguageMiddleware::class)
    ->name('actions.repository.performs')->withoutMiddleware('auth:sanctum');

Route::post(
    'restify/{repository}/public-actions',
    \Binaryk\LaravelRestify\Http\Controllers\PerformActionController::class
)
    ->middleware(LanguageMiddleware::class)
    ->name('actions.performs')->withoutMiddleware('auth:sanctum');

Route::get(
    'restify/{repository}/public-getters/{getter}',
    \Binaryk\LaravelRestify\Http\Controllers\PerformGetterController::class
)->name('getters.perform')->withoutMiddleware('auth:sanctum');

Route::get(
    'restify/{repository}/{repositoryId}/public-getters/{getter}',
    \Binaryk\LaravelRestify\Http\Controllers\PerformRepositoryGetterController::class
)->name('getters.repository.perform')->withoutMiddleware('auth:sanctum');
