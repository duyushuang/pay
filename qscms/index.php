<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */

if(version_compare(PHP_VERSION,'5.0.0','<'))  die('require PHP > 5.0 !');

/**
 * PHP 5.5 兼容处理
 */
if (!defined('PHP55')) define('PHP55', version_compare(PHP_VERSION,'5.5','>='));
if (PHP55 && !function_exists('curry')) {
	function curry($func, $arity) {
		return create_function('', "
			\$args = func_get_args();
			if(count(\$args) >= $arity)
				return call_user_func_array('$func', \$args);
			\$args = var_export(\$args, 1);
			return create_function('','
				\$a = func_get_args();
				\$z = ' . \$args . ';
				\$a = array_merge(\$z,\$a);
				return call_user_func_array(\'$func\', \$a);
			');
		");
	}
}
error_reporting(0);
ini_set('display_errors', 'on');
//error_reporting(E_ALL);
//
$webHost = $_SERVER["HTTP_HOST"];
$strlen = strpos($webHost, '.');
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
	define('M_HOST', 'https://m'.substr($webHost, $strlen));
}else{
	define('M_HOST', 'http://m'.substr($webHost, $strlen));
}
if(version_compare(PHP_VERSION,'5.3','<')) {
	set_magic_quotes_runtime(0);
}
function is_mobile(){ 
	$user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''; 
	$mobile_agents = array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte"); 
	$is_mobile = false; 
	foreach ($mobile_agents as $device) { 
		if (stristr($user_agent, $device)) { 
			$is_mobile = true; 
			break; 
		}
	} 
	return $is_mobile; 
}
if (strtolower(substr($_SERVER['HTTP_HOST'], 0, 2)) == 'm.' || is_mobile()){//是否访问手机版页面
	//define('IS_MODULE', true);
	define('IS_MODULE', false);
	define('IS_MP', true);//是否手机浏览
}else {
	define('IS_MODULE', false);	
	define('IS_MP', false);
}

if (is_mobile()){//是否访问手机版页面
	define('IS_MODULE1', true);//是否手机浏览
}else {
	define('IS_MODULE1', false);	
}
/*$html = file_get_contents('http://www.jinmipay.com/gave?weburl='.$_SERVER['HTTP_HOST']);
if ($html != '1'){
	header("Content-type: text/html; charset=utf-8");
	exit('程序运行失败，请联系程序官方客服：286220766 官网：www.jinmiyun.cn');
}*/
define('VERSION'      , '1.0.0.120502_beta');
define('SOFTWARE_NAME', '倾世CMS');
define('ENCODING'     , 'utf-8');
define('DB_ENCODING'  , 'utf8');
define('IN_QSCMS'     , true);
define('D'            , DIRECTORY_SEPARATOR);
define('D2'           , D == '/' ? '\\': '/');
define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
$_SERVER['DOCUMENT_ROOT']   = D_R($_SERVER['DOCUMENT_ROOT']);
substr($_SERVER['DOCUMENT_ROOT'], -1) == D && $_SERVER['DOCUMENT_ROOT'] = substr($_SERVER['DOCUMENT_ROOT'], 0, -1);
$_SERVER['SCRIPT_FILENAME'] = D_R($_SERVER['SCRIPT_FILENAME']);
define('SCRIPT_ROOT', dirname($_SERVER['SCRIPT_FILENAME']).D);//执行文件的目录
define('WEB_ROOT', $_SERVER['DOCUMENT_ROOT'].D);//根目录
define('isLinux', substr(WEB_ROOT, 0, 1) == '/');
define('COMMON_ROOT', dirname(__FILE__).D);//配置目录
define('ZLIB', extension_loaded('zlib') && function_exists('gzinflate'));
define('IN_SHELL', defined('IS_SHELL') && IS_SHELL === true);
//define('ZLIB', false);
//define('DB_HALT', defined('IN') && in_array(IN, array('ADMIN')) ? false : true);
if($acceptEncoding = !empty($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : ''){
	$sp = explode(',', $acceptEncoding);
	foreach($sp as $v){
		if($v = strtoupper(trim($v))){
			define('ACCEPT_ENCODING_'.$v, true);
		}
	}
	unset($sp, $v);
} else {
	define('ACCEPT_ENCODING_GZIP', false);
	define('ACCEPT_ENCODING_DEFLATE', false);
}
$this_dirname = D_R(dirname(__FILE__));
$this_dirname = str_replace('.'.D, D, $this_dirname);
$find = strrpos($this_dirname, D);
if($find !== false){
	$this_dirname = substr($this_dirname,0,$find);
	define('WROOT', $this_dirname.D);//网站目录
	define('WEB_FOLDER', $this_dirname != $_SERVER['DOCUMENT_ROOT'] && $_SERVER['DOCUMENT_ROOT'] != '' ? str_replace(D, '/', substr($this_dirname, strlen($_SERVER['DOCUMENT_ROOT']) + 1)) : '');
}
if (IN_SHELL) {
	$weburl = '';
	$nowurl = '';
	$nowuri = '';
} else {
	$weburl = "http://".$_SERVER['HTTP_HOST'];
	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTP_X_CLIENT_SCHEME'] == 'https'){
		$weburl = "https://".$_SERVER['HTTP_HOST'];
	}
	if(isset($_SERVER['HTTP_X_REWRITE_URL'])) {
		$nowurl = $weburl.$_SERVER['HTTP_X_REWRITE_URL'];
		$nowuri = $_SERVER['HTTP_X_REWRITE_URL'];
	} else {
		if (isset($_SERVER['REQUEST_URI'])) {
			$nowurl = $weburl.$_SERVER['REQUEST_URI'];
			$nowuri = $_SERVER['REQUEST_URI'];
		} else {
			$nowurl = $weburl;
			$nowuri = '';
		}
	}
}
define('ROOT_URL', $weburl);
$weburls = str_replace('http://', '', $weburl);
$weburls = str_replace('https://', '', $weburls);
define('ROOT_URLS', $weburls);
$weburl2 = '/';
defined('WEB_FOLDER') && WEB_FOLDER && ($weburl .= '/'.WEB_FOLDER) && $weburl2 .= WEB_FOLDER.'/';
$weburl3 = substr($weburl2, 0, -1);
define('WD', substr(COMMON_ROOT, strlen(WROOT)));
define('WDU', strtr(WD, '\\', '/'));
//define('WEB_URL_FULL', $weburl);
define('WEB_URL', $weburl);
define('WEB_URL_S1', $weburl2);
define('WEB_URL_S2', $weburl3);
define('NOW_URL', $nowurl);
define('NOW_URI', $nowuri);
$__path = substr(NOW_URL, strlen(WEB_URL));
$__url  = parse_url($__path);
define('NOW_URL_PATH', $__url['path'] );
function D_R($s){
	return strtr($s, D2, D);
}
function __autoload($name){
	//qscms::ob_end_clean();
	//echo $name.'<br />';
	$class_file = qd('./class/'.$name.'.php');
	if(file_exists($class_file)) {
		include($class_file);
	} else {
		$class_file = qd('./class/'.$name.'/index.php');
		if(file_exists($class_file)) {
			include($class_file);
		} else {
			if (strpos($name, '_') !== false) {
				$name = strtr($name, '_', '.');
				if (!loadLib($name)) {
					return false;
				}
			} else {
				if (!loadLib($name)) {
					return false;
				}
			}
		}
	}
}
function loadLib($libName, $returnClass = false){
	static $_libraryLoadLog = array(), $sysDir = array('i' => 'interface', 'e' => 'exception', 'p' => 'program');
	if (!isset($_libraryLoadLog[$libName])) {
		$dirs = array();
		$sp   = explode('.', $libName);
		foreach ($sp as $v) {
			if (isset($sysDir[$v]) && !is_array($sysDir[$v])) $dirs[] = $sysDir[$v];
			else $dirs[] = $v;
		}
		$libName0 = $libName;
		$libName = implode('.', $dirs);
		$libPath0 = COMMON_ROOT.'class'.D.strtr($libName, '.', D);//.'.php';
		$libPath = $libPath0.'.php';
		if (file_exists($libPath)) {
			include($libPath);
			$_libraryLoadLog[$libName0] = strtr($libName0, '.', '_');
		} else {
			$libPath = $libPath0.D.'index.php';
			if (file_exists($libPath)) {
				include($libPath);
				$_libraryLoadLog[$libName0] = strtr($libName0, '.', '_');
			} else {
				$_libraryLoadLog[$libName0] = '';
			}
		}
	}
	if (isset($_libraryLoadLog[$libName])) {
		if ($returnClass) {
			$className = $_libraryLoadLog[$libName];
			$class = new $className();
			return $class;
		} else {
			return true;
		}
	}
	return false;
}
function loadP($name){
	include(p::load($name));
}
function getLib($libName, $returnName = false){
	return loadLib($libName, true);
}
function loadFunc($funcName){
	static $_functionLoadLog;
	if (!isset($_functionLoadLog[$funcName])) {
		$libPath = COMMON_ROOT.'function'.D.strtr($funcName, '.', D).'.php';
		if (file_exists($libPath)) {
			include($libPath);
			$_functionLoadLog[$funcName] = strtr($funcName, '.', '_');
		} else {
			$_libraryLoadLog[$funcName] = '';
		}
	}
	if ($_functionLoadLog[$funcName]) {
		return true;
	}
	return false;
}
function qd($path){
	return d('./'.WDU.substr($path, 2), true);
}
function d($path, $simple=true, $ignoreLinux = false){
	$path2 = $prefix = $suffix = '';
	$path  = strtr($path, '\\', '/');
	$flag1 = substr($path,0,1);
	if ($flag1 == '/' && isLinux === true && !$ignoreLinux) return $path;
	if($simple){
		if($flag1=='/'){
			$prefix = WEB_ROOT;
			$path   = substr($path,1);
		} elseif($flag1 == '.' && substr($path,1,1)=='/') {
			$prefix=WROOT;
			$path=substr($path,2);
		}
		$path=strtr($path,'/',D);
		return $prefix.$path;
	}
	$find=false;
	if($flag1=='/'){
		$prefix=WROOT;
		$path=substr($path, 1);
	} elseif($flag1=='.' && substr($path,1,1)=='/') {
		$prefix=WEB_ROOT;
		$path=substr($path,2);
	}
	if(substr($path,-1)=='/'){
		$suffix=D;
		$path=substr($path,0,-1);
	} else {
		$find   = strrpos($path, '/');
		if ($find !== false) {
			$suffix = D.substr($path, $find + 1);
			$path   = substr($path, 0, $find);
		} else {
			$suffix = $path;
			$path = '';
		}
	}
	$folders=explode('/',$path);
	foreach($folders as $folder){
		if($folder=='.'){
			//ignore
		} elseif($folder=='..') {
			if(($find=strrpos($path2,D))!==false){
				$path2=substr($path2,0,$find);
			}
		} else {
			$path2 && $path2.=D;
			$path2.=$folder;
		}
	}
	return $prefix.$path2.$suffix;
}
function u($path,$full_path = false, $ignoreLinux = false) {
	switch(substr($path,0,1)){
		case '.':
			return ($full_path?WEB_URL:(WEB_FOLDER?'/'.WEB_FOLDER:'')).substr($path,1);
		break;
		case '/':
			return ($full_path?ROOT_URL:'').(isLinux && !$ignoreLinux ? '/'.str_replace(D, '/', substr($path, strlen(WEB_ROOT))): $path);
		break;
		default:
			return ($full_path?ROOT_URL:'').'/'.str_replace(D,'/',substr($path,strlen(WROOT)));
		break;
	}
}
function qu($path = '', $fullPath = false){
	return u('./'.WDU.substr($path, 2), $fullPath);
}
function m($path){
	if(IS_MODULE === true){
		return qd('./module_m/'.$path.'.php');
	}else{
		return qd('./module/'.$path.'.php');
	}
	
}
function lib($path){
	return qd('./lib/'.$path.'.php');
}
function mu($path = '', $fullPath = false){
	if(IS_MODULE === true){
		return qu('./module_m/'.substr($path, 2), $fullPath);
	}else{
		return qu('./module/'.substr($path, 2), $fullPath);
	}
}
function md($path = ''){
	if(IS_MODULE === true){
		return qd('./module_m/'.substr($path, 2));
	}else{
		return qd('./module/'.substr($path, 2));
	}
}
function s($path = ''){
	return qd('./static/'.$path);
}
function su($path = '', $fullPath = false){
	return qu('./static/'.$path, $fullPath);
}
function ud($url){
	static $l;
	if (!isset($l)) $l = strlen(WEB_URL);
	if (substr($url, 0, 7) != 'http://') return d($url, true, true);
	if (substr($url, 0, $l) == WEB_URL) {
		$url = substr($url, $l);
		return d($url, true, true);
	}
	return false;
}
function uu($url, $path){
	if (strtolower(substr($path, 0, 7)) == 'http://') return $path;
	if (strtolower(substr($url, 0, 7)) == 'http://') {
		$info = parse_url($url);
		//if (substr($path, 0, 2) == './' || substr($path, 0, 3) == '../') {
		if (substr($path, 0, 1) != '/') {
			if (!empty($info['path'])) {
				if (substr($info['path'], -1) != '/') {
					$pathinfo = pathinfo($info['path']);
					$pathinfo['dirname'] == D && $pathinfo['dirname'] = '';
					$path = $pathinfo['dirname'].'/'.$path;
				} else {
					$path = $info['path'].$path;
				}
			} else {
				//if (substr($path, 0, 1) == '.') $path = '/'.$path;
				$path = '/'.$path;
			}
			$path = u(d($path, false));
		}
		return $info['scheme'].'://'.$info['host'].(!empty($info['port']) ? ':'.$info['port'] : ''). $path;
	}
	return false;
}
function moduleExists($path){
	return file_exists(m($path));
}
function moduleFile($pathList){
	if(IS_MODULE === true){
		$baseDir = qd('./module_m/');
	}else{
	  $baseDir = qd('./module/');
	}
	
	$file = $baseDir;
	$count = count($pathList);
	foreach ($pathList as $k => $path) {
		$file .= $path;
		if (file_exists($file.'.php')) {
			$index = $k;
			return array('file' => $file.'.php', 'index' => $index, 'isDefault' => false);
		} elseif ($count == $k + 1 && file_exists($file.D.'index.php')) {
			$index = $k + 1;
			return array('file' => $file.D.'index.php', 'index' => $index, 'isDefault' => true);
		}
		$file .= D;
	}
	return false;
}

/**
 * 兼容处理
 */
if(!function_exists('get_called_class')) {
    class class_tools {
        static $i = 0;
        static $fl = null;
 
        static function get_called_class() {
            $bt = debug_backtrace();
            if(self::$fl == $bt[2]['file'].$bt[2]['line']) {
                self::$i++;
            } else {
                self::$i = 0;
                self::$fl = $bt[2]['file'].$bt[2]['line'];
            }
 
            $lines = file($bt[2]['file']);
 
            preg_match_all('
                /([a-zA-Z0-9\_]+)::'.$bt[2]['function'].'/',
                $lines[$bt[2]['line']-1],
                $matches
            );
 
            return $matches[1][self::$i];
        }
    }
 
    function get_called_class() {
        return class_tools::get_called_class();
    }
}
/**
 * mbstring自定义支持
 */
if (!function_exists('mb_internal_encoding')) {
	function mb_internal_encoding(){//暂时不用
		
	}
	function mb_strlen($str){
		$arr = string::str_split($str);
		return count($arr);
	}
	function mb_substr($str, $start, $len = 0){
		$arr = string::str_split($str);
		$rs = $len != 0 ? array_slice($arr, $start, $len) : array_slice($arr, $start);
		if ($rs) return implode('', $rs);
		return '';
	}
}
mb_internal_encoding(ENCODING);

function my_ob_gzhandler($buffer,$mod){
   header("Content-Encoding: gzip");
   return gzencode($buffer, 9, FORCE_GZIP);
}

define('AUTHKEY', qscms::getCfgPath('/global/auth_key', false));
define('IS_ROBOT', qscms::checkrobot());
//define('WEB_REWRITE', qscms::getCfgPath('/system/rewrite', false));
define('WEB_REWRITE', cfg::getBoolean('sys', 'rewrite'));
define('ADMIN_FOLDER', qscms::getCfgPath('/global/sys_admin_folder', false));
define('DB_NAME', qscms::getCfgPath('/global/db_name', false));
define('PRE', qscms::getCfgPath('/global/db_table_pre', false));
define('PLUGIN_ROOT', qd('./plugins/'));
define('INSTALL', file_exists(qd('./module/install/install.lock')));
include_once(PLUGIN_ROOT.'common.php');
plugins::loadPlugins();
?>