<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class table{
	private static function tableDataFormat(&$datas){
		$datas = qscms::filterArray($datas, array('name', 'onInsert', 'onInsertType', 'onInsertRun', 'onUpdate', 'onUpdateType', 'onUpdateRun', 'onDelete', 'onDeleteType', 'onDeleteRun'), true);
		$datas['name'] = trim($datas['name']);
		$datas['onInsertRun'] = trim($datas['onInsertRun']);
		$datas['onUpdateRun'] = trim($datas['onUpdateRun']);
		$datas['onDeleteRun'] = trim($datas['onDeleteRun']);
		qscms::setType($datas['onInsert'], '01');
		qscms::setType($datas['onInsertType'], 'SQL,PHP');
		qscms::setType($datas['onUpdate'], '01');
		qscms::setType($datas['onUpdateType'], 'SQL,PHP');
		qscms::setType($datas['onDelete'], '01');
		qscms::setType($datas['onDeleteType'], 'SQL,PHP');
		if (!$datas['onInsert']) {
			$datas['onInsertType'] = $datas['onInsertRun'] = '';
		} elseif (!$datas['onInsertRun']) {
			$datas['onInsert'] = 0;
			$datas['onInsertType'] = '';
		}
		if (!$datas['onUpdate']) {
			$datas['onUpdateType'] = $datas['onUpdateRun'] = '';
		} elseif (!$datas['onUpdateRun']) {
			$datas['onUpdate'] = 0;
			$datas['onUpdateType'] = '';
		}
		if (!$datas['onDelete']) {
			$datas['onDeleteType'] = $datas['onDeleteRun'] = '';
		} elseif (!$datas['onDeleteRun']) {
			$datas['onDelete'] = 0;
			$datas['onDeleteType'] = '';
		}
	}
	public static function createTable($datas){
		self::tableDataFormat($datas);
		if (db::exists('sys_table', array('name' => $datas['name'])) || db::tableExists($datas['name'])) return '该表名已经存在了';
		$sort = intval(db::resultFirst('SELECT MAX(sort) FROM `'.db::table('sys_table').'`')) + 1;
		$datas['sort'] = $sort;
		if ($datas['name']) {
			if (db::insert('sys_table', $datas)) {
				return true;
			}
			return db::error();
		} return '参数错误';
	}
	public static function tableIdExists($id) {
		return db::exists('sys_table', array(!is_string($id) ? 'id' : 'name' => $id));
	}
	public static function tableExists($id){
		return db::exists('sys_table', array(!is_string($id) ? 'id' : 'name' => $id));
	}
	public static function modifyTable($datas, $id){
		if (self::tableIdExists($id)) {
			self::tableDataFormat($datas);
			$oldTbName = db::one_one('sys_table', 'name', "id='$id'");
			if ($oldTbName != $datas['name']) {//原表名与现在的不同
				if (db::exists('sys_table', array('name' => $datas['name'])) || db::tableExists($datas['name'])) return '新的表名已经存在了';
			}
			if (db::update('sys_table', $datas, "id='$id'")) {
				return true;
			}
			return db::error();
		}
		return '要修改的表不存在';
	}
	public static function getTables(){
		return db::get_keys('sys_table', 'name');
	}
	public static function getTable($id, $fields = '*'){
		return db::one('sys_table', $fields, !is_string($id) ? "id='$id'" : "name='$id'");
	}
	private static function fieldDataFormat(&$datas){
		$datas = qscms::filterArray($datas, array(
			'name', 
			'fieldName', 
			'fieldType', 
			'auto',
			'htmlName', 
			'htmlType', 
			'htmlWidth', 
			'htmlHeight',
			'imageWidth',
			'imageHeight',
			'htmlListValue',
			'htmlDefaultValue',
			'htmlIsReg',
			'htmlRegStr',
			'tip'
		));
		$datas = qscms::trim($datas);
		qscms::setType($datas['auto'], '01');
		qscms::setType($datas['htmlWidth'], 'int');
		qscms::setType($datas['htmlHeight'], 'int');
		qscms::setType($datas['imageWidth'], 'int');
		qscms::setType($datas['imageHeight'], 'int');
		qscms::setType($datas['htmlIsReg'], '01');
		$datas && ($datas = qscms::stripslashes($datas));
	}
	public static function addField($datas, $tid){
		if (!self::tableIdExists($tid)) return '要添加字段的表不存在';
		self::fieldDataFormat($datas);
		$next = true;
		$msg = '';
		$table = db::one('sys_table', 'name,fieldCount', "id='$tid'");
		$tbName = PRE.$table['name'];
		$isFirst = intval($table['fieldCount']) == 0;
		if ($next) {
			if (!$isFirst && $datas['auto']) {
				if (db::exists('sys_table_field', array('tid' => $tid, 'auto' => 1))) {
					$msg = '自增长字段一个表只能有一个';
					$next = false;
				}
			}
		}
		if ($next) {
			if (db::exists('sys_table_field', array('tid' => $tid, 'fieldName' => $datas['fieldName']))) {
				$msg = '该字段名已经存在了';
				$next = false;
			}
		}
		if ($next) {
			if (db::exists('sys_table_field', array('tid' => $tid, 'htmlName' => $datas['htmlName']))) {
				$msg = '该HTML名已经存在了';
				$next = false;
			}
		}
		if ($next) {
			if ($isFirst) {
				if ($datas['auto']) {
					$sql = 'CREATE TABLE `'.PRE.$table['name'].'` (`'.$datas['fieldName'].'` '.$datas['fieldType'].' auto_increment,PRIMARY KEY (`'.$datas['fieldName'].'`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET='.DB_ENCODING;
				} else {
					$sql = 'CREATE TABLE `'.PRE.$table['name'].'` (`'.$datas['fieldName'].'` '.$datas['fieldType'].') ENGINE=MyISAM DEFAULT CHARSET='.DB_ENCODING;
				}
				if (!db::query($sql)) {
					$msg = db::error();
					$next = false;
				}
			} else {
				$sql = "ALTER TABLE `$tbName` ADD `$datas[fieldName]` $datas[fieldType]";
				if (!db::query($sql)) {
					$msg = db::error();
					$next = false;
				} else {
					if ($datas['auto']) {
						$sql = "ALTER TABLE `$tbName` add primary key($datas[fieldName]);ALTER TABLE `$tbName` CHANGE `$datas[fieldName]` `$datas[fieldName]` $datas[fieldType] auto_increment=1";
						db::querys($sql);
					}
				}
			}
		}
		if ($next) {
			$sort = intval(db::one_one('sys_table_field', 'MAX(sort)', "tid='$tid'")) + 1;
			$datas = array('tid' => $tid, 'sort' => $sort) + $datas;
			$datas = qscms::addslashes($datas);
			if (db::insert('sys_table_field', $datas)) {
				db::update('sys_table', 'fieldCount=fieldCount+\'1\'', "id='$tid'");
				return true;
			} else {
				if ($isFirlst) $sql = 'DROP TABLE '.$tbName;
				else {
					if ($table['auto']) {
						$sql = "ALTER TABLE `$tbName` CHANGE $datas[fieldName] $datas[fieldName] $datas[fieldType];ALTER TABLE `$tbName` DROP primary key;ALTER TABLE `$tbName` DROP $datas[fieldName]";
					} else {
						$sql = "ALTER TABLE `".PRE."$table[name]` DROP `$datas[fieldName]`";
					}
				}
				db::querys($sql);
				return '添加失败，请重试！';
				$next = false;
			}
		} else return $msg;
	}
	public static function editField($datas, $fid){
		$oldInfo = db::one('sys_table_field', '*', "id='$fid'");
		if (!$oldInfo) return '该字段不存在';
		$tid = intval($oldInfo['tid']);
		if (!self::tableIdExists($tid)) return '要修改字段的表不存在';
		self::fieldDataFormat($datas);
		$next = true;
		$msg = '';
		$table = db::one('sys_table', 'name,fieldCount', "id='$tid'");
		$tbName = PRE.$table['name'];
		$isFirst = intval($table['fieldCount']) == 0;
		if ($next) {
			if (!$isFirst && $datas['auto']) {
				if (db::exists('sys_table_field', "tid='$tid' AND auto='1' AND id<>'$fid'")) {
					$msg = '自增长字段一个表只能有一个';
					$next = false;
				}
			}
		}
		if ($next) {
			if (db::exists('sys_table_field', "tid='$tid' and fieldName='$datas[fieldName]' and id<>'$fid'")) {
				$msg = '该字段名已经存在了';
				$next = false;
			}
		}
		if ($next) {
			if (db::exists('sys_table_field', "tid='$tid' and htmlName='$datas[htmlName]' and id<>'$fid'")) {
				$msg = '该HTML名已经存在了';
				$next = false;
			}
		}
		if ($next) {
			if ($oldInfo['auto'] != $datas['auto']) {
				if ($datas['auto']) {
					$sql = "ALTER TABLE `$tbName` ADD PRIMARY KEY($datas[fieldName])";
					if (db::query($sql)) {
						$sql = "ALTER TABLE `$tbName` CHANGE `$oldInfo[fieldName]` `$datas[fieldName]` $datas[fieldType]  AUTO_INCREMENT";
						if (!db::query($sql)) {
							db::query("ALTER TABLE `$tbName` DROP PRIMARY KEY");
							$msg = db::error();
							$next = false;
						}
					} else {
						$msg = db::error();
						$next = false;
					}
				} else {
					$sql = "ALTER TABLE `$tbName` CHANGE `$oldInfo[fieldName]` `$datas[fieldName]` $datas[fieldType]";
					if (db::query($sql)) {
						db::query("ALTER TABLE `$tbName` DROP PRIMARY KEY");
					} else {
						$msg = db::error();
						$next = false;
					}
				}
			} else {
				$sql = "ALTER TABLE `$tbName` CHANGE `$oldInfo[fieldName]` `$datas[fieldName]` $datas[fieldType]".($datas['auto'] ? ' AUTO_INCREMENT' : '');
				if (!db::query($sql)) {
					$msg = db::error();
					$next = false;
				} else {
					
				}
			}
		}
		if ($next) {
			$datas = qscms::addslashes($datas);
			if (db::update('sys_table_field', $datas, "id='$fid'")) {
				return true;
			} else {
				$msg = db::error();
				$next = false;
			}
		}
		return $msg;
	}
	public static function getFields($tid, $fields = '*'){
		return db::select('sys_table_field', $fields, "tid='$tid'", 'sort');
	}
	public static function getField($id){
		return db::one('sys_table_field', '*', "id='$id'");
	}
	public static function setSort($tid, $ids, $sort){
		if ($count = form::array_equal($ids, $sort)) {
			for ($i = 0; $i < $count; $i++) {
				$id = $ids[$i];
				$st = $sort[$i];
				db::update('sys_table_field', array('sort' => $st), "id='$id'");
			}
			$fields = db::select('sys_table_field', 'id,fieldName,fieldType,sort', "tid='$tid'", 'sort');
			$lastName = '';
			$tbName = PRE.db::one_one('sys_table', 'name', "id='$tid'");
			foreach ($fields as $k => $v) {
				if ($k > 0) {
					$sql = "ALTER TABLE `$tbName` CHANGE `$v[fieldName]` `$v[fieldName]` $v[fieldType] AFTER `$lastName`";
					db::query($sql);
					echo $sql , '<br />';
				}
				$lastName = $v['fieldName'];
			}
		}
	}
	public static function dropField($ids){
		!is_array($ids) && $ids = array($ids);
		$count = 0;
		foreach ($ids as $id) {
			$field = self::getField($id);
			if ($field) {
				$tb = db::one('sys_table', 'name,fieldCount', "id='$field[tid]'");
				$tbName = PRE.$tb['name'];
				if ($field['auto']) {
					db::querys("ALTER TABLE `$tbName` CHANGE `$field[fieldName]` `$field[fieldName]` $field[fieldType];ALTER TABLE `$tbName` DROP PRIMARY KEY");
				}
				if ($tb['fieldCount'] > 1) {
					db::query("ALTER TABLE `$tbName` DROP $field[fieldName]");
				} else {
					db::query("DROP TABLE `$tbName`");
				}
				db::update('sys_table', "fieldCount=fieldCount-'1'", "id='$field[tid]'");
				db::del_id('sys_table_field', $id);
				$count++;
			}
		}
		return $count;
	}
	public static function dropTable($ids){
		!is_array($ids) && $ids = array($ids);
		$count = 0;
		foreach ($ids as $id) {
			$table = self::getTable($id, 'name,fieldCount');
			if ($table['fieldCount'] > 0) {
				db::query('DROP TABLE `'.PRE.$table['name'].'`');
			}
			db::del_id('sys_table', $id);
			db::del_key('sys_table_field', 'tid', $id);
			$count++;
		}
		return $count;
	}
	private static function indexDataFormat(&$datas){
		$datas = qscms::filterArray($datas, array('name', 'indexName', 'indexFields', 'indexType'), true);
		$datas = qscms::trim($datas);
		qscms::setType($datas['indexType'], '01');
		$datas['indexFields'] = qscms::trimSplit('/,|，|\s+/', $datas['indexFields']);
		qscms::arrayUnsetEmpty($datas['indexFields']);
		$datas['indexFields'] = qscms::trim($datas['indexFields'], '`');
		$datas['indexFields'] = '`'.implode('`,`', $datas['indexFields']).'`';
		$datas['indexFields'] == '``' && $datas['indexFields'] = '';
	}
	public static function createIndex($tb, $datas){
		$table = self::getTable($tb);
		if (!$table) return '要添加索引的表不存在';
		$tid = $table['id'];
		$tbName = PRE.$table['name'];
		self::indexDataFormat($datas);
		$datas && extract($datas);
		$next = true;
		$msg = '';
		if ($next) {
			if (db::exists('sys_table_index', array('tid' => $tid, 'indexName' => $indexName))) {
				$msg = '该字索引已经存在了';
				$next = false;
			}
		}
		if ($next) {
			$indexList = db::fetchAll('SHOW INDEX FROM `'.$tbName.'`');
			$indexList = array_unique(qscms::arrid($indexList, 'Key_name'));
			if (in_array($indexName, $indexList)) {
				$msg = '该索引是系统索引，请换一个！';
				$next = false;
			}
		}
		if ($next) {
			//获取排序ID
			$sort = intval(db::one_one('sys_table_index', 'max(sort)', "tid='$tid'")) + 1;
		}
		if ($next) {
			$sql = 'CREATE '.($indexType == 1 ? ' UNIQUE ' : '').'INDEX `'.$indexName.'` ON `'.$tbName.'`('.$indexFields.')';
			if (!db::query($sql)) {
				$msg = db::error();
				$next = false;
			}
		}
		if ($next) {
			$datas = array('tid' => $tid, 'sort' => $sort) + $datas;
			if (db::insert('sys_table_index', $datas)) {
				
			} else {
				$sql = 'DROP INDEX `'.$indexName.'` ON `'.$tbName.'`';
				db::query($sql);
				$msg = '添加失败，请重试！';
				$next = false;
			}
		}
		if ($next) return true;
		return $msg;
	}
	public static function modifyIndex($iid, $datas){
		$indexInfo = self::getIndex($iid);
		if (!$indexInfo) return '要修改的索引不存在';
		$tid = intval($indexInfo['tid']);
		$table = self::getTable($tid);
		if (!$table) return '索引对应的表不存在，无法修改';
		$tbName = PRE.$table['name'];
		self::indexDataFormat($datas);
		$datas && extract($datas);
		$next = true;
		$msg = '';
		if ($next) {
			if (db::exists('sys_table_index', "tid='$tid' and indexName='$indexName' and id<>'$iid'")) {
				$msg  = '该字索引已经存在了';
				$next = false;
			}
		}
		if ($next && $indexName != $indexInfo['indexName']) {
			$indexList = db::fetchAll('SHOW INDEX FROM `'.PRE.$cmsTable.'`');
			$indexList = array_unique(qscms::arrid($indexList, 'Key_name'));
			if (in_array($indexName, $indexList)) {
				$msg = '该索引是系统索引，请换一个！';
				$next = false;
			}
		}
		if ($next) {
			$sql = 'DROP INDEX `'.$indexInfo['indexName'].'` ON `'.$tbName.'`';
			if (!db::query($sql)) {
				$msg = db::error();
				$next = false;
			}
		}
		if ($next) {
			$sql = 'CREATE '.($indexType == 1 ? ' UNIQUE ' : '').'INDEX `'.$indexName.'` ON `'.$tbName.'`('.$indexFields.')';
			if (!db::query($sql)) {
				$sql = 'CREATE '.($indexInfo['indexType'] == 1 ? ' UNIQUE ' : '').'INDEX `'.$indexInfo['indexName'].'` ON `'.$tbName.'`('.$indexInfo['indexFields'].')';
				db::query($sql);
				$msg = db::error();
				$next = false;
			}
		}
		if ($next) {
			if (db::update('sys_table_index', $datas, "id='$iid'")) {
				
			} else {
				$sql = 'DROP INDEX `'.$indexName.'` ON `'.$tbName.'`';
				db::query($sql);
				$sql = 'CREATE '.($indexInfo['indexType'] == 1 ? ' UNIQUE ' : '').'INDEX `'.$indexInfo['indexName'].'` ON `'.$tbName.'`('.$indexInfo['indexFields'].')';
				db::query($sql);
				$msg = '添加失败，请重试！';
				$next = false;
			}
		}
		if ($next) return true;
		return $msg;
	}
	public static function dropIndex($tid, $ids){
		$table = self::getTable($tid);
		if (!$table) return 0;
		$tbName = PRE.$table['name'];
		is_array($ids) || $ids = array($ids);
		$ids0 = $ids;
		$ids = '\'' . implode('\',\'', $ids0) . '\'';
		foreach (db::get_keys('sys_table_index', 'indexName', "tid='$tid' AND id IN($ids)") as $v) {
			$sql = 'DROP INDEX `'.$v.'` ON `'.$tbName.'`';
			db::query($sql);
		}
		return db::del_ids('sys_table_index', $ids0);
	}
	public static function indexSetSort($ids, $sort){
		$count = form::arrayEqual($ids, $sort);
		if ($count) {
			for ($i = 0; $i < $count; $i++) {
				$id = $ids[$i];
				$st = $sort[$i];
				db::update('sys_table_index', array('sort' => $st), "id='$id'");
			}
			return true;
		}
		return false;
	}
	public static function getIndexs($tb, $fields = '*'){
		if (is_string($tb)) {
			$line = self::getTable($tb, 'id');
			$tid = $line['id'];
		} else $tid = $tb;
		return db::select('sys_table_index', $fields, "tid='$tid'", 'sort');
	}
	public static function getIndex($iid, $fields = '*'){
		return db::one('sys_table_index', $fields, "id='$iid'");
	}
	public static function getDatas($tbName, $datas){
		$fieldDatas = array();
		$table = db::one('sys_table', 'id,name,fieldCount', "name='$tbName'");
		if ($table) {
			if ($table['fieldCount'] > 0) {
				$autoName = '';
				$fields = db::select('sys_table_field', 'auto,fieldName,htmlName,htmlType,htmlListValue,htmlIsReg,htmlRegStr', "tid='$table[id]'");
				foreach ($fields as $v) {
					if ($v['auto']) $autoName = $v['fieldName'];
					switch ($v['htmlType']) {
						case 'hidden':
							$data = !empty($datas[$v['htmlName']]) ? $datas[$v['htmlName']] : '';
						break;
						case 'txt':
							$data = $datas[$v['htmlName']];
						break;
						case 'textarea':
							$data = $datas[$v['htmlName']];
						break;
						case 'radio':
							$data = $datas[$v['htmlName']];
						break;
						case 'checkbox':
							$data = string::getCheckBox($datas[$v['htmlName']]);
						break;
						case 'select':
							$data = $datas[$v['htmlName']];
						break;
						case 'file':
							$data = $_FILES[$v['htmlName']]['name'];
						break;
						case 'image':
							if (!($data = $_FILES[$v['htmlName']]['name'])) {
								
							} else {
								
							}
						break;
						case 'editor':
							$data = $datas[$v['htmlName']];
						break;
					}
					if ($v['htmlIsReg'] && $v['htmlRegStr'] && $v['tip']) {
						$tips   = qscms::trimExplode('|', $v['tip']);
						$regStr = $v['htmlRegStr'];
						if (preg_match('/^(\/.*?\/)(\w*)$/', $regStr, $matches)) {
							$regStr = $matches[1];
							if (strpos($matches[2], 'i') !== false) $regStr .= 'i';
							if (strpos($matches[2], 'm') !== false) $regStr .= 's';
						} else {
							$regStr = '';
						}
						if ($regStr) {
							if (!preg_match($regStr, $data)) {
								empty($tips[1]) || $tips[1] = $tips[0];
								return $tips[1];
							}
						}
					}
					switch ($v['htmlType']) {
						case 'hidden':
							$data !== '' && $fieldDatas[$v['htmlName']] = $data;
						break;
						case 'txt':
							$fieldDatas[$v['htmlName']] = $data;
						break;
						case 'textarea':
							$fieldDatas[$v['htmlName']] = $data;
						break;
						case 'radio':
							$fieldDatas[$v['htmlName']] = $data;
						break;
						case 'checkbox':
							$fieldDatas[$v['htmlName']] = $data;
						break;
						case 'select':
							$fieldDatas[$v['htmlName']] = $data;
						break;
						case 'file':
							$saveDir0 = date('Y/m/d/', $timestamp);
							$saveDir  = d(qscms::getCfgPath('/system/fileRoot') . $saveDir0);
							if ($data = upload::uploadFile($v['htmlName'], $saveDir)) {
								$data = $saveDir0 . $data;
								$fieldDatas[$v['htmlName']] = $data;
							}
						break;
						case 'image':
							$saveDir0 = date('Y/m/d/', $timestamp);
								$saveDir  = d(qscms::getCfgPath('/system/imgRoot') . $saveDir0);
								if ($data = upload::uploadFile($v['htmlName'], $saveDir)) {
									$sourcePic = $saveDir . $data;
									$pathinfo = pathinfo($sourcePic);
									$thumbPic  = $pathinfo['dirname'] . D . $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
									$pic1 = $saveDir0 . $data;
									$pic2 = '';
									if ($v['imageWidth'] && $v['imageHeight']) {
										if (image::thumb($sourcePic, $thumbPic, array('width' => $v['imageWidth'], 'height' => $v['imageHeight']))) {
											$pic2 = $saveDir0 . $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
										}
										//$fieldDatas[$v['htmlName'].'_thumb'] = $pic2;
									}
									//$pic2 || $pic2 = $pic1;
									$fieldDatas[$v['htmlName']] = $pic1;
								}
						break;
						case 'editor':
							//$data = addslashes(links_add::union(stripslashes($data), db::get_list('union_links', '`key`,url', '', 'sort,timestamp desc', -1)));
							$fieldDatas[$v['htmlName']] = $data;
						break;
					}
				}
				if (!$fieldDatas) return '没有获取到数据';
				return $fieldDatas;
			} else return '自定义表：'.$tbName.' 还未添加字段';
		} else return '自定义表：'.$tbName.' 不存在';
	}
	public static function insert($tbName, $datas, $returnId = false) {
		$datas = self::getDatas($tbName, $datas);
		if (!is_array($datas)) return $datas;
		if ($rs = edb::insert($tbName, $datas, $returnId)) {
			return $rs;
		}
		return '添加失败，请重试';
	}
	public static function update($tbName, $datas, $where = ''){
		$datas = self::getDatas($tbName, $datas);
		if (!is_array($datas)) return $datas;
		if (edb::update($tbName, $datas, $where)) return true;
		return '修改失败';
	}
	public static function delete($tbName, $where = ''){
		return edb::delete($tbName, $where);
	}
}