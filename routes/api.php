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

# Auth
Route::post('verifyEmail', 'User\AuthController@verifyEmail');
Route::post('verifyDocumentNumber', 'User\AuthController@verifyDocumentNumber');
Route::post('register', 'User\AuthController@register');
Route::post('login', 'User\AuthController@login')->middleware('assign.guard:users');
Route::post('forgotPassword', 'User\AuthController@forgotPassword');
Route::post('redefinePassword', 'User\AuthController@redefinePassword');
Route::get('activateAccount/{code}', 'User\AuthController@activateAccount');
Route::get('socket', 'User\AuthController@socket');


Route::group(['middleware' => ['auth.jwt', 'assign.guard:users']], function () {

    # Auth
    Route::get('logout', 'User\AuthController@logout');
    Route::get('auth', 'User\AuthController@auth');
    Route::get('sendConfirmEmail', 'User\AuthController@sendConfirmEmail');

    # Google2FA
    Route::get('googleAuth', 'User\Google2FAController@googleAuth');
    Route::post('verifyGoogleAuth', 'User\Google2FAController@verifyGoogleAuth');
    Route::post('disableGoogleAuth', 'User\Google2FAController@disableGoogleAuth');

    # Order
    Route::post('simulateBuy', 'User\OrderController@simulateBuy');
    Route::post('buy', 'User\OrderController@buy');
    Route::post('sale', 'User\OrderController@sale');

});
