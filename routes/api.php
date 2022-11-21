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
  Route::post('assignauthortobook', [AdminController::class,  'assignauthortobook']);



});
