<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KmtController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QrcodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});

Route::post('/callback', [KmtController::class, 'callback']);
Route::get('/publickey', function () {
    return env('PUBLIC_KEY', false);
});
Route::get('/billerid', function () {
    return env('BILLER_ID', false);
});
Route::get('/bizmchid', function () {
    return env('BIZMCH_ID', false);
});
Route::get('/channal', function () {
    return env('CHANNEL', false);
});
Route::get('/uuid', function () {
    return Str::uuid();
});
Route::get('/qrcode', [KmtController::class, 'qrCode']);
Route::get('/transection/{id}', [KmtController::class, 'transection']);
Route::get('/transection', [KmtController::class, 'transectionList']);
Route::get('/settle', [KmtController::class, 'settleList']);
Route::get('/verify', [KmtController::class, 'verify']);

Route::get('/sign', [KmtController::class, 'getSign']);
Route::get('/header', [KmtController::class, 'header']);

// Route::middleware('api')->group( function () {
Route::resource('qrcodes', QrcodeController::class);
// });

Route::group([
    'middleware' => 'apikey',
], function ($router) {
    $router->get('/sign', [KmtController::class, 'getSign']);
});

Route::group([
    'prefix' => 'v2',
], function ($router) {

    Route::group([
        'prefix' => 'payment'
    ], function ($router) {
        Route::get('/token', [PaymentController::class, 'getToken']);
    });
});
