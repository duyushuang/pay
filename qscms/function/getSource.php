<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
set_time_limit(0);
winsock::$gzip = false;
function getUrl($str){
	$url = '';
	$flag = substr($str, 0, 1);
	if (in_array($flag, array('\'', '"'))) {
		$url = substr($str, 1, strpos($str, $flag, 1) -1);
	} else {
		if (($f = strpos($str, ' ')) !== false) {
			$url = substr($str, 0, $f - 1);
		} else {
			$url = $str;
		}
	}
	return $url;
}
function getUrl2($str){
	$rs = array();
	$flag = substr($str, 0, 1);
	if (in_array($flag, array('\'', '"'))) {
		$f   = strpos($str, $flag, 1);
		$rs['url'] = substr($str, 1, $f -1);
		$rs['str'] = substr($str, $f + 1);
	} else {
		if (($f = strpos($str, ' ')) !== false) {
			$rs['url'] = substr($str, 0, $f - 1);
			$rs['str'] = substr($str, $f);
		} else {
			if (substr($str, -1) == '/') {
				$rs['url'] = substr($str, 0, -1);
				$rs['str'] = '/';
			} else {
				$rs['url'] = $str;
				$rs['str'] = '';
			}
		}
	}
	return $rs;
}
function downImage($str, $basePath, $currPath, $saveDir){
	$str = stripslashes($str);
	$str = trim($str);
	$str1 = '';
	$str2 = '';
	if (preg_match('/<img(.*?)src\s*=(.+)>/', $str, $matches)) {
		$str1 = $matches[1];
		$str2 = trim($matches[2]);
	}
	if ($str2) {
		$urlArr = getUrl2($str2);
		$url = $urlArr['url'];
		if ($url) {
			if (strtolower(substr($url, 0, 7)) != 'http://') {
				if (substr($url, 0, 1) == '/') $url = $basePath . substr($url, 1);
				else $url = $currPath . $url;
			}
			$saveDir .= 'images' . D;
			file_exists($saveDir) || file::createFolder($saveDir);
			$urlInfo = parse_url($url);
			$pathInfo = pathinfo($urlInfo['path']);
			if (!empty($pathInfo['basename'])) {
				$saveFile = $saveDir . $pathInfo['basename'];
				if (!file_exists($saveFile)) {
					winsock::download($url, $saveFile);
				}
				return '<img'.$str1.'src="{u}images/'.$pathInfo['basename'].'"'.$urlArr['str'].'>';
			}
		}
	}
	return $str;
}
function downImage2($str, $basePath, $currPath, $saveDir){
	$str = stripslashes($str);
	$str = trim($str);
	$str2 = $str;
	if ($str2) {
		$urlArr = getUrl2($str2);
		$url = $urlArr['url'];
		if ($url) {
			if (strtolower(substr($url, 0, 7)) != 'http://') {
				if (substr($url, 0, 1) == '/') $url = $basePath . substr($url, 1);
				else $url = $currPath . $url;
			}
			$saveDir .= 'images' . D;
			file_exists($saveDir) || file::createFolder($saveDir);
			$urlInfo = parse_url($url);
			$pathInfo = pathinfo($urlInfo['path']);
			if (!empty($pathInfo['basename'])) {
				$saveFile = $saveDir . $pathInfo['basename'];
				if (!file_exists($saveFile)) {
					winsock::download($url, $saveFile);
				}
				return '"{u}images/'.$pathInfo['basename'].'"'.$rs['str'];
			}
		}
	}
	return $str;
}
function downImage3($str, $basePath, $currPath, $saveDir, $addYH = true){
	$str = stripslashes($str);
	$str = trim($str);
	$url = $str;
	if ($url) {
		if (strtolower(substr($url, 0, 7)) != 'http://') {
			if (substr($url, 0, 1) == '/') $url = $basePath . substr($url, 1);
			else $url = $currPath . $url;
		}
		$saveDir .= 'images' . D;
		file_exists($saveDir) || file::createFolder($saveDir);
		$urlInfo = parse_url($url);
		$pathInfo = pathinfo($urlInfo['path']);
		if (!empty($pathInfo['basename'])) {
			$saveFile = $saveDir . $pathInfo['basename'];
			if (!file_exists($saveFile)) {
				winsock::download($url, $saveFile);
			}
			return ($addYH ? '"' : '').'{u}images/'.$pathInfo['basename'].($addYH ? '"' : '');
		}
	}
	return $str;
}
function downCSSImage($str, $basePath, $currPath, $saveDir){
	$str = stripslashes($str);
	$str = trim($str);
	$str = trim($str, '\'');
	$str = trim($str, '"');
	$url = $str;
	if ($url) {
		if (strtolower(substr($url, 0, 7)) != 'http://') {
			if (substr($url, 0, 1) == '/') $url = $basePath . substr($url, 1);
			else $url = $currPath . $url;
		}
		//$saveDir .= 'css' . D;
		file_exists($saveDir) || file::createFolder($saveDir);
		$urlInfo = parse_url($url);
		$pathInfo = pathinfo($urlInfo['path']);
		if (!empty($pathInfo['basename'])) {
			$saveFile = $saveDir . $pathInfo['basename'];
			if (!file_exists($saveFile)) {
				winsock::download($url, $saveFile);
			}
			return $pathInfo['basename'];
		}
	}
	return $str;
}
function downJs($str, $basePath, $currPath, $saveDir){
	$str = stripslashes($str);
	$str = trim($str);
	$str = trim($str, '\'');
	$str = trim($str, '"');
	$str2 = '';
	if (preg_match('/<script.*?src\s*=(.+)>/i', $str, $matches)) {
		$str2 = trim($matches[1]);
	}
	if ($str2) {
		$url = getUrl($str2);
		if ($url) {
			if (strtolower(substr($url, 0, 7)) != 'http://') {
				if (substr($url, 0, 1) == '/') $url = $basePath . substr($url, 1);
				else $url = $currPath . $url;
			}
			$saveDir .= 'js' . D;
			file_exists($saveDir) || file::createFolder($saveDir);
			$urlInfo = parse_url($url);
			$pathInfo = pathinfo($urlInfo['path']);
			if (!empty($pathInfo['filename'])) {
				$pathInfo['filename'] .= '___' . md5($url);
				$saveFile = $saveDir . $pathInfo['filename'] . '.js';
				if (!file_exists($saveFile)) {
					winsock::download($url, $saveFile);
				}
				return '{jsFile '.$pathInfo['filename'].'}';
			}
		}
	}
	return $str;
}

function downCSS($str, $basePath, $currPath, $saveDir){
	$str = stripslashes($str);
	$str = trim($str);
	$str2 = '';
	if (strpos(strtolower($str), 'text/css') !== false) {
		if (preg_match('/<link.*?href\s*=(.+)>/i', $str, $matches)) {
			$str2 = trim($matches[1]);
		}
	}
	if ($str2) {
		$url = '';
		$flag = substr($str2, 0, 1);
		if (in_array($flag, array('\'', '"'))) {
			$url = substr($str2, 1, strpos($str2, $flag, 1) -1);
		} else {
			if (($f = strpos($str2, ' ')) !== false) {
				$url = substr($str2, 0, $f - 1);
			} else {
				$url = $str2;
			}
		}
		if ($url) {
			if (strtolower(substr($url, 0, 7)) != 'http://') {
				if (substr($url, 0, 1) == '/') $url = $basePath . substr($url, 1);
				else $url = $currPath . $url;
			}
			$saveDir .= 'css' . D;
			$urlInfo = parse_url($url);
			$pathInfo = pathinfo($urlInfo['path']);
			file_exists($saveDir) || file::createFolder($saveDir);
			if (!empty($pathInfo['filename'])) {
				$pathInfo['filename'] .= '___' . md5($url);
				$saveFile = $saveDir . $pathInfo['filename'] . '.css';
				if (!file_exists($saveFile)) {
					winsock::download($url, $saveFile);
				}
				if (file_exists($saveFile)) {
					$cssCode = file::read($saveFile);
					$dirname = str_replace('\\', '/', $pathInfo['dirname']);
					$dirname == '/' && $dirname = '';
					$cssPath = substr($basePath, 0, -1) . $dirname . '/';
					$cssCode = preg_replace('/(background(?:-image)?\s*\:.*?url\()(.+?)\)/ie', 'stripslashes(\'$1\').downCSSImage(\'$2\', $basePath, $cssPath, $saveDir).\')\'', $cssCode);
					file::write($saveFile, $cssCode);
				}
				return '{cssFile '.$pathInfo['filename'].'}';
			}
		}
	}
	return $str;
}
function downCSSFile($url, $saveDir){
	if (($url = trim($url)) && ($saveDir = trim($saveDir))) {
		if (!preg_match('/^http:\/\/.*$/i', $url)) return 'URL格式错误，请输入完整URL地址';
		if (!preg_match('/^\.\/(\w+\/)*$/', $saveDir)) return '保存地址格式错误，请以./开头';
		$urlInfo = parse_url($url);
		$tplFile = $tplFilename = '';
		$tplSaveDir = d($saveDir);
		if (empty($urlInfo['path'])) $urlInfo['path'] = '/';
		if ($urlInfo['path'] == '/') {
			$tplFilename = 'index';
			$urlBasePath = $urlInfo['scheme'] . '://' . $urlInfo['host'] . (!empty($urlInfo['port']) ? ':' . $urlInfo['port'] : '') . '/';
			$urlCurrPath = $urlBasePath;
		} else {
			$pathinfo = pathinfo($urlInfo['path']);
			if ($pathinfo['filename']) $tplFilename = $pathinfo['filename'];
			else $tplFilename = 'index';
			if (substr($urlInfo['path'], -1) == '/') $dirname = substr($urlInfo['path'], 0, -1);
			else $dirname = str_replace('\\', '/', $pathinfo['dirname']);
			$urlBasePath = $urlInfo['scheme'] . '://' . $urlInfo['host'] . (!empty($urlInfo['port']) ? ':' . $urlInfo['port'] : '') . '/';
			$urlCurrPath = $urlInfo['scheme'] . '://' . $urlInfo['host'] . (!empty($urlInfo['port']) ? ':' . $urlInfo['port'] : '') . ($dirname == '/' ? $dirname : $dirname . '/');
		}
		if ($tplFilename) {
			$tplFile = $tplSaveDir . $tplFilename;
			if (file_exists($tplSaveDir)) {
				$i = 0;
				$tplFile0 = $tplFile;
				//让文件名不重复
				/*while (file_exists($tplFile0 . '.htm')) {
					$tplFile0 = $tplFile . '_' . $i;
					$i ++;
				}*/
				$tplFile = $tplFile0 . '.css';
			} else {
				file::createFolder($tplSaveDir);
				$tplFile .= '.css';
			}
			if ($cssCode = winsock::get_html($url)) {
				$cssCode = preg_replace('/(background(?:-image)?\s*\:.*?url\()(.+?)\)/ie', 'stripslashes(\'$1\').downCSSImage(\'$2\', $urlBasePath, $urlCurrPath, $saveDir).\')\'', $cssCode);
				file::write($tplFile, $cssCode);
				return true;
			} else {
				return '获取HTML超时！';
			}
		} else {
			return '错误！';
		}
	} else {
		return '参数填写不完整！';
	}
}
function startDown($url, $saveDir){
	if (($url = trim($url)) && ($saveDir = trim($saveDir))) {
		if (!preg_match('/^http:\/\/.*$/i', $url)) return 'URL格式错误，请输入完整URL地址';
		if (!preg_match('/^\.\/(\w+\/)*$/', $saveDir)) return '保存地址格式错误，请以./开头';
		$urlInfo = parse_url($url);
		$tplFile = $tplFilename = '';
		$tplSaveDir = d($saveDir);
		if (empty($urlInfo['path'])) $urlInfo['path'] = '/';
		if ($urlInfo['path'] == '/') {
			$tplFilename = 'index';
			$urlBasePath = $urlInfo['scheme'] . '://' . $urlInfo['host'] . (!empty($urlInfo['port']) ? ':' . $urlInfo['port'] : '') . '/';
			$urlCurrPath = $urlBasePath;
		} else {
			$pathinfo = pathinfo($urlInfo['path']);
			if ($pathinfo['filename']) $tplFilename = $pathinfo['filename'];
			else $tplFilename = 'index';
			if (substr($urlInfo['path'], -1) == '/') $dirname = substr($urlInfo['path'], 0, -1);
			else $dirname = str_replace('\\', '/', $pathinfo['dirname']);
			$urlBasePath = $urlInfo['scheme'] . '://' . $urlInfo['host'] . (!empty($urlInfo['port']) ? ':' . $urlInfo['port'] : '') . '/';
			$urlCurrPath = $urlInfo['scheme'] . '://' . $urlInfo['host'] . (!empty($urlInfo['port']) ? ':' . $urlInfo['port'] : '') . ($dirname == '/' ? $dirname : $dirname . '/');
		}
		if ($tplFilename) {
			$tplFile = $tplSaveDir . $tplFilename;
			if (file_exists($tplSaveDir)) {
				$i = 0;
				$tplFile0 = $tplFile;
				//让文件名不重复
				/*while (file_exists($tplFile0 . '.htm')) {
					$tplFile0 = $tplFile . '_' . $i;
					$i ++;
				}*/
				$tplFile = $tplFile0 . '.htm';
			} else {
				file::createFolder($tplSaveDir);
				$tplFile .= '.htm';
			}
			if ($html = winsock::get_html($url)) {
				//替换
				$html = preg_replace('/(<img.*?>)/ie', 'downImage(\'$1\', $urlBasePath, $urlCurrPath, $tplSaveDir)', $html);
				$html = preg_replace('/(<.*?background)\s*=(.+)>/ie', 'stripslashes(\'$1\').\'=\'.downImage2(\'$2\', $urlBasePath, $urlCurrPath, $tplSaveDir).\'>\'', $html);
				$html = preg_replace('/(<.*?")([^"]+?\.swf)"/ie', 'stripslashes(\'$1\').downImage3(\'$2\', $urlBasePath, $urlCurrPath, $tplSaveDir, false).\'"\'', $html);
				$html = preg_replace('/(<.*?\')([^\']+?\.swf)\'/ie', 'stripslashes(\'$1\').downImage3(\'$2\', $urlBasePath, $urlCurrPath, $tplSaveDir, false).\'\\\'\'', $html);
				$html = preg_replace('/(background(?:-image)?\s*\:\s*url\()(.+?)\)/ie', 'stripslashes(\'$1\').downImage3(\'$2\', $urlBasePath, $urlCurrPath, $tplSaveDir, false).\')\'', $html);
				$html = preg_replace('/(<script.*?src\s*=.+?>\s*<\/script>)/ie', 'downJs(\'$1\', $urlBasePath, $urlCurrPath, $tplSaveDir)', $html);
				$html = preg_replace('/(<link\s.*?>)/ie', 'downCSS(\'$1\', $urlBasePath, $urlCurrPath, $tplSaveDir)', $html);
				file::write($tplFile, $html);
				return true;
			} else {
				return '获取HTML超时！';
			}
		} else {
			return '错误！';
		}
	} else {
		return '参数填写不完整！';
	}
}
?>