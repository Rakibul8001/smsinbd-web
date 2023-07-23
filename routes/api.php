<?php

use Illuminate\Http\Request;
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

Route::get('/test', function (Request $request){
    dd("hello");
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->get('/send-sms', 'VerifyClientPhoneController@manageSmsMessage');
Route::middleware('auth:api')->post('/send-sms', 'VerifyClientPhoneController@manageSmsMessage');
Route::middleware('auth:api')->get('/send-dynamic-sms', 'VerifyClientPhoneController@manageSmsRotationMessage');
Route::middleware('auth:api')->post('/send-dynamic-sms', 'VerifyClientPhoneController@manageSmsRotationMessage');
Route::middleware('auth:api')->get('/turbo-sms', 'VerifyClientPhoneController@manageSmsTemplateRotationMessage');
Route::middleware('auth:api')->post('/turbo-sms', 'VerifyClientPhoneController@manageSmsTemplateRotationMessage');
Route::middleware('auth:api')->get('/sms-balance', 'VerifyClientPhoneController@smsBalance');
Route::middleware('auth:api')->get('/dlr-report', 'VerifyClientPhoneController@detailDlr');
