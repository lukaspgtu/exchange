<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'Welcome to API Diamond Trading'
    ]);
});

# Auth
Route::get('allPlans', 'Client\PlanController@allPlans');
Route::get('validateManager/{manager}', 'Client\AuthController@validateManager');
Route::get('validateUsername/{username}', 'Client\AuthController@validateUsername');
Route::post('register', 'Client\AuthController@register');
Route::post('login', 'Client\AuthController@login')->middleware('assign.guard:users');
Route::post('forgotPassword', 'Client\AuthController@forgotPassword');
Route::post('redefinePassword', 'Client\AuthController@redefinePassword');
Route::get('sendConfirmEmail', 'Client\AuthController@sendConfirmEmail');
Route::post('confirmEmail', 'Client\AuthController@confirmEmail');
Route::get('mail', 'Client\AuthController@mail');

Route::group(['middleware' => ['auth.jwt', 'assign.guard:users']], function () {

    # Auth
    Route::get('logout', 'Client\AuthController@logout');
    Route::get('auth', 'Client\AuthController@auth');

    # Google2FA
    Route::get('googleAuth', 'Client\Google2FAController@googleAuth');
    Route::post('verifyGoogleAuth', 'Client\Google2FAController@verifyGoogleAuth');
    Route::post('disableGoogleAuth', 'Client\Google2FAController@disableGoogleAuth');

});
