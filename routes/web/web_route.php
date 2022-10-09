<?php

//后台路由
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\RegisterController;
use App\Http\Controllers\Web\ChatController;

Route::group(['prefix'    => 'web',
              'namespace' => 'Web'], function () {

//    //发送邮件
    Route::get('user/email', function () {
        //发送文本
        //        Mail::raw('邮件测试',function(\Illuminate\Mail\Message $message){
        //            //获取回调方法中的形参
        ////            dump(func_get_args());
        //            //发送对象
        //            $message->to('554778689@qq.com','CT');
        //            //主题
        //            $message->subject('测试邮件');
        //        });

        //发送富文本
        //参数1 模板
        //参数2 视图数据
        Mail::send('mail.adduser', ['user' => '测试'], function (\Illuminate\Mail\Message $message) {
            //发送对象
            $message->to('554778689@qq.com', 'CT');
            //主题
            $message->subject('测试邮件');
        });
    });

    //login
    Route::get('login', [LoginController::class,
                         'index'])->name('web.login');

    Route::post('login', [LoginController::class,
                          'login'])->name('web.login');

    //register
    Route::get('register', [RegisterController::class,
                         'index'])->name('web.register');

    Route::post('register', [RegisterController::class,
                         'register'])->name('web.register');

    //chat
    Route::get('chat', [ChatController::class,
                            'index'])->name('web.chat')->middleware('login');

    //    Route::resource('login','\App\Http\Controllers\Web\LoginController',['as'=>'web'])->middleware('checklogin');

});
