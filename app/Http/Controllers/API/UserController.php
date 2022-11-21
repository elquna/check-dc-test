<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Userrole;
use App\Models\Book;
use App\Models\Bookauthor;
use App\Models\Subscriptionplan;
use App\Models\Booksubscriptionplan;
use Validator;

class UserController extends Controller
{
      public function notauthenticated()
      {
        return response()->json(['status'=>'error', 'message'=>'user not authenticated',  'data' =>''],401);
      }

      public function subscribe_to_a_plan(Request $request)
      {
          $loggedinuser = auth()->guard('sanctum')->user();
          $id = $loggedinuser->id;
      }
}
