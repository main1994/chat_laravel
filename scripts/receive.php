<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

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

//我们可以使用basic_qos方法，并设置prefetch_count=1。这样是告诉RabbitMQ，再同一时刻，不要发送超过1条消息给一个工作者（worker），
//直到它已经处理了上一条消息并且作出了响应。这样，RabbitMQ就会把消息分发给下一个空闲的工作者（worker）
$channel->basic_qos(null, //单最大未确认消息的字节数
	50, //一次最多能处理多少条消息
	null //是否将上面设置true应用于channel级别还是取false代表Con级别
);

$callback = function ($message) {
    echo 'received = ', $message->body . "\n";
	//使用basic_nack批量处理拒绝消息
	//requeue 如果为true则消息从新进入队列，如果false，消息删除队列
	//$message->basic_recover(requeue); //用来请求mq从新发送还未被确认的消息。如果true则没被确认的消息会从新加入队列，false则同一条消息会被分配给与之前相同的消费者。

	$message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']); //响应ack

	//$message->delivery_info['channel']->basic_reject($message->delivery_info['delivery_tag'],false[ture(放回队列头部),false(丢弃消息)]);//消息拒绝
};

//获取队列当中的消息，绑定监听
$channel->basic_consume($queueName, //队列名称
	$consumer_tag = '', //消费者标签。用来区分多个消费者
	$no_local = false, //AMQP的标准,但rabbitMQ并没有做实现
	$no_ack = false, //收到消息后,是否不需要回复确认即被认为被消费；设置为true,表示自动应答；设置为false表示手动应答
	$exclusive = false, //设置是否排他。排他消费者,即这个队列只能由一个消费者消费.适用于任务不允许进行并发处理的情况
	$nowait = false, //如果为true则表示不等待服务器回执信息.函数将返回NULL,但若排他开启的话,则必须需要等待结果的,如果两个一起开就会报错
	$callback, //callback函数
	$ticket = null,
	$arguments = array() //一些额外配置
);

//监听方法
while ($channel->is_consuming()) {
	$channel->wait(); //阻塞消费
}

$channel->close();
$connection->close();
