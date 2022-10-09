@extends('web.common.main')

@section('cnt')
    <div class="header"></div>
    <div class="loginWraper">
        <div id="loginform" class="loginBox">
            {{--引入验证--}}
            @include('web.common.validate')
            @include('web.common.msg')

            <form class="form form-horizontal" action="{{route('web.register')}}" method="post">
                {{--{{csrf_field()}}--}}
                @csrf
                <div class="row cl">
                    <label class="form-label col-xs-3"><i class="Hui-iconfont">&#xe60d;</i></label>
                    <div class="formControls col-xs-8">
                        <input id="username" name="username" type="text" placeholder="账户" class="input-text size-L">
                    </div>
                </div>
                <div class="row cl">
                    <label class="form-label col-xs-3"><i class="Hui-iconfont">&#xe60e;</i></label>
                    <div class="formControls col-xs-8">
                        <input id="password" name="password" type="password" placeholder="密码" class="input-text size-L">
                    </div>
                </div>
                <div class="row cl">
                    <label class="form-label col-xs-3"><i class="Hui-iconfont">&#xe603;</i></label>
                    <div class="formControls col-xs-8">
                        <input id="mail" name="email" type="text" placeholder="邮箱" class="input-text size-L">
                    </div>
                </div>
                {{--            <div class="row cl">--}}
                {{--                <div class="formControls col-xs-8 col-xs-offset-3">--}}
                {{--                    <input class="input-text size-L" type="text" placeholder="验证码" onblur="if(this.value==''){this.value='验证码:'}" onclick="if(this.value=='验证码:'){this.value='';}" value="验证码:" style="width:150px;">--}}
                {{--                    <img src=""> <a id="kanbuq" href="javascript:;">看不清，换一张</a> </div>--}}
                {{--            </div>--}}
                {{--            <div class="row cl">--}}
                {{--                <div class="formControls col-xs-8 col-xs-offset-3">--}}
                {{--                    <label for="online">--}}
                {{--                        <input type="checkbox" name="online" id="online" value="">--}}
                {{--                        使我保持登录状态</label>--}}
                {{--                </div>--}}
                {{--            </div>--}}
                <div class="row cl">
                    <div class="formControls col-xs-8 col-xs-offset-3">
                        <button type="submit" class="btn btn-success radius size-L">&nbsp;提&nbsp;&nbsp;&nbsp;&nbsp;交&nbsp;
                        </button>
                        <a href="{{route('web.login')}}" class="btn btn-warning radius size-L">&nbsp;返&nbsp;&nbsp;&nbsp;&nbsp;回&nbsp;</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')
@endsection
