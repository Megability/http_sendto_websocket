<?php

$table = new swoole_table(1024);
$table->column('fd', swoole_table::TYPE_INT);
$table->create();

$server = new swoole_websocket_server("0.0.0.0", 9502);
$server->table = $table;

$server->on('open', function (swoole_websocket_server $server, $request) {
    //$token = $request->get['token'];echo $token;
    echo "\nconnection open: " . $request->fd . "\n";
    if($request->header['upgrade'] == 'websocket'){//只存储websocket-client
        if(!$server->table->exist($request->fd)){
            $server->table->set($request->fd, array('fd' => $request->fd));//获取客户端id插入table
        }
    }
});

$server->on('message', function (swoole_websocket_server $server, $frame) {
    /*
    echo "\nreceive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}" . "\n";
    foreach ($server->table as $u) {
        $server->push($u['fd'], $frame->data );//消息广播给所有客户端
    }
    */
});

$server->on('close', function ($server, $fd) {
    echo "\nconnection close: " . $fd . "\n";
    $server->table->del($fd);//从table中删除断开的id
    //foreach ($server->table as $u) {
    //    var_dump($u); //输出整个table
    //}
});

$server->on('Request', function($request, $response) use($server) {
    echo "\nconnection open: " . $request->fd . " msg: ".$request->post["msg"] . "\n";
    foreach ($server->table as $u) {
        if($request->fd != $u['fd']){
            $server->push($u['fd'], $request->post["msg"]);
        }
    }

    $response->end("success");
});

$server->start();