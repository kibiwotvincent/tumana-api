<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\EquityTransactionController;
use App\Http\Controllers\MpesaDepositController;
use App\Http\Controllers\StripePaymentIntentController;
use App\Http\Controllers\OrderController;

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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

//Route::get('/admin/roles', [RoleController::class, 'index']);
Route::post('/users/register', [RegisteredUserController::class, 'store']);
Route::post('/users/login', [AuthenticatedSessionController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
	Route::get('/user/transactions', [OrderController::class, 'index']);
	
	//admin routes
	//roles & permissions
	Route::get('/admin/roles', [RoleController::class, 'index']);
	Route::post('/admin/roles/create', [RoleController::class, 'store']);
	Route::post('/admin/roles/{id}/update', [RoleController::class, 'update']);
	Route::post('/admin/roles/{id}/rename', [RoleController::class, 'rename']);
	Route::post('/admin/roles/{id}/delete', [RoleController::class, 'delete']);
	Route::get('/admin/permissions', [PermissionController::class, 'index']);
	Route::post('/admin/permissions/create', [PermissionController::class, 'store']);
	Route::post('/admin/permissions/{id}/update', [PermissionController::class, 'update']);
	Route::post('/admin/permissions/{id}/delete', [PermissionController::class, 'delete']);
	//users
	Route::get('/admin/users', [UserController::class, 'index']);
	Route::post('/admin/users/{id}/update/roles', [UserController::class, 'updateRoles']);
    
    Route::post('/paypal/order/create', [PaypalController::class, 'createOrder']);
	
	Route::post('/order/create', [OrderController::class, 'create']);

});


//paypal routes
Route::post('/paypal/order/{id}/capture', [PaypalController::class, 'captureOrder']);

//equity routes
Route::post('/equity/callback', [EquityTransactionController::class, 'handleCallback']);
Route::get('/equity/test', [EquityTransactionController::class, 'test']);

//mpesa routes
Route::post('/mpesa/timed_out', [MpesaDepositController::class, 'timeoutCallback']);
Route::post('/mpesa/process_callback', [MpesaDepositController::class, 'processCallback']);
Route::get('/mpesa/send', [MpesaDepositController::class, 'send']);

Route::get('/stripe/payment_intent/create', [StripePaymentIntentController::class, 'create']);
Route::post('/stripe/webhook', [OrderController::class, 'handleWebhook']);

Route::get('/exchange_rate', function() {
	return response()->json(['rate' => 88.55]);
});

Route::get('/fees', function() {
	return response()->json(['fees' => 0.55]);
});