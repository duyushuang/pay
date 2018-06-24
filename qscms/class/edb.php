<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class edb extends db{
	private static function parseKeyValue($str){
		$str = trim($str);
		$l = strlen($str);
		$atName = $atStr = false;
		$strFlag = $key = $val = '';
		$list = array();
		for ($i = 0; $i < $l; $i++) {
			$s = substr($str, $i, 1);
			$end = $i + 1 == $l;
			if ($atStr) {
				if ($strFlag) {
					$val .= $s;
					if ($s == $strFlag && (substr($str, $i - 1, 1) != '\\' || substr($str, $i - 2, 1) == '\\')) {//end
						$list[$key] = substr($val, 1, -1);
						$key = $val = '';
						$atStr = false;
					}
				} else {
					if (in_array($s, array(' ', "\r", "\n", "\t", ",")) || $end) {//end
						$end && $val .= $s;
						$list[$key] = $val;
						$key = $val = '';
						$atStr = false;
					} else $val .= $s;
				}
			} elseif ($atName) {
				if ($s == '=') {
					$key = trim($key);
					$key = trim($key, '`');
					$atName = false;
				} else $key .= $s;
			} elseif(!in_array($s, array(' ', "\r", "\n", "\t", ','))) {
				if (!$key) {
					$key = $s;
					$atName = true;
				} else {
					$val = $s;
					$atStr = true;
					if ($s == '\'' || $s == '"') $strFlag = $s;
					else $strFlag = false;
				}
			}
		}
		return $list;
	}
	public static function parseInsert($sql){
		$sql = trim($sql);
		$f = 12;//insert into 
		$f1 = strpos($sql, '(', $f);
		$tbName = '';
		if ($f1 !== false) $tbName = substr($sql, $f, $f1 - $f);
		elseif (($f1 = strpos($sql, ' ', $f)) !== false) {
			$tbName = substr($sql, $f, $f1 - $f);
			$f1 = strpos($tbName, ' ');
			if ($f1 !== false) $tbName = substr($tbName, 0, $f1);
		}
		$tbName = trim($tbName);
		$f += strlen($tbName);
		$list = array();
		if (preg_match('/^insert into [a-z_]+\s*\((.+?)\)\s*values\s*\((.+?)\)$/is', $sql, $matches)) {
			$keyStr = $matches[1];
			$valStr = $matches[2];
			$list0 = qscms::trimExplode(',', $keyStr);
			
			@eval('$list1 = array('.$valStr.');');
			$count = form::arrayEqual($list0, $list1);
			if ($count) {
				for ($i = 0; $i < $count; $i++) {
					$list[trim($list0[$i], '`')] = $list1[$i];
				}
			}
			
		} elseif (preg_match('/^insert into [a-z_]+\s+set\s+(.+)$/is', $sql, $matches)) {
			$str = $matches[1];
			$list = self::parseKeyValue($str);
		}
		return $list;
	}
	public static function eventSql($sql){
		$f = strpos($sql, ' ');
		if ($f !== false){
			$type = strtoupper(substr($sql, 0, $f));
			if (in_array($type, array('INSERT', 'UPDATE', 'DELETE'))) {
				($type == 'INSERT' || $type == 'DELETE') && $f += 5;
				$f += 1;
				$f1 = strpos($sql, '(', $f);
				$tbName = '';
				if ($f1 !== false) $tbName = substr($sql, $f, $f1 - $f);
				elseif (($f1 = strpos($sql, ' ', $f)) !== false) {
					$tbName = substr($sql, $f, $f1 - $f);
					$f1 = strpos($tbName, ' ');
					if ($f1 !== false) $tbName = substr($tbName, 0, $f1);
				} else $tbName = substr($sql, $f);
				$tbName = trim($tbName);
				$tbName = trim($tbName, '`');
				if ($tbName) {
					$l = strlen(PRE);
					if (substr(PRE, 0, $l) == substr($tbName, 0, $l)) {
						$tbName = substr($tbName, $l);
						switch ($type) {
							case 'INSERT':
								$datas = self::parseInsert($sql);
								$on = db::one('sys_table', 'onInsert,onInsertType,onInsertRun', "name='$tbName'");
								if ($on && $on['onInsert']) {
									$datas['pre'] = PRE;
									$run = qscms::replaceVars($on['onInsertRun'], $datas);
									switch ($on['onInsertType']) {
										case 'SQL':
											self::query($run);
										break;
										case 'PHP':
											@eval($run);
										break;
									}
								}
							break;
							case 'UPDATE':
								$where = string::getPregVal('/^.*where (.+?)$/is', $sql);
								$sql = db::sqlSelect($tbName, '*', $where);
								$on = db::one('sys_table', 'onUpdate,onUpdateType,onUpdateRun', "name='$tbName'");
								foreach (db::fetchAll($sql) as $datas) {
									if ($on && $on['onUpdate']) {
										$datas['pre'] = PRE;
										$run = qscms::replaceVars($on['onUpdateRun'], $datas);
										switch ($on['onUpdateType']) {
											case 'SQL':
												self::query($run);
											break;
											case 'PHP':
												@eval($run);
											break;
										}
									}
								}
							break;
							case 'DELETE':
								$where = string::getPregVal('/^.*where (.+?)$/is', $sql);
								$sql = db::sqlSelect($tbName, '*', $where);
								$on = db::one('sys_table', 'onDelete,onDeleteType,onDeleteRun', "name='$tbName'");
								foreach(db::fetchAll($sql) as $datas) {
									if ($on && $on['onDelete']) {
										$datas['pre'] = PRE;
										$run = qscms::replaceVars($on['onDeleteRun'], $datas);
										switch ($on['onDeleteType']) {
											case 'SQL':
												self::query($run);
											break;
											case 'PHP':
												@eval($run);
											break;
										}
									}
								}
							break;
						}
					}
				}
			}
		}
	}
	public static function query($sql, $type = ''){
		$type = strtoupper(trim(substr($sql, 0, 7)));
		if ($type == 'DELETE') self::eventSql($sql);
		if ($rs = self::execute('query', $sql, $type)) {
			if ($type != 'DELETE') self::eventSql($sql);
		}
		return $rs;
	}
	public static function queryUnbuffered($sql) {
		return self::execute('query_unbuffered', $sql);
	}
	public static function querys($sqls){
		$sp = qscms::trimExplode(';', $sqls);
		foreach ($sp as $sql) {
			if ($sql = trim($sql)) {
				self::execute('query', $sql, 'UNBUFFERED');
			}
		}
	}
	public static function insert($tbname,$args,$return_insert_id=false){
		$sql = self::sqlInsert($tbname, $args);
		if ($sql) {
			if(self::query($sql)){
				if($return_insert_id) {
					return self::insertId();
				}
				return true;
			}
		}
		return false;
	}
	public static function insert2($tbname,$args,$return_insert_id=false){
		self::query('LOCK TABLE `'.self::table($tbname).'` WRITE');
		do {
			$id = self::createId();
		} while(db::exists($tbname, array('id' => $id)));
		$args = array_merge(array('id'=>$id), $args);
		self::insert($tbname, $args);
		self::query('UNLOCK TABLES');
		if($return_insert_id)return $id;
		return true;
	}
	public static function update($tbname, $args, $where='', $count = -1){
		$sql = self::sqlUpdate($tbname, $args, $where, $count);
		if($sql) {
			return self::query($sql) ? true : false;
		}
		return false;
	}
	public static function updateSql($sql){
		return self::query($sql) ? true : false;
	}
	public static function replace($tbname, $args, $where=''){
		$sql = self::sqlReplace($tbname, $args, $where);
		if($set) {
			return self::query($sql) ? true : false;
		}
		return false;
	}
	public static function delete($tbname, $where = ''){
		self::query('DELETE FROM '.self::table($tbname).($where ? ' WHERE '.$where : ''));
		return self::affectedRows();
	}
	public static function del_key($tbname,$key,$val){
		return self::delete($tbname,'`'.$key.'`=\''.$val.'\'');
	}
	public static function del_keys($tbname,$key,$val){
		if(is_array($val) && count($val)>0) {
			return self::delete($tbname,'`'.$key.'` in(\''.implode('\',\'',$val).'\')');
		}
		return 0;
	}
	public static function del_id($tbname,$id){
		return self::del_key($tbname,'id',$id);
	}
	public static function del_ids($tbname,$id){
		return self::del_keys($tbname,'id',$id);
	}
	public static function clear($tb, $reset = false){
		self::query('DELETE FROM `'.self::table($tb).'`');
		$reset && self::query('ALTER TABLE `'.self::table($tb).'` AUTO_INCREMENT=1');
	}
}
?>