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


class AdminController extends Controller
{

     public function __construct()
     {
       $auth_user = auth()->guard('sanctum')->user();// get logged in user details from auth data
       if(!$auth_user)
       {
         return response()->json(['status'=>'error', 'message'=>"user not found",  'data' =>''],400);
         die;
       }
       $user_id = $auth_user->id;

       //check if user is admin
       $userrole =  Userrole::where(['user_id'=>$user_id, 'role_id'=>1])->first();
       if($userrole == NULL)
       {
          echo json_encode(array('status'=>'error', 'message'=>"this is an admin action",  'data' =>''));
          die;
       }

     }

    //fetches all users
    public function viewusers()
    {
        $users = User::orderby('id','desc')->get();
        return response()->json(['status'=>'success', 'message'=>"users fetched successfully",  'data' =>$users],200);
    }

    //fetch one user using the id
    public function getuserbyid($id)
    {
        $user = User::where('id',$id)->first();
        if($user == null)
        {
            return response()->json(['status'=>'error', 'message'=>"user not found",  'data' =>''],400);
        }
        return response()->json(['status'=>'success', 'message'=>"user fetched successfully",  'data' =>$user],200);
    }



   //update user from admin end
    public function updateuser(Request $request)
    {



        $validator = Validator::make($request->all(),[
            'id' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'id of user  is required' , 'data'=>''],400);
        }


