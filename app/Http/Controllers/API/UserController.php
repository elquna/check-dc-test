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
use App\Models\Usersubscriptionplan;
use App\Models\Lending;
use Validator;

class UserController extends Controller
{
      public function notauthenticated()
      {
        return response()->json(['status'=>'error', 'message'=>'user not authenticated',  'data' =>''],401);
      }

      /*
      * This method is used to subscribe a user to a plan.
      * It first checks whether the user has an active plan
      */
      public function subscribe_to_a_plan(Request $request)
      {

            $validator = Validator::make($request->all(),[
                'subscriptionplan_id' => 'required',
            ]);
            if($validator->fails()){
            return response()->json(['status' => 'error' , 'message'=>'subscriptionplan_id  is required' , 'data'=>''],400);
            }

            $validator = Validator::make($request->all(),[
                'amount_paid' => 'required',
            ]);
            if($validator->fails()){
            return response()->json(['status' => 'error' , 'message'=>'amount_paid  is required' , 'data'=>''],400);
            }

          $auth_user = auth()->guard('sanctum')->user();// get logged in user details from auth data
          $user_id = $auth_user->id;
          $us =  Usersubscriptionplan::where(['user_id' => $user_id, 'status'=>'active'])->first();
          if($us != NULL)
          {
              return response()->json(['status'=>'error', 'message'=>'you have an active plan already',  'data' =>''],400);
          }

          $get_plan_details = Subscriptionplan::where('id', $request->subscriptionplan_id)->first();

          if($get_plan_details == NULL)
          {
              return response()->json(['status'=>'error', 'message'=>'plan does not exist',  'data' =>''],400);
          }

          if($request->amount_paid != $get_plan_details->price)
          {
            return response()->json(['status'=>'error', 'message'=>'insufficient amount paid',  'data' =>''],400);
          }


          $date = new \DateTime("Africa/Lagos");
          $start_date =   $date->format("Y-m-d");

          if($get_plan_details->name == 'Free')
          {
            $end_date = NULL;
          }
          else
          {
            $date2    = new \DateTime("Africa/Lagos");
            $end_date = $date2->modify( '+ '.$get_plan_details->duration.' days' );  // Adds days from the subscription plan duration
            $end_date->format( 'Y-m-d' );
          }

          $usp = new Usersubscriptionplan();
          $usp->subscriptionplan_id = $request->subscriptionplan_id;
          $usp->user_id = $user_id;
          $usp->start_date = $start_date;
          $usp->end_date = $end_date;
          $usp->save();
          return response()->json(['status'=>'success', 'message'=>"plan subscribed successfully",  'data' =>$usp],200);
      }


      public function get_active_subscription()
      {
          $auth_user = auth()->guard('sanctum')->user();// get logged in user details from auth data
          $user_id = $auth_user->id;
          $activesub = Usersubscriptionplan::where(['user_id'=>$user_id, 'status'=>'active'])->first();
          if($activesub == NULL)
          {
              return response()->json(['status'=>'error', 'message'=>'you dont have an active Subscription plan',  'data' =>''],400);
          }
          $sub_details = Subscriptionplan::where('id',$activesub->subscriptionplan_id)->first();
          $data = array("sub"=>$activesub, "details"=>$sub_details);
          return response()->json(['status'=>'success', 'message'=>"plan subscribed successfully",  'data' =>$data],200);
      }


      public function view_inactive_subscriptions()
      {
        $auth_user = auth()->guard('sanctum')->user();// get logged in user details from auth data
        $user_id = $auth_user->id;
        $activesub = Usersubscriptionplan::where(['user_id'=>$user_id, 'status'=>'inactive'])->get();

        foreach ($activesub as $key)
        {
            $key->details = Subscriptionplan::where('id',$key->subscriptionplan_id)->first();
        }

        return response()->json(['status'=>'success', 'message'=>"plan subscribed successfully",  'data' =>$activesub],200);
      }


