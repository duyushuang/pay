<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class html_page{
	public static function checkModify($time, $contentType = 'text/html'){
		$gmt_mtime = gmdate('D, d M Y H:i:s', $time).' GMT';
		if((isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && array_shift(explode(';', $_SERVER['HTTP_IF_MODIFIED_SINCE'])) ==  $gmt_mtime)){
			header("HTTP/1.1 304 Not Modified");
			header("Expires: ");
			header("Cache-Control: ");
			header("Pragma: ");
			header('Content-Type: '.$contentType.'; charset='.ENCODING);
			header("Tips: Cache Not Modified");
			header('Content-Length: 0');
			return false;
		}
		header("Last-Modified:" . $gmt_mtime);
		header("Expires: ");
		header("Cache-Control: ");
		header("Pragma: ");
		header('Content-Type: '.$contentType.'; charset='.ENCODING);
		header("Tips: Normal Respond");
		return true;
	}
	public static function checkModifyToTpl($tplName){
		$tplPath = template::getPath($tplName, false);
		if (file_exists($tplPath)) {
			return self::checkModify(@filemtime($tplPath));
		}
		return true;
	}
}
?>