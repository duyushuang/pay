<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
function __delAndUpdate($datas){
	$sql = 'UPDATE `'.db::table($datas['upTb']['name']).'` t0 RIGHT JOIN (SELECT `'.$datas['delTb']['union'].'`,COUNT(*) AS total FROM `'.db::table($datas['delTb']['name']).'` WHERE `'.$datas['delTb']['key'].'`=\''.$datas['delTb']['val'].'\' GROUP BY `'.$datas['delTb']['union'].'`) t1 ON t1.'.$datas['delTb']['union'].'=t0.'.$datas['upTb']['key'].' SET t0.`'.$datas['upTb']['up'].'`=t0.`'.$datas['upTb']['up'].'`-t1.`total`';
	if (db::updateSql($sql)) {
		db::del_key($datas['delTb']['name'], $datas['delTb']['key'], $datas['delTb']['val']);
	}
}
function __updateAndUpate($datas){
	
}
function __delToSub($datas){
	$sql = 'DELETE FROM `'.db::table($datas['sub']['name']).'` WHERE `'.$datas['sub']['key'].'` IN(SELECT `'.$datas['parent']['key'].'` FROM `'.db::table($datas['parent']['name']).'` WHERE `'.$datas['key'].'`=\''.$datas['val'].'\')';
	db::query($sql);
}
function __delTbsToKey($tbs, $key, $val){
	is_array($tbs) || $tbs = array($tbs);
	foreach ($tbs as $tb) {
		db::del_key($tb, $key, $val);
	}
}
function __delFiles($path, $suffix, $tps){
	foreach ($tps as $type) {
		@unlink(d('./'.$path.'_'.$type.'.'.$suffix));
	}
}
function del_albums($ids){
	is_array($ids) || $ids = array($ids);
	$i = 0;
	foreach ($ids as $aid) {
		//删除专辑
		if ($album = db::one('albums', '*', "id='$aid'")) {
			db::del_key('album_comments', 'aid', $aid);//删除专辑留言
			db::delete('tags_list', "type='album' AND iid='$aid'");//删除专辑标签
			db::del_key('fav_albums', 'aid', $aid);//删除专辑被收录的数据
			db::del_key('album_pics', 'aid', $aid);//删除专辑的图片
			__delAndUpdate(array(
				'delTb' => array(
					'name'  => 'group_albums',
					'key'   => 'aid',
					'union' => 'gid',
					'val'   => $aid
				),
				'upTb' => array(
					'name' => 'group',
					'key'  => 'id',
					'up'   => 'albums'
				)
			));//删除专辑所在小组
			@unlink(d('./'.$album['avatar']));//删除专辑封面图片
			db::del_id('albums', $aid);
			$uid = $album['uid'];
			if (member::exists($uid)) {
				db::update('member_fields', "albums=albums-'1'", "uid='$uid'");//
			}
			$i++;
		}
	}
	return $i;
}
function del_groups($ids){
	is_array($ids) || $ids = array($ids);
	$i = 0;
	foreach ($ids as $gid) {
		//删除小组
		if ($group = db::one('group', '*', "id='$gid'")) {
			db::del_key('group_comment', 'gid', $gid);//删除小组留言
			
			db::del_key('fav_group', 'gid', $gid);//删除小组被收录的数据
			db::del_key('group_pics', 'gid', $gid);//删除小组的图片
			db::del_key('group_albums', 'gid', $gid);//删除小组的专辑
			db::del_key('group_members', 'gid', $gid);//删除小组成员
			db::update('topic', "gid='0'", "gid='$gid'");//删除小组文章
			__delFiles($group['avatar'], $group['avatarSuffix'], array('small', 'big', 'source'));//删除封面
			db::del_id('group', $gid);
			$uid = $group['uid'];
			if (member::exists($uid)) {
				db::update('member_fields', "group=group-'1'", "uid='$uid'");//
			}
			$i++;
		}
	}
	return $i;
}
function del_pics($ids){
	is_array($ids) || $ids = array($ids);
	$i = 0;
	foreach ($ids as $iid) {
		if ($pic = db::one('pics', '*', "id='$iid'")) {
			db::del_key('pic_comments', 'iid', $iid);//删除图片留言
			db::del_key('fav_img', 'iid', $iid);//删除图片收录
			db::delete('tags_list', "type='pic' AND iid='$iid'");//删除图片标签
			__delAndUpdate(array(
				'delTb' => array(
					'name'  => 'album_pics',
					'key'   => 'iid',
					'union' => 'aid',
					'val'   => $iid
				),
				'upTb' => array(
					'name' => 'albums',
					'key'  => 'id',
					'up'   => 'pics'
				)
			));//删除图片所在专辑
			__delAndUpdate(array(
				'delTb' => array(
					'name'  => 'group_pics',
					'key'   => 'iid',
					'union' => 'gid',
					'val'   => $iid
				),
				'upTb' => array(
					'name' => 'group',
					'key'  => 'id',
					'up'   => 'pics'
				)
			));//删除图片所在小组
			__delFiles($pic['path'].$pic['name'], $pic['suffix'], array('tiny', 'small', 'medium', 'big', 'source'));//删除图片文件
			db::del_id('pics', $iid);//删除图片
			$i++;
		}
	}
	return $i;
}
function del_member($uid){
	$pre = PRE;
	$i = 0;
	if ($memberInfo = member::getInfo($uid)) {
		
		__delAndUpdate(array(
			'delTb' => array(
				'name'  => 'album_comments',
				'key'   => 'uid',
				'union' => 'aid',
				'val'   => $uid
			),
			'upTb' => array(
				'name' => 'albums',
				'key'  => 'id',
				'up'   => 'comments'
			)
		));//删除专辑留言
		db::del_key('album_list', 'uid', $uid);//删除专辑列表
		__delAndUpdate(array(
			'delTb' => array(
				'name'  => 'fav_albums',
				'key'   => 'uid',
				'union' => 'aid',
				'val'   => $uid
			),
			'upTb' => array(
				'name' => 'albums',
				'key'  => 'id',
				'up'   => 'favs'
			)
		));//删除专辑收录
		__delAndUpdate(array(
			'delTb' => array(
				'name'  => 'fav_group',
				'key'   => 'uid',
				'union' => 'gid',
				'val'   => $uid
			),
			'upTb' => array(
				'name' => 'group',
				'key'  => 'id',
				'up'   => 'favs'
			)
		));//删除小组收录
		db::del_key('fav_img', 'uid', $uid);//删除图片收录
		__delAndUpdate(array(
			'delTb' => array(
				'name'  => 'fav_topic',
				'key'   => 'uid',
				'union' => 'tid',
				'val'   => $uid
			),
			'upTb' => array(
				'name' => 'topic',
				'key'  => 'id',
				'up'   => 'favs'
			)
		));//删除文章收录
		db::del_key('fav_img', 'uid', $uid);//删除图片收录
		db::del_key('feed', 'uid', $uid);//删除动态信息
		db::del_key('group_list', 'uid', $uid);//删除小组列表
		db::del_key('member_active', 'uid', $uid);//删除用户活跃数据
		db::del_key('member_radios', 'uid', $uid);//删除用户活跃数据
		__delAndUpdate(array(
			'delTb' => array(
				'name'  => 'pic_comments',
				'key'   => 'uid',
				'union' => 'iid',
				'val'   => $uid
			),
			'upTb' => array(
				'name' => 'pics',
				'key'  => 'id',
				'up'   => 'comments'
			)
		));//删除图片留言
		db::del_key('tags_list', 'uid', $uid);//删除用户的标签
		db::del_key('tips', 'uid', $uid);//删除系统消息
		__delAndUpdate(array(
			'delTb' => array(
				'name'  => 'topic_comment',
				'key'   => 'uid',
				'union' => 'tid',
				'val'   => $uid
			),
			'upTb' => array(
				'name' => 'topic',
				'key'  => 'id',
				'up'   => 'comments'
			)
		));//删除文章留言
		db::del_key('fav_member', 'fuid', $uid);//删除关注我的数据
		db::del_key('fav_member', 'tuid', $uid);//删除我关注的数据
		db::del_key('member_comments', 'tuid', $uid);//删除用户留言
		db::del_key('member_comments', 'fuid', $uid);//删除用户留言
		db::del_key('message_box', 'tuid', $uid);//删除用户私信
		db::del_key('message_box', 'fuid', $uid);//删除用户私信
		db::del_key('message_comments', 'tuid', $uid);//删除用户私信
		db::del_key('message_comments', 'fuid', $uid);//删除用户私信
		del_albums(db::get_ids('albums', "uid='$uid'"));//删除用户专辑
		del_groups(db::get_ids('group', "uid='$uid'"));//删除用户小组
		__delAndUpdate(array(
			'delTb' => array(
				'name'  => 'topic',
				'key'   => 'uid',
				'union' => 'uid',
				'val'   => $uid
			),
			'upTb' => array(
				'name' => 'member_fields',
				'key'  => 'uid',
				'up'   => 'topic'
			)
		));//删除文章
		$query = db::query("SELECT id FROM {$pre}pics WHERE uid='$uid'");
		while ($iid = db::fetchArrayFirst($query)) {
			del_pics($iid);//删除图片
		}//删除用户上传的图片
		$fs = member::getAvatar($uid, $memberInfo['avatarSuffix']);
		@unlink(d('./'.$fs['tiny']));
		@unlink(d('./'.$fs['small']));
		@unlink(d('./'.$fs['big']));//删除头像
		db::del_key('members', 'id', $uid);
		db::del_key('member_fields', 'uid', $uid);
		$i++;
	}
	return $i;
}
function del_tags($ids){
	is_array($ids) || $ids = array($ids);
	db::del_keys('tags_list', 'tid', $ids);
	return db::del_ids('tags', $ids);
}
?>