        $validator = Validator::make($request->all(),[
            'first_name' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'firstname is required',  'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'last_name' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'lastname is required',  'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'date_of_birth' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'date_of_birth is required',  'data'=>''],400);
        }

        $check_dob = $this->validateDate($request->date_of_birth);
        if($check_dob == false)
        {
            return response()->json(['status' => 'error' , 'message'=>'date_of_birth must be in the format Y-m-d',  'data'=>''],400);
        }

        $user = User::where('id',$request->id)->first();
        if($user == null)
        {
            return response()->json(['status'=>'error', 'message'=>"user not found",  'data' =>''],400);
        }

        $user->first_name =  ucwords($request->input('first_name'));
        $user->last_name =  ucwords($request->input('last_name'));
        $user->date_of_birth = $request->input('date_of_birth');
        $user->address = $request->input('address');
        return response()->json(['status'=>'success', 'message'=>"user updated successfully",  'data' =>$user],200);
    }


    //checks whether the front end sent a correct date format
    private function validateDate($date, $format = 'Y-m-d')
    {
          $d = \DateTime::createFromFormat($format, $date);
          return $d && $d->format($format) == $date;
    }


    //update the status of a user
    public function updateuserstatus(Request $request)
    {

            $validator = Validator::make($request->all(),[
                'id' => 'required',
            ]);
            if($validator->fails()){
            return response()->json(['status' => 'error' , 'message'=>'id of user  is required' , 'data'=>''],400);
            }

            $validator = Validator::make($request->all(),[
                'status' => 'required',
            ]);
            if($validator->fails()){
            return response()->json(['status' => 'error' , 'message'=>'status  is required' , 'data'=>''],400);
            }

            $user = User::where('id',$request->id)->first();
            if($user == null)
            {
                return response()->json(['status'=>'error', 'message'=>"user not found",  'data' =>''],400);
            }

            if($request->status != 'active' && $request->status != 'inactive')
            {
              return response()->json(['status'=>'error', 'message'=>"status should be active or inactive",  'data' =>''],400);
            }

            $user->status =  $request->status;
            $user->save();
            return response()->json(['status'=>'success', 'message'=>"status updated successfully",  'data' =>$user],200);
    }


    //createbook
    public function createbook(Request $request)
    {
          $validator = Validator::make($request->all(),[
              'title' => 'required',
          ]);
          if($validator->fails()){
          return response()->json(['status' => 'error' , 'message'=>'title is required' , 'data'=>''],400);
          }

          $validator = Validator::make($request->all(),[
              'access_level' => 'required',
          ]);
          if($validator->fails()){
          return response()->json(['status' => 'error' , 'message'=>'access_level is required' , 'data'=>''],400);
          }

          $validator = Validator::make($request->all(),[
              'title' => 'unique:books',
          ]);
          if($validator->fails()){
          return response()->json(['status' => 'error' , 'message'=>'title has been taken' , 'data'=>''],400);
          }


          $validator = Validator::make($request->all(),[
              'edition' => 'required',
          ]);
          if($validator->fails()){
          return response()->json(['status' => 'error' , 'message'=>'edition is required' , 'data'=>''],400);
          }

          $book = new Book();
          $book->title = $request->title;
          $book->edition = $request->edition;
          $book->description = $request->description;
          $book->prologue = $request->prologue;
          $book->access_level = $request->access_level;
          $book->save();
          return response()->json(['status'=>'success', 'message'=>"book added successfully",  'data' =>$book],200);
    }


    //update a book from admin end
    public function updatebook(Request $request)
    {
          $validator = Validator::make($request->all(),[
              'id' => 'required',
          ]);
          if($validator->fails()){
          return response()->json(['status' => 'error' , 'message'=>'id is required' , 'data'=>''],400);
          }

          $validator = Validator::make($request->all(),[
              'title' => 'required',
          ]);
          if($validator->fails()){
          return response()->json(['status' => 'error' , 'message'=>'title is required' , 'data'=>''],400);
          }



          $validator = Validator::make($request->all(),[
              'edition' => 'required',
          ]);
          if($validator->fails()){
          return response()->json(['status' => 'error' , 'message'=>'edition is required' , 'data'=>''],400);
          }

          $book = Book::where('id', $request->id)->first();
          if($book == null)
          {
              return response()->json(['status' => 'error' , 'message'=>'book not found' , 'data'=>''],400);
          }
          $book->title = $request->title;
          $book->edition = $request->edition;
          $book->description = $request->description;
          $book->prologue = $request->prologue;
          $book->save();
          return response()->json(['status'=>'success', 'message'=>"book updated successfully",  'data' =>$book],200);
    }


    //admin view all the books
    public function viewbooks()
    {
          $books =  Book::orderby('id','desc')->get();
          return response()->json(['status'=>'success', 'message'=>"books fetched successfully",  'data' =>$books],200);
    }


    //view details of a single book
    public function getbookbyid($id)
    {
          $book =  Book::where('id',$id)->first();
          if($book == null){ return response()->json(['status' => 'error' , 'message'=>'book not found' , 'data'=>''],400); }
          return response()->json(['status'=>'success', 'message'=>"book fetched successfully",  'data' =>$book],200);
    }


        //update the status of a  book
    public function updatebookstatus(Request $request)
    {

              $validator = Validator::make($request->all(),[
                  'id' => 'required',
              ]);
              if($validator->fails()){
              return response()->json(['status' => 'error' , 'message'=>'id is required' , 'data'=>''],400);
              }

              $validator = Validator::make($request->all(),[
                  'status' => 'required',
              ]);
              if($validator->fails()){
              return response()->json(['status' => 'error' , 'message'=>'status is required' , 'data'=>''],400);
              }

              $values_of_status =  array("available", "borrowed");

              $book =  Book::where('id',$request->id)->first();
              $book->status = $request->status;
              $book->save();

              if($book == null){ return response()->json(['status' => 'error' , 'message'=>'book not found' , 'data'=>''],400); }
              return response()->json(['status'=>'success', 'message'=>"book updated successfully",  'data' =>$book],200);
    }


    /*assign a book to an author
    * an author is a user, so the user_id in the users table will be used
    */
    public function assign_author_tobook(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'book_id' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'book_id is required' , 'data'=>''],400);
        }


        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'user_id is required' , 'data'=>''],400);
        }

        //check if user exist
        $checkuser = User::where('id',$request->user_id)->first();
        if($checkuser == null)
        {
            return response()->json(['status' => 'error' , 'message'=>'user not found' , 'data'=>''],400);
        }

        //check if book exist
        $checkbook = Book::where('id',$request->book_id)->first();
        if($checkbook == null)
        {
          return response()->json(['status' => 'error' , 'message'=>'book not found' , 'data'=>''],400);
        }

        $bk = new Bookauthor();
        $bk->user_id = $request->user_id;
        $bk->book_id = $request->book_id;
        $bk->save();
        return response()->json(['status'=>'success', 'message'=>"author assigned to book successfully",  'data' =>$bk],200);


    }


    //assign a subscription plan . this can be done  more than once with different books
    public function assign_subscription_plan_to_book(Request $request)
    {
      $validator = Validator::make($request->all(),[
          'book_id' => 'required',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'book_id is required' , 'data'=>''],400);
      }


      $validator = Validator::make($request->all(),[
          'subscriptionplan_id' => 'required',
      ]);
      if($validator->fails()){
      return response()->json(['status' => 'error' , 'message'=>'subscriptionplan_id is required' , 'data'=>''],400);
      }

      //check if user exist
      $checksub = Subscriptionplan::where('id',$request->subscriptionplan_id)->first();
      if($checksub == null)
      {
          return response()->json(['status' => 'error' , 'message'=>'subscriptionplan not found' , 'data'=>''],400);
      }

      //check if subscriptionplan exist
      $checkbook = Book::where('id',$request->book_id)->first();
      if($checkbook == null)
      {
        return response()->json(['status' => 'error' , 'message'=>'book not found' , 'data'=>''],400);
      }

      $bs = new Booksubscriptionplan();
      $bs->subscriptionplan_id = $request->subscriptionplan_id;
      $bs->book_id = $request->book_id;
      $bs->save();
      return response()->json(['status'=>'success', 'message'=>"subscription plan assigned to book successfully",  'data' =>$bs],200);

    }


    public function create_subscription_plan(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:subscriptionplans',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'name is required and must be unique' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'price' => 'required|numeric',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'price is required' , 'data'=>''],400);
        }


        $validator = Validator::make($request->all(),[
            'duration' => 'required|integer',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'duration is required' , 'data'=>''],400);
        }

        $added_at =   new \DateTime("Africa/Lagos");
        $formatted_added_at = $added_at->format("Y-m-d");

        $subscriptionplan = new Subscriptionplan();
        $subscriptionplan->name = $request->name;
        $subscriptionplan->price = $request->price;
        $subscriptionplan->duration = $request->duration;
        $subscriptionplan->added_at = $formatted_added_at;
        $subscriptionplan->save();
        return response()->json(['status'=>'success', 'message'=>"subscriptionplan added successfully",  'data' =>$subscriptionplan],200);
    }

    public function update_subscription_plan(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => 'required|numeric',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'id  is required' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'name is required and must be unique' , 'data'=>''],400);
        }

        $validator = Validator::make($request->all(),[
            'price' => 'required|numeric',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'price is required' , 'data'=>''],400);
        }


        $validator = Validator::make($request->all(),[
            'duration' => 'required|integer',
        ]);
        if($validator->fails()){
        return response()->json(['status' => 'error' , 'message'=>'duration is required' , 'data'=>''],400);
        }

        $added_at =   new \DateTime("Africa/Lagos");
        $formatted_added_at = $added_at->format("Y-m-d");

        $subscriptionplan = Subscriptionplan::where('id',$request->id)->first();
        $subscriptionplan->name = $request->name;
        $subscriptionplan->price = $request->price;
        $subscriptionplan->duration = $request->duration;
        $subscriptionplan->added_at = $formatted_added_at;
        $subscriptionplan->save();
        return response()->json(['status'=>'success', 'message'=>"subscriptionplan added successfully",  'data' =>$subscriptionplan],200);
    }


    public function view_subscription_plans()
    {
      $sb = Subscriptionplan::orderby('id', 'desc')->get();
      return response()->json(['status'=>'success', 'message'=>"subscriptionplans fetched successfully",  'data' =>$sb],200);
    }

    public function view_subscription_plan_by_id($id)
    {
      $sub = Subscriptionplan::where('id', $id)->first();
      if($sub == NULL)
      {
        return response()->json(['status' => 'error' , 'message'=>'subscription plan not found' , 'data'=>''],400);
      }
      return response()->json(['status'=>'success', 'message'=>"subscription plan fetched successfully",  'data' =>$sub],200);
    }



}
