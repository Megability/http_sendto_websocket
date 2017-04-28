<?php

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$server = new swoole_websocket_server("0.0.0.0", 9502);

$server->on('open', function (swoole_websocket_server $server, $request) use($redis) {
    //$token = $request->get['token'];echo $token;
    echo "\nconnection open: " . $request->fd . "\n";
    if($request->header['upgrade'] == 'websocket'){//只存储websocket-client
        $arr = json_decode($redis->get("fd"), true);
        if(empty($arr)){$arr = array();}
        if(!in_array($request->fd, $arr)){
           array_push($arr, $request->fd);
           $str = json_encode($arr);
           $redis->set("fd", $str);
        } 
    }
});

$server->on('message', function (swoole_websocket_server $server, $frame) use($redis) {
    /*
    echo "\nreceive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}" . "\n";
    $arr = json_decode($redis->get("fd"), true);
    if(empty($arr)){$arr = array();}
    foreach ($arr as $v) {
       if($frame->fd != $v){//不给自己发送
          $server->push($v, $frame->data);
       }
    }
    */
});

$server->on('close', function ($server, $fd) use($redis) {
    echo "\nconnection close: " . $fd . "\n";
    $arr = json_decode($redis->get("fd"), true);
    if(empty($arr)){$arr = array();}
    $point = array_keys($arr, $fd, true);
    if(!empty($point[0])){array_splice($arr, $point['0'],1);}//数组中删除
    $redis->set("fd", json_encode($arr));
});

$server->on('Request', function($request, $response) use($server,$redis) {
    echo "\nconnection open: " . $request->fd . " msg: ".$request->post["msg"] . "\n";
    $arr = json_decode($redis->get("fd"), true);
    if(empty($arr)){$arr = array();}
    foreach ($arr as $v) {
        if($request->fd != $v){
            $server->push($v, $request->post["msg"]);
        }
    }

    $response->end("success");
});

$server->start();