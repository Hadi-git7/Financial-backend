<?php

use App\Http\Controllers\IncomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ExpenseController;

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
Route::get('/income', [IncomeController::class, 'index']);
Route::get('/income/{id}', [IncomeController::class, 'show']);
Route::get('/income/search/{title}', [IncomeController::class, 'search']);
Route::get('/category', [CategoryController::class, 'index']);
Route::get('/category/{id}', [CategoryController::class, 'show']);
Route::get('/category/search/{title}', [CategoryController::class, 'search']);
Route::get('/expense', [ExpenseController::class, 'index']);
Route::get('/expense/{id}', [ExpenseController::class, 'show']);
Route::get('/expense/search/{title}', [ExpenseController::class, 'search']);
Route::get('/goal', [GoalController::class, 'index']);


// Protected Routes
Route::group(['middleware' =>['auth:sanctum']], function (){
    Route::post('/logout',[AdminController::class, 'logout']);
    Route::post('/income', [IncomeController::class, 'store'] );
    Route::put('/income/{id}', [IncomeController::class, 'update'] );
    Route::delete('/income/{id}', [IncomeController::class, 'destroy'] );
    Route::put('/admin/{id}', [AdminController::class, 'update']);
    Route::post('/admin', [AdminController::class, 'createAdmin']);
    Route::delete('/admin/{id}', [AdminController::class, 'destroy'] );
    Route::post('/category', [CategoryController::class, 'create'] );
    Route::put('/category/{id}', [CategoryController::class, 'update']);
    Route::delete('/category/{id}', [CategoryController::class, 'destroy'] );
    Route::post('/expense', [ExpenseController::class, 'store']);
    Route::put('/expense/{id}', [ExpenseController::class, 'update']);
    Route::delete('/expense/{id}', [ExpenseController::class, 'destroy']);
    Route::post('/goal', [GoalController::class, 'create'] );
    Route::put('/goal/{id}', [GoalController::class, 'update'] );
    Route::delete('/goal/{id}', [GoalController::class, 'destroy'] );

});




Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});