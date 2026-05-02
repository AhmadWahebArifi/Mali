<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-users', function() {
    $users = \App\Models\User::all();
    $output = "Users in database:<br>";
    foreach ($users as $user) {
        $output .= "ID: {$user->id}, Email: {$user->email}, Approved: " . ($user->is_approved ?? 'null') . "<br>";
    }
    return $output;
});
