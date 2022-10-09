<?php


class TCP {
	private $server = null;

	public function __construct() {
		$this->server = new Swoole\Server('127.0.0.1', 9500);
		$this->server->set(array(
			//进程数
			'worker_num'  => 4,
			//最大请求数
			'max_request' => 50,
			//后台运行
			//			'daemonize' => 1
		));
		//回调函数绑定
		$this->server->on('Connect', [$this,
		                              'onConnect']);
		$this->server->on('Receive', [$this,
		                              'onReceive']);
		$this->server->on('Close', [$this,
		                            'onClose']);
		//服务启动
		$this->server->start();
	}

	//连接进入事件
	public function onConnect($server, $fd) {
		echo "客户端id: {$fd} 连接.\n";
	}

	//数据接收事件
	public function onReceive($server, $fd, $reactor_id, $data) {
		$server->send($fd, "发送的数据: {$data}");
	}

	//连接关闭事件
	public function onClose($server, $fd) {
		echo "客户端id: {$fd} 关闭.\n";
	}

}

new TCP();