      public function borrow_book(Request $request)
      {
            $date = new \DateTime("Africa/Lagos");
            $borrow_date =   $date->format("Y-m-d H:i:s");

            $validator = Validator::make($request->all(),[
            'book_id' => 'required',
            ]);
            if($validator->fails()){
            return response()->json(['status' => 'error' , 'message'=>'book_id  is required' , 'data'=>''],400);
            }

            $validator = Validator::make($request->all(),[
            'datetime_due' => 'required',
            ]);
            if($validator->fails()){
            return response()->json(['status' => 'error' , 'message'=>'datetime_due  is required' , 'data'=>''],400);
            }


            $auth_user = auth()->guard('sanctum')->user();// get logged in user details from auth data
            $user_id = $auth_user->id;

            //first check if book exist and is available
            $checkbook = Book::where(['book_id'=>$request->book_id, 'status'=>'available'])->first();
            if($checkbook == NULL)
            {
              return response()->json(['status' => 'error' , 'message'=>'book is not available' , 'data'=>''],400);
            }

            //get user active subscription , anc check whether the user has subscribed for a plans that the book has
            $activesub = Usersubscriptionplan::where(['user_id'=>$user_id, 'status'=>'active'])->first();
            if($activesub == NULL)
            {
                return response()->json(['status'=>'error', 'message'=>'you dont have an active Subscription plan',  'data' =>''],400);
            }





            $subcribed = false;
            $get_book_subcriptions = Booksubscriptionplan::where('book_id', $request->book_id)->get();
            foreach($get_book_subcriptions as $onesub)
            {

                if($onesub->subscriptionplan_id == $activesub->subscriptionplan_id)// if user has subscribed a plan that the book has
                {
                    $subcribed  = true;

                    break;
                }
            }

            if($subcribed  == false)
            {
                return response()->json(['status'=>'error', 'message'=>'you dont have a Subscription plan for the requested book',  'data' =>''],400);
            }

            $datetime_due = new \DateTime($request->datetime_due);
            $datetime_due->format('Y-m-d H:i:s');

            $check_datetimedue = $this->validateDatetime($request->datetime_due);
            if($check_datetimedue == false)
            {
                return response()->json(['status' => 'error' , 'message'=>'datetime_due must be in the format Y-m-d H:i:s',  'data'=>''],400);
            }



            $lending =  new Lending();
            $lending->user_id = $user_id;
            $lending->book_id = $request->book_id;
            $lending->datetime_borrowed = $borrow_date;
            $lending->datetime_due = $datetime_due;
            $lending->subscriptionplan_id = $activesub->subscriptionplan_id;
            $lending->save();
            return response()->json(['status'=>'success', 'message'=>"book borrowed successfully",  'data' =>$lending],200);

      }// ends borrow_book method


      public function return_book(Request $request)
      {
        $auth_user = auth()->guard('sanctum')->user();// get logged in user details from auth data
        $user_id = $auth_user->id;

        $date = new \DateTime("Africa/Lagos");
        $datetime_returned =   $date->format("Y-m-d H:i:s");

        $validator = Validator::make($request->all(),[
        'book_id' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'book_id  is required' , 'data'=>''],400);
        }

        $lending = Lending::where(['book_id'=>$request->book_id, 'user_id'=>$user_id, 'status'=>'active'])->first();
        if($lending == NULL)
        {
          return response()->json(['status' => 'error' , 'message'=>'lending not found' , 'data'=>''],400);
        }

        if($lending->datetime_due > $datetime_returned)
        {
          $aquiredpoint = 2;
        }
        else if($lending->datetime_due == $datetime_returned)
        {
          $aquiredpoint = 1;
        }
        else{
            $aquiredpoint = -1;
        }

        $lending->points = $aquiredpoint;
        $lending->status = 'inactive';
        $lending->save();

        //update book to available
        $book = Book::where('id',$request->book_id)->first();
        $book->status = 'available';
        $book->save();

        //now effect change in user property regarding points
        $user = User::where('id', $user_id)->first();
        $user->lendingpoint =  $user->lendingpoint + $aquiredpoint;
        $user->save();

        return response()->json(['status'=>'success', 'message'=>"book returned successfully",  'data' =>$lending],200);

      }

      public function view_returned_books()
      {
        $auth_user = auth()->guard('sanctum')->user();// get logged in user details from auth data
        $user_id = $auth_user->id;
        $lending =  Lending::where(['user_id' => $user_id, 'status'=>'inactive'])->get();
        foreach ($lending as $onelend)
        {
           $onelend->book = Book::where('id',$onelend->book_id)->first();
        }
        return response()->json(['status'=>'success', 'message'=>"returned books fetched successfully",  'data' =>$lending],200);
      }


      public function view_borrowed_books()
      {
        $auth_user = auth()->guard('sanctum')->user();// get logged in user details from auth data
        $user_id = $auth_user->id;
        $lending =  Lending::where(['user_id' => $user_id, 'status'=>'active'])->get();
        foreach ($lending as $onelend)
        {
           $onelend->book = Book::where('id',$onelend->book_id)->first();
        }
        return response()->json(['status'=>'success', 'message'=>"borrowed books fetched successfully",  'data' =>$lending],200);
      }



      //checks whether the front end sent a correct date format
      private function validateDatetime($date, $format = 'Y-m-d H:i:s')
      {
            $d = \DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) == $date;
      }
}
