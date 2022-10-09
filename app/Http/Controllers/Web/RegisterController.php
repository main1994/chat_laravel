<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Controller;
use App\Models\Models\User;
use Illuminate\Http\Request;

//邮件类
use Mail;
use \Illuminate\Mail\Message;

class RegisterController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view("web.register.register");
    }


    public function register(Request $request) {
        //表单验证
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
            'email'     => 'required'
        ]);
        //提交token
        $token = $request->all()['_token'];
        if (!$token) {
            return redirect(route('web.register'))->withErrors(['error' => 'token失效']);
        }
        //校验是否缓存token
        $bool = Redis::get('log_token:' . $request->all()['username'] . ':' . $token);
        if ($bool) {
            return redirect(route('web.register'))->withErrors(['error' => '请稍后再注册']);
        }
        Redis::set('log_token:' . $request->all()['username'] . ':' . $token, 1);
        Redis::expire('log_token:' . $request->all()['username'] . ':' . $token, 60);
        //获取表单数据
        $post = $request->except(['_token',
                                  $request->path()]);
        $password = $post['password'];
        $post['password'] = bcrypt($password);
        $post['last_login_token'] = $token;
        //添加用户入库
        $userModel = User::create($post);
        //        $userModel = User::insert($post);
        //发邮件给用户  匿名函数传入外部变量use
        Mail::send('mail.useradd', compact('userModel','password'), function (Message $message) use ($userModel) {
            //发送对象
            $message->to($userModel->email);
            //主题
            $message->subject('开通账号通知');
        });
        return redirect(route('web.login'))->with('success', '注册成功');
    }

}
