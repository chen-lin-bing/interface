<?php

include 'Curl.class.php';
$time = time();
$curl = new Curl\Curl;
if (filesize('access_token.txt') == 0) {
	$api_url = 'https://api.weixin.qq.com/cgi-bin/token';
	$result = $curl->get($api_url,['grant_type'=>'client_credential','appid'=>'wx54adcbe69c9121f1','secret'=>'a656b1f04cc5278d04a7e522f48fe8ee']);
	$result_arr = json_decode($result,true);
	//var_dump($result_arr);
	//exit();
	if (isset($result_arr['access_token'])) {
		file_put_contents('access_token.txt', $result_arr['access_token']);
		return $result_arr['access_token'];
	}
	
}elseif((7200 + filemtime('access_token.txt')) <= $time){

	//超时间后重新设置access_token
	$api_url = 'https://api.weixin.qq.com/cgi-bin/token';
	$result = $curl->get($api_url,['grant_type'=>'client_credential','appid'=>'wx54adcbe69c9121f1','secret'=>'a656b1f04cc5278d04a7e522f48fe8ee']);
	$result_arr = json_decode($result,true);
	//echo 2;
	if (isset($result_arr['access_token'])) {
		file_put_contents('access_token.txt', $result_arr['access_token']);
		return $result_arr['access_token'];
	}
	
}else{
	$result_token = file_get_contents('access_token.txt');
	//var_dump($result_token);
	//var_dump(result_token)
	return $result_token;
}





//真实的业务开发中，你要把获取到的access_token保存到数据库


