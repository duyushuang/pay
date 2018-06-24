<?php
/**
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class bencode{
	public static function encode($data){
		$type = gettype($data);
		switch ($type) {
			case 'string':
				return strlen($data).':'.$data;
			break;
			case 'integer':
				return 'i'.$data.'e';
			break;
			case 'array':
				if (!$data) return 'de';
				$keys = array_keys($data);
				$len = count($keys);
				/*$isList = true;
				for ($i = 0; $i < $len; $i++){
					if (!is_int($keys[$i])) {
						$isList = false;
						break;
					}
				}*/
				$isList = is_int($keys[0]);
				$rs = '';
				if ($isList) {
					$rs = 'l';
					foreach ($data as $v) {
						$rs .= self::encode($v);
					}
					$rs .= 'e';
				} else {
					$rs = 'd';
					foreach ($data as $k => $v) {
						$rs .= self::encode($k).self::encode($v);
					}
					$rs .= 'e';
				}
				return $rs;
			break;
		}
	}
	private static function _decode(&$str, &$index, &$len, &$error){
		if (!$str) return false;
		if ($error) return false;
		$s = $str{$index};
		switch ($s) {
			case 'd':
				$rs = array();
				$index++;
				while ($str{$index} != 'e') {
					$key = self::_decode($str, $index, $len, $error);
					if ($error) return false;
					//echo $key, '=(';
					$value = self::_decode($str, $index, $len, $error);
					if ($error) return false;
					$rs[$key] = $value;//echo $value, ')<br />';
					//echo "next:", $str{$index}, '<br />';
					if ($index >= $len) {
						$error =true;
						$rs = false;
						break;
					}
				}
				$index++;
				return $rs;
			break;
			case 'l':
				$rs = array();
				$index++;
				while ($str{$index} != 'e') {
					$value = self::_decode($str, $index, $len, $error);
					//echo $value, ',';
					if ($error) return false;
					$rs[] = $value;
					if ($index >= $len) {
						$error = true;
						$rs = false;
						break;
					}
				}
				$index++;
				return $rs;
			break;
			case 'i':
				$rs = '';
				$index++;
				$intLen = 0;
				while ($str{$index} != 'e') {
					if ($index >= $len) {
						$error = true;
						break;
					}
					$s = $str{$index};
					$ord = ord($s);
					if ($intLen == 0 && $s == '-') {
						$rs .= $s;
					} elseif ($ord >= 48 && $ord <= 57) {
						$rs .= $s;
					} else {
						$error = true;
						break;
					}
					$index++;
					$intLen++;
					if ($index >= $len) {
						$error = true;
						break;
					}
				}
				if (!$error) {
					$index++;
					return intval($rs);
				}
				return false;
			break;
			default:
				$o = ord($s) - 48;
				if ($o >= 0 && $o <= 9) {
					$numStr = '';
					while ($str{$index} != ':') {
						$s = $str{$index};
						$ord = ord($s);
						if ($ord >= 48 && $ord <= 57) {
							$numStr .= $s;
						} else {
							$error = true;
							break;
						}
						$index++;
						if ($index >= $len) {
							$error = true;
							break;
						}
					}
					if (!$error) {
						$strLen = intval($numStr);
						$index++;
						if ($strLen + $index > $len) {
							$error = true;
							return false;
						}
						$rs = substr($str, $index, $strLen);
						$index += $strLen;
						return $rs;
					} else return false;
				} else {
					$error = true;
					return false;
				}
			break;
		}
	}
	public static function decode($str){
		$error = false;
		$index = 0;
		$len = strlen($str);
		$rs = self::_decode($str, $index, $len, $error);
		if ($error) $rs = array();
		if (!$rs) $rs = array();
		if ($index != $len) $rs = array();
		return $rs;
	}
}
?>