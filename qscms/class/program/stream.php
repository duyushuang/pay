<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class p_stream{
	private $arr, $count, $index;
	public $end;
	public function __construct($data){
		$this->arr = string::str_split($data);
		$this->count = count($this->arr);
		$this->end = $this->count > 0 ? false : true;
		$this->index = 0;
	}
	public function getToTab($tabCount = 0, $trimTab = true){
		$arr = array();
		$atGet = $atStr = false;
		$str = $getStr = '';
		$thisTabCount = 0;
		while ($this->index < $this->count) {
			$s = $this->arr[$this->index];
			$this->index++;
			$str .= $s;
			if (!$atStr) {
				if ($s == "\t") $thisTabCount++;
				else $atStr = true;
			} else {
				if ($s == "\n" || $this->index == $this->count) {
					$str = rtrim($str);
					if (!$atGet) {
						if ($thisTabCount == $tabCount) {
							$atGet = true;
							$getStr && $getStr .= "\n";
							$tabCount > 0 && $trimTab && $str = substr($str, $tabCount);
							$getStr .= $str;
						}
					} else {
						if ($thisTabCount > $tabCount) {
							$getStr && $getStr .= "\n";
							$tabCount > 0 && $trimTab && $str = substr($str, $tabCount);
							$getStr .= $str;
						} else {
							$getStr && $arr[] = $getStr;
							if ($thisTabCount == $tabCount) {
								$getStr = $str;
							} else {
								$atGet = false;
								$getStr = '';
							}
						}
					}
					$str = '';
					$atStr = false;
					$thisTabCount = 0;
				}
			}
		}
		$getStr && $arr[] = $getStr;
		return $arr;
	}
	public function parseTab($tabCount = 0){
		$this->goGet();
		$str = '';
		$thisTabCount = 0;
		$atStr = false;
		$arr = $rn = array();
		while ($this->index < $this->count) {
			$s = $this->arr[$this->index];
			$this->index++;
			if (!$atStr) {
				if ($s == "\t") $thisTabCount++;
				else {
					$atStr = true;
					$str .= $s;
				}
			} else {
				if ($s == "\n" || $this->index == $this->count) {
					if ($tabCount == $thisTabCount) {
						
					}
				}
			}
		}
	}
	public function next($count = 1){
		if (!$this->end) {
			$str = '';
			for ($i = $this->index; $i < $this->index + $count; $i++) {
				$str .= $this->arr[$i];
			}
			$this->index += $count;
			if ($this->index >= $count) $this->end = true;
			return $str;
		}
		return '';
	}
	public function nextAll(){
		if (!$this->end) {
			$arr = array_slice($this->arr, $this->index);
			$this->end = true;
			$this->index = $this->count;
			return implode('', $arr);
		}
		return '';
	}
	public function last($count = 1){
		if ($this->index > 0) {
			$str = '';
			for ($i = $this->index - $count; $i < $this->index; $i++) {
				$i < 0 && $i = 0;
				$str .= $this->arr[$i];
			}
			$this->index -= $count;
			return $str;
		}
		return '';
	}
	public function nextLine(){
		if (!$this->end) {
			$str = '';
			$this->goGet();
			while ($this->index < $this->count) {
				$s = $this->arr[$this->index];
				$this->index++;
				$str .= $s;
				if ($s == "\n") break;
			}
			return trim($str);
		}
		return '';
	}
	public function getCmd(){
		$cmd = '';
		$atGet = false;
		while ($this->index < $this->count) {
			$s = $this->arr[$this->index];
			$this->index++;
			if (!$atGet) {
				if (!in_array($s, array("\r", "\n", "\t", ' '))) {
					$atGet = true;
					$cmd = $s;
				}
			} else {
				if (!in_array($s, array("\r", "\n", "\t", ' '))) {
					$cmd .= $s;
				} else {
					break;
				}
			}
		}
		return $cmd;
	}
	public function reset(){
		$this->index = 0;
		$this->end = $this->count > 0 ? false : true;
	}
	public function getStr($endAt = false){
		$atGet = false;
		$isFlag = false;
		$flagA = $flagB = '';
		$str = '';
		$lastS = '';
		$zhuanCount = 0;
		while ($this->index < $this->count) {
			$s = $this->arr[$this->index];
			if ($s == '\\') {
				$zhuanCount++;
			}
			$this->index++;
			if (!$atGet) {
				if (!in_array($s, array("\r", "\n", "\t", ' '))) {
					$atGet = true;
					if ($s == '"' || $s == '\'' || $s == '“') {
						$isFlag = true;
						if ($s == '“') {
							$flagA = '“';
							$flagB = '”';
						} else {
							$flagA = $s;
							$flagB = $s;
						}
					} else {
						$str .= $s;
					}
				}
			} else {
				if ($isFlag) {
					if ($flagA == '“') {
						if ($s == '”') {
							if ($lastS != '\\') {
								//$str = str_replace('\“', '“', $str);
								//$str = str_replace('\”', '”', $str);
								$str = stripslashes($str);
								break;
							} else $str .= $s;
						} else $str .= $s;
					} else {
						if ($s == $flagB) {
							if ($zhuanCount == 0 || $zhuanCount % 2 == 0) {
								$str = stripslashes($str);
								break;
							}
							else $str .= $s;
						} else $str .= $s;
					}
				} else {
					if ($endAt) {
						if (strpos($endAt, $s) !== false) {
							break;
						} else $str .= $s;
					} else {
						if (!in_array($s, array("\r", "\n", "\t", ' '))) {
							$str .= $s;
						} else {
							break;
						}
					}
				}
			}
			if ($s != '\\') $zhuanCount = 0;
			$lastS = $s;
		}
		return $str;
	}
	public function goGet(){
		while ($this->index < $this->count) {
			$s = $this->arr[$this->index];
			if (!in_array($s, array("\r", "\n", "\t", ' '))) break;
			$this->index++;
		}
	}
	public function getEndAt($flag){
		$str = '';
		$flagS = '';
		while ($this->index < $this->count) {
			$s = $this->arr[$this->index];
			$this->index++;
			if (strpos($flag, $s) !== false) {
				$flagS = $s;
				break;
			} else $str .= $s;
		}
		return array($str, $flagS);
	}
	public function getFuncName(){
		$this->goGet();
		$name = $varName = $varVal = '';
		$vars = array();
		$varCount = 0;
		$getName = false;
		$atKuo = false;
		while ($this->index < $this->count) {
			$s = $this->arr[$this->index];
			$this->index++;
			if (!$atKuo) {
				if (!$getName) {
					if ($s == '(') {
						$getName = true;
						$atKuo = true;
						/**
						 * 解析括号参数
						 */
						$data = $s.$this->nextAll();
						$data = substr($data, 1, -1);
						$arr = p::encodeCmdStr($data);
						$data = $arr[0];
						foreach (explode(',', $arr[0]) as $v) {
							$v = trim($v);
							$val = false;
							if (($f = strpos($v, '=')) !== false) {
								$key = substr($v, 0, $f);
								$val = substr($v, $f + 1);
							} else $key = $v;
							$key = trim($key);
							$key = p::decodeCmdStr($key, $arr[1]);
							if ($val !== false) {
								$val = trim($val);
								$val = p::decodeCmdStr($val, $arr[1]);
							}
							$vars[$key] = $val;
						}
						
					} else {
						if (in_array($s, array("\r", "\n", "\t", ' '))) $getName = true;
						else $name .= $s;
					}
				} else {
					if ($s == '(') {
						$atKuo = true;//明天重做
						/**
						 * 解析括号参数
						 */
						$data = $s.$this->nextAll();
						$data = substr($data, 1, -1);
						$arr = p::encodeCmdStr($data);
						$data = $arr[0];
						foreach (explode(',', $arr[0]) as $v) {
							$v = trim($v);
							$val = false;
							if (($f = strpos($v, '=')) !== false) {
								$key = substr($v, 0, $f);
								$val = substr($v, $f + 1);
							} else $key = $v;
							$key = trim($key);
							$key = p::decodeCmdStr($key, $arr[1]);
							if ($val !== false) {
								$val = trim($val);
								$val = p::decodeCmdStr($val, $arr[1]);
							}
							$vars[$key] = $val;
						}
					}
				}
			} else {
				$this->index--;
				$this->goGet();
				$arr = $this->getEndAt('=,)');
				$varName = trim($arr[0]);
				if ($varName === '') {
					if ($arr[1] == ',') continue;
					break;
				}
				$varCount++;
				switch ($arr[1]) {
					case '=':
						$this->goGet();
						$varVal = $this->getStr(',)');
						$vars[$varName] = $varVal;
						if ($this->arr[$this->index - 1] == ')') break 2;
					break;
					case ',':
						$vars[$varName] = false;
					break;
					case ')':
						$vars[$varName] = false;
						break 2;
					break;
				}
			}
		}
		return array($name, $vars);
	}
}
?>