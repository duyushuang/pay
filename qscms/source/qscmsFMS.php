<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */
if(version_compare(PHP_VERSION,'5.0.0','<'))  die('require PHP > 5.0 !');
error_reporting(E_ALL);
//print_r($_POST);
//print_r($_FILES);
//echo file_get_contents($_FILES['postData']['tmp_name']);
//exit;
extract($_GET);
$_key = 'qscms_fms_key';//通信密钥 非常重要
if (empty($key) || $key != $_key) exit;
if(version_compare(PHP_VERSION,'5.3','<')) {
	set_magic_quotes_runtime(0);
}
define('SOFTWARE_NAME', '倾世CMS文件管理');
define('ENCODING'     , 'utf-8');
define('D'            , DIRECTORY_SEPARATOR);
define('D2'           , D == '/' ? '\\': '/');
define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
$_SERVER['DOCUMENT_ROOT']   = D_R($_SERVER['DOCUMENT_ROOT']);
$_SERVER['SCRIPT_FILENAME'] = D_R($_SERVER['SCRIPT_FILENAME']);
define('SCRIPT_ROOT', dirname($_SERVER['SCRIPT_FILENAME']).D);//执行文件的目录
define('WEB_ROOT', $_SERVER['DOCUMENT_ROOT'].D);//根目录
define('isLinux', substr(WEB_ROOT, 0, 1) == '/');
define('COMMON_ROOT', dirname(__FILE__).D);//配置目录
define('ZLIB', extension_loaded('zlib') && function_exists('gzinflate'));

