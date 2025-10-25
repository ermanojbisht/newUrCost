<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assuming 'legacy_mysql' is configured in config/database.php for the old database
        $oldUsers = DB::connection('legacy_mysql')->table('users')->get(); // Assuming old users table is named 'users'

        foreach ($oldUsers as $oldUser) {
            DB::table('users')->insert([
                'id' => $oldUser->id, // Assuming old user table has 'id'
                'name' => $oldUser->name, // Assuming old user table has 'name'
                'email' => $oldUser->email, // Assuming old user table has 'email'
                'email_verified_at' => null, // Set to null or map if available in old system
                'password' => Hash::make($oldUser->password), // Assuming old user table has 'password'
                'remember_token' => null, // Set to null
                'created_at' => $oldUser->created_at ?? now(), // Assuming old user table has 'created_at'
                'updated_at' => $oldUser->updated_at ?? now(), // Assuming old user table has 'updated_at'
            ]);
        }
    }
}