<?php

namespace Curl;

class Curl
{
	private $ch;

	public function __construct()
	{
		$this->ch = curl_init();
		$this->ready();
	}


	public function get($url, $params = [])
	{
		if(count($params) > 0)
		{
			$url .= '?'.http_build_query($params);
		}
		$this->setUrl($url);
		return $this->exec();
	}

	public function post($url, $params)
	{
		//$this->setopt(CURLOPT_SAFE_UPLOAD,false)
		$this->setOpt(CURLOPT_POST, 1);
		$this->setOpt(CURLOPT_POSTFIELDS, $params);
		$this->setUrl($url);
		return $this->exec();
	}	

	public function put()
	{
		$this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
		$this->setOpt(CURLOPT_POSTFIELDS, $params);
		$this->setUrl($url);
		return $this->exec();
	}

	public function delete()
	{
		$this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
		$this->setOpt(CURLOPT_POSTFIELDS, $params);
		$this->setUrl($url);
		return $this->exec();
	}

	private function ready()
	{
		$this->setOpt(CURLOPT_RETURNTRANSFER);
		$this->setOpt(CURLOPT_HEADER, 0);
	}


	private function setOpt($option, $value = 1)
	{
		curl_setopt($this->ch, $option, $value);
	}

	private function setUrl($url)
	{
		$this->setOpt(CURLOPT_URL, $url);
	}

	private function exec()
	{
		$result = curl_exec($this->ch);
		if($result)
		{
			return $result;
		}else{

			return [
				'errno' => curl_errno($this->ch),
				'error' => curl_error($this->ch)
			];
		}
	}
}

// $curl = new Curl();
// $params['appid'] = '11100110';
// $params['secret'] = 'kjjdsadsadkj89dsa';
// var_dump($curl->get('http://localhost/code/1607/0310/91porn/api/auth.php', $params));