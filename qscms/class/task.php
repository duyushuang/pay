<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class task{
	private static function getData(&$datas){
		if (!$datas) $datas = form::get2('name', 'time0', 'time2', array('timeType', 'int'), array('type', 'int'), 'filename', 'code');
		else $datas = qscms::filterArray($datas, array('name', 'time0', 'time2', 'timeType', 'type', 'filename', 'code'));
		if (!in_array($datas['type'], array(0, 1))) return '任务类型错误';
		if (!in_array($datas['timeType'], array(0, 2))) return '时间类型错误';
		$checkFlag = array();
		$checkFlag['null'] = array(
			'name' => false,
			'time0' => true,
			'time2' => true,
		);
		$checkFlag['maxLength'] = array(
			'name' => 32
		);
		$checkFalg['preg'] = array(
			'time0' => '/^\d+$/',
			'time2' => '/(^\d{4}-\d{1,2}-\d{1,2} \d{1,2}(?::\d{1,2}(?::\d{1,2})?)?$)|(^\d{4}-\d{1,2}-\d{1,2}$)|(^\d{1,2}(?::\d{1,2}(?::\d{1,2})?)?$)/'
		);
		switch ($datas['type']) {
			case 0:
				//文件
				unset($datas['code']);
				$checkFlag['null']['filename']      = false;
				$checkFlag['maxLength']['filename'] = 32;
			break;
			case 1:
				//代码
				unset($datas['filename']);
				$checkFlag['null']['code'] = false;
			break;
			default:
				return '任务类型错误';
			break;
		}
		$rs = form::checkData($datas, $checkFlag, array(
			'name'     => '任务名称',
			'time0'    => '执行时间',
			'time2'    => '间隔时间',
			'type'     => '任务类型',
			'filename' => '执行文件名',
			'code'     => '执行代码'
		));
		if ($rs === true) {
			if ($datas['timeType'] == 0) {
				$datas['time'] = $datas['time0'];
				unset($datas['time0'], $datas['time2']);
				if (preg_match('/^\d{1,2}(?::\d{1,2}(?::\d{1,2})?)?$/', $datas['time'])) {
					$sp = explode(':', $datas['time']);
					switch (count($sp)) {
						case 1:
							$sp = array(0, 0, $sp[0]);
						break;
						case 2:
							$sp = array(0, $sp[0], $sp[1]);
						break;
					}
					$datas['time'] = $sp[0] * 3600 + $sp[1] * 60 + $sp[2];
					$datas['timeType'] = 0;
				} elseif (preg_match('/^w:([1-7])(?: (\d{1,2}(?::\d{1,2}(?::\d{1,2})?)?))?$/', $datas['time'], $matches)) {
					$week = intval($matches[1]) - 1;
					$time = !empty($matches[2]) ? $matches[2] : '';
					if ($time) {
						$sp = explode(':', $time);
						switch (count($sp)) {
							case 1:
								$sp = array(0, 0, $sp[0]);
							break;
							case 2:
								$sp = array(0, $sp[0], $sp[1]);
							break;
						}
						$time = $sp[0] * 3600 + $sp[1] * 60 + $sp[2];
					} else $time = 0;
					$time += 86400 * $week;
					$datas['time'] = $time;
					$datas['timeType'] = 3;
				} elseif (preg_match('/^m:((?:[1-9]|1[0-2]))(?: (\d{1,2}(?::\d{1,2}(?::\d{1,2})?)?))?$/', $datas['time'], $matches)) {
					$day = intval($matches[1]) - 1;
					$time = !empty($matches[2]) ? $matches[2] : '';
					if ($time) {
						$sp = explode(':', $time);
						switch (count($sp)) {
							case 1:
								$sp = array(0, 0, $sp[0]);
							break;
							case 2:
								$sp = array(0, $sp[0], $sp[1]);
							break;
						}
						$time = $sp[0] * 3600 + $sp[1] * 60 + $sp[2];
					} else $time = 0;
					$time += 86400 * $day;
					$datas['time'] = $time;
					$datas['timeType'] = 4;
				} else {
					$datas['time'] = time::getGeneralTimestamp($datas['time']);
					$datas['timeType'] = 1;
				}
			} elseif ($datas['timeType'] == 2) {
				$datas['time'] = $datas['time2'];
				unset($datas['time0'], $datas['time2']);
			}
		}
		return $rs;
	}
	public static function add($name, $timeType, $time, $type, $data, $status = 0){
		$datas = array(
			'name'     => $name,
			'time0'    => $timeType == 0 ? $time : '',
			'time2'    => $timeType == 2 ? $time : '',
			'timeType' => $timeType,
			'type'     => $type,
			'filename' => $type == 0 ? $data : '',
			'code'     => $type == 1 ? $data : ''
		);
		$rs = self::getData($datas);
		if ($rs === true) {
			$datas['status'] = $status;
			if ($id = db::insert('sys_task', $datas, true)) {
				if ($status) self::upCache();
				return $id;
			}
		}
	}
	public static function edit($id, $name, $timeType, $time, $type, $data, $status = 0){
		$datas = array(
			'name'     => $name,
			'time0'    => $timeType == 0 ? $time : '',
			'time2'    => $timeType == 2 ? $time : '',
			'timeType' => $timeType,
			'type'     => $type,
			'filename' => $type == 0 ? $data : '',
			'code'     => $type == 1 ? $data : ''
		);
		$rs = self::getData($datas);
		if ($rs === true) {
			$datas['status'] = $status;
			if (db::update('sys_task', $datas, "id='$id'", "id='$id'")) {
				self::upCache();
				return true;
			}
		}
	}
	public static function addToPost(){
		$datas = array();
		$rs = self::getData($datas);
		if ($rs === true) {
			if (db::insert('sys_task', $datas)) {
				//self::upCache();
				return true;
			}
			return '插入失败';
		}
		return $rs;
	}
	public static function editToPost($id){
		$datas = array();
		$rs = self::getData($datas);
		if ($rs === true) {
			if (db::update('sys_task', $datas, "id='$id'")) {
				if (db::one_one('sys_task', 'status', "id='$id'")) self::upCache();
				return true;
			}
			return '数据库出错';
		}
	}
	private static function formatTask($arr){
		$t = array();
		switch ($arr['type']) {
			case 0:
				$t = array(
					'type'     => $arr['type'],
					'time'     => $arr['time'],
					'timeType' => $arr['timeType'],
					'data'     => $arr['filename'].'.php'
				);
			break;
			case 1:
				$t = array(
					'type'     => $arr['type'],
					'time'     => $arr['time'],
					'timeType' => $arr['timeType'],
					'data'     => $arr['code']
				);
			break;
		}
		if ($t) {
			if (!$t['timeType']) {
				$t['timeInfo'] = qscms::filterArray(time::daytime($t['time']), array('hour', 'minute', 'second'));
			}
		}
		return $t;
	}
	public static function upCache(){
		$listOld = cache::get_array('sys_task');
		$list = array();
		foreach (self::getList(0, 0, '*') as $v) {
			if ($v['status']) {
				$t = self::formatTask($v);
				if (!empty($listOld[$v['id']])) {
					if (!empty($listOld[$v['id']]['nextTime'])) $t['nextTime'] = $listOld[$v['id']]['nextTime'];
				}
				$list[$v['id']] = $t;
			}
		}
		if ($list) {
			cache::write_array('sys_task', $list);
		} else {
			cache::delete_array('sys_task');
		}
	}
	public static function total(){
		return db::data_count('sys_task');
	}
	public static function getList($pagesize = 0, $page = 0, $fields = 'id,`sort`,name,type,time,timeType,status'){
		return db::select('sys_task', $fields, '', 'sort', $pagesize, $page);
	}
	public static function changeStatus($id){
		if (db::update('sys_task', "status=1-status", "id='$id'")) {
			self::upCache();
			return true;
		}
		return false;
	}
	public static function get($id){
		return db::one('sys_task', '*', "id='$id'");
	}
	public static function run($id = 0){
		if ($id) {
			$task = db::one('sys_task', '*', "id='$id'");
			if ($task) {
				$task = self::formatTask($task);
				switch ($task['type']) {
					case 0:
						$file = $task['data'];
						$file = qd(qscms::getCfgPath('/system/taskRoot').$file);
						@include($file);
					break;
					case 1:
						@eval($task['data']);
					break;
				}
			}
		} else {return false;
			//加上内存缓存 更有效果
			$th = time::tshInfo();
			$tm = time::tsmInfo();
			$tw = time::tswkInfo();
			$tsm = time::tsm();
			$taskList = cache::get_array('sys_task');
			$isRun = false;
			foreach ($taskList as $k => $v) {
				$run = false;
				switch ($v['timeType']) {
					case 0:
						if (empty($v['timeInfo']['hour']) && empty($v['timeInfo']['minute']) && empty($v['timeInfo']['second']) || $v['timeInfo']['hour']) {
							if (!empty($v['nextTime'])) $time = $v['nextTime'];
							else $time = time::$todayStart + $v['time'];
							$run = time::$timestamp >= $time;
							$run && $taskList[$k]['nextTime'] = time::$todayStart + $v['time'] + 86400;
						} elseif ($v['timeInfo']['minute']) {
							if (!empty($v['nextTime'])) $time = $v['nextTime'];
							else $time = $th['start'] + $v['time'];
							$run = time::$timestamp >= $time;
							$run && $taskList[$k]['nextTime'] = $th['start'] + $v['time'] + 3600;
						} else {
							if (!empty($v['nextTime'])) $time = $v['nextTime'];
							else $time = $tm['start'] + $v['time'];
							$run = time::$timestamp >= $time;
							$run && $taskList[$k]['nextTime'] = $tm['start'] + $v['time'] + 60;
						}
					break;
					case 1:
						if (empty($v['nextTime'])) {
							$run = time::$timestamp >= $v['time'];
							$run && $taskList[$k]['nextTime'] = time::$timestamp;
						}
					break;
					case 2:
						if (!empty($v['nextTime'])) $time = $v['nextTime'];
						else $time = time::$timestamp - $v['time'];
						$run = time::$timestamp - $time >= $v['time'];
						$run && $taskList[$k]['nextTime'] = time() + $v['time'];
					break;
					case 3:
						if (!empty($v['nextTime'])) $time = $v['nextTime'];
						else $time = $tw['start'] + $v['time'];
						$run = time::$timestamp >= $time;
						$run && $taskList[$k]['nextTime'] = $tw['start'] + $v['time'] + 604800;
					break;
					case 4:
						if (!empty($v['nextTime'])) $time = $v['nextTime'];
						else $time = $tsm['start'] + $v['time'];
						$run = time::$timestamp >= $time;
						$run && $taskList[$k]['nextTime'] = time::addMonth($tsm['start'] + $v['time']);
					break;
				}
				$taskList[$k]['run'] = $run;
				!$isRun && $run && $isRun = true;
			}
			if ($isRun) cache::write_array('sys_task', $taskList);
			foreach ($taskList as $k => $v) {
				if ($v['run']) {
					$lock = new lock($k, 3600);
					if (!$lock->islock) {
						switch ($v['type']) {
							case 0:
								//文件
								$file = $v['data'];
								$file = qd(qscms::getCfgPath('/system/taskRoot').$file);
								@include($file);
							break;
							case 1:
								//代码
								@eval($v['data']);
							break;
						}
					}
				}
			}
		}
	}
	public static function setSort($ids, $sts){
		if ($count = form::arrayEqual($ids, $sts)) {
			for ($i = 0; $i < $count; $i++) {
				$id = $ids[$i];
				$st = $sts[$i];
				db::update('sys_task', array('sort' => $st), "id='$id'");
			}
			self::upCache();
		}
	}
	public static function del($ids){
		is_array($ids) || $ids = array($ids);
		if ($rs = db::del_ids('sys_task', $ids)) {
			self::upCache();
		}
		return $rs;
	}
}
?>