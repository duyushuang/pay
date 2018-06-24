<?php
/**
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class http_head{
	private $args;
	public $cookie;
	public function __construct($str){
		$rn = $httpInfo = $args = array();
		$sp = explode("\r\n", $str);
		$sp2 = explode(' ', $sp[0]);
		$httpInfo['version'] = substr($sp2[0], 5);
		array_shift($sp2);
		$httpInfo['statusNum']  = $sp2[0];
		$httpInfo['statusNum1'] = substr($sp2[0],0,1);
		$httpInfo['status']     = $httpInfo['statusNum1'] == '2';
		array_shift($sp2);
		$httpInfo['info']   = implode('', $sp2);
		array_shift($sp);
		foreach($sp as $v){
			$f = strpos ( $v, ':' );
			if ($f> 0) {
				$key = trim(substr ($v, 0, $f));
				
				$key = strtr($key, '-', '_');
				$key = strtolower($key);
				$val = trim(substr($v, $f + 1));
				$args[$key][] = $val;
			}
		}
		$rn['info'] = $httpInfo;
		$rn['args'] = $args;
		if (!empty($rn['info'])) {
			foreach ($rn['info'] as $k => $v) {
				$this->$k = $v;
			}
		}
		$this->args = $rn['args'];
		$this->cookie = new http_cookie();
		if (!empty($this->args['set_cookie'])) {
			foreach ($this->args['set_cookie'] as $v) {
				$this->cookie->set($v);
			}
		}
	}
	public function __get($key){
		return $this->get($key, 0);
	}
	public function get($key, $index = 0){
		if (!empty($this->args[$key][$index])) return $this->args[$key][$index];
		return NULL;
	}
	public function getCookie($url){
		return $this->cookie->get($url);
	}
}
?>