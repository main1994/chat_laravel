<?php


class NOSSLWS {
    private $ws = null;

    public function __construct() {
        //创建WebSocket Server对象，监听0.0.0.0:9502端口
        $this->ws = new Swoole\WebSocket\Server('0.0.0.0', 9503);
        //设置异步任务的工作进程数量
        $this->ws->set([
            'task_worker_num' => 4,
            'daemonize'       => true
        ]);
        //回调函数绑定
        $this->ws->on('request', [$this,
                                  'onOpen']);
        $this->ws->on('message', [$this,
                                  'onMessage']);
        $this->ws->on('task', [$this,
                               'onTask']);
        $this->ws->on('finish', [$this,
                                 'onFinish']);
        $this->ws->on('close', [$this,
                                'onClose']);
        //服务启动
        $this->ws->start();
    }

    //监听WebSocket连接打开事件
    public function onOpen($ws, $request) {
        $ws->push($request->fd, "欢迎客户端： {$request->fd} \n");
    }

    //监听WebSocket消息事件
    public function onMessage($ws, $frame) {
        echo "信息: {$frame->data}\n";
        foreach ($ws->connections as $fd) {
            $ws->task([
                'fd'      => $fd,
                'message' => json_encode([$fd == $frame->fd ? '我：' : "{$frame->fd}号用户：",
                                          $frame->data],JSON_UNESCAPED_UNICODE)
            ]);
        }
    }

    //处理异步任务(此回调函数在task进程中执行)
    public function onTask($ws, $task_id, $reactor_id, $data) {
        $ws->push($data['fd'], $data['message']);
    }

    //处理异步任务的结果(此回调函数在worker进程中执行)
    public function onFinish($ws, $task_id, $data) {
        $ws->finish("{$data} -> OK");
    }

    //监听WebSocket连接关闭事件
    public function onClose($ws, $fd) {
        echo "服务端-{$fd} is closed\n";
    }
}

new NOSSLWS();
