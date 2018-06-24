<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class dfile{
	public function __construct($type, $did = -1){
		$did == -1 && $did = cfg::getInt('sys', 'disperse_'.$type);
		$this->isLocal = true;
		$this->cacheDir = d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/upload'));
		$this->downDir = d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/download'));
		file::createFolder($this->cacheDir);
		file::createFolder($this->downDir);
		if ($did > 0) {
			$d = disperse_obj::getObj($did);
			if ($d) {
				$this->isLocal = false;
				$this->d = $d;
			}
		}
		if ($this->isLocal) {
			$this->localDir = qscms::getImgDir($type);
			$this->localUrl = qscms::getImgUrl($type);
			$this->did = 0;
		} else $this->did = $did;
	}
	public function isImage($img_content){//判断数据是否是图片
		if (function_exists('imagecreatefromstring')){
			$is_img = @imagecreatefromstring($img_content);
			if ($is_img != false){
				return true;
			}
			return 'Not a picture';
		}
		return 'System does not support this function';
	}
	public function uploadImageContent($img_content, $saveName, $is_rand = true, $saveDir = ''){//上传图片内容  $saveName 要带后缀
		$rs = pathinfo($saveName);
		$suffix = $rs['extension'];
		if (!in_array(strtolower($suffix), array('jpg', 'jpeg', 'png', 'gif'))) return 'No name and suffix';
		$saveDir || $saveDir = date('/Y/m/d/', time::$timestamp);
		if (substr($saveDir, -1) != '/') {
			$f = strrpos($saveDir, '/');
			$saveName = substr($saveDir, $f + 1);
			$saveDir = substr($saveDir, 0, $f + 1);
		}
		$upDir = $this->cacheDir;
		$is_img = $this->isImage($img_content);
		if ($is_img === true){
			if ($this->isLocal) {//本地
				if ($saveName && $suffix){
					$rand = time().rand(0, 1000);
					$saveName = ($is_rand ? $rand.'.'.$suffix : $saveName);
					$dfile = $saveDir.$saveName;
					$dfile = $this->localDir.strtr(substr($dfile, 1), '/', D);
					file::createFolderToFile($dfile);
					if (file_put_contents($dfile, $img_content)){
						return array('basename' => substr($saveDir.$saveName, 1), 'filename' => substr($saveDir.$rand, 1), 'suffix' => $suffix, 'sz' => getimagesize($dfile));
					} return 'Path error';
				}
				return 'No name and suffix';
			} else {//远程
				return 'Without this function';
			/*
				$this->d->uploadFile($v['dfile'], $v['sfile']);
				@unlink($v['sfile']);
				$rs[] = substr($v['dfile'], 1);
			*/
			}
			//$rs = array('filename' => substr($saveDir, 1).($saveName ? $saveName : $arr['filename']), 'suffix' => $arr['suffix'], 'files' => $rs);
			//$rs['basename'] = $rs['filename'].'.'.$rs['suffix'];
		}else return $is_img;
	}
	public function uploadImage($controlName, $saveDir = '', $thumbs = array()){
		$saveDir || $saveDir = date('/Y/m/d/', time::$timestamp);
		$saveName = '';
		if (substr($saveDir, -1) != '/') {
			$f = strrpos($saveDir, '/');
			$saveName = substr($saveDir, $f + 1);
			$saveDir = substr($saveDir, 0, $f + 1);
		}
		$upDir = $this->cacheDir;
		$arr = upload::uploadImage($controlName, $upDir, true);
		if(!empty($arr['errors'])) return $arr;
		if ($arr) {
			$files = array();
			$sfile = $upDir.$arr['basename'];
			$files[] = array('sfile' => $sfile, 'dfile' => $saveDir.($saveName ? $saveName.'.'.$arr['suffix'] : $arr['basename']));
			foreach ($thumbs as $k => $v) {
				$dfile = $upDir.($saveName ? $saveName : $arr['filename']).$k.'.'.$arr['suffix'];
				if ($v['width'] && $v['height']) {//裁剪
					image::thumb($sfile, $dfile, $v, 'zoom');//cutout
				} else image::thumb($sfile, $dfile, $v, 'zoom');//缩小
				$files[] = array('sfile' => $dfile, 'dfile' => $saveDir.($saveName ? $saveName : $arr['filename']).$k.'.'.$arr['suffix']);
			}
			$rs = array();
			if ($this->isLocal) {
				foreach ($files as $v) {
					$sfile = $v['sfile'];
					$dfile = $v['dfile'];
					$dfile = $this->localDir.strtr(substr($dfile, 1), '/', D);
					file::createFolderToFile($dfile);
					file::moveFile($sfile, $dfile);
					$rs[] = substr($v['dfile'], 1);
				}
			} else {
				foreach ($files as $v) {
					$this->d->uploadFile($v['dfile'], $v['sfile']);
					@unlink($v['sfile']);
					$rs[] = substr($v['dfile'], 1);
				}
			}
			$rs = array('filename' => substr($saveDir, 1).($saveName ? $saveName : $arr['filename']), 'suffix' => $arr['suffix'], 'files' => $rs);
			$rs['basename'] = $rs['filename'].'.'.$rs['suffix'];
			return $rs;
		}
		return false;
	}
	public function downloadImage($urls, $saveDir = '', $thumbs = array()){
		$saveDir || $saveDir = date('/Y/m/d/', time::$timestamp);
		$sp = qscms::trimExplode('|', $urls);
		qscms::arrayUnsetEmpty($sp);
		$ids = array();
		$list = array();
		if ($sp) {
			foreach ($sp as $url) {
				$suffix = strtolower(substr($url, strrpos($url, '.') + 1));
				if (in_array($suffix, array('jpg', 'jpeg', 'png', 'gif'))) {
					$cacheFile = upload::tempname($this->downDir, $suffix);
					$info = parse_url($url);
					if (winsock::download($url, $cacheFile, array('Referer' => $info['scheme'].'://'.$info['host'].'/'))) {
						$arr = pathinfo($cacheFile);
						$arr['suffix'] = $arr['extension'];
						$files = array();
						$sfile = $this->downDir.$arr['basename'];
						$files[] = array('sfile' => $sfile, 'dfile' => $saveDir.$arr['basename']);
						foreach ($thumbs as $k => $v) {
							$dfile = $this->downDir.$arr['filename'].$k.'.'.$arr['suffix'];
							if ($v['width'] && $v['height']) {//裁剪
								image::thumb($sfile, $dfile, $v, 'cutout');
							} else image::thumb($sfile, $dfile, $v, 'zoom');//缩小
							$files[] = array('sfile' => $dfile, 'dfile' => $saveDir.$arr['filename'].$k.'.'.$arr['suffix']);
						}
						$rs = array();
						if ($this->isLocal) {
							foreach ($files as $v) {
								$sfile = $v['sfile'];
								$dfile = $v['dfile'];
								$dfile = $this->localDir.strtr(substr($dfile, 1), '/', D);
								file::createFolderToFile($dfile);
								file::moveFile($sfile, $dfile);
								$rs[] = substr($v['dfile'], 1);
							}
						} else {
							foreach ($files as $v) {
								$this->d->uploadFile($v['dfile'], $v['sfile']);
								@unlink($v['sfile']);
								$rs[] = substr($v['dfile'], 1);
							}
						}
						$list[] = array('filename' => substr($saveDir, 1).$arr['filename'], 'suffix' => $arr['suffix'], 'files' => $rs);
					} else {
						file_exists($cacheFile) && @unlink($cacheFile);
					}
				}
			}
		}
		return $list;
	}
	public function getUrl($url){
		if ($this->isLocal) {
			return $this->localUrl.substr($url, 1);
		} else return $this->d->webUrl.$url;
	}
	public function del($url){
		if ($this->isLocal) {
			file::unlink($this->localDir.substr(strtr($url, '/', D), 1));
		} else $this->d->delFile($url, true);
	}
	public function delThumb($url, $suffix, $thumb){
		$this->del($url.'.'.$suffix);
		foreach (array_keys($thumb) as $v) $this->del($url.$v.'.'.$suffix);
	}
	public function moveFile($s, $d){
		if ($this->isLocal) {
			$d = $this->localDir.substr(strtr($d, '/', D), 1);
			echo $s.'<br />'.$d;exit;
			file::createFolderToFile($d);
			file::moveFile($s, $d);
		} else {
			$this->d->uploadFile($d, $s);
			file::unlink($s);
		}
	}
	public function moveFiles($s, $d){
		if ($this->isLocal) {
			//$d = $this->localDir.substr(strtr($d, '/', D), 1);
			//echo $s.'<br />'.$d;exit;
			file::createFolderToFile($d);
			file::moveFile($s, $d);
		} else {
			$this->d->uploadFile($d, $s);
			file::unlink($s);
		}
	}
	public static function getObj($type, $did = -1){
		static $cacheList = array();
		if (isset($cacheList[$type.'_'.$did])) return $cacheList[$type.'_'.$did];
		$obj = new self($type, $did);
		$cacheList[$type.'_'.$did] = $obj;
		return $obj;
	}
}
?>