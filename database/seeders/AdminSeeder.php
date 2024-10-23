<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      User::create([
        'firstname'=>'Africred',
        'lastname'=>'Africred',
        'email'=>'africred@gmail.com',
        'password'=>Hash::make('pass123'),
        'status'=>'approved',
        'user_type'=>'admin',
      ]);
    }
}
