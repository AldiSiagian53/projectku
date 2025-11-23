<?php
// Temporary test route - DELETE THIS FILE AFTER TESTING
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::get('/test-user', function() {
    // Check if user exists
    $user = User::where('name', 'Kel3')->orWhere('email', 'kel3@ecogreen.com')->first();
    
    if ($user) {
        $passwordCheck = Hash::check('Kel3', $user->password);
        return response()->json([
            'user_found' => true,
            'name' => $user->name,
            'email' => $user->email,
            'password_match' => $passwordCheck,
            'hashed_password' => $user->password
        ]);
    }
    
    // Create user if not exists
    $newUser = User::create([
        'name' => 'Kel3',
        'email' => 'kel3@ecogreen.com',
        'password' => Hash::make('Kel3'),
    ]);
    
    return response()->json([
        'user_created' => true,
        'name' => $newUser->name,
        'email' => $newUser->email,
    ]);
});


