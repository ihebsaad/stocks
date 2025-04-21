<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Seeders;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seeder = Seeders::where('class_name',__CLASS__)->count();

        //Check if the seeder exist in the DB
        //if ($seeder==0) {
        if (User::count()==0) {
            $users =
            [
                'id'                => 1,
                'name'              => 'admin',
                'user_type'              => 'admin',
                'email'             => 'admin@zahome.tn',
                'password'          => bcrypt('admin@zahome.tn'),
                'email_verified_at' => date('Y-m-d H:i:s')
            ];


        $user=User::create($users);
        //Creation of the seeder in the DB;
        Seeders::create(array('class_name'=>__CLASS__));
        }

    }
}
