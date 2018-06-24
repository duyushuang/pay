<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class template{
	private static $tpl_folder;
	private static $cache_folder;
	private static $tpl_suffix;
	private static $this_tpl_folder;
	private static $cache_suffix;
	private static $configs=array(),$js_lib=array(),$css_lib=array(),$subTplList=array();
	public static $vars = array();
	public static function initialize($tpl, $cache, $suffix='.htm',$cache_suffix='.php'){
		self::$tpl_folder   = $tpl;
		self::$cache_folder = $cache;
		self::$tpl_suffix   = $suffix;
		self::$cache_suffix = $cache_suffix;
	}
	public static function addPath($tpl, $cache){
		if ($tpl !== false) {
		$tpl   = strtr($tpl, '/\\', D.D);
			substr($tpl, -1)     != D && $tpl   .= D;
			substr($tpl, 0, 1)   == D && $tpl    = substr($tpl, 1);
			self::$tpl_folder   .= $tpl;
		}
		if ($cache !== false) {
			$cache = strtr($cache, '/\\', D.D);
			substr($cache, -1)   != D && $cache .= D;
			substr($cache, 0, 1) == D && $cache  = substr($cache, 1);
			self::$cache_folder .= $cache;
		}
		
	}
	public static function setSuffix($suffix){
		self::$tpl_suffix = '.'.$suffix;
	}
	public static function getTpl($name = ''){
		$file = self::$tpl_folder;
		$name && $file.=$name.self::$tpl_suffix;
		return $file;
	}
	public static function getCache($name = ''){
		$file = self::$cache_folder;
		$name && $file.=$name.self::$cache_suffix;
		return $file;
	}
	public static function config($save=true,$name='def'){
		if(!$save){
			if(isset(self::$configs[$name])){
				self::set_config(self::$configs[$name]);
			} else throw new e_qscms('template config load error!');
		} else {
			self::$configs[$name]=self::get_config();
		}
	}
	public static function get_config(){
		return array('tpl_folder'=>self::$tpl_folder,'cache_folder'=>self::$cache_folder,'tpl_suffix'=>self::$tpl_suffix,'this_tpl_folder'=>self::$this_tpl_folder,'cache_suffix'=>self::$cache_suffix);
	}
	public static function set_config($config){
		self::$tpl_folder=$config['tpl_folder'];
		self::$cache_folder=$config['cache_folder'];
		self::$tpl_suffix=$config['tpl_suffix'];
		self::$this_tpl_folder=$config['this_tpl_folder'];
		self::$cache_suffix=$config['cache_suffix'];
	}
	public static function install(){
		file::createFolders(self::$tpl_folder, self::$cache_folder);
	}
	public static function exists($name,$tpl=true){
		$name=strtr($name,'\\/',D.D);
		return file_exists($tpl?self::$tpl_folder.$name.self::$tpl_suffix:self::$cache_folder.$name.self::$cache_suffix);
	}
	public static function getPath($name, $isTpl = true){
		$name = strtr($name, '\\/', D.D);
		return $isTpl ? self::$tpl_folder.$name.self::$tpl_suffix : self::$cache_folder.$name.self::$cache_suffix;
	}
	public static function getCachePath($filename, $prefix = true){
		return self::$cache_folder.($prefix && !empty(self::$vars['thisTplName1']) ? self::$vars['thisTplName1']. '_' : '').$filename;
	}
	public static function writeXinTpl($s, $d){
		$arr = cache::get_array('tplNames');
		$arr[$s] = $d;
		cache::write_array('tplNames', $arr);
	}
	public static function getXinTpl($s){
		$arr = cache::get_array('tplNames');
		return isset($arr[$s]) ? $arr[$s] : false;
	}
	public static function load($tpl_name, $ispath = false, $custom_cache_name = ''){
		self::$vars = array('thisTplName' => $tpl_name, 'thisTplName1' => strtr($tpl_name, '\\/', '__'));
		$cache_name = $ispath ? strtr($tpl_name, '/', D) : $tpl_name;
		//$cache_name = strtr($cache_name, '*', '#');
		$tpl   = d(self::$tpl_folder . $tpl_name . self::$tpl_suffix, false);
		$cache = d(self::$cache_folder . $cache_name.self::$cache_suffix, false);
		/**
		 * 判断是否有星号
		 */
		if (strpos($tpl, '*') !== false) {
			$tpl0 = $tpl;
			//$tpl = file_exists($cache) ? self::getXinTpl($tpl0) : false;$tpl=false;
			$tpl = false;
			if (!$tpl) {
				$pathinfo = pathinfo($tpl0);
				if (!empty($pathinfo['dirname']) && !empty($pathinfo['filename']) && !empty($pathinfo['extension'])) {
					$ignore = $pathinfo['basename'];
					$ignore = str_replace('.', '\\.', $ignore);
					$ignore = str_replace('*', '.*?', $ignore);
					$ignore = '/^'.$ignore.'$/i';
					$files = file::getFiles($pathinfo['dirname'], $ignore);
					if ($count = count($files)) {
						$tplIndex = rand(0, $count - 1);
						$tplName = $files[$tplIndex];
						$tpl = $pathinfo['dirname'].D.$tplName;
						//self::writeXinTpl($tpl0, $tpl);
						$cache = str_replace('*', substr($tplName, 0, -strlen(self::$tpl_suffix)), $cache);
					} else throw new e_qscms("Current template file '".u($tpl)."' not found or have no access!");
				} else throw new e_qscms('未获取到模版路径，请检查是否有权限');
			}
		}
		
		return self::load_base($tpl,$cache);
	}
	public static function load_folder($tpl_name,$folder=''){
		$folder&&($folder.='/');
		self::$this_tpl_folder=self::$tpl_folder.$folder;
		return self::load_base(self::$this_tpl_folder.$tpl_name.self::$tpl_suffix,self::$cache_folder.$folder.$tpl_name.self::$cache_suffix);
	}
	public static function load_base($tpl_file,$cache_file,$ignoreTime = false){
		//$parse = false;
		$parse = $ignoreTime;
		if(!$ignoreTime){
			if(!file_exists($cache_file)){
				if(file_exists($tpl_file)){
					$parse = true;
				} else {
					throw new e_qscms("Current template file '".u($tpl_file)."' not found or have no access!");
				}
			} else {
				if(file_exists($tpl_file)&&(filemtime($tpl_file)>filemtime($cache_file)))$parse=true;
			}
		}
		if($parse){
			$cacheLockFile = $cache_file.'.lock';
			if(!file_exists($cacheLockFile)){
				self::$subTplList = array();
				$tpl_file         = d($tpl_file);
				$cache_file       = d($cache_file);
				$cache_file_info  = pathinfo($cache_file);
				file::createFolder($cache_file_info['dirname']);
				touch($cacheLockFile);//锁定模板解析
				$code = file_get_contents($tpl_file);
				$code = self::parse_code($code);
				$pathinfo = pathinfo($tpl_file);
				$code = '<?php !defined("IN_QSCMS")&&exit("error");$__tplUrl = \''.u($pathinfo['dirname'] . D).'\';'.substr($code,6);
				$refreshCode = '';
				foreach(self::$subTplList as $subTpl){
					$refreshCode && $refreshCode.='||';
					$refreshCode.='filemtime(\''.$subTpl.'\')>$_tplModify';
				}
				$refreshCode && ($refreshCode = '$_tplModify=filemtime(\''.$cache_file.'\');if('.$refreshCode.'){include(template::load_base(\''.$tpl_file.'\',\''.$cache_file.'\',true));exit;}') && $code = '<?php '.$refreshCode.'?>'.$code;
				$code = parse_php::formatArray($code);
				file_put_contents($cache_file,$code);
				self::$js_lib=array();
				self::$css_lib=array();
				unlink($cacheLockFile);
			} else {
				throw new e_qscms('php template:'.u($tpl_file).' is parseing,please wait...');
			}
		}
		return $cache_file;
		
	}
	public static function parse_code($code){
		//language::load('index');
		$nest = 6;
		//$code = self::stripblock($code);
		//$code = preg_replace("/\{lang\s+(.+?)\}/ies", "self::languagevar('\\1')", $code);
		//$code = preg_replace("/[\n\r\t]*\{template\s+([^\/:*?\"<>|}]+)\}[\n\r\t]*/ies", "self::stripvtemplate('\\1', 0)", $code);
		//$code = preg_replace("/[\n\r\t]*\{subtemplate\s+([^\/:*?\"<>|}]+)\}[\n\r\t]*/ies", "self::stripvtemplate('\\1', 1)", $code);
		//$code = preg_replace("/\{lang2\s+(.+?)\}/ies", "'<?=language::get('.self::stripphpvtags('\\1').')? >'", $code);
		$code = parse_php::parse($code);
		return '<?php '.$code.'?>';
	}
	private static function stripvtemplate($tpl, $sub) {
		$vars = explode(':', $tpl);
		$codeid = 0;
		$tpldir = '';
		if(count($vars) == 2) {
			list($codeid, $tpl) = $vars;
			$tpldir = './plugins/'.$codeid.'/templates';
		}
		if($sub) {
			return self::loadsubtemplate($tpl, $codeid, $tpldir);
		} else {
			return self::stripvtags("<? include template::load('$tpl', '$codeid', '$tpldir'); ?>", '');
		}
	}
	public static function loadsubtemplate($tpl_name, $codeid = 0, $tpldir = '') {
		($folder=self::$this_tpl_folder)||($folder=self::$tpl_folder);
		$tpl=$folder.$tpl_name.self::$tpl_suffix;
		$tpl = realpath($tpl);//获取规范化路径
		//if(file_exists($tpl)){
		if ($tpl !== false) {
			$pathinfo = pathinfo($tpl);//模板路径信息
			$cacheName = md5($tpl);//缓存配置名字
			template::config(true, $cacheName);//缓存当前配置
			$tplFolder   = $pathinfo['dirname'].D;//当前模板文件夹
			$cacheFolder = str_replace('templates', 'cache', $tplFolder);//缓存模板文件夹
			self::$tpl_folder   = $tplFolder;//进入当前模板路径
			self::$cache_folder = $cacheFolder;//进入当前缓存路径
			self::$subTplList[]=$tpl;
			$code = file_get_contents($tpl);
			$code = self::parse_code($code);
			template::config(false, $cacheName);//还原之前的模板路径
			return $code;
			//$code=parse_php::parse($code);
			//return '<?php '.$code.'? >';
		} else return 'sub_template '.$tpl_name.' not found';
		return $content;
	}
	public static function css_select($css_list){
		$css_list=explode(',',$css_list);
		foreach($css_list as $css){
			if(!isset(self::$css_lib[$css])){
				css::select_lib($css);
				self::$css_lib[$css]=true;
			}
		}
		return css::output(true);
	}
	public static function js_select($js_list){
		$js_list=explode(',',$js_list);
		foreach($js_list as $js){
			if(empty(self::$js_lib[$js]) || !self::$js_lib[$js]){
				js::select_lib($js);
				self::$js_lib[$js]=true;
			}
		}
		return js::output(true);
	}
	private static function addquote($var) {
		return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
	}
	private static function languagevar($var) {
		return language::get($var,'index','default');
	}
	private static function stripphpvtags($expr) {
		$expr = str_replace("\\\"", "\"", preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr));
		return $expr;
	}
	private static function stripvtags($expr, $statement) {
		$expr = str_replace("\\\"", "\"", preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr));
		$statement = str_replace("\\\"", "\"", $statement);
		return $expr.$statement;
	}
	public static function stripblock($marker) {
		global $db,$pre;
		$data=$db->result_first("select data from {$pre}block where marker='$marker'");
		if($data){
			return self::parse_code($data);
		}
	}
}
?>