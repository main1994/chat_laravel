<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as AuthUser;

class Login extends AuthUser
{
    use HasFactory;

    //设置添加的字段 create 添加数据有效
    //拒绝不添加的字段
    protected $guarded = [];

    //隐藏字符
    protected $hidden = ['password'];
}
