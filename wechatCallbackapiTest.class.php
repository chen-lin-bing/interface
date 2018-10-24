<?php

/**
 * Class wechatCallbackapiTest
 * 如果验证成功，那么久返回echoStr完成验证
 */
include 'Curl.class.php';
use Curl\Curl;
define("TOKEN", "chenlinbing");
class wechatCallbackapiTest
{

    
    private $_msg_template = array(
        'text' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%s]]></Content></xml>',//文本回复XML模板
        'image' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[image]]></MsgType><Image><MediaId><![CDATA[%s]]></MediaId></Image></xml>',//图片回复XML模板
        'music' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[music]]></MsgType><Music><Title><![CDATA[%s]]></Title><Description><![CDATA[%s]]></Description><MusicUrl><![CDATA[%s]]></MusicUrl><HQMusicUrl><![CDATA[%s]]></HQMusicUrl><ThumbMediaId><![CDATA[%s]]></ThumbMediaId></Music></xml>',//音乐模板
        'news' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[news]]></MsgType><ArticleCount>%s</ArticleCount><Articles>%s</Articles></xml>',// 新闻主体
        'news_item' => '<item><Title><![CDATA[%s]]></Title><Description><![CDATA[%s]]></Description><PicUrl><![CDATA[%s]]></PicUrl><Url><![CDATA[%s]]></Url></item>',//某个新闻模板
    );

    

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        //$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $xml_str = file_get_contents("php://input");
        //php7通过这个来接受post数据,原先的$GLOBALS["HTTP_RAW_POST_DATA"]不在使用

         if(empty($xml_str)){
            die('');
        }
        if(!empty($xml_str)){
            // 解析该xml字符串，利用simpleXML
            libxml_disable_entity_loader(true);
            //禁止xml实体解析，防止xml注入
              $request_xml = simplexml_load_string($xml_str, 'SimpleXMLElement', LIBXML_NOCDATA);
            //判断该消息的类型，通过元素MsgType
            switch ($request_xml->MsgType){
                case 'event':
                    //判断具体的时间类型（关注、取消、点击）
                    $event = $request_xml->Event;
                      if ($event=='subscribe') { // 关注事件
                          $this->_doSubscribe($request_xml);
                      }elseif ($event=='CLICK') {//菜单点击事件
                          $this->_doClick($request_xml);
                      }elseif ($event=='VIEW') {//连接跳转事件
                          $this->_doView($request_xml);
                      }else{

                      }
                    break;
                case 'text'://文本消息
                    $this->_doText($request_xml);
                    break;
                case 'image'://图片消息
                    $this->_doImage($request_xml);
                    break;
                /*case 'voice'://语音消息
                    $this->_doVoice($request_xml);
                    break;
                case 'video'://视频消息
                    $this->_doVideo($request_xml);
                    break;
                case 'shortvideo'://短视频消息
                    $this->_doShortvideo($request_xml);
                    break;
                case 'location'://位置消息
                    $this->_doLocation($request_xml);
                    break;
                case 'link'://链接消息
                    $this->_doLink($request_xml);
                    break;*/
            }        
        }        
    }

    /**
     * 发送文本信息
     * @param  [type] $to      目标用户ID
     * @param  [type] $from    来源用户ID
     * @param  [type] $content 内容
     * @return [type]          [description]
     */
    private function _msgText($to, $from, $content) {
        $response = sprintf($this->_msg_template['text'], $to, $from, time(), $content);
        die($response);
    }

    

    //发送音乐
    private function _msgMusic($to, $from, $music_url, $hq_music_url, $thumb_media_id, $title='', $desc='') {
        $response = sprintf($this->_msg_template['music'], $to, $from, time(), $title, $desc, $music_url, $hq_music_url, $thumb_media_id);
        die($response);
    }

    //发送图片
    private function _msgImage($to,$from,$file){
        //判断是不是media_id
        
        /*if ((259200 + filemtime('media_id.txt')) <= $time) {
        	$is_id = false;
        } else {
        	$is_id = true;
        }*/
        
        /*if($is_id){
            $media_id = $file;
        }else{
            // 上传图片到微信公众服务器，获取mediaID
            $result_obj = $this->uploadTmp($file, 'image');
            $media_id = $result_obj->media_id;
        }*/
            //拼凑xml图片发给微信平台，然后平台返回图片给用户
            $media_id = $this->uploadTmp($file,'image');
            $response = sprintf($this->_msg_template['image'],$to,$from,time(),$media_id);
            die($response);
    }

    //上传
    public function uploadTmp($file,$type){
    	$curl = new Curl();
    	$time = time();
    	$type_arr = array('image'=>'','thumb'=>'thumb_');
    	//var_dump('media_id_'.$type.'.txt');
    	if (filesize('media_id_'.$type.'.txt') == 0) {

			$url='https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$this->getAccessToken().'&type='.$type;
        	$data = array('media'=>new CURLFile($file));
			// /home/wwwroot/wechat.bingobingo.xin/2.jpg
        	$result = $curl->post($url,$data);
        	$result_obj = json_decode($result,true);
        	//var_dump($result_obj) ;
        	$type_tmp = $type_arr[$type];
        	//var_dump($type_tmp);
        	if (isset($result_obj[$type_tmp.'media_id'])) {
        		$file_name =  'media_id_'.$type.'.txt';
	        	file_put_contents($file_name, $result_obj[$type_tmp.'media_id']);
	        	return $result_obj['media_id'];
	        }

	    }elseif((259200 + filemtime('media_id_'.$type.'.txt')) <= $time){
			//超时间后重新设置access_token
			$url='https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$this->getAccessToken().'&type='.$type;
        	$data = array('media'=>new CURLFile($file));
			// /home/wwwroot/wechat.bingobingo.xin/2.jpg
        	$result = $curl->post($url,$data);
        	$result_obj = json_decode($result,true);
        	//var_dump($result_obj) ;
        	$type_tmp = $type_arr[$type];
        	//var_dump($type_tmp);
        	if (isset($result_obj[$type_tmp.'media_id'])) {
        		$file_name =  'media_id_'.$type.'.txt';
	        	file_put_contents($file_name, $result_obj[$type_tmp.'media_id']);
	        	return $result_obj['media_id'];
	        }
		}else{
			$result_token = file_get_contents('media_id_'.$type.'.txt');
			//var_dump($result_token);
			return $result_token;
		}
    }

    //获取token
    public function getAccessToken()
    {

    	$curl = new Curl();
       	$time = time();
		//$curl = new Curl\Curl;
		if (filesize('access_token.txt') == 0) {

			$api_url = 'https://api.weixin.qq.com/cgi-bin/token';
			$result = $curl->get($api_url,['grant_type'=>'client_credential','appid'=>'wx54adcbe69c9121f1','secret'=>'a656b1f04cc5278d04a7e522f48fe8ee']);
			$result_arr = json_decode($result,true);
			
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
			return $result_token;
		}
    }



    public function _doText($request_xml){
    //接受文本信息
    $content = $request_xml->Content;
    if('图片' == $content){
            $file = '/www/wechat.bingobingo.xin/2.jpg';
            $this->_msgImage($request_xml->FromUserName, $request_xml->ToUserName,$file);
        }elseif('音乐' == $content)
        {
        	$music_url='http://www.xiami.com/song/1776099904';
            $hq_music_url='http://www.xiami.com/song/1776099904';
            $file = '/www/wechat.bingobingo.xin/thumb1.jpg';
            $thumb_media_id = $this->uploadTmp($file,'thumb');
            $title = '大鱼';
            $desc = '周深';
            $this->_msgMusic($request_xml->FromUserName, $request_xml->ToUserName, $music_url, $hq_music_url, $thumb_media_id, $title, $desc);
        }else{
        			$url = 'http://www.baike.com/wiki/'. $content .'&prd=button_doc_entry';
            		$curl = new Curl();

              		$html = $curl->get($url);

              		//file_put_contents($content.'.html', $html);
              		//var_dump($html);
              		//echo $html;
              		//var_dump(preg_match('/<div class="summary" name="anchor" id="anchor"><p>.*<\/p>/', $html, $array));

                    //var_dump(preg_match('/<div id="anchor" name="anchor" class="summary"><p>.*<\/p>/', $html, $array1));
                    //exit();


              		
                    if (preg_match('/<div class="summary" name="anchor" id="anchor"><p>.*<\/p>/', $html, $array)) {
                        //var_dump($array);
                        $content = strip_tags(trim($array[0]));
                    }elseif (preg_match('/<div id="anchor" name="anchor" class="summary"><p>.*<\/p>/', $html, $array1)) {
                        //var_dump($array1);
                        //专门匹配那些城市的
                        $content = strip_tags(trim($array1[0]));
                    }
                    else{
                        $content = '输入别的常用词试试';
                    }
              		
       
                    
              		$this->_msgText($request_xml->FromUserName, $request_xml->ToUserName, $content);

                	//$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);

                	//file_put_contents('test.xml', $resultStr);
                	//echo $resultStr;

        	/*$curl = new Curl();
        	//$content = $request_xml->Content;
        	//$url = 'http://www.tuling123.com/openapi/api?key='.$this->_appkey.'&info='.$content.'&userid='.$request_xml->FromUserName;
        	$url = 'http://www.tuling123.com/openapi/api;'
            // $data['key'] = $this->_appkey;//
            // $data['info'] = $content;//用户输入的内容
            // $data['userid'] = $request_xml->FromUserName;
            $response_content = json_decode($curl->get($url,['key'=>'09124d5979df4999b73def19016f1898','info'=>$content,'userid'=>$request_xml->FromUserName]);
            //$response_content->code决定返回的是什么
            //100000  文本 text
            //200000 链接  text+url
            //302000   新闻 text +list(新闻列表，里面有：article,source,icon,detailurl)分别是标题、来源、图片、详情地址
            //308000   菜谱  text+name+info+detailurl+icon
            $this->_msgText($request_xml->FromUserName, $request_xml->ToUserName, $response_content->text);*/
        }
    }

    //图灵机器人接入         
          

            
 	
	

	//关注后做的事件
	private function _doSubscribe($request_xml){
        //处理该关注事件，向用户发送关注信息
        $content = '你好,欢迎关注xiao八的测试公众号,你可以回复图片、音乐关键字,也可以问我一些简单的词语,如输入中秋节';
        $this->_msgText($request_xml->FromUserName, $request_xml->ToUserName, $content);
    }

    //验证相关的

    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
                
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}
