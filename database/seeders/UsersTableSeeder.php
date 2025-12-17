<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            "name"=> "Ronald Danilo Rodriguez Quintero",
            "document"=> "1000455655",
            "birthdate"=> "2002-03-15",
            "gender"=> "Masculino",
            "phone"=> "3214194839",
            "email"=> "ronalddanilo1234@gmail.com",
            "password"=> Hash::make("Isvi2025*"),
        ]);    
    }
}
