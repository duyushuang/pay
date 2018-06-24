<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class cfg extends ext_base{
	private static $tb_cate = 'sys_cfg_cate', $tb_list = 'sys_cfg_list', $cacheName = 'sys_config_';
	public static function addCate($name, $remark){
		if ($name && mb_strlen($name) <= 32 && mb_strlen($remark) <= 64) {
			if (!db::exists(self::$tb_cate, array('name' => $name))) {
				if ($cid = db::insert(self::$tb_cate, array(
					'name'   => $name,
					'remark' => $remark
				), true)) {
					return $cid;
				}
				return '添加失败！';
			}
			return '该分类名已经存在了！';
		}
		return '添加失败！';
	}
	public static function getCateTotal(){
		return intval(db::data_count(self::$tb_cate));
	}
	public static function getCateId($name){
		return db::one_one(self::$tb_cate, 'id', "name='$name'");
	}
	public static function getCates($page = 1, $pageSize = 20){
		$page = intval($page);
		//$page < 1 && $page = 1;
		return db::select(self::$tb_cate, '*', '', '', $pageSize, $page);
	}
	public static function getCate($id){
		$id = intval($id);
		return db::one(self::$tb_cate, '*', "id='$id'");
	}
	public static function editCate($name, $remark, $id){
		if ($name && mb_strlen($name) <= 32 && mb_strlen($remark) <= 64) {
			if (db::exists(self::$tb_cate, array('id' => $id))) {
				if (!db::exists(self::$tb_cate, "name='$name' and id<>'$id'")) {
					if (db::update(self::$tb_cate, array(
						'name'   => $name,
						'remark' => $remark
					), "id='$id'")) {
						return true;
					}
					return '修改失败！';
				}
			}
			return '该分类名已经存在了！';
		}
		return '修改失败！';
	}
	public static function addCfg($pid, $name, $type, $attach, $remark, $run = ''){
		if (db::exists(self::$tb_cate, array('id' => $pid))) {
			if (!db::exists(self::$tb_list, array(
				'cid' => $pid,
				'name' => $name
			))) {
				if ($id = db::insert(self::$tb_list, array(
					'cid'    => $pid,
					'name'   => $name,
					'type'   => $type,
					'attach' => $attach,
					'run'    => $run,
					'remark' => $remark
				), true)) {
					return $id;
				}
				return '添加失败！';
			}
			return '该配置名称已经存在！';
		}
		return '添加失败！';
	}
	public static function getCfgTotal($id){
		$id = intval($id);
		return db::data_count(self::$tb_list, "cid='$id'");
	}
	public static function getCfgs($cid, $page = 1, $pageSize = 20){
		$page = intval($page);
		return db::select(self::$tb_list, '*', "cid='$cid'", 'sort', $pageSize, $page);
	}
	public static function delCfg($ids){
		is_array($ids) || $ids = array($ids);
		$sids = '\''.implode('\',\'', $ids).'\'';
		foreach (db::select(self::$tb_list, 'value', "id in($sids) and type='image' and value<>''") as $v) {
			@unlink(d('./'.$v['value']));
		}
		$cfg = self::getCfg($ids[0]);
		$rs = db::del_ids(self::$tb_list, $ids);
		if ($rs > 0) self::updateCfg($cfg['cid']);
		return $rs;
	}
	public static function setSort($ids, $sorts){
		if (!empty($ids) && !empty($sorts) && is_array($ids) && is_array($sorts)) {
			$count = form::array_equal($ids, $sorts);
			if ($count) {
				for ($i = 0; $i < $count; $i++) {
					$id = $ids[$i];
					$sort = $sorts[$i];
					db::update(self::$tb_list, array('sort' => $sort), "id='$id'");
				}
				return true;
			}
		}
		return false;
	}
	public static function getCfg($id){
		$id = intval($id);
		return db::one(self::$tb_list, '*', "id='$id'");
	}
	public static function getCfgInfoToCate($name){
		if ($cate = db::one(self::$tb_cate, '*', "name='$name'")) {
			$rn = array('cate' => $cate, 'list' => self::getCfgs($cate['id'], 0, 0));
			return $rn;
		}
		return false;
	}
	public static function editCfg($id, $name, $type, $attach, $remark, $run = ''){
			if (!db::exists(self::$tb_list, "name='$name' and id<>'$id'")) {
				if (db::update(self::$tb_list, array(
					'name'   => $name,
					'type'   => $type,
					'attach' => $attach,
					'run'    => $run,
					'remark' => $remark
				), "id='$id'")) {
					return true;
				}
				return '修改失败！';
			}
			return '该配置名称已经存在！';
		return '修改失败！';
	}
	public static function setCfg($cid, $arr){
		$var = qscms::v('_G');
		if ($cfgList = self::getCfgs($cid, 0, 0)) {
			$cate = self::getCate($cid);
			foreach ($cfgList as $v) {
				if (isset($arr[$v['name']])) $val = $arr[$v['name']];
				else $val = '';
				switch ($v['type']) {
					case 'text':
					break;
					case 'select':
					break;
					case 'radio':
					break;
					case 'radio2':
					break;
					case 'checkbox':
						if (is_array($val)) {
							$val = string::getCheckBox($val);
						} else {
							$val = 0;
						}
					break;
					case 'image':
						if (!$var->menuAjax) {
							$imgRoot = qscms::getCfgPath('/system/imgRoot');
							//$saveDir0 = 'img/cfg/'.$cate['name'].'/';
							//$saveDir0 = u($imgRoot.$cate['name'].'/');
							$saveDir0 = substr($imgRoot.$cate['name'].'/', 1);
							$saveDir0 = substr($saveDir0, 1);
							$saveDir1 = d('./'.$saveDir0);
							file::createFolder($saveDir1);
							if ($img = upload::uploadImage($v['name'], $saveDir1)) {
								$val = $saveDir0.$img;
								if ($attach = $v['attach']) {
									foreach (qscms::trimExplode("\n", $attach) as $__v) {
										$__sp = qscms::trimExplode('=', $__v);
										if ($__sp[0] == 'filename') {
											//重命名文件名
											$name = !empty($__sp[1]) ? $__sp[1] : '';
											if ($name) {
												if (($f = strrpos($img, '.')) !== false) {
													$suffix = substr($img, $f + 1);
													$val = $name.'.'.$suffix;
													if ($val != $img) {
														if (!@rename($saveDir1.$img, $saveDir1.$val)) {
															if (@copy($saveDir1.$img, $saveDir1.$val)) {
																@unlink($saveDir1.$img);
																$val = $saveDir0.$val;
															} else {
																$val = $saveDir0.$img;
															}
														} else {
															$val = $saveDir0.$val;
														}
													} else {
														$val = $saveDir0.$img;
													}
												}
											}
										}
									}
								}
								if ($v['value'] && $v['value'] != $val) @unlink(d('./'.$v['value']));
							} else $val = $v['value'];
						}
					break;
					default:
					break;
				}
				if ($run = $v['run']) {
					//$run = qscms::stripslashes($run);
					$run = str_replace('{value}', '\''.qscms::addcslashes(qscms::stripslashes($val)).'\'', $run);
					@eval($run);
				}
				db::update(self::$tb_list, array('value' => $val), "id='$v[id]'");
			}
			self::updateCfg($cid);
			return true;
		}
		return false;
	}
	public static function uploadImg($datas){
		$datas = form::get4($datas, array(array('cfgId', 'int')));
		$cfgId = $datas['cfgId'];
		if ($cfg = self::getCfg($cfgId)) {
			$cid = $cfg['cid'];
			if ($cate = self::getCate($cid)) {
				$imgRoot = qscms::getCfgPath('/system/imgRoot');
				$saveDir0 = substr($imgRoot.$cate['name'].'/', 1);
				$saveDir0 = substr($saveDir0, 1);
				$saveDir1 = d('./'.$saveDir0);
				file::createFolder($saveDir1);
				$val = false;
				if ($img = upload::uploadImage($cfg['name'], $saveDir1)) {
					$val = $saveDir0.$img;
					if ($attach = $cfg['attach']) {
						foreach (qscms::trimExplode("\n", $attach) as $__v) {
							$__sp = qscms::trimExplode('=', $__v);
							if ($__sp[0] == 'filename') {
								//重命名文件名
								$name = !empty($__sp[1]) ? $__sp[1] : '';
								if ($name) {
									if (($f = strrpos($img, '.')) !== false) {
										$suffix = substr($img, $f + 1);
										$val = $name.'.'.$suffix;
										if ($val != $img) {
											if (!@rename($saveDir1.$img, $saveDir1.$val)) {
												if (@copy($saveDir1.$img, $saveDir1.$val)) {
													@unlink($saveDir1.$img);
													$val = $saveDir0.$val;
												} else {
													$val = $saveDir0.$img;
												}
											} else {
												$val = $saveDir0.$val;
											}
										} else {
											$val = $saveDir0.$img;
										}
									}
								}
							}
						}
					}
					if ($cfg['value'] && $cfg['value'] != $val) @unlink(d('./'.$cfg['value']));
				} else $val = $v['value'];
				if ($val) return array($val);
				return '上传失败';
			}
			return '获取分类失败';
		}
		return '获取配置失败';
	}
	public static function updateCfg($cid){
		if ($cate = self::getCate($cid)) {
			$cacheName = self::$cacheName.$cate['name'];
			if ($list = self::getCfgs($cid, 0, 0)) {
				$datas = array();
				foreach ($list as $v) {
					$datas[$v['name']] = $v['value'];
				}
			}
			cache::write_array($cacheName, $datas);
		}
	}
	public static function updateCfgAll(){
		foreach (self::getCates(0, 0) as $v) self::updateCfg($v['id']);
	}
	public static function delCate($ids){
		is_array($ids) || $ids = array($ids);
		foreach ($ids as $id) {
			if ($cate = self::getCate($id)) {
				$cacheName = self::$cacheName.$cate['name'];
				foreach (db::select(self::$tb_list, 'value', "cid='$cate[id]' and type='image' and value<>''") as $v) {
					@unlink(d('./'.$v['value']));
				}
				db::del_key(self::$tb_list, 'cid', $cate['id']);
				db::del_id(self::$tb_cate, $cate['id']);
				cache::delete_array($cacheName);
			}
		}
	}
	public static function getMemory($key){
		return memory::get(self::$cacheName.$key);
	}
	public static function writeMemory($key, $value){
		memory::write(self::$cacheName.$key, $value);
	}
	public static function get($pname, $name = ''){
		$cacheName = self::$cacheName.$pname;
		$datas = memory::get($cacheName);
		$isSet = false;
		if (isset($datas)) {
			$isSet = true;
		}
		if (!$isSet) {
			$datas = cache::get_array($cacheName, true);
			if ($datas) memory::write($cacheName, $datas);
		}
		if ($name) {
			if (strpos($name, '*') === false) {
				if (isset($datas[$name])) return $datas[$name];
				return NULL;
			} else {
				$rn = array();
				$ignore = qscms::addcslashes($name, '.\\');
				$ignore = str_replace('*', '.*?', $ignore);
				$ignore = '/^'.$ignore.'$/i';
				foreach ($datas as $k => $v) {
					preg_match($ignore, $k) > 0 && $rn[] = $v;
				}
				return $rn;
			}
		}
		return $datas;
	}
	public static function getRand($pname, $name, $cache = false, $cacheData = false){
		static $cacheArr, $cacheDatas;
		$cacheKey = $pname.'_'.$name.'_'.$cache;
		$cacheKey2 = $cacheKey.'_'.$cacheData;
		if ($cacheData && isset($cacheDatas[$cacheKey2])) return $cacheDatas[$cacheKey2];
		if ($cache && isset($cacheArr[$cacheKey])) $data = $cacheArr[$cacheKey];
		else {
			$arr = self::get($pname, $name);
			if (strpos($name, '*') !== false) {
				$data = $arr[rand(0, count($arr) - 1)];
			} else $data = $arr;
			$data = qscms::trimExplode("\n", $data);
			qscms::arrayUnsetEmpty($data);
			$cache && $cacheArr[$cacheKey] = $data;
		}
		$count = count($data);
		$rs = $data[rand(0, $count - 1)];
		$cacheData && $cacheDatas[$cacheKey2] = $rs;
		return $rs;
	}
	public static function getRandPinyin($pname, $name, $cache = false, $cacheData = false){
		loadFunc('text');
		static $cacheArr;
		if ($cacheData && isset($cacheArr[$cacheData])) return $cacheArr[$cacheData];
		$data = self::getRand($pname, $name, $cache, $cacheData);
		$rs = getPinyin($data);
		$cacheData && $cacheArr[$cacheData] = $rs;
		return $rs;
	}
	public static function getInt($pname, $name){
		return intval(self::get($pname, $name));
	}
	public static function getMoney($pname, $name){
		return qscms::formatMoney(self::get($pname, $name));
	}
	public static function getMoneySpace($pname, $name){
		static $cache = array();
		$key = $pname.'_'.$name;
		if (isset($cache[$key])) return $cache[$key];
		$str = self::get($pname, $name);
		$sp = qscms::trimSplit('/~|-|,|，/', $str);
		if (count($sp) == 2) $rs = array(qscms::formatMoney($sp[0]), qscms::formatMoney($sp[1]));
		else $rs = false;
		$cache[$key] = $rs;
		return $rs;
	}
	public static function getMoneySpaceRand($pname, $name){
		$arr = self::getMoneySpace($pname, $name);
		if ($arr !== false) {
			$level = 10 / $arr[0];
			$i = $arr[0] * $level;
			$j = $arr[1] * $level;
			return rand($i, $j) / $level;
		}
		return false;
	}
	public static function getFloat($pname, $name){
		return floatval(self::get($pname, $name));
	}
	public static function getBoolean($pname, $name){
		$data = self::get($pname, $name);
		if (isset($data)) {
			$data = strtolower($data);
			if (in_array($data, array('true', '1', 'yes' ,'ok'))) return true;
			return false;
		}
		return false;
	}
	public static function getPercent($pname, $name){
		$data = self::get($pname, $name);
		if (isset($data)) {
			$data = doubleval($data);
			$data = floor($data * 10000) / 100;
			return $data.'%';
		}
		return '0%';
	}
	public static function getImage($pname, $name){
		$data = self::get($pname, $name);
		if (isset($data)) {
			return '<img src="'.WEB_URL.'/'.$data.'" />';
		}
		return '';
	}
	public static function getList($pname, $name){
		$list = self::getMemory($pname.'_'.$name);
		if (isset($list)) return $list;
		$list = array();
		if ($str = self::get($pname, $name)) {
			foreach (explode(';', $str) as $v) {
				@list($key, $value, $checked) = explode(',', $v);
				isset($checked) || $checked = false;
				$list[] = array(
					'key'     => $key,
					'value'   => $value,
					'checked' => $checked
				);
			}
		}
		self::writeMemory($pname.'_'.$name, $list);
		return $list;
	}
	public static function getListValue($pname, $name, $key){
		$_key = $pname.'_'.$name.'_'.$key;
		$val = self::getMemory($_key);
		if (isset($val)) return $val;
		if ($list = self::getList($pname, $name)) {
			foreach ($list as $v) {
				if ($v['key'] == $key) {
					self::writeMemory($_key, $v['value']);
					return $v['value'];
				}
			}
		}
		return false;
	}
	public static function getListValues($pname, $name){
		if ($list = self::getList($pname, $name)) {
			$rs = array();
			foreach ($list as $v) {
				$rs[] = $v['value'];
			}
			return $rs;
		}
		return false;
	}
	public static function getNavigation($pname, $name){
		$list = array();
		if ($str = self::get($pname, $name)) {
			foreach (common::trimExplode("\n", $str) as $v) {
				if ($v = trim($v)) {
					$url = $name = $action = '';
					$__arr = common::trimExplode('|', $v);
					//list($url, $name, $action) = common::trimExplode('|', $v);
					$url    = isset($__arr[0]) ? $__arr[0] : '';
					$name   = isset($__arr[1]) ? $__arr[1] : '';
					$action = isset($__arr[2]) ? $__arr[2] : '';
					$list[] = array('url' => $url, 'name' => $name, 'action' => $action, 'actions' => explode(',', $action));
				}
			}
		}
		return $list;
	}
	public static function getLanList($pname, $name){
		$list = array();
		$keys = array();
		if ($str = self::get($pname, $name)) {
			foreach (qscms::trimExplode("\n", $str) as $v) {
				if ($v = trim($v)) {
					$__arr = qscms::trimExplode('|', $v);
					if (!$keys) $keys = $__arr;
					else {
						foreach ($__arr as $__k => $__v) {
							!isset($list[$keys[$__k]]) && $list[$keys[$__k]] = array();
							$list[$keys[$__k]][] = $__v;
						}
					}
				}
			}
		}
		return $list;
	}
	public static function getJSON($pname, $name){
		return string::json_decode(self::get($pname, $name));
	}
}
?>