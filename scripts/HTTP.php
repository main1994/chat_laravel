<?php


class HTTP {
	private $http = null;

	public function __construct() {
		$this->http = new Swoole\Http\Server('0.0.0.0', 9502);
		$this->http->set([
			'document_root' => '/www/swoole_item/static/',
			'enable_static_handler' => true //开启静态文件请求处理功能
		]);
		//回调函数绑定
		$this->http->on('request', [$this,
		                              'onRequest']);
		//服务启动
		$this->http->start();
	}

	//数据接收事件
	public function onRequest($request, $response) {
		$response->header('Content-Type', 'text/html; charset=utf-8');
		$response->end('<h1>Hello Swoole. #' . rand(1000, 9999) . '</h1>');
	}
}

new HTTP();