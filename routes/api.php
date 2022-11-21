<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\AuthenticationController;

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

Route::post('signup', [AuthenticationController::class, 'signup']);
Route::post('signin', [AuthenticationController::class, 'signin']);

Route::middleware('auth:sanctum')->group( function () {

  //admin routes  manage users
  Route::get('viewusers', [AdminController::class, 'viewusers']);
  Route::get('getuserbyid/{id}', [AdminController::class, 'getuserbyid']);
  Route::post('updateuser', [AdminController::class, 'updateuser']);
  Route::post('updateuserstatus', [AdminController::class, 'updateuserstatus']);

  //admin routes  manage books
  Route::post('createbook', [AdminController::class, 'createbook']);
  Route::post('updatebook', [AdminController::class, 'updatebook']);
  Route::get('viewbooks', [AdminController::class, 'viewbooks']);
  Route::get('getbookbyid/{id}', [AdminController::class, 'getbookbyid']);
  Route::post('updatebookstatus', [AdminController::class,  'updatebookstatus']);
  Route::post('assign_author_to_book', [AdminController::class,  'assign_author_tobook']);
  Route::post('assign_subscription_plan_to_book', [AdminController::class,  'assign_subscription_plan_to_book']);

  //general routes
  Route::post('subscribe_to_a_plan', [UserController::class,  'subscribe_to_a_plan']);
  Route::get('get_active_subscription', [UserController::class,  'get_active_subscription']);
  Route::get('view_inactive_subscriptions', [UserController::class,  'view_inactive_subscriptions']);
  Route::post('borrow_book', [UserController::class,  'borrow_book']);
  Route::post('return_book', [UserController::class,  'return_book']);
  Route::get('view_borrowed_books', [UserController::class,  'view_borrowed_books']);
  Route::get('view_returned_books', [UserController::class,  'view_returned_books']);

  //author routes
  Route::post('author/createbook', [UserController::class, 'createbook']);
  Route::post('author/updatebook', [UserController::class, 'updatebook']);
  Route::post('author/updatebookstatus', [UserController::class,  'updatebookstatus']);



});
