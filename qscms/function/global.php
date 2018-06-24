<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
function tag_split($tags){
	$list = array();
	if ($tags = trim($tags)) {
		foreach (qscms::trimSplit('/,|，|\s+/', $tags) as $tag) {
			$tag = mb_substr($tag, 0, 16);
			$list[] = $tag;
		}
	}
	$list = array_unique($list);
	return $list;
}
function album_exists($id){
	return db::exists('album', array('id' => $id));
}
function album_create($cid, $title, $from, $tags = ''){
	$title && $title = mb_substr($title, 0, 80);
	if ($title) {
		if ($aid = db::insert('album', array(
			'cid'   => $cid,
			'title' => $title,
			'from'  => $from,
			'time'  => time::$timestamp
		), true)) {
			db::update('info_cate', 'total=total+1', "id='$cid'");
			$count = album_setTag($aid, $tags);
			$count > 0 && db::update('album', "tag='$count'", "id='$aid'");
			return $aid;
		}
	}
	return false;
}
function album_edit($aid, $title, $from, $tags){
	$title && $title = mb_substr($title, 0, 80);
	if ($title) {
		if (db::update('album', array(
			'title' => $title,
			'from'  => $from,
			'tag'   => 0,
			'time'  => time::$timestamp
		), "id='$aid'")) {
			$count = album_setTag($aid, $tags);
			$count > 0 && db::update('album', "tag='$count'", "id='$aid'");
			return true;
		}
	}
	return false;
}
function album_thumb($pid, $ignoreExists = false){
	$photo = db::one('pic', '*', "id='$pid'");
	if (!$photo) return false;
	$aid   = $photo['iid'];
	$album = db::one('album', '*', "id='$aid'");
	if (!$album) return false;
	$cid = $album['cid'];
	$cate  = db::one('info_cate', '*', "id='$cid'");
	if (!$cate) return false;
	$thumb = $cate['thumb'];
	$list = string::parseChoose($thumb, false);
	$baseFile = qscms::getImgDir('pic', $photo['filename']);
	$count = 0;
	foreach ($list as $val) {
		$k = $val['key'];
		$v = $val['value'];
		if (preg_match('/^(\d+)x(\d+)$/i', $v, $matches)) {
			$width  = intval($matches[1]);
			$height = intval($matches[2]);
			$s = $baseFile.'.'.$photo['suffix'];
			$d = $baseFile.$k.'.'.$photo['suffix'];
			if (!$ignoreExists || $ignoreExists && !file_exists($d)) {
				image::thumb($s, $d, array('width' => $width, 'height' => $height), !$width || !$height ? 'zoom' : 'cutout');
			}
			$count++;
		}
	}
	return $count;
}
function album_setFace($aid, $pid){
	$face = db::one_one('album', 'face', "id='$aid'");
	if (!$face) db::update('album', array('face' => $pid), "id='$aid'");
}
function album_resetFace($aid){
	$pid = db::one_one('pic', 'id', "iid='$aid'",  'id');
	if ($pid) db::update('album', array('face' => $pid), "id='$aid'");
}
function album_uploadPic(){
	$datas = form::get3(array('aid', 'int'), 'content');
	$aid = $datas['aid'];
	if (!album_exists($aid)) return '图集不存在';
	$content  = !empty($datas['content']) ? $datas['content']:'';
	$upName   = 'file';
	$saveDir1 = qscms::getArticlePath($aid, '/').'/';//date('Y/m/d/', time::$timestamp);
	$saveDir  = qscms::getImgDir('pic', $saveDir1);
	$rs = upload::uploadImage($upName, $saveDir, true);
	if ($rs) {
		$file = $saveDir.$rs['basename'];
		$md5  = md5_file($file);
		//if (!db::exists('pic', array('md5' => $md5))) {
		if (true) {
			$content && $content = mb_substr($content, 0, 600);
			image::watermark($file);
			$datas = array(
				'iid'      => $aid,
				'filename' => $saveDir1.$rs['filename'],
				'suffix'   => $rs['suffix'],
				'content'  => $content,
				'time'     => time::$timestamp,
				'md5'      => $md5
			);
			if ($pid = db::insert('pic', $datas, true)) {
				db::update('album', "total=total+1", "id='$aid'");
				album_thumb($pid);
				album_setFace($aid, $pid);
				return true;
			} else {
				@unlink($file);
			}
		} else {
			@unlink($file);
		}
	}
	return false;
}
function album_downloadPic($aid, $url, $content){
	loadFunc('robot');
	$saveDir1 = qscms::getArticlePath($aid, '/').'/';//date('Y/m/d/', time::$timestamp);
	$saveDir  = qscms::getImgDir('pic', $saveDir1);
	$file = robot_downImg($url, $saveDir);
	if ($file) {
		$content && $content = mb_substr($content, 0, 600);
		image::watermark($file);
		$pinfo = pathinfo($file);
		$md5  = md5_file($file);
		$datas = array(
			'iid'      => $aid,
			'filename' => $saveDir1.$pinfo['filename'],
			'suffix'   => $pinfo['extension'],
			'content'  => $content,
			'time'     => time::$timestamp,
			'md5'      => $md5
		);
		if ($pid = db::insert('pic', $datas, true)) {
			db::update('album', "total=total+1", "id='$aid'");
			album_thumb($pid);
			album_setFace($aid, $pid);
			return true;
		} else {
			@unlink($file);
		}
	}
}
function album_setPicContent($pid, $content){
	$content && $content = mb_substr($content, 0, 255);
	db::update('pic', array('content' => $content), "id='$pid'");
}
function album_pic_dels($ids){
	$aid = db::one_one('pic', 'iid', "id='$ids[0]'");
	if (!$aid) return 0;
	$album = db::one('album', '*', "id='$aid'");
	if (!$album) return 0;
	$cate = db::one('info_cate', '*', "id='$album[cid]'");
	$thumb = $cate['thumb'];
	$list = string::parseChoose($thumb, false);
	$sids = '\''.implode('\',\'', $ids).'\'';
	foreach (db::select('pic', '*', "id IN($sids)") as $pic) {
		$baseFile = qscms::getImgDir('pic', $pic['filename']);
		foreach ($list as $v) @unlink($baseFile.$v['key'].'.'.$pic['suffix']);
		@unlink($baseFile.'.'.$pic['suffix']);
	}
	$count = db::del_ids('pic', $ids);
	db::update('album', 'total=total-'.$count, "id='$aid'");
	if (in_array($album['face'], $ids)) {
		$pid = db::one_one('pic', 'id', "iid='$aid'");
		if ($pid) album_setFace($aid, $pid);
		else album_setFace($aid, 0);
	}
	return $count;
}
function album_delTag($aid){
	item_delTag('album', $aid);
}
function album_setTag($aid, $tags){
	return item_setTag('album', $aid, $tags);
}
function item_setTag($type, $iid, $tags){
	item_delTag($type, $iid);
	db::lockTableWrite('tag');
	$ids = array();
	$tags = tag_split($tags);
	$time = time::$timestamp;
	foreach ($tags as $tag){
		if ($id = db::one_one('tag', 'id', "name='$tag'")) {
			db::update('tag', 'lastTime=\''.$time.'\'', 'id=\''.$id.'\'');
			$ids[] = $id;
		} else {
			if ($id = db::insert('tag', array('name' => $tag, 'time' => $time, 'lastTime' => $time), true)) {
				$ids[] = $id;
				$time++;
			}
		}
	}
	db::unlockTables();
	$count = 0;
	foreach ($ids as $tid) {
		$data = array(
			'type' => $type,
			'tid'  => $tid,
			'iid'  => $iid
		);
		if (!db::exists('tag_item', $data)) {
			$data['time'] = $time;
			if (db::insert('tag_item', $data)) {
				db::update('tag', "total=total+'1'", "id='$tid'");
				$count++;
				$time++;
			}
		}
	}
	return $count;
}
function item_delTag($type, $iid){
	$pre = PRE;
	db::querys("
	UPDATE 
		`{$pre}tag` t0 
	RIGHT JOIN
		(SELECT tid FROM `{$pre}tag_item` WHERE type='$type' AND iid='$iid') t1
	ON
		t1.tid=t0.id
	SET
		t0.total=IF(t0.total>'0',t0.total-1,0);
	DELETE FROM `{$pre}tag_item` WHERE type='$type' AND iid='$iid'");
}
function item_getTags($type, $iid){
	$ids = db::get_keys('tag_item', 'tid', "type='$type' AND iid='$iid'");
	if ($ids) {
		return db::get_keys('tag', 'name', 'id IN(\''.implode('\',\'', $ids).'\')');
	}
	return false;
}
function album_getTags($aid){
	return item_getTags('album', $aid);
}
function call_album_getTags(&$data){
	$tags = album_getTags($data['id']);
	$data['tags'] = $tags;
}
function album_del($id){
	if ($album = db::one('album', '*', "id='$id'")) {
		$cate = db::one('info_cate', '*', "id='$album[cid]'");
		$thumb = $cate['thumb'];
		$list = string::parseChoose($thumb, false);
		foreach (db::select('pic', '*', "iid='$id'") as $pic) {
			$baseFile = qscms::getImgDir('pic', $pic['filename']);
			foreach ($list as $v) @unlink($baseFile.$v['key'].'.'.$pic['suffix']);
			@unlink($baseFile.'.'.$pic['suffix']);
		}
		db::del_key('pic', 'iid', $id);//删除对应的图片
		album_delTag($id);//删除标签
		db::del_id('album', $id);//删除图集
		db::update('info_cate', 'total=total-1', "id='$cate[id]'");//更新分类图集数量
		return true;
	}
}
function album_dels($ids){
	$count = 0;
	foreach ($ids as $id) album_del($id) && $count++;
	return $count;
}
function info_cate_del($id){
	if ($cate = db::one('info_cate', '*', "id='$id'")) {
		foreach (db::select('album', 'id', "cid='$id'") as $v) {
			album_del($v['id']);
		}
		db::del_id('info_cate', $id);
		return true;
	}
	return false;
}
function info_cate_dels($ids){
	$count = 0;
	foreach ($ids as $id) info_cate_del($id) && $count++;
	return $count;
}
function _parseFormField($item){
	$var = qscms::v('_G');
	$editData = $var->editData;
	include(template::load('add_tpl'));
	$html = qscms::ob_get_contents();
	qscms::ob_clean();
	return $html;
}
function parseFormField($datas){
	$html = '';
	if (is_array($datas)) {
		foreach ($datas as $v) {
			$html .= _parseFormField($v);
			if ($v['sub']) {
				foreach ($v['sub'] as $k1 => $v1) {
					$vals = qscms::trimExplode('|', $k1);
					$show = in_array($v['htmlDefaultValue'], $vals);
					echo "<div showBox=\"$v[htmlName]\" showData=\"$k1\"".(!$show ? ' style="display:none"' : '').">";
					$html .= parseFormField($v1);
					echo '</div>';
				}
				
			}
		}
	}
	return $html;
}
?>