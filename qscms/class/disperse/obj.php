<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
set_time_limit(0);
class disperse_obj{
	private static function formatData(&$datas){
		$datas = qscms::filterArray($datas, array('name', 'type', 'url', 'status', 'ftp', 'web'), true);
		$rs = form::checkData($datas, array(
			'null' => array(
				'name'   => false, 
				'type'   => false, 
				'url'    => false,
				'status' => false
			),
			'maxLength' => array(
				'name' => 32,
				'type' => 16,
				'url'  => 128
			)
		));
		if ($rs !== true) return $rs;
		qscms::setType($datas['status'], '01');
		substr($datas['url'], -1) == '/' && $datas['url'] = substr($datas['url'], 0, -1);
		switch ($datas['type']) {
			case 'ftp':
				$data = $datas['ftp'];
				$rs = form::checkData($data, array(
					'null' => array(
						'ip'       => false,
						'port'     => false,
						'username' => false,
						'password' => false
					),
					'function' => array(
						/*'ip' => 'form::checkIP',*/
						'port' => 'is_numeric'
					)
				), array(
					'ip'       => 'FTP IP地址',
					'port'     => 'FTP端口',
					'username' => 'FTP帐号',
					'password' => 'FTP密码'
				));
				if ($rs !== true) return $rs;
				substr($data['path'], 0, 1) != '/' && $data['path'] = '/'.$data['path'];
				substr($data['path'], -1) != '/' && $data['path'] .= '/';
			break;
			case 'web':
				$data = $datas['web'];
				$rs = form::checkData($data, array(
					'null' => array(
						'url'     => false,
						'key'     => false,
						'varName' => false
					),
					'function' => array(
						'url' => 'form::checkLink'
					)
				), array(
					'ip'       => 'WEB通信地址',
					'key'      => 'WEB通信密钥',
					'username' => 'WEB通信变量'
				));
				if ($rs !== true) return $rs;
				substr($data['path'], 0, 1) != '/' && $data['path'] = '/'.$data['path'];
				substr($data['path'], -1) != '/' && $data['path'] .= '/';
			break;
			default:
				return '错误类型';
			break;
		}
		unset($datas['ftp'], $datas['web']);
		$datas['data'] = addslashes(serialize($data));
		return true;
	}
	public static function add($datas){
		$rs = self::formatData($datas);
		if ($rs === true) {
			if ($id = db::insert('disperse', $datas, true)) {
				return true;
			}
			return '插入数据库失败';
		}
		return $rs;
	}
	public static function edit($datas, $id){
		$rs = self::formatData($datas);
		if ($rs === true) {
			if (db::update('disperse', $datas, "id='$id'")) {
				return true;
			}
			return '插入数据库失败';
		}
		return $rs;
	}
	public static function total(){
		return db::data_count('disperse');
	}
	public static function getList($pagesize = 0, $page = 0){
		return db::select('disperse', 'id,sort,name,type,url', '', 'sort', $pagesize, $page);
	}
	public static function get($id){
		$datas = memory::get('disperse_data_'.$id);
		if ($datas) return $datas;
		if ($datas = db::one('disperse', '*', "id='$id'")) {
			$datas['data'] = unserialize($datas['data']);
			memory::write('disperse_data_'.$id, $datas);
			return $datas;
		}
		return false;
	}
	public static function getType($id){
		$data = memory::get('disperse_type_'.$id);
		if ($data) return $data;
		$data = db::one_one('disperse', 'type', "id='$id'");
		memory::write('disperse_type_'.$id, $data);
		return $data;
	}
	public static function getFtp($id){
		if ($datas = self::get($id)) {
			if ($datas !== false) {
				if ($datas['type'] == 'ftp') {
					$data = $datas['data'];
					$obj = new disperse_ftp(
						$data['ip'],
						$data['port'],
						$data['username'],
						$data['password'],
						$data['path'],
						$datas['url']
					);
					$obj->id = $id;
					return $obj;
				}
			}
		}
		return false;
	}
	public static function getWeb($id){
		if ($datas = self::get($id)) {
			if ($datas !== false) {
				if ($datas['type'] == 'web') {
					$data = $datas['data'];
					$obj =  new disperse_web(
						$data['url'],
						$data['key'],
						$data['varName'],
						$data['path'],
						$datas['url']
					);
					$obj->id = $id;
					return $obj;
				}
			}
		}
		return false;
	}
	public static function getObj($id, $cache = true){
		static $cacheList = array();
		if ($cache && isset($cacheList[$id])) return $cacheList[$id];
		$obj = false;
		switch (self::getType($id)) {
			case 'ftp':
				$obj = self::getFtp($id);
			break;
			case 'web':
				$obj = self::getWeb($id);
			break;
		}
		$cache && $cacheList[$id] = $obj;
		return $obj;
	}
	public static function setSort($ids, $sts){
		if ($count = form::arrayEqual($ids, $sts)) {
			for ($i = 0; $i < $count; $i++) {
				$id = $ids[$i];
				$st = $sts[$i];
				db::update('disperse', array('sort' => $st), "id='$id'");
			}
		}
	}
	public static function del($ids){
		is_array($ids) || $ids = array($ids);
		return db::del_ids('disperse', $ids);
	}
}
?>