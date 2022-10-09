<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as AuthUser;

class User extends AuthUser {
    use HasFactory;

    protected $fillable = ['username','password','email','last_login_token'];
//    protected $guarded = ['password'];
//    protected $hidden = ['password'];
}
