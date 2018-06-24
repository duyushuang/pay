<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
function info_thumb($ids, $ignoreExists = false){
	is_array($ids) || $ids = array($ids);
	$count = 0;
	$tmpDir = d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/download'));
	file::createFolder($tmpDir);
	$saveDir = qscms::getImgDir('info');
	foreach (db::select('info_img', 'id,mid,filename,suffix,did', 'id '.sql::getInStr($ids)) as $v) {
		$thumbs = module::getThumbAllAt($v['mid']);
		$thumbNames = array_keys($thumbs);
		if ($v['did']) {
			$d = disperse_obj::getObj($v['did']);
			if (!$d) continue;
		} else $d = false;
		$isSet = false;
		if ($ignoreExists) {//忽略已存在的
			if ($d) {//远程储存的
				if ($d->fileExists('/'.$v['filename'].'.'.$v['suffix'])) {
					foreach ($thumbNames as $v1) {
						if (!$d->fileExists('/'.$v['filename'].$v1.'.'.$v['suffix'])) {
							$isSet = true;
							break;
						}
					}
				} else continue;//远程源文件不存在 跳过
			} else {//本地储存的
				if (file_exists($saveDir.$v['filename'].'.'.$v['suffix'])) {
					foreach ($thumbNames as $v1) {
						if (!file_exists($saveDir.$v['filename'].$v1.'.'.$v['suffix'])) {
							$isSet = true;
							break;
						}
					}
				} else continue;//本地源文件不存在 跳过
			}
		} else {
			$isSet = true;
		}
		$ignoreExists = true;
		if ($isSet) {
			if ($d) {
				$surl1 = strtr($v['filename'], '/\\', '__');
				$dfilePre = $tmpDir.$surl1;
				$sfile = $dfilePre.'.'.$v['suffix'];
				if (!winsock::downloadFull($d->webUrl.'/'.$v['filename'].'.'.$v['suffix'], $sfile)) {
					continue;
				}
			} else {
				$surl1 = $v['filename'];
				$sfile = $saveDir.$v['filename'].'.'.$v['suffix'];
				$dfilePre = $saveDir.$v['filename'];
			}
			foreach ($thumbs as $k => $v1) {
				$dfile =  $dfilePre.$k.'.'.$v['suffix'];
				if (!$ignoreExists || ((!$d && !file_exists($dfile)) || ($d && !$d->fileExists('/'.$v['filename'].$k.'.'.$v['suffix'])))) {//忽略存在的
					if ($v1['width'] && $v1['height']) {//裁剪
						image::thumb($sfile, $dfile, $v1, 'cutout');
					} else image::thumb($sfile, $dfile, $v1, 'zoom');//缩小
				}
			}
			if ($d) {
				foreach ($thumbNames as $v1) {
					$sfile = $dfilePre.$v1.'.'.$v['suffix'];
					$dfile = '/'.$v['filename'].$v1.'.'.$v['suffix'];
					if (file_exists($sfile)) {
						$d->uploadFile($dfile, $sfile);
						@unlink($sfile);
					}
				}
				@unlink($dfilePre.'.'.$v['suffix']);
			}
		}
	}
}
?>