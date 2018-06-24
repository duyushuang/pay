<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class css_aTb{
	private $arr, $isFormat;
	public function __construct($isFormat = false, $datas = array()){
		$this->arr      = $datas;
		$this->isFormat = $isFormat;
		$this->code = '';
	}
	public function add($key, $val = false){
		if ($val !== false) {
			is_array($key) || $key = array($key);
			is_array($val) || $val = array($val);
			foreach ($key as $k) {
				(!isset($this->arr[$k]) || !is_array($this->arr[$k])) && $this->arr[$k] = array();
				foreach ($val as $v) {
					$this->arr[$k][] = $v;
				}
			}
		} elseif (is_array($key)) {
			foreach ($key as $k => $v) {
				$val = is_array($v) ? $v : array($v);
				(!isset($this->arr[$k]) || !is_array($this->arr[$k])) && $this->arr[$k] = array();
				foreach ($val as $v) {
					$this->arr[$k][] = $v;
				}
			}
		} else {
			$sp = explode(';', trim($key));
			foreach ($sp as $v0) {
				if ($v0 = trim($v0)) {
					$sp2 = explode(':', $v0);
					$k = trim($sp2[0]);
					array_shift($sp2);
					$v = implode(':', $sp2);
					(!isset($this->arr[$k]) || !is_array($this->arr[$k])) && $this->arr[$k] = array();
					$this->arr[$k][] = $v;
				}
			}
		}
		return $this;
	}
	public function getCode($append = ''){
		$code = '';
		$ln = chr(13).chr(10);
		$flag1 = $this->isFormat ? $ln : '';
		$flag2 = $this->isFormat ? ': '    : ':';
		foreach ($this->arr as $key => $val) {
			foreach ($val as $v) {
				$code && $code .= $flag1;
				$code .= $append.$key.$flag2.$v.';';
			}
		}
		return $code;
	}
	public function __toString(){
		return $this->getCode();
	}
}
?>