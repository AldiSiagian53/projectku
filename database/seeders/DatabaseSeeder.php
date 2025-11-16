<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create user Kel3
        User::updateOrCreate(
            ['name' => 'Kel3'],
            [
                'email' => 'kel3@ecogreen.com',
                'password' => \Illuminate\Support\Facades\Hash::make('Kel3'),
            ]
        );
    }
}
