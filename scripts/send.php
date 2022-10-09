<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

//新建连接
$connection = new AMQPStreamConnection('192.168.123.20', 5672, 'guest', 'guest');

//创建信道
$channel = $connection->channel();

//交换机名称
$exchangeName = 'taskExchange';

//队列名称
$queueName = 'queueName';

//路由名称
$routingKey = 'task';

//创建交换机
$channel->exchange_declare($exchangeName, //交换机名称
	'direct', //交换器类型，常见的如fanout、direct、topic、headers四种。
	$passive = false, //如果设置true，存在则返回OK,否则就报错。设置false存在返回ok，不存在则自动创建
	$durable = false, //设置是否持久化。设置true表示持久化，反之非持久化。持久化可以将交换器存盘，在服务器重启的时候不会丢失相关信息。
	$auto_delete = true, //设置是否自动删除。设置true表示自动删除。自动删除的前提：至少有一个队列或交换器与这个交换器绑定，之后所有与这个交换器绑定的队列或交换器都与此解绑。不要错误的理解：“当与此交换器连接的客户端都断开时，RabbitMQ会自动删除本交换器”。
	$internal = false, //设置是否是内置的。设置true表示是内置的交换器，客户端程序无法直接发送消息到这个交换器中，只能通过交换器路由到交换器这个方式。
	$nowait = false, //如果为true则表示不等待服务器回执信息.函数将返回NULL,可以提高访问速度。
	$arguments = array(), //其他一些结构化参数
	$ticket = null
);

//创建队列
$channel->queue_declare($queueName, //队列名称
	$passive = false, //果设置true，存在则返回OK,否则就报错。设置false存在返回ok，不存在则自动创建
	$durable = false, //设置是否持久化。设置true表示持久化，反之非持久化。持久化可以将交换器存盘，在服务器重启的时候不会丢失相关信息。
	$exclusive = false, //设置是否排他。排他消费者,即这个队列只能由一个消费者消费.适用于任务不允许进行并发处理的情况
	$auto_delete = true, ////设置是否自动删除。设置true表示自动删除。自动删除的前提：至少有一个队列或交换器与这个交换器绑定，之后所有与这个交换器绑定的队列或交换器都与此解绑。不要错误的理解：“当与此交换器连接的客户端都断开时，RabbitMQ会自动删除本交换器”。
	$nowait = false, //如果为true则表示不等待服务器回执信息.函数将返回NULL,可以提高访问速度。
	$arguments = array(), //其他一些结构化参数
	$ticket = null
);

//绑定消息交换机和队列
$channel->queue_bind($queueName, //队列名称
	$exchangeName, //交换器名称
	$routingKey, //用来绑定队列和交换器的路由键
	$nowait = false, //如果为true则表示不等待服务器回执信息.函数将返回NULL,可以提高访问速度。
	$arguments = array(), //定义绑定的一些参数
	$ticket = null
);

//消息
$data = [
	'msg_id'      => uniqid(),
	'create_time' => time(),
	'notify_url'  => '127.0.0.1/asd/qwe'
];

//发布消息
$msg = new AMQPMessage(
	json_encode($data),
	['delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT]
);

//immediate会影响镜像队列的性能，一般用TTL或DLX的方法替代。
//如果发送的消息不设置mandatory参数，那么消息在未被路由的情况下会丢失，如果添加了必须加returnlistener进行监听，如果不想复杂化代码又想要不让消息丢失，
//那么可以使用备份交换器，这样可以把未被路由的消息存储在rabbitmq中，在有需要的时候在去处理它。
//
//队列可以设置TTL,这种情况下，队列中的消息都是设置的这个过期时间
//如果社会之ttl为0：消息可以直接投递到消费者，否则该消息会被直接丢弃。
//也可以给每条消息设置ttl，例如：
//new AMQPMessage($_GET['d'], ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,'expiration'=>5000]);
//这个消息过期设置不是在过期后就直接删除，而是在队列数据读取出来的时候发现已经过期时间，在删除该消息
$channel->basic_publish($msg, //生产的消息
	$exchangeName, //交换器的名称，指明消息需要发送到哪个交换器中。如果设置为空字符串，则消息会被发送到RabbitMQ默认的交换器中。
	$routingKey, //用来绑定队列和交换器的路由键
	$mandatory = true, //设置为true时，交换器无法根据自身的类型和路由键找到一个符合条件额队列，那么RabbitMQ会调用Basic.Return命令将消息返回给生产者。当设置为false的时，出现上述问题，则消息直接被丢弃。
	$immediate = false, //RabbitMQ3.0版本开始去掉对immediate参数的支持。
	$ticket = null
);

$wait = true;

//监听消息模板
$returnListener = function (
	$replyCode,
	$replyText,
	$exchange,
	$routingKey,
	$message
) use(&$wait){
	$wait = false;

	echo "return:".PHP_EOL,
		"replyCode  :$replyCode".PHP_EOL,
		"replyText  :$replyText".PHP_EOL,
		"exchange   :$exchange".PHP_EOL,
		"routingKey :$routingKey".PHP_EOL,
		"message    :$message->body".PHP_EOL;
};

//设置监听模式
$channel->set_return_listener($returnListener);

while ($wait) {
	//消息循环输出到控制台
	$channel->wait();
}

$channel->close();
$connection->close();
