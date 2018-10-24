<?php

include 'Curl.class.php';
$access_token = include 
$curl = new Curl\Curl;
https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=ACCESS_TOKEN
        $url='https://api.weixin.qq.com/cgi-bin/material/add_news'.$this->getAccessToken().'&type='.$type;
        $data = array(
            'media' => '@'.$file,
            );
        $result = $curl>post($url,['access_token'=>$access_token,'type'=>'']);
        $result_obj = json_decode($result);
        return $result_obj;