<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class memory{
	private static $datas = array();
	public static $isMemcache = false;
	public static $mem = false;
	public static function write($name, $data, $returnSource = false, $writeMem = true){
		
		if (self::$isMemcache && $writeMem) {
			self::$mem->set('m_'.$name, $data);
		}
		if ($returnSource) {
			self::$datas[$name] = $data;
			return $data;
		}
		return self::$datas[$name] = $data;
	}
	public static function get($name){
		if (isset(self::$datas[$name])) return self::$datas[$name];
		if (self::$isMemcache) {
			$data = self::$mem->get('m_'.$name);
			self::write($name, $data, false, false);
			if ($data !== false) return $data;
		}
		return NULL;
		/*$data = self::$datas[$name];
		!isset($data) && $data = NULL;
		return $data;*/
	}
	public static function writeClass($name, $data){
		$name = '_class_'.$name.'_data_';
		return self::write($name, $data);
	}
	public static function getClass($name){
		$name = '_class_'.$name.'_data_';
		//echo $name.'<br />';
			//if ($name == '_class_plugins_plugins_data_'){
			//print_r(3333);exit;
		//}
		return self::get($name);
	}
	public static function getBoolean($name){
		$data = self::get($name);
		if ($data !== false) return $data === true || $data == '1' || $data == 'true' || $data == 'yes' ? true : false;
		return false;
	}
	public static function getInt($name){
		return intval(self::get($name));
	}
	public static function delete($name){
		unset(self::$datas[$name]);
		if (self::$isMemcache) self::$mem->delete('m_'.$name);
	}
	public static function flush(){
		self::$datas = array();
		if (self::$isMemcache) self::$mem->flush();
	}
}
if (cfg::getBoolean('sys', 'memcache') && extension_loaded('memcache')) {
	if ($__list = cache::get_array('memcache_server')) {
		memory::$mem = new Memcache();
		foreach ($__list as $v) memory::$mem->addServer($v['ip'], intval($v['port']), true, intval($v['weight']));
		memory::$isMemcache = true;
	}
}
?>