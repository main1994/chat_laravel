<?php


class UDP {
	private $server = null;

	public function __construct() {
		$this->server = new Swoole\Server('127.0.0.1', 9501, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);
		//回调函数绑定
		$this->server->on('Packet', [$this,
		                             'onPacket']);
		//服务启动
		$this->server->start();
	}

	//数据接收事件
	public function onPacket($server, $data, $clientInfo) {
		var_Dump($clientInfo);
		$server->sendto($clientInfo['address'], $clientInfo['port'], "Server：{$data}");
	}

}

new UDP();