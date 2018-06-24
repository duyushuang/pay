<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
define('DB_PRE_FIX', 'version:1.0;©2013 www.qscms.com;author:373718549@qq.com');
define('DB_PRE_FIX_LEN', strlen(DB_PRE_FIX));
function db_writeData($f, $data){
	$len = strlen($data);
	fwrite($f, sprintf('% 10d', $len));
	fwrite($f, $data);
}
function db_getWrite($data){
	$str = '';
	$len = strlen($data);
	$str = sprintf('% 10d', $len);
	$str .= $data;
	return $str;
}
function db_getTrigger($name, $replacePre = false){
	is_array($name) || $name = array($name);
	$list = array();
	foreach ($name as $v) {
		$item = db::fetchFirst('SHOW CREATE TRIGGER '.$v);
		//print_r($item);
		$tname = $item['Trigger'];
		$sql0 = 'DROP TRIGGER IF EXISTS '.$tname;
		$sql1 = $item['SQL Original Statement'];
		$sql1 = preg_replace('/^CREATE(.+?)TRIGGER/', 'CREATE TRIGGER', $sql1);
		if ($replacePre) {
			$sql0 = str_replace(PRE, '{pre}', $sql0);
			$sql1 = str_replace(PRE, '{pre}', $sql1);
		}
		$list[] = $sql0;
		$list[] = $sql1;
		//return db_getWrite($sql0).db_getWrite($sql1);
	}
	return $list;
}
function db_writeHeader($f){
	fwrite($f, DB_PRE_FIX.str_repeat(' ', 10));
}
function db_writeCount($f, $count){
	$count = sprintf('% 10d', $count);
	fseek($f, DB_PRE_FIX_LEN);
	fwrite($f, $count);
}
function db_openFile($file){
	if ($f = @fopen($file, 'rb')) {
		$r = fread($f, DB_PRE_FIX_LEN);
		if ($r == DB_PRE_FIX) {
			fseek($f, 0, SEEK_SET);
			return $f;
		} else {
			fclose($f);
			return false;
		}
	}
	return false;
}
function db_getCount($f){
	fseek($f, DB_PRE_FIX_LEN);
	$count = intval(trim(fread($f, 10)));
	return $count;
}
?>