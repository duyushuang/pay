<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');error_reporting(E_ALL);ini_set('display_errors', 'on');
class syn{
	private static $allowSyn;
	private static $allowGetSyn;
	static function ini(){
		self::$allowSyn = self::synCfg() ? true : false;
		self::$allowGetSyn = self::getSynCfg() ? true : false;
	}
	static function synCfg(){
		static $syn;
		if (!isset($syn)) {
			$config = qscms::getConfig('system', false);
			if (isset($config['syn'])) {
				$arr = $config['syn'];
				if (isset($arr['key']) && isset($arr['update']) && is_array($arr['update']) && count($arr['update']) > 0) {
					foreach ($arr['update'] as $k => $v) {
						$f = strpos($v, '://');
						if ($f === false) $v = 'http://'.$v;
						substr($v, -1) != '/' && $v .= '/';
						$arr['update'][$k] = $v;
					}
					$syn = $arr;
				}
			} else $syn = false;
		}
		return $syn;
	}
	static function getSynCfg(){
		static $syn;
		if (!isset($syn)) {
			$config = qscms::getConfig('system', false);
			if (isset($config['getSyn']['key'])) {
				$syn = array('key' => $config['getSyn']['key']);
			} else $syn = false;
		}
		return $syn;
	}
	static function test(){
		print_r(self::synCfg());
		print_r(self::getSynCfg());
	}
	static function synData($data, $path){
		if (self::$allowSyn) {
			$syn = self::synCfg();
			$postData = 'file='.rawurlencode($path).'&data='.rawurlencode($data).'&key='.rawurlencode($syn['key']);
			foreach ($syn['update'] as $url) {
				$rs = winsock::open($url.'api/syn', $postData);
				//if (strpos($rs, '404') === false) echo $url.'api/syn', $rs;
			}
		}
	}
	static function synFile($file, $path = false){
		$path || $path = $file;
		$file = d('.'.$file);
		if (file_exists($file)) self::synData(file::read($file), $path);
	}
	static function getSyn(){
		if (self::$allowGetSyn) {
			if ($_POST) {
				$_POST = qscms::stripslashes($_POST);
				if (isset($_POST['file']) && isset($_POST['key']) && isset($_POST['data'])) {
					$syn = self::getSynCfg();
					if ($_POST['key'] == $syn['key']) {
						$file = d('.'.$_POST['file']);
						file::createFolderToFile($file);
						file::write($file, $_POST['data']);
						return 'ok';
					}
				}
			}
		}
	}
}
syn::ini();
?>