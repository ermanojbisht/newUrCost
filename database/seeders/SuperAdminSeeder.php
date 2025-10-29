<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

            DB::table('users')->insert([
                'id' => 1,
                'name' => 'Manoj Kumar Bisht',
                'email' => 'er_manojbisht@yahoo.com',
                'password' => bcrypt('12345678'),
            ]);

            $user = User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@urcost.com',
                'password' => bcrypt('12345678')
            ]);

            $role = Role::firstOrCreate(['name' => 'super-admin']);
            $user->assignRole($role);

            $user = User::create([
                'name' => 'Seeder User1',
                'email' => 'seeder1@example.com',
                'password' => bcrypt('12345678'),
            ]);

            $user = User::create([
                'name' => 'Seeder User2',
                'email' => 'seeder2@example.com',
                'password' => bcrypt('12345678'),
            ]);

            $user = User::create([
                'name' => 'Seeder User3',
                'email' => 'seeder3@example.com',
                'password' => bcrypt('12345678'),
            ]);

            $user = User::create([
                'name' => 'Seeder User4',
                'email' => 'seeder4@example.com',
                'password' => bcrypt('12345678'),
            ]);

            $user = User::create([
                'name' => 'Seeder User5',
                'email' => 'seeder5@example.com',
                'password' => bcrypt('12345678'),
            ]);

            User::create(['id' => 1000], [
                'name' => 'System User',
                'email' => 'system@example.com',
                'password' => bcrypt('12345678'),
            ]);

    }
}
