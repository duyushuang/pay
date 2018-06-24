<?php
/**
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class http_cookie{
	private $datas = array();
	private function splitData($data){
		if (($f = strpos($data, '=')) !== false) {
			$key = substr($data, 0, $f);
			$val = substr($data, $f + 1);
			return array($key, $val);
		}
		return false;
	}
	public function set($str){
		substr($str, -1) == ';' && $str = substr($str, 0, -1);
		$sp = explode('; ', $str);
		$value = array_shift($sp);
		$dataSp = $this->splitData($value);
		if (!$dataSp) return false;
		list($dataKey, $dataValue) = $dataSp;
		$path = false;
		$domain = false;
		$expireTime = false;
		$isDel = false;
		foreach ($sp as $data) {
			if ($dataSp = $this->splitData($data)) {
				list($key, $val) = $dataSp;
				switch (strtolower($key)) {
					case 'path':
						$path = $val;
					break;
					case 'domain':
						$domain = $val;
					break;
					case 'expires':
						$expireTime = time::GMTToTimestamp($val);
						if ($expireTime < time()) $isDel = true;
					break;
					
				}
			} else return false;
		}
		if ($isDel) {
			if (!empty($this->datas[$dataKey])) unset($this->datas[$dataKey]);
		} else {
			$this->datas[$dataKey] = array('val' => $dataValue, 'domain' => $domain, 'path' => $path, 'expire' => $expireTime);
		}
		//print_r($this->datas);
	}
	public function get($url){
		$urlInfo = parse_url($url);
		empty($urlInfo['host']) && ($urlInfo['host'] = $urlInfo['path']) && $urlInfo['path'] = '/';
		empty($urlInfo['path']) && $urlInfo['path'] = '/';
		$host = $urlInfo['host'];
		$path = $urlInfo['path'];
		$rs = '';
		foreach ($this->datas as $k => $v) {
			if ($v['domain']) {
				if (substr($host, -strlen($v['domain'])) == $v['domain']) {
					if ($v['path']) {
						if (substr($path, 0, strlen($v['path'])) == $v['path']) {
							$rs && $rs .= '; ';
							$rs .= $k.'='.$v['val'];
						}
					} else {
						$rs && $rs .= '; ';
						$rs .= $k.'='.$v['val'];
					}
				}
			} else {
				$rs && $rs .= '; ';
				$rs .= $k.'='.$v['val'];
			}
		}
		return $rs;
	}
}
?>