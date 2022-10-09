@extends('web.common.main')

@section('css')
    <style>
        #text {
            margin-top: 20px;
            width: 500px;
            height: 500px;
            border: 1px solid #000;
            overflow-y: scroll;
        }

        #chat {
            text-align: center;
        }
    </style>
@endsection
@section('cnt')
    <div style="margin:0 auto;padding-top: 100px;">
        <div id="text" style="margin:0 auto;">
            <div id="chat">聊天室</div>
        </div>
        <div style="margin:0 auto;width: 500px;margin-top: 10px;">
        <input type="text" id="input" style="height: 40px;width: 390px;">
        <button type="button" class="btn btn-success radius size-L" onclick="send();">&nbsp;发&nbsp;&nbsp;&nbsp;&nbsp;送&nbsp;
        </button>
        </div>
    </div>
@endsection

@section('js')
    <script>
        var socket;
        var lockReconnect = false; //避免socket重复连接
        var url = "ws://www.chat.com:8700/websocket";//利用nginx进行端口转发

        if (!window.WebSocket) {
            window.WebSocket = window.MozWebSocket;
        }

        if (window.WebSocket) {//判断当前设备是否支持websocket
            socket = new WebSocket(url);
            socket.onclose = function (event) {
                reconnect(url);  //尝试重新连接
                console.log(event);
                console.log('连接关闭')
            };
            socket.onerror = function () {
                reconnect(url);   //尝试重新连接
                console.log(event);
                console.log('连接错误')
            };
            socket.onopen = function (event) {
                heartCheck.reset().start(); //心跳检测重置
                console.log(event);
                console.log('连接开启')
            };
            socket.onmessage = function (event) {
                heartCheck.reset().start(); //心跳检测重置
                console.log(event);
                console.log('消息接收到了，只要有接收到消息，连接都是正常的')
                let data = JSON.parse(event.data);
                if (data[1] != 'Msg') {
                    let str = data[0] == '我：' ? "<h6 style='text-align: right;'>" : "<h6 style='text-align: left;'>";
                    $('#chat').append(str + data[0] + data[1] + "</h6>");
                }

            };
        } else {
            alert.log("你的浏览器不支持WebSocket！");
        }

        // 监听窗口关闭事件，当窗口关闭时，主动去关闭websocket连接，防止连接还没断开就关闭窗口，server端会抛异常。
        window.onbeforeunload = function () {
            socket.close();
        }

        // 重新连接
        function reconnect(url) {
            if (lockReconnect) return;
            lockReconnect = true;
            setTimeout(function () { //没连接上会一直重连，设置延迟避免请求过多
                socket = new WebSocket(url);
                lockReconnect = false;
            }, 2000);
        }

        //心跳检测
        var heartCheck = {
            timeout: 55000, //1分钟发一次心跳,时间设置小一点较好（50-60秒之间）
            timeoutObj: null,
            serverTimeoutObj: null,
            reset: function () {
                clearTimeout(this.timeoutObj);
                clearTimeout(this.serverTimeoutObj);
                return this;
            },
            start: function () {
                var self = this;
                this.timeoutObj = setTimeout(function () {
                    //这里发送一个心跳，后端收到后，返回一个心跳消息，
                    //onmessage拿到返回的心跳就说明连接正常
                    socket.send("Msg");
                    self.serverTimeoutObj = setTimeout(function () {//如果超过一定时间还没重置，说明后端主动断开了
                        socket.close(); //如果onclose会执行reconnect，我们执行socket.close()就行了.如果直接执行reconnect 会触发onclose导致重连两次
                    }, self.timeout)
                }, this.timeout)
            }
        }

        function send() {
            let input = $('#input').val();
            socket.send(input);
            $('#input').val('');
        }


        // 重新连接
        function reconnect(url) {
            if (lockReconnect) return;
            lockReconnect = true;
            setTimeout(function () { //没连接上会一直重连，设置延迟避免请求过多
                socket = new WebSocket(url);
                lockReconnect = false;
            }, 2000);
        }
    </script>
@endsection
