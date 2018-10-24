<?php
include 'Curl.class.php';
use Curl\Curl;
class test extends Curl
{
	public function uploadTmp($file,$type){
        $url='https://api.weixin.qq.com/cgi-bin/media/upload?access_token=13_nt1UHcShaolFFE3ld0eA9yC6F0a86MtPZqrDcEEsA4HjL1o5pileFQSXVKjVGREoGvYgALSBBqLDr9YKeZD7PuKROnJSkjS3dFGbQPn8WyAZoPHWLqIGFnIzwhbWljRqQ3fTjBfC5Sed5P56HQIiAGAVEW&type='.$type;

        $data = array('media'=>new CURLFile('/home/wwwroot/wechat.bingobingo.xin/2.jpg'));
/*
        $data ['media']  = '@'.$file;
        $data = array(
            'media' => '@'.$file,
            );*/
        $result = $this->post($url,$data);

        $result_obj = json_decode($result);
        return $result_obj;
    }
}
$test = new test();
$c = $test->uploadTmp('2.jpg','image');
var_dump($c);




    