<?php
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class mem{
	public static function addServer($datas){
		$datas = qscms::filterArray($datas, array('ip', 'port', 'weight'), true);
		$rs = form::checkData($datas, array(
			'null' => array(
				'ip'     => false,
				'port'   => false,
				'weight' => false
			),
			'function' => array(
				'ip'     => 'form::checkIP',
				'port'   => 'is_numeric',
				'weight' => 'is_numeric'
			)
		), array(
			'ip'     => '端口IP',
			'port'   => '服务器端口',
			'weight' => '服务器权重'
		));
		if ($rs === true) {
			$datas['ip'] = qscms::ipint($datas['ip']);
			if (!db::exists('memcache', array('ip' => $datas['ip'], 'port' => $datas['port']))) {
				$sort = db::dataCount('memcache') + 1;
				$datas['sort'] = $sort;
				$datas['time'] = time();
				if (db::insert('memcache', $datas)) {
					self::upCache();
					return true;
				}
				return '添加失败，请重试';
			}
			return '该服务器已经存在了';
		}
		return $rs;
	}
	public static function addServerToForm(){
		if (form::hash()) {
			return self::addServer($_POST);
		}
		return false;
	}
	public static function editServer($datas, $id){
		$datas = qscms::filterArray($datas, array('ip', 'port', 'weight'), true);
		$rs = form::checkData($datas, array(
			'null' => array(
				'ip'     => false,
				'port'   => false,
				'weight' => false
			),
			'function' => array(
				'ip'     => 'form::checkIP',
				'port'   => 'is_numeric',
				'weight' => 'is_numeric'
			)
		), array(
			'ip'     => '端口IP',
			'port'   => '服务器端口',
			'weight' => '服务器权重'
		));
		if ($rs === true) {
			$datas['ip'] = qscms::ipint($datas['ip']);
			if (!db::exists('memcache', "ip='$datas[ip]' AND port='$datas[port]' AND id<>'$id'")) {
				$datas['time'] = time();
				if (db::update('memcache', $datas, "id='$id'")) {
					self::upCache();
					return true;
				}
				return '修改失败，请重试';
			}
			return '该服务器已经存在了';
		}
		return $rs;
	}
	public static function editServerToForm($id){
		if (form::hash()) {
			return self::editServer($_POST, $id);
		}
		return false;
	}
	public static function getServerAll($cache = true, $fields = '*'){
		$list = array();
		if ($cache) {
			return cache::get_array('memcache_server');
		} else {
			foreach (db::select('memcache', $fields, '', '`sort`') as $v) {
				isset($v['ip']) && $v['ip'] = qscms::intip($v['ip']);
				$list[] = $v;
			}
		}
		return $list;
	}
	public static function getServer($id){
		if ($item = db::one('memcache', '*', "id='$id'")) {
			$item['ip'] = qscms::intip($item['ip']);
			return $item;
		}
		return false;
	}
	public static function delServer($del){
		if ($del && is_array($del)) {
			$count =  db::del_ids('memcache', $del);
			if ($count > 0) self::upCache();
			return $count;
		}
		return 0;
	}
	public static function setSort($ids, $sorts){
		if ($count = form::arrayEqual($ids, $sorts)) {
			for ($i = 0; $i < $count; $i++) {
				db::update('memcache', array('sort' => $sorts[$i]), "id='$ids[$i]'");
			}
			self::upCache();
		}
	}
	public static function upCache(){
		$list = self::getServerAll(false, 'ip,port,weight');
		cache::write_array('memcache_server', $list);
	}
}
?>