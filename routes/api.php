<?php

use Illuminate\Support\Facades\Route;

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

# Auth Routes
Route::get('activateAccount/{id}', 'User\AuthController@activateAccount')->name('activateAccount');
Route::post('verifyEmail', 'User\AuthController@verifyEmail');
Route::post('verifyDocumentNumber', 'User\AuthController@verifyDocumentNumber');
Route::post('register', 'User\AuthController@register');
Route::post('login', 'User\AuthController@login')->middleware('assign.guard:users');
Route::post('loginTwoFactor', 'User\AuthController@loginTwoFactor')->middleware('assign.guard:users');
Route::post('forgotPassword', 'User\AuthController@forgotPassword');
Route::put('generateNewPassword', 'User\AuthController@generateNewPassword');

# Order Routes
Route::get('orderStreaming', 'User\OrderController@orderStreaming');

Route::group(['middleware' => ['auth.jwt', 'assign.guard:users']], function () {

    # Auth Routes
    Route::post('logout', 'User\AuthController@logout');
    Route::get('auth', 'User\AuthController@auth');
    Route::get('sendConfirmEmail', 'User\AuthController@sendConfirmEmail');

    # Account Routes
    Route::get('operationalLimits', 'User\AccountController@operationalLimits');
    Route::get('sessions', 'User\AccountController@sessions');
    Route::put('updateEmail', 'User\AccountController@updateEmail');
    Route::put('updatePassword', 'User\AccountController@updatePassword');

    # Google2FA Routes
    Route::get('qrcode2FA', 'User\Google2FAController@qrcode2FA');
    Route::post('verify2FA', 'User\Google2FAController@verify2FA');

    # Order Routes
    Route::get('allOrders', 'User\OrderController@allOrders');
    Route::get('ordersCanceled', 'User\OrderController@ordersCanceled');
    Route::get('ordersExecuted', 'User\OrderController@ordersExecuted');
    Route::get('ordersRunning', 'User\OrderController@ordersRunning');
    Route::get('ordersWaiting', 'User\OrderController@ordersWaiting');
    Route::post('buyLimitedPrice', 'User\OrderController@buyLimitedPrice');
    Route::post('saleLimitedPrice', 'User\OrderController@saleLimitedPrice');
    Route::post('buyMarketPrice', 'User\OrderController@buyMarketPrice');
    Route::post('saleMarketPrice', 'User\OrderController@saleMarketPrice');
    Route::post('simulateBuy', 'User\OrderController@simulateBuy');
    Route::post('buy', 'User\OrderController@buy');
    Route::post('sale', 'User\OrderController@sale');

    # Extracts Routes
    Route::get('allExtracts', 'User\ExtractController@allExtracts');
    Route::get('buyExtracts', 'User\ExtractController@buyExtracts');
    Route::get('buyFeeExtracts', 'User\ExtractController@buyFeeExtracts');
    Route::get('saleExtracts', 'User\ExtractController@saleExtracts');
    Route::get('saleFeeExtracts', 'User\ExtractController@saleFeeExtracts');

});

# Admin Routes
Route::prefix('admin')->group(function () {

    Route::group(['middleware' => 'assign.guard:users'], function () {

        Route::post('login', 'Admin\AuthController@login');
        Route::post('loginTwoFactor', 'Admin\AuthController@loginTwoFactor');

        Route::group(['middleware' => 'auth.jwt'], function () {



        });

    });

});
