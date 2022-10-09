<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller {

    //登录显示
    public function index() {
        //判断用户是否已经登录
        return view("web.login.login");
    }

    //登录 别名 web.login 根据别名生成url route(别名)
    public function login(Request $request) {
        //表单验证
        //        $post= $this->validate($request,[
        //            'username'=>'required',
        //            'password'=>'required'
        //        ],[ //自定义提示
        //            'username.required'=>'用户名格式错误'
        //        ]);
        $post = $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);
        //登录
        $bool = auth()->guard('web')->attempt($post);
        if ($bool) {
            //跳转到后台页面
            return redirect(route('web.chat'));
        }
        //withErrors 把信息写入到验证错误提示中 特殊的session 闪存
        //闪存 从设置之后 只会在第一个http请求中获取到
        return redirect(route('web.login'))->withErrors(['error' => '登录失败']);
    }

}
