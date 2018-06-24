<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
function url_addOne($url, $alias, &$rs = ''){
	if ($url) {
		$md5 = md5($url);
		$time = time::$timestamp;
		db::lockTableWrite('url');
		if ($alias) {
			if (!preg_match('/^[a-zA-Z0-9-_]{1,16}$/', $alias)) {
				return '自定义名称格式不正确';
			} else {
				if (!db::exists('url', array('surl' => $alias))) {
					if (db::insert('url', array(
						'url'  => $url,
						'surl' => $alias,
						'md5'  => $md5,
						'isCus' => 1,
						'time'  => $time
					))) {
						$rs = $alias;
						return true;
					} else return '添加失败，请重试';
				} else return '该网址已经存在了';
			}
		} else {
			$info = db::one('url', '*', "md5='$md5'");
			if ($info) {
				$rs = $info['surl'];
				return true;
			} else {
				$surl = db::one_one('url', 'surl', "isCus='0'", 'time DESC');
				do {
					$surl = num64::plusOne($surl);
				} while (db::exists('url', array('surl' => $surl)));
				if (db::insert('url', array(
					'url'  => $url,
					'surl' => $surl,
					'md5'  => $md5,
					'time' => $time
				))) {
					$rs = $surl;
					return true;
				} else return '添加失败，请重试';
			}
		}
		db::unlockTables();
	} else return '请输入长网址';
}
function url_getLongUrl($url, &$longUrl = ''){
	if ($url) {
		$l = strlen(WEB_URL.'/');
		if (substr($url, 0, $l) == WEB_URL.'/') {
			$surl = substr($url, $l);
			if (preg_match('/^[a-zA-Z0-9-_]{1,16}$/', $surl)) {
				$info = db::one('url', '*', "surl='$surl'");
				if ($info) {
					$longUrl = $info['url'];
					return true;
				} else return '不存在该短网址';
			}
		}
	}
	return '短网址格式错误';
}
?>