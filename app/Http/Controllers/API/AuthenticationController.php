<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Userrole;
use Validator;

class AuthenticationController extends Controller
{

            /*
             *This end point creates  new user, it also add assign the user a reader role
            * The validation is custom, it takes into consideration all the inputs and returns
            * an appriopriate message to the front end whenever there is a bad request so that it
            * will be easy for the front end guy and the user to know what went wrong
            */
            public function signup(Request $request)
            {
                      $validator = Validator::make($request->all(),[
                          'email' => 'required',
                      ]);
                      if($validator->fails()){
                      return response()->json(['status' => 'error' , 'message'=>'email  is required' , 'data'=>''],400);
                      }

                      $validator = Validator::make($request->all(),[
                          'email' => 'unique:users',
                      ]);
                      if($validator->fails()){
                      return response()->json(['status' => 'error' , 'message'=>'email  has been taken' , 'data'=>''],400);
                      }

                      $validator = Validator::make($request->all(),[
                          'password' => 'required',
                      ]);
                      if($validator->fails()){
                      return response()->json(['status' => 'error' , 'message'=>'password  is required' , 'data'=>''],400);
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
                          'username' => 'required',
                      ]);
                      if($validator->fails()){
                      return response()->json(['status' => 'error' , 'message'=>'username is required',  'data'=>''],400);
                      }

                      $validator = Validator::make($request->all(),[
                          'username' => 'unique:users',
                      ]);
                      if($validator->fails()){
                      return response()->json(['status' => 'error' , 'message'=>'username is has been taken',  'data'=>''],400);
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



                      $user = new User();
                      $user->email = $request->input('email');
                      $user->first_name =  ucwords($request->input('first_name'));
                      $user->last_name =  ucwords($request->input('last_name'));
                      $user->password =  bcrypt($request->input('password'));
                      $user->username = $request->input('username');
                      $user->date_of_birth = $request->input('date_of_birth');
                      $user->address = $request->input('address');


                      if($user->save())
                      {
                          $token =  $user->createToken('AppAuth')->plainTextToken;

                          //now assign reader role to the user according to the requirement. we seeded the database and the reader role has id = 2
                          $userrole = new Userrole();
                          $userrole->user_id = $user->id;
                          $userrole->role_id = 2;
                          $userrole->save();
                          return response()->json(['status'=>'success', 'message'=>"user created successfully",  'data' =>$user, 'token'=>$token],200);
                      }
                      else
                      {
                          return response()->json(['status'=>'error', 'message'=>'cannot create user',  'data' =>''],400);
                      }

            }// ends signup function


          //checks whether the front end sent a correct date format
          private function validateDate($date, $format = 'Y-m-d')
          {
                $d = \DateTime::createFromFormat($format, $date);
                return $d && $d->format($format) == $date;
          }


          public function signin(Request $request)
          {
              if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                  $authUser = Auth::user();

                  $authUser['token'] =  $authUser->createToken('AppAuth')->plainTextToken;


                  return response()->json(['status' => 'success' , 'message'=>'user logged in' , 'data'=>$authUser],200);
              }
              else{
                  return response()->json(['status' => 'error' , 'message'=>'wrong details' , 'data'=>''],400);
              }
          }
}
