<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Subscriptionplan;
use App\Models\Userrole;

class Universalseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

            $added_at =   new \DateTime("Africa/Lagos");
            $formatted_added_at = $added_at->format("Y-m-d");

              Role::create([

                  'role' => 'admin',
              ]);

              Role::create([

                  'role' => 'reader',
              ]);

              Role::create([

                  'role' => 'author',
              ]);


              User::create([
                'email'=>'admin',
                'password'=> bcrypt("password"),
                'first_name'=>'admin',
                'last_name'=>'admin',
                'username'=>'admin',
                'date_of_birth'=>'1987-06-08'
              ]);


              Userrole::create([
                'user_id'=>1,
                'role_id'=>1
              ]);

              Userrole::create([
                'user_id'=>1,
                'role_id'=>2
              ]);

              Subscriptionplan::create([
                'name'=>'Free',
                'duration'=>'',
                'price'=>0,
                'added_at'=> $formatted_added_at,

              ]);

              Subscriptionplan::create([
                'name'=>'Silver',
                'duration'=>30,
                'price'=>1000,
                'added_at'=> $formatted_added_at,

              ]);

              Subscriptionplan::create([
                'name'=>'Bronze',
                'duration'=>30,
                'price'=>700,
                'added_at'=> $formatted_added_at,

              ]);

              Subscriptionplan::create([
                'name'=>'Gold',
                'duration'=>30,
                'price'=>1500,
                'added_at'=> $formatted_added_at,
              
              ]);


    }
}
