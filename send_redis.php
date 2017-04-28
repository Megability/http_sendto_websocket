<?php
$post_data = array("msg"=>"msg: ".$_GET['msg']);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://ip:port/socket_redis.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$ret = curl_exec($ch);
$curl_errno = curl_errno($ch);
$curl_error = curl_error($ch);
$retcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
curl_close($ch);
echo $ret."\n";