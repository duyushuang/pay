<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class rewrite{
	public static function add($pattern, $goto = ''){
		$rules = self::getRule();
		$exist = false;
		foreach ($rules as $r) {
			if ($r[0] == $pattern && $r[1] == $goto) {
				$exist = true;
				break;
			}
		}
		if (!$exist) {
			$rules[] = array($pattern, $goto);
			return self::saveRule($rules);
		}
		return true;
	}
	public static function remove($pattern, $goto){
		$rules0 = self::getRule();
		$rules  = array();
		$exist = false;
		foreach ($rules0 as $r) {
			if ($r[0] == $pattern && $r[1] == $goto) {
				$exist = true;
			} else $rules[] = $r;
		}
		if ($exist) {
			return self::saveRule($rules);
		}
		return true;
	}
	public static function getRule(){
		$reFile = qd(qscms::getCfgPath('/system/files/rewrite'));
		$rules  = array();
		if (file_exists($reFile)) {
			$data = file::read($reFile);
			$data = str_replace("\r\n", "\n", $data);
			foreach (explode("\n", $data) as $v) {
				if ($v = trim($v)) {
					$sp = preg_split("/\s+/", $v);
					if (count($sp) == 2) {
						$rules[] = $sp;
					}
				}
			}
		}
		return $rules;
	}
	public static function saveRule($rules){
		$data = '';
		$reFile = qd(qscms::getCfgPath('/system/files/rewrite'));
		foreach ($rules as $r) {
			$data && $data .= "\n";
			$data .= $r[0].' '.$r[1];
		}
		return file::write($reFile, $data);
	}
}
?>