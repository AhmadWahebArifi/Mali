<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'name',
        'email',
        'date_of_birth',
        'father_name',
        'address',
        'phone_number',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    // Additional model methods can be added here
}
