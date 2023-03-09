<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();

        $user->name = 'Administrator';
        $user->email = 'admin@admin';
        $user->picture = '';
        $user->isActive = '1';
        $user->position = 'Administrator';
        $user->password = bcrypt('password');
        //$user->remember_token = str_random(10);
        $user->save();

        $user->attachRole('Administrator');
    }
}
