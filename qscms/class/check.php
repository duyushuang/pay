<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class check{
	public static function isInt($data){
		if (!is_array($data)) return is_int($data);
		if ($data) {
			foreach ($data as $v) {
				if (!is_int($v)) return false;
			}
			return true;
		}
		return false;
	}
}
?>