<?php

use App\Http\Controllers\FixedPaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\RecurringPaymentController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

// Route::resource('fixedpayment', FixedPaymentController::class);


// Public routes
Route::post('/register',[AdminController::class, 'register']);
Route::post('/login',[AdminController::class, 'login']);
Route::get('/admin', [AdminController::class, 'index']);
Route::get('/admin/{id}', [AdminController::class, 'show']);
Route::get('/fixedpayment', [FixedPaymentController::class, 'index']);
Route::get('/fixedpayment/{id}', [FixedPaymentController::class, 'show']);
Route::get('/fixedpayment/search/{title}', [FixedPaymentController::class, 'search']);
Route::get('/category', [CategoryController::class, 'index']);
Route::get('/category/{id}', [CategoryController::class, 'show']);
Route::get('/category/search/{title}', [CategoryController::class, 'search']);
Route::get('/recurring-payments', [RecurringPaymentController::class, 'index']);
Route::get('/recurring-payments/{id}', [RecurringPaymentController::class, 'show']);
Route::get('/recurring-payments/search/{title}', [RecurringPaymentController::class, 'search']);
Route::get('/goal', [GoalController::class, 'index']);


// Protected Routes
Route::group(['middleware' =>['auth:sanctum']], function (){
    Route::post('/logout',[AdminController::class, 'logout']);
    Route::post('/fixedpayment', [FixedPaymentController::class, 'store'] );
    Route::put('/fixedpayment/{id}', [FixedPaymentController::class, 'update'] );
    Route::delete('/fixedpayment/{id}', [FixedPaymentController::class, 'destroy'] );
    Route::put('/admin/{id}', [AdminController::class, 'update']);
    Route::post('/admin', [AdminController::class, 'createAdmin']);
    Route::delete('/admin/{id}', [AdminController::class, 'destroy'] );
    Route::post('/category', [CategoryController::class, 'create'] );
    Route::put('/category/{id}', [CategoryController::class, 'update']);
    Route::delete('/category/{id}', [CategoryController::class, 'destroy'] );
    Route::post('/recurring-payments', [RecurringPaymentController::class, 'store']);
    Route::put('/recurring-payments/{id}', [RecurringPaymentController::class, 'update']);
    Route::delete('/recurring-payments/{id}', [RecurringPaymentController::class, 'destroy']);
    Route::post('/goal', [GoalController::class, 'create'] );
    Route::put('/goal/{id}', [GoalController::class, 'update'] );
    Route::delete('/goal/{id}', [GoalController::class, 'destroy'] );

});




Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});