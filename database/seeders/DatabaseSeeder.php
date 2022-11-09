<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('users')->insert([
            'first_name' => 'master',
            'last_name' => 'admin',
            'username' => 'masteradmin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('admin'),
            'type' => 'admin',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('users')->insert([
            'first_name' => 'sample',
            'last_name' => 'user',
            'username' => 'user123',
            'email' => 'user@mail.com',
            'password' => Hash::make('user'),
            'type' => 'user',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('posts')->insert([
            'user_id' => '2',
            'title' => 'Blog Sample 1',
            'body' => 'Blog sample number one',
            'summary' => 'Sample summary for this blog',
            'tags' => 'Travel',
            'image' => '/sample/image/location',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