mb_internal_encoding(ENCODING);
$this_dirname = D_R(dirname(__FILE__));
$this_dirname = str_replace('.'.D, D, $this_dirname);
$find = strrpos($this_dirname, D);
if($find !== false){
	define('WROOT', $this_dirname.D);//网站目录
	define('WEB_FOLDER', $this_dirname != $_SERVER['DOCUMENT_ROOT'] && $_SERVER['DOCUMENT_ROOT'] != '' ? str_replace(D, '/', substr($this_dirname, strlen($_SERVER['DOCUMENT_ROOT']) + 1)) : '');
}
$weburl = "http://".$_SERVER['HTTP_HOST'];
if(isset($_SERVER['HTTP_X_REWRITE_URL'])) $nowurl = $weburl.$_SERVER['HTTP_X_REWRITE_URL'];
else $nowurl = $weburl.$_SERVER['REQUEST_URI'];
define('ROOT_URL', $weburl);
$weburl2 = '/';
defined('WEB_FOLDER') && WEB_FOLDER && ($weburl .= '/'.WEB_FOLDER) && $weburl2 .= WEB_FOLDER.'/';
$weburl3 = substr($weburl2, 0, -1);
if (empty($path)) $path = '';
else {
	$path = trim($path);
	$path = str_replace('\\', '/', $path);
	substr($path, 0, 1) == '/' && $path = substr($path, 1);
	substr($path, -1) != '/' && $path .= '/';
}
define('WD', str_replace('/', D, $path));
define('WDU', $path);
//define('WEB_URL_FULL', $weburl);
define('WEB_URL', $weburl);
define('WEB_URL_S1', $weburl2);
define('WEB_URL_S2', $weburl3);
define('NOW_URL', $nowurl);
$__path = substr(NOW_URL, strlen(WEB_URL));
$__url  = parse_url($__path);
define('NOW_URL_PATH', $__url['path'] );
function D_R($s){
	return strtr($s, D2, D);
}
function qd($path){
	if (WDU == '/') return d('./'.substr($path, 2), true);
	return d('./'.WDU.substr($path, 2), true);
}
function d($path, $simple=true){
	$path2 = $prefix = $suffix = '';
	$path  = strtr($path, '\\', '/');
	$flag1 = substr($path,0,1);
	if ($flag1 == '/' && isLinux === true) return $path;
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
		$path=substr($path,1);
	} elseif($flag1=='.' && substr($path,1,1)=='/') {
		$prefix=WEB_ROOT;
		$path=substr($path,2);
	}
	if(substr($path,-1)=='/'){
		$suffix=D;
		$path=substr($path,0,-1);
	} else {
		$find=strrpos($path,'/');
		$suffix=D.substr($path,$find+1);
		$path=substr($path,0,$find);
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
function u($path,$full_path = false) {
	switch(substr($path,0,1)){
		case '.':
			return ($full_path?WEB_URL:(WEB_FOLDER?'/'.WEB_FOLDER:'')).substr($path,1);
		break;
		case '/':
			return ($full_path?ROOT_URL:'').(isLinux ? '/'.strtr(substr($path, strlen(WEB_ROOT)), D, '/'): $path);
		break;
		default:
			return ($full_path?ROOT_URL:'').'/'.strtr(substr($path,strlen(WROOT)), D,'/');
		break;
	}
}
function qu($path, $fullPath = false){
	return u('./'.WDU.substr($path, 2), $fullPath);
}
function p($path){
	static $root;
	if (!isset($root)) $root = qd('./');
	return '/'.strtr(substr($path, strlen($root)), D, '/');
}
function delFile($file){
	return @unlink($file);
}
function delDir($path){
	if (file_exists($path)) {
		substr($path, -1) != D && $path .= D;
		if ($o = opendir($path)) {
			while ($r = readdir($o)) {
				if (!in_array($r, array('.', '..'))) {
					$file = $path.$r;
					if (filetype($file) == 'dir') {
						if (!delDir($file)) return false;
					} else {
						if (!delFile($file)) return false;
					}
				}
			}
			closedir($o);
		}
		return @rmdir($path);
	}
	return false;
}
function createPath($path){
	//echo file_exists('/alidata/www/mmm/qscms') ? 1: 2;exit;
	$p = '';
	substr($path, -1) == D && $path = substr($path, 0, -1);
	if (file_exists($path)) return true;
	//substr($path, 0, 1) == D && $path = substr($path, 1);
	//echo $path.'<br />';
	foreach (explode(D, $path) as $v) {
		if ($v){
			$p .= $v;
			if (!file_exists($p)) {
				if (!@mkdir($p)) return false;
			}
		}
		$p .= D;
	}
	return true;
}
function getPath($file){
	static $root;
	if (!isset($root)) $root = qd('./');
	if (file_exists($root)) {
		$file = empty($file) ? '' : $file;
		$file = iconv(ENCODING, 'GBK', $file);
		$file = strtr($file, '/', D);
		substr($file, 0, 1) == D && $file = substr($file, 1);
		substr($file, -1) != D && $file .= D;
		$file == D && $file = '';
		return $root.$file;
		
	}
	return false;
}
function getFDList($file){
	static $root;
	if (!isset($root)) $root = qd('./');
	$list = array();
	$path = getPath($file);
	if ($path && @file_exists($path)) {
		if ($o = @opendir($path)) {
			$dirs = $files = array();
			while ($r = readdir($o)) {
				if (!in_array($r, array('.', '..'))) {
					$file = $path.$r;
					if (@filetype($file) == 'dir') {
						$dirs[] = iconv('GBK', ENCODING, $r);
					} else {
						$files[] = iconv('GBK', ENCODING, $r);
					}
				}
			}
			closedir($o);
			$list['dirs']  = $dirs;
			$list['files'] = $files;
		}
	}
	return $list;
}
function dirIsEmpty($file){
	$arr = getFDList($file);
	return !$arr['dirs'] && !$arr['files'];
}
if (!empty($action)) {
	$path = qd('./');
	switch ($action) {
		case 'fdlist':
			echo serialize(getFDList(empty($file) ? '' : $file));
		break;
		case 'exists':
			echo file_exists(getPath($file)) ? 'success' : 'none';
		break;
		case 'del':
			if (!empty($op)) {
				switch ($op) {
					case 'file':
						if (!empty($file)) {
							$file = iconv(ENCODING, 'GBK', $file);
							$file = str_replace('/', D, $file);
							substr($file, 0, 1) == D && $file = substr($file, 1);
							$file = $path.$file;
							if (file_exists($file)) {
								if (delFile($file)) echo 'success';
							}
						}
					break;
					case 'dir':
						if (!empty($file)) {
							$file = iconv(ENCODING, 'GBK', $file);
							$file = str_replace('/', D, $file);
							substr($file, 0, 1) == D && $file = substr($file, 1);
							$file = $path.$file;
							if (file_exists($file)) {
								if (delDir($file)) echo 'success';
							}
						}
					break;
					case 'fileAndDir':
						if (!empty($file)) {
							$file = iconv(ENCODING, 'GBK', $file);
							$file = str_replace('/', D, $file);
							substr($file, 0, 1) == D && $file = substr($file, 1);
							$file = $path.$file;
							$info = pathinfo($file);
							$dir = p($info['dirname']);
							if (file_exists($file)) {
								if (delFile($file)) {
									if ($dir != '/' && dirIsEmpty($dir)) {
										@rmdir($info['dirname']);
									}
									echo 'success';
								}
							}
						}
					break;
				}
			}
		break;
		case 'upload':
			if (!empty($file)) {
				$file = iconv(ENCODING, 'GBK', $file);
				$file = str_replace('/', D, $file);
				substr($file, 0, 1) == D && $file = substr($file, 1);
				$file = $path.$file;
				$pathinfo = pathinfo($file);
				$path = $pathinfo['dirname'];
				$path = str_replace('/', D, $path);
				if (!empty($varName)) {
					if (createPath($path)) {
						$f = !empty($_FILES[$varName]) ? $_FILES[$varName] : array();
						if ($f) {
							if (@move_uploaded_file($f['tmp_name'], $file) || @copy($f['tmp_name'], $file)) {
								@unlink($f['tmp_name']);
								echo 'success';
							}
						}
					}
				}
			}
		break;
	}
}
?>