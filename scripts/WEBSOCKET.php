<?php
//
//use Swoole\Http\Request;
//use Swoole\Http\Response;
//use Swoole\WebSocket\CloseFrame;
//use Swoole\Coroutine\Http\Server;
//use function Swoole\Coroutine\run;
//
//run(function () {
//    $server = new Server('127.0.0.1', 9503, false);
//    $server->handle('/websocket', function (Request $request, Response $ws) {
//        $ws->upgrade();
//        while (true) {
//            $frame = $ws->recv();
//            if ($frame === '') {
//                $ws->close();
//                break;
//            } elseif ($frame === false) {
//                echo 'errorCode: ' . swoole_last_error() . "\n";
//                $ws->close();
//                break;
//            } else {
//                if ($frame->data == 'close' || get_class($frame) === CloseFrame::class) {
//                    $ws->close();
//                    break;
//                }
//                $ws->push($frame->data);
//            }
//        }
//    });
//
//    $server->start();
//});
