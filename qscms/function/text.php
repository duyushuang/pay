<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */

function getTextData(){
	$txtData = memory::get('gbTextData');
	if (!$txtData) {
		$nameFile   = s('data/name.txt');
		$pinyinFile = s('data/pinyin.txt');
		$gbFile     = s('data/gb.txt');
		$txtData = array(
			'name'    => '',
			'nameLen' => 0,
			'pinyin'  => '',
			'gb'      => '',
			'gbLen'   => 0
		);
		if (file_exists($nameFile) && file_exists($pinyinFile) && file_exists($gbFile)) {
			$txtData['name'] = iconv('gbk', ENCODING, file::read($nameFile));
			$txtData['nameLen'] = mb_strlen($txtData['name']);
			$data = iconv('gbk', ENCODING, file::read($pinyinFile));
			$list = array();
			foreach (qscms::trimExplode("\n", $data) as $v) {
				$sp = qscms::trimExplode('|', $v);
				$list[$sp[0]] = $sp[1];
			}
			$txtData['pinyin'] = $list;
			unset($list);
			$txtData['gb'] = iconv('gbk', ENCODING, file::read($gbFile));
			$txtData['gbLen'] = mb_strlen($txtData['gb']);
			memory::write('gbTextData', $txtData);
		}
	}
	return $txtData;
}
function getRandName(){
	$txtData = getTextData();
	if ($txtData) {
		$rn = '';
		$len = rand(1, 3);
		$rn = mb_substr($txtData['name'], rand(1, $txtData['nameLen']) - 1, 1);
		for ($i = 0; $i < $len; $i++) {
			$rn .= mb_substr($txtData['gb'], rand(1, $txtData['gbLen']) - 1, 1);
		}
		return $rn;
	}
	return '';
}
function getPinyin($txt){
	$txtData = getTextData();
	if ($txtData) {
		$rn = '';
		$len = mb_strlen($txt);
		for ($i = 0; $i < $len; $i++) {
			$s = mb_substr($txt, $i, 1);
			$isFind = false;
			foreach ($txtData['pinyin'] as $k => $v) {
				if (strpos($v, $s) !== false) {
					$rn .= $k;
					$isFind = true;
					break;
				}
			}
			//!$isFind && $rn .= $s;
		}
		return $rn;
	}
	return '';
}
function getPinyinFirst($txt){
	$txtData = getTextData();
	if ($txtData) {
		$rn = '';
		$len = mb_strlen($txt);
		for ($i = 0; $i < $len; $i++) {
			$s = mb_substr($txt, $i, 1);
			$isFind = false;
			foreach ($txtData['pinyin'] as $k => $v) {
				if (strpos($v, $s) !== false) {
					$rn .= substr($k, 0, 1);
					$isFind = true;
					break;
				}
			}
			//!$isFind && $rn .= $s;
		}
		return $rn;
	}
	return '';
}
function getRandNamePY(){
	return getPinyin(getRandName());
}
?>