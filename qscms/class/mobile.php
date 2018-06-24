<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class mobile{
	public static function address($mobile){
		if (form::checkMobilephone($mobile)) {
			$code = substr($mobile, 0, 7);
			if ($line = db::one('mobile', 'province,city', "code='$code'")) {
				return $line['province'].$line['city'];
			}
		}
		return false;
	}
	public static function type($mobile, $getId = false){
		static $types = array('未知', '中国电信', '中国移动', '中国联通', '虚拟运营商');
		if (form::checkMobilephone($mobile)) {
			$code = substr($mobile, 0, 7);
			if ($line = db::one('mobile', 'type', "code='$code'")) {
				$type = $line['type'];
				if ($type) {
					if ($getId) return array_search($type, $types);
					return $type;
				}
			}
		}
		return false;
	}
	public static function telAddress($tel){
		if (preg_match('/^(\d{2,4})-\d{7,8}$/', $tel, $matches)) {
			$areaCode = $matches[1];
			if ($line = db::one('mobile', 'province,city', "areaCode='$areaCode'")) {
				return $line['province'].$line['city'];
			}
		}
		return false;
	}
	public static function telAreaCode($tel){
		if (preg_match('/^(\d{2,4})-\d{7,8}$/', $tel, $matches)) {
			$areaCode = $matches[1];
			return $areaCode;
		}
		return false;
	}
}
?>