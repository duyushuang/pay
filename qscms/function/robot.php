<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
function robot_getUrl($urls, $index){
	$urls = qscms::trimExplode("\n", $urls);
	qscms::arrayUnsetEmpty($urls);
	$urls = array_values($urls);
	return $urls[$index];
}
function robot_getUrlPage($url){
	if (preg_match('/\{(\d+)-(\d+)\}/', $url, $matches)) {
		return array('start' => intval($matches[1]), 'end' => intval($matches[2]));
	}
	return false;
}
function robot_getNowUrl($urls, $urlIndex, $pageIndex){
	$url = robot_getUrl($urls, $urlIndex);
	$pageInfo = robot_getUrlPage($url);
	if ($pageInfo) return preg_replace('/\{.+?\}/', $pageIndex, $url);
	return $url;
}
function robot_downImg($url, $saveDir){
	file::createFolder($saveDir);
	$urlinfo = parse_url($url);
	$pathinfo = pathinfo($urlinfo['path']);
	$saveFile = upload::tempname($saveDir, $pathinfo['extension']);
	winsock::download($url, $saveFile);
	if (file_exists($saveFile)) {
		return $saveFile;
	}
	return false;
}
function robot_isPreg($str){
	return preg_match('/^\/.+?\/[a-z]*$/s', $str) > 0;
}
?>