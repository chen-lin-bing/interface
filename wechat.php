<?php
/**
  * wechat php
  */

//my token
// define("TOKEN", "askjdjskadlk");
// $wechatObj = new wechatCallbackapiTest();
// $wechatObj->valid();



include 'wechatCallbackapiTest.class.php';
//$url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=ACCESS_TOKEN&type=TYPE';
//$curl = new Curl\Curl;
//$curl->post($url,['access_token'=>$access_token,'type'=>]);


$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
//$z = $wechatObj->uploadTmp('/home/wwwroot/wechat.bingobingo.xin/thumb1.jpg','thumb');
//var_dump($z);
//$wechatObj->getAccessToken();
$wechatObj->getAccessToken();
$wechatObj->responseMsg();
/*class a{
    public  $Content = '和田玉';
}*/

//$xmlString= new a();
//var_dump($xmlString);
//$wechatObj->_doText($xmlString);
//$wechatObj->valid();






