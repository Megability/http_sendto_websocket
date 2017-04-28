<?php
$client = new swoole_http_client('127.0.0.1', 9502);

$client->on('message', function ($_cli, $frame) {
    //var_dump($frame);
});

$client->upgrade('/', function ($client) use($argv) {
    $client->push($argv[1]);//swoole_http_client::upgrade(): async-io must use in cli environment
    die;//只发送一次
});