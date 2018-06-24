<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class num64{
	private static $list = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
	private static $listLen = 64;
	private static function plusOne_(&$s){
		//static $list = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
		//static $listLen = 64;
		$index = strpos(self::$list, $s);
		if ($index + 1 == self::$listLen) {
			$s = substr(self::$list, 0, 1);
			return true;
		} else {
			$s = substr(self::$list, $index + 1, 1);
			return false;
		}
	}
	public static function plusOne($str = ''){
		if (!$str) $rs = '1';
		else {
			$len = strlen($str);
			$rs = '';
			$next = true;
			for ($i = $len - 1; $i >= 0; $i--) {
				$s = substr($str, $i, 1);
				if ($next) {
					$next = self::plusOne_($s);
				}
				$rs = $s.$rs;
			}
			if ($next) $rs = '1'.$rs;
		}
		return $rs;
	}
	public static function decToNum($num){
		$s = '';
		do {
			$d = $num % self::$listLen;
			$s = substr(self::$list, $d, 1).$s;
			$num = ($num - $d) / self::$listLen;
		} while($num >= self::$listLen);
		$num > 0 && $s = substr(self::$list, $num, 1).$s;
		return $s;
	}
	public static function numToDec($str){
		$l = strlen($str);
		$dec = 0;
		for ($i = 0; $i < $l; $i++) {
			$d = strpos(self::$list, substr($str, $i, 1));
			$dec += $d * pow(64, $l - $i - 1);
		}
		return $dec;
	}
	private static function numPlusNum_($s0, $s1, &$s){
		$i0 = strpos(self::$list, $s0);
		$i1 = strpos(self::$list, $s1);
		$i = $i0 + $i1;
		if ($i >= self::$listLen) {
			$s = substr(self::$list, $i - 64, 1);
			return true;
		} else $s = substr(self::$list, $i, 1);
		return false;
	}
	public static function numPlusNum($str0, $str1){
		$str0 = strrev($str0);
		$str1 = strrev($str1);
		$len0 = strlen($str0);
		$len1 = strlen($str1);
		$len = $len0;
		$len > $len1 && $len = $len1;
		$yichu = '';
		if ($len0 > $len1) {
			$len = $len1;
			$yichu = substr($str0, $len);
		} else {
			$len = $len0;
			$yichu = substr($str1, $len);
		}
		$str = '';
		$lastJin = false;
		for ($i = 0; $i < $len; $i++) {
			$s0 = substr($str0, $i, 1);
			$s1 = substr($str1, $i, 1);
			//echo '$s0 + $s1 = '.$s0.' + '.$s1.' = ';
			if ($lastJin) {
				$lastJin = self::numPlusNum_($s0, $lastJin, $s0);
			}
			$s  = '';
			$jin = self::numPlusNum_($s0, $s1, $s);
			if ($jin) {
				if ($lastJin) self::numPlusNum_($lastJin, '1', $lastJin);
				else $lastJin = '1';
			}
			//echo $s.' $jin = '.$lastJin, '<br />';
			$str .= $s;
		}
		if ($lastJin) {
			if ($yichu) {
				$str .= strrev(self::numPlusNum(strrev($yichu), $lastJin));
			} else {
				$str .= $lastJin;
			}
		} elseif ($yichu) $str .= $yichu;
		$str = strrev($str);
		return $str;
	}
	public static function numPlusDec($str, $num){//echo '000000'.self::decToNum($num), '<br />';
		return self::numPlusNum($str, self::decToNum($num));
	}
}