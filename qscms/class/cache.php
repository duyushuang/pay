<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class cache{
	public static $cacheRoot;
	public static function getFile($type, $name){
		static $suffixList = array('js' => 'js', 'text' => 'txt');
		$suffix = 'php';
		if (isset($suffixList[$type])) $suffix = $suffixList[$type];
		return d(self::$cacheRoot.$type.'/'.$name.'.'.$suffix);
	}
	public static function replaceDB($type, $name, $data){
		if (db::replace('sys_cache', array(
			'type' => $type,
			'name' => $name,
			'data' => qscms::addslashes($data)
		))) {
			self::dbToFile($type, $name);
		}
		return self::getFile($type, $name);
	}
	public static function removeDB($type, $name){
		if (db::delete('sys_cache', "type='$type' AND name='$name'")) {
			$file = self::getFile($type, $name);
			return @unlink($file);
			memory::delete('sys_cache_'.$type.'_'.$name);
		}
		return false;
	}
	public static function dbToFile($type, $name){
		if ($data = db::one_one('sys_cache', 'data', "type='$type' AND name='$name'")) {
			$file = self::getFile($type, $name);
			file::createFolderToFile($file);
			file::write($file, $data);
			//memory::write('sys_cache_'.$type.'_'.$name, $data);
			memory::delete('sys_cache_'.$type.'_'.$name);
		}
	}
	public static function write_rewrite($name,$data){
		$file = d(self::$cacheRoot.'rewrite/'.$name.'.php');
		file::createFolderToFile($file);
		file::write($file,$data);
		return $file;
	}
	public static function get_rewrite($name){
		$file=d(self::$cacheRoot.'rewrite/'.$name.'.php');
		if(file_exists($file))return $file;
		return false;
	}
	public static function write_data($name,$data){
		return self::replaceDB('data', $name, $data);
	}
	public static function get_data($name){
		$file = self::getFile('data', $name);
		if(file_exists($file))return $file;
		return false;
	}
	public static function write_area($name, $array){
		$file = self::getFile('area', $name);
		file::createFolderToFile($file);
		file::write($file,'<?php exit;?>'.serialize($array));
		return $file;
	}
	public static function get_area($name){
		$file = self::getFile('area', $name);
		$rn = array();
		if(file_exists($file)){
			$rn = @unserialize(substr(file::read($file),13));
			!is_array($rn) && $rn=array();
		}
		return $rn;
	}
	public static function write_cate($name,$array){
		$file=d(self::$cacheRoot.'cate/'.$name.'.php');
		file::createFolderToFile($file);
		file::write($file,'<?php $cate='.string::format_array($array).';?>');
		return $file;
	}
	public static function get_cate($name){
		$file=d(self::$cacheRoot.'cate/'.$name.'.php');
		if(file_exists($file)){
			include($file);
			return $cate;
		}
		return false;
	}
	public static function write_text($name,$data){
		return self::replaceDB('text', $name, $data);
	}
	public static function get_text($name){
		$data = memory::get('sys_cache_text_'.$name);
		if ($data) return $data;
		$file = self::getFile('text', $name);
		if(file_exists($file)){
			$data = file::read($file);
			memory::write('sys_cache_text_'.$name, $data);
			return $data;
		}
		return false;
	}
	public static function write_code($name,$data){
		$data = trim($data);
		if ($data) {
			$data = '<?php '.$data.';?>';
			return self::replaceDB('code', $name, $data);
		} else {
			return self::del_code($name);
		}
	}
	public static function get_code($name){
		$file = self::getFile('code', $name);
		if(file_exists($file)){
			return $file;
		}
		return false;
	}
	public static function del_code($name){
		return self::removeDB('code', $name);
	}
	public static function write_php($name,$data){
		$data = trim($data);
		if ($data) {
			$data = '<?php '.$data.';?>';
			self::replaceDB('php', $name, $data);
		} else {
			self::removeDB('php', $name);
		}
	}
	public static function get_php($name){
		$file = self::getFile('php', $name);
		if(file_exists($file)){
			return $file;
		}
		return false;
	}
	public static function write_array($name,$arr){
		if ($arr) {
			$data = '<?php exit;?>'.serialize($arr);
			self::replaceDB('array', $name, $data);
		} else {
			self::delete_array($name);
		}
	}
	public static function delete_array($name){
		return self::removeDB('array', $name);
	}
	public static function get_array($name) {
		//qscms::ob_end_clean();
		$rn = memory::get('sys_cache_array_'.$name);
		if ($rn) return $rn;
		$rn   = array();
		$file = self::getFile('array', $name);
		//if ($name == 'cache_plugins'){
			//echo $file.'<br />';
		//}
		if(file_exists($file)){
			$rn = @unserialize(substr(file::read($file), 13));
			!is_array($rn) && $rn = array();
		}
		memory::write('sys_cache_array_'.$name, $rn);
		return $rn;
	}
	public static function write_js($name,$data){
		return self::replaceDB('js', $name, $data);
	}
	public static function get_js($name){
		$file = self::getFile('js', $name);
		if(file_exists($file))return u($file);
		return false;
	}
	public static function upCache(){
		foreach (db::select('sys_cache', 'type,name') as $v) {
			self::dbToFile($v['type'], $v['name']);
		}
	}
}
cache::$cacheRoot = qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/data');
?>