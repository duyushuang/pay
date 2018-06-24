<?php

 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class form{
	public static function get(){
		$list=array();
		if(func_num_args()>0) {
			foreach(func_get_args() as $v) {
				if(is_array($v)){
					if(!isset($v[2]) || $v[2]===false)$ignore=false;
					else $ignore=true;
					if((!$ignore && isset($_POST[$v[0]])) || $ignore){
						$list[$v[0]]=$_POST[$v[0]];
						qscms::setType($list[$v[0]],$v[1]);
					}
				} else {
					if(isset($_POST[$v]))$list[$v]=$_POST[$v];
					else $list[$v] = '';
				}
			}
		}
		return $list;
	}
	public static function get2(){
		$list=array();
		if(func_num_args()>0) {
			foreach(func_get_args() as $v) {
				if(is_array($v)){
						$list[$v[0]] = isset($_POST[$v[0]]) ? $_POST[$v[0]] : '';
						qscms::setType($list[$v[0]],$v[1]);
				} else {
					if(isset($_POST[$v]))$list[$v]=$_POST[$v];
					else $list[$v] = '';
				}
			}
		}
		return $list;
	}
	public static function get3(){
		$list=array();
		if(func_num_args()>0) {
			foreach(func_get_args() as $v) {
				if(is_array($v)){
						$list[$v[0]] = isset($_POST[$v[0]]) ? $_POST[$v[0]] : '';
						qscms::setType($list[$v[0]],$v[1]);
				} else {
					if(isset($_POST[$v]))$list[$v]=$_POST[$v];
					else $list[$v] = '';
				}
			}
		}
		$list && $list = qscms::filterHtml($list);
		return $list;
	}
	public static function get4($datas, $filterArr, $filterHtml = true){
		$list = array();
		if(is_array($filterArr)) {
			foreach($filterArr as $v) {
				if(is_array($v)){
						$list[$v[0]] = isset($datas[$v[0]]) ? $datas[$v[0]] : '';
						qscms::setType($list[$v[0]],$v[1]);
				} else {
					if(isset($datas[$v]))$list[$v]=$datas[$v];
					else $list[$v] = '';
				}
			}
		}
		$list && $filterHtml && $list = qscms::filterHtml($list);
		return $list;
	}
	public static function reg(){
		if(func_num_args()>0) {
			foreach(func_get_args() as $v) {
				if(is_array($v)){
					if(!isset($v[2]) || $v[2]===false)$ignore=false;
					else $ignore=true;
					if((!$ignore && isset($_POST[$v[0]])) || $ignore){
						$GLOBALS[$v[0]]=$_POST[$v[0]];
						settype($GLOBALS[$v[0]],$v[1]);
					}
				} else {
					if(isset($_POST[$v]))$GLOBALS[$v]=$_POST[$v];
				}
			}
		}
	}
	public static function filter_html_($data){
		return preg_replace('/<.*?>/s','',$data);
	}
	public static function filter_html(){
		if(func_num_args()>0){
			foreach(func_get_args() as $v){
				$GLOBALS[$v]=preg_replace('/<.*?>/s','',$GLOBALS[$v]);
			}
		}
	}
	public static function array_equal($arr1,$arr2){
		if(is_array($arr1) && is_array($arr2) && ($count=count($arr1))==count($arr2))return $count;
		return false;
	}
	public static function arrayEqual(){
		if (func_num_args() > 0) {
			$count = 0;
			foreach (func_get_args() as $k => $v) {
				if (is_array($v)) {
					if ($k == 0) $count = count($v);
					else {
						if (count($v) != $count) return false;
					}
				} else {
					return false;
				}
			}
			return $count;
		}
		return false;
	}
	public static function is_form_hash($checkAuthority = true){
		$rs = $_POST && !empty($_POST['hash']) && self::is_hash($_POST['hash']);
		if ($rs) {
			if (qscms::defineTrue('IN_ADMIN')) {
				if ($checkAuthority) {
					return checkWrite();
				}
			}
		}
		return $rs;
	}
	public static function hash($checkAuthority = true){
		return self::is_form_hash($checkAuthority);
	}
	public static function is_form_hash2(){
		$var = qscms::v('_G');
		if ($var->postData && !empty($_POST['hash2'])) {
			$hash = base64_decode($_POST['hash2']);
			if ($hash) {
				$rs = self::is_hash(qscms::authcode($hash, false));
				if ($rs) {
					return true;
				} else {
					return 'form_expire';
				}
			} else {
				return false;
			}
		}
	}
	public static function hash2(){
		return self::is_form_hash2();
	}
	public static function is_hash($hash){
		return $hash == qscms::v('_G')->sys_hash;
	}
	public static function check_vcode(){
		
		if($vcode=$_POST['vcode']){
			$img = new securimage();
			return $img->check($vcode);
		}
		return false;
	}
	public static function checkVcode($clear = false){
		if($vcode = $_POST['vcode']){
			return vcode2::check($vcode, $clear);
		}
		return false;
	}
	public static function checkRealname($v){
		return self::isMatch('/^[\x{4e00}-\x{9fa5}]{2,4}$/u', $v);;
	}
	public static function check_qq($v){
		if(preg_match('/^[1-9]\d{4,11}$/i',$v))return true;
		return false;
	}
	public static function checkQQ($v){
		return self::check_qq($v);
	}
	public static function chack_hanzi($v, $min = 1, $max = 5){
		if (preg_match('/^[A-Za-z\x{4E00}-\x{9FA5}]{1}[A-Za-z0-9\x{4E00}-\x{9FA5}._-]{'.$min.','.$max.'}$/u', $v)) return true;
		return false;
	}
	public static function check_email($v){
		if(preg_match('/^[a-z0-9][a-z0-9_]*@[a-z0-9]+(?:\.[a-z]+){1,3}$/i',$v))return true;
		return false;
	}
	public static function checkEmail($v){
		return self::check_email($v);
	}
	public static function check_phone($v){
		if(preg_match('/(?:^1(?:(?:3[0-9])|(?:5[0-35-9])|(?:8[6-9]))\d{8}$)|(?:^\d{2,4}-\\d{7,8}$)|(?:^\d{7,8}$)/',$v))return true;
		return false;
	}
	public static function checkPhone($v){
		return self::check_phone($v);
	}
	public static function check_telephone($v){
		if(preg_match('/(?:^\d{2,4}-\d{7,8}$)|(?:^\d{7,8}$)/',$v)) return true;
		return false;
	}
	public static function checkTelephone($v){
		return self::check_telephone($v);
	}
	public static function check_mobilephone($v){
		if(preg_match('/^1(3[0-9]|4[57]|5[0-35-9]|7[0-9]|8[0-9])\\d{8}$/',$v)) return true;
		//if(preg_match('/^1(?:(?:3[0-9])|(?:5[0-35-9])|(?:8[0236-9]))\\d{8}$/',$v)) return true;
		return false;
	}
	public static function chack_mobilephone1($v){
		//if(preg_match('/^(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/',$v)) return true;
	}
	public static function checkMobilephone($v){
		return self::check_mobilephone($v);
	}
	public static function check_link($v){
		return preg_match('/^https?:\/\/.+$/', $v) ? true : false;
	}
	public static function checkLink($v){
		return self::check_link($v);
	}
	public static function checkCnId($id){
		static $ck1 = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
		static $ck2 = array(1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2);
		if (preg_match('/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})(\d|x|X)$/', $id)) {
			$id{17} = str_replace('x', 'X', $id{17});
			$num = 0;
			for ($i = 0; $i < 17; $i++) {
				$num += intval($id{$i}) * $ck1[$i];
			}
			$m = $num % 11;
			if($id{17} == $ck2[$m]) return true;
		}
		return false;
	}
	public static function checkIP($v){
		return preg_match('/^\d{1,3}(?:\.\d{1,3}){3}$/', $v) ? true : false;
	}
	public static function checkMD5($v){
		return preg_match('/(^[a-zA-Z0-9]{32}$)|(^[a-zA-Z0-9]{16}$)/', $v) ? true : false;
	}
	public static function checkAlipay($v){
		return preg_match('/(?:^1(?:(?:3[0-9])|(?:4[57])|(?:5[0-35-9])|(?:8[0-9]))\d{8}$)|(?:^[a-z0-9][a-z0-9_]+@[a-z0-9]+(\.[a-z0-9]+){1,3}$)/', $v) ? true : false;
	}
	public static function checkZip($v){
		return self::isMatch('/^\d{6}$/', $v);
	}
	public static function isMatch($pattern, $str){
		return preg_match($pattern, $str) > 0;
	}
	public static function checkBankId($id){
		if (!self::isMatch('/^\d+$/', $id)) return false;
		$len = strlen($id);
		if ($len != 16 && $len != 18 && $len != 19) return false;
		$arr = array();
		for ($i = 0; $i < $len; $i++) {
			$arr[] = intval($id{$i});
		}
		$arr = array_reverse($arr);
		$v = array_shift($arr);
		$num = 0;
		$len--;
		for ($i = 0; $i < $len; $i++) {
			$n0 = $arr[$i];
			if ($i % 2 == 0) {
				$n1 = $n0 * 2;
				if ($n0 > 4) {
					$s = strval($n1);
					$num += intval($s{0}) + intval($s{1});
				} else $num += $n1;
			} else $num += $n0;
		}
		return ceil($num / 10) * 10 - $num == $v;
	}
	public static function checkData($datas, $flag, $msgAlias = array()){
		/**
		 * 检查NULL
		 */
		 
		if (!empty($flag['null']) && is_array($flag['null'])) {
			foreach ($flag['null'] as $k => $v) {
				if ($v === false) {
					if (!isset($datas[$k]) || $datas[$k] === '') {
						if (!empty($msgAlias[$k])) return $msgAlias[$k].'不能为空';
						return false;
					}
				}
			}
		}
		
		/**
		 * 检查最大长度
		 */
		
		if (!empty($flag['maxLength']) && is_array($flag['maxLength'])) {
			foreach ($flag['maxLength'] as $k => $v) {
				if (isset($datas[$k]) && $datas[$k] !== '') {
					$data = $datas[$k];
					$len = mb_strlen($data);
					if ($len > $v) {
						/**
						 * 超过最大长度
						 */
						if (!empty($msgAlias[$k])) return $msgAlias[$k].'长度不能大于'.$v.'个字符';
						return false;
					}
				} else {
					$null = $flag['null'][$k];
					if ($null !== true) {
						if (!empty($msgAlias[$k])) return $msgAlias[$k].'不能为空';
						return false;
					}
				}
			}
		}
		
		/**
		 * 检查最小长度
		 */
		
		if (!empty($flag['minLength']) && is_array($flag['minLength'])) {
			foreach ($flag['minLength'] as $k => $v) {
				if (isset($datas[$k]) && $datas[$k] !== '') {
					$data = $datas[$k];
					$len = mb_strlen($data);
					if ($len < $v) {
						/**
						 * 小于最小长度
						 */
						if (!empty($msgAlias[$k])) return $msgAlias[$k].'长度不能小于'.$v.'个字符';
						return false;
					}
				} else {
					$null = !empty($flag['null'][$k]) ? $flag['null'][$k] : false;
					if ($null !== true) {
						if (!empty($msgAlias[$k])) return $msgAlias[$k].'不能为空';
						return false;
					}
				}
			}
		}
		
		/**
		 * 函数验证数据
		 */
		 
		if (!empty($flag['function']) && is_array($flag['function'])) {
			foreach ($flag['function'] as $k => $v) {
				if (isset($datas[$k]) && $datas[$k] !== '') {
					$data = $datas[$k];
					if (strpos($v, '::') !== false) {
						$sp = explode('::', $v);
						$functionExists = method_exists($sp[0], $sp[1]);
					} else {
						$functionExists = function_exists($v);
					}
					if (!$functionExists) {
						/**
						 * 函数不存在
						 */
						if (!empty($msgAlias[$k])) return $msgAlias[$k].'验证失败！';
						return false;
					} else {
						if (!@eval('return '.$v.'(\''.addcslashes($data, '\'\\').'\');')) {
							if (!empty($msgAlias[$k])) return $msgAlias[$k].'格式错误！';
							return false;
						}
					}
				} else {
					$null = isset($flag['null'][$k]) ? $flag['null'][$k] : NULL;
					if ($null !== true) {
						if (!empty($msgAlias[$k])) return $msgAlias[$k].'不能为空';
						return false;
					}
				}
			}
		}
		
		/**
		 * 正则表达式验证数据
		 */
		
		if (!empty($flag['preg']) && is_array($flag['preg'])) {
			foreach ($flag['preg'] as $k => $v) {
				if (isset($datas[$k])) {
					$data = $datas[$k];
					if (!preg_match($v, $data)) {
						/**
						 * 函数不存在
						 */
						 
						if (!empty($msgAlias[$k])) return $msgAlias[$k].'格式错误！';
						return false;
					}
				} else {
					$null = $flag['null'][$k];
					if ($null !== true) {
						if (!empty($msgAlias[$k])) return $msgAlias[$k].'不能为空';
						return false;
					}
				}
			}
		}
		
		/**
		 * 检查是否在数组内
		 */
		
		if (!empty($flag['in']) && is_array($flag['in'])) {
			foreach ($flag['in'] as $k => $v) {
				if (isset($datas[$k]) && $datas[$k] !== '') {
					$data = $datas[$k];
					if (!in_array($data, $v)) {
						/**
						 * 小于最小长度
						 */
						if (!empty($msgAlias[$k])) return $msgAlias[$k].'只能是：'.implode(',', $v);
						return false;
					}
				} else {
					$null = !empty($flag['null'][$k]) ? $flag['null'][$k] : false;
					if ($null !== true) {
						if (!empty($msgAlias[$k])) return $msgAlias[$k].'不能为空';
						return false;
					}
				}
			}
		}
		
		/**
		 * 检查是否存在于数据库
		 */
		if (!empty($flag['dbFind']) && is_array($flag['dbFind'])) {
			foreach ($flag['dbFind'] as $k => $v) {
				if (isset($datas[$k]) && $datas[$k] !== '') {
					$data = $datas[$k];
					if (db::exists($v, array($k => $data))) {
						if (!empty($msgAlias[$k])) return $msgAlias[$k].'已存在';
						return false;
					}
				} else {
					$null = !empty($flag['null'][$k]) ? $flag['null'][$k] : false;
					if ($null !== true) {
						if (!empty($msgAlias[$k])) return $msgAlias[$k].'不能为空';
						return false;
					}
				}
			}
		}
		return true;
	}
}
?>