<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function notauthenticated()
  {
    return response()->json(['status'=>'error', 'message'=>'user not authenticated',  'data' =>''],401);
  }
}
