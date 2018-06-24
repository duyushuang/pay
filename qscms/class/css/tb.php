<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class css_tb{
	private $arr, $isFormat;
	public $count, $type, $cacheName;
	public function __construct($isFormat = false, $datas = array()){
		$this->isFormat = $isFormat;
		$this->arr = $datas;
		$this->type = 'code';
		$this->cacheName = '';
	}
	public function add($key, $val = false){
		if (is_array($key) && $val === false) {
			foreach ($key as $k => $v) {
				(!isset($this->arr[$k])) && $this->arr[$k] = getLib('css.aTb', $this->isFormat);
				$this->arr[$k]->add($v);
			}
		} else {
			is_array($key) || $key = array($key);
			//is_array($val) || $val = array($val);
			foreach ($key as $k) {
				(!isset($this->arr[$k])) && $this->arr[$k] = getLib('css.aTb', $this->isFormat);
				$this->arr[$k]->add($val);
			}
		}
		$this->count = count($this->arr);
		return $this;
	}
	public function setType($type, $cacheName = ''){
		$tps = array('code', 'file');
		in_array($type, $tps) || $type = $tps[0];
		$this->type = $type;
		$this->cacheName = $cacheName;
		return $this;
	}
	public function allAdd($key, $val){
		foreach ($this->arr as $v) $v->add($key, $val);
	}
	public function getCode(){
		$code = '';
		$ln = chr(13).chr(10);
		$t  = chr(9);
		$flag1 = $this->isFormat ? $ln : '';
		$flag2 = $this->isFormat ? $t: '';
		foreach ($this->arr as $k => $v) {
			$code && $code .= $flag1;
			$code .= $k.'{'.$flag1.$v->getCode($flag2).$flag1.'}';
		}
		return $code;
	}
	public function getCacheUrl($cacheName = ''){
		$cacheName || $cacheName = $this->cacheName;
		$code = $this->getCode();
		$cacheName || $cacheName = md5($code);
		$saveDir = d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/css'));
		$saveFile = $saveDir.$cacheName.'.css';
		if (file_exists($saveFile)) {
			return u($saveFile);
		}
		if (file::createFolder($saveDir)) {
			file::write($saveFile, $code);
			return u($saveFile);
		} else {
			throw new e_qscms('创建CSS缓存目录失败<br />缓存目录：'.u($saveDir).'<br />请检查上级目录是否可写');
			return false;
		}
	}
	public function __toString(){
		return $this->getCode();
	}
}
?>