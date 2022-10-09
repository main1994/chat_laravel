<?php


class NOSSLWS {
	private $ws = null;

	public function __construct() {
		//创建WebSocket Server对象，监听0.0.0.0:9503端口
		$this->ws = new Swoole\WebSocket\Server('0.0.0.0', 9503, SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);
		$this->ws->set(
			[
				'ssl_cert_file' => '__DIR__fullchain.pem',//证书位置
				'ssl_key_file' => '__DIR__privkey.pem',//证书位置
				// 'open_http2_protocol' => true,
			]
		);
		//回调函数绑定
		$this->ws->on('request', [$this,
		                          'onOpen']);
		$this->ws->on('message', [$this,
		                          'onMessage']);
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
	public function onMessage($ws, $frame){
		echo "信息: {$frame->data}\n";
		foreach($ws->connections as $fd){
			//$fd指单前用户，$frame->fd外来连接的用户
			if($fd == $frame->fd){
				$ws->push($fd, "我: {$frame->data}");
			}else{
				$ws->push($fd, "对方: {$frame->data}");
			}
		}
	}

	//监听WebSocket连接关闭事件
	public function onClose($ws, $fd) {
		echo "服务端-{$fd} is closed\n";
	}
}

new NOSSLWS();