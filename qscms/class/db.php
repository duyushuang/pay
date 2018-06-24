<?php

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class db{
	public static $connected = false;
	private static $pre = '';
	public static function initialize(){
		if (qscms::defineTrue('INSTALL')) {
			self::$pre = qscms::getCfgPath('/global/db_table_pre');
			self::object();
		}
	}
	public static function setVar($key, $val){
		static $db;
		if(empty($db)) $db = &self::object();
		$db->$key = $val;
	}
	public static function &object(){
		static $db;
		if (!isset($db)) {
			if ($config = qscms::getConfig('global')) {
				$db = new db_mysql($config['db_host'], $config['db_user'], $config['db_pwd'], $config['db_name'], $config['db_port'], 0, defined('DB_HALT') && DB_HALT === true);
				self::$connected = $db->connected;
				//self::$pre = $config['db_table_pre'];
			}
		}
		return $db;
	}
	public static function execute($cmd, $arg1 = NULL, $arg2 = NULL, $arg3 = NULL){
		static $db;
		if(empty($db)) $db = &self::object();
		if (!is_null($arg1)) {
			if (!is_null($arg2)) {
				if (!is_null($arg3)) {
					return $db->$cmd($arg1, $arg2, $arg3);
				}
				return $db->$cmd($arg1, $arg2);
			}
			return $db->$cmd($arg1);
		}
		return $db->$cmd();
	}
	public static function table($tableName){
		return self::$pre.$tableName;
	}
	public static function get_insert($arr,$null=true){
		if($arr){
			foreach($arr as $k=>$v){
				if(($v=trim($v))==''&&!$null)continue;
				if($rn)$rn.=',';
				$rn.="`$k`='$v'";
			}
			return $rn;
		}
	}
	public static function query($sql, $type = ''){
		return self::execute('query', $sql, $type);
	}
	public static function queryUnbuffered($sql) {
		return self::execute('query_unbuffered', $sql);
	}
	public static function queryRows($query){
		return self::execute('num_rows', $query);
	}
	public static function error(){
		return self::execute('error');
	}
	public static function errno(){
		return self::execute('errno');
	}
	public static function querys($sqls){
		$sp = qscms::trimExplode(';', $sqls);
		foreach ($sp as $sql) {
			if ($sql = trim($sql)) {
				self::execute('query', $sql, 'UNBUFFERED');
			}
		}
	}
	public static function fetch($query, $result_type = MYSQL_ASSOC){
		return self::execute('fetch_array', $query, $result_type);
	}
	public static function fetchAll($sql, $result_type = MYSQL_ASSOC, $call = false){
		return self::execute('fetch_all', $sql, $result_type, $call);
	}
	public static function fetchArrayFirst($query){
		return self::execute('fetch_array_first', $query);
	}
	public static function fetchArrayFirstAll($sql){
		return self::execute('fetch_first_all', $sql);
	}
	public static function fetchFirst($sql, $resultType = MYSQL_ASSOC){
		return self::execute('fetch_first', $sql, $resultType);
	}
	public static function resultFirst($sql){
		return self::execute('result_first', $sql);
	}
	public static function getFieldNames($query){
		return self::execute('get_fields_name', $query);
	}
	public static function getInsert($args){
		if(is_array($args) && count($args)>0) {
			$keys = '';
			$vals = '';
			foreach($args as $k => $v) {
				$keys && $keys .= ',';
				$keys .= '`'.$k.'`';
				$vals && $vals .= ',';
				$vals .= '\''.$v.'\'';
			}
			$insert = "($keys) values($vals)";
		} else {
			$insert = trim($args);
			if(substr($insert, 0, 1) != '(') {
				strtolower(substr($insert, 0, 3)) != 'set' && $insert = 'set '.$insert;
				$insert = ' '.$insert;
			}
		}
		return $insert;
	}
	public static function sqlInsert($tbname, $args){
		$insert = '';
		if(is_array($args) && count($args)>0) {
			$keys='';
			$vals='';
			foreach($args as $k=>$v) {
				$keys && $keys.=',';
				$keys.='`'.$k.'`';
				$vals && $vals.=',';
				$vals.='\''.$v.'\'';
			}
			$insert="($keys) values($vals)";
		} else {
			$insert=trim($args);
			if(substr($insert,0,1)!='(') {
				strtolower(substr($insert,0,3))!='set' && $insert='set '.$insert;
				$insert=' '.$insert;
			}
		}
		if($insert) {
			$insert = 'insert into '.self::table($tbname).$insert;
			return $insert;
		}
		return false;
	}
	public static function insert($tbname,$args,$return_insert_id=false){
		$sql = self::sqlInsert($tbname, $args);
		if($sql) {
			if(self::query($sql)){
				if ($return_insert_id) return self::execute('insert_id');
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
	public static function insert_return_sn($tbname,$args,$return_insert_id=false){
		self::query('LOCK TABLE `'.self::table($tbname).'` WRITE');
		do {
			$id = self::createId();
		} while(db::exists($tbname, array('sn' => $id)));
		$args = array_merge(array('sn'=>$id), $args);
		$inser_id = self::insert($tbname, $args, $return_insert_id);
		self::query('UNLOCK TABLES');
		if($return_insert_id) return $id;
		return true;
	}
	public static function createSn(){//16位的
		list($millisecond, $second)=explode(' ', microtime());
		$millisecond  = (float)$millisecond;
		$second       = (int)$second;
		$millisecond *= 1000000;
		$millisecond  = sprintf('%06d', floor($millisecond));
		//$salt = substr(uniqid(rand()), -5);
		$id = date('dmsHi', $second).$millisecond;	
		return $id;
	}
	public static function createId(){ //20位的
		list($millisecond, $second)=explode(' ', microtime());
		$millisecond  = (float)$millisecond;
		$second       = (int)$second;
		$millisecond *= 1000000;
		$millisecond  = sprintf('%06d', floor($millisecond));
		//$salt = substr(uniqid(rand()), -5);
		$id = date('YmdHis', $second).$millisecond;
		return $id;
	}
	public static function sqlUpdate($tbname, $args, $where='', $count = -1){
		$set = '';
		if(is_array($args) && count($args)>0) {
			$keys='';
			$vals='';
			foreach($args as $k=>$v) {
				$set && $set.=',';
				$set.="`$k`='$v'";
			}
		} else $set=$args;
		strtolower(substr($set,0,3))!='set' && $set=' set '.$set;
		if($set) {
			$set='update '.self::table($tbname).$set.($where?' where '.$where:'').($count != -1?' LIMIT '.$count:'');
			return $set;
		}
		return false;
	}
	public static function update($tbname, $args, $where='', $count = -1){
		$sql = self::sqlUpdate($tbname, $args, $where, $count);
		if($sql) {
			//if (self::error()) return false;
			//return true;
			return self::query($sql)?true:false;
		}
		return false;
	}
	public static function updateSql($sql){
		return self::query($sql) ? true : false;
	}
	public static function sqlReplace($tbname, $args, $where=''){
		$set = '';
		if(is_array($args) && count($args)>0) {
			$keys = '';
			$vals = '';
			foreach ($args as $k => $v) {
				$set && $set .= ',';
				$set .= "`$k`='$v'";
			}
		} else $set=$args;
		strtolower(substr($set,0,3))!='set' && $set=' set '.$set;
		if($set) {
			$set='replace '.self::table($tbname).$set.($where?' where '.$where:'');
			return $set;
		}
		return false;
	}
	public static function replace($tbname, $args, $where=''){
		$sql = self::sqlReplace($tbname, $args, $where);
		if($sql) {
			return self::query($sql)?true:false;
		}
		return false;
	}
	public static function delete($tbname, $where = '', $limit = ''){
		self::query('DELETE FROM '.self::table($tbname).($where ? ' WHERE '.$where : '').($limit ? ' LIMIT '.$limit : ''));
		return self::affectedRows();
	}
	public static function affectedRows(){
		return self::execute('affected_rows');
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
	public static function exists($tbname, $args, $f = '', $forUpdate = false){
		$where='';
		if(is_array($args)){
			foreach($args as $k=>$v){
				$where && $where.=' and ';
				$where.='`'.$k.'`=\''.$v.'\'';
				$f=='' && $f=$k;
			}
		} else $where=$args;
		$f || $f = '*';
		//echo 'SELECT '.$f.' FROM '.self::table($tbname).' WHERE '.$where;exit;
		return self::execute('fetch_first', 'SELECT '.$f.' FROM '.self::table($tbname).' WHERE '.$where.($forUpdate ? ' FOR UPDATE' : ''))?true:false;
	}
	public static function data_count($tbname,$where='', $addPrefix = true) {
		$tb = $tbname;
		$addPrefix && $tb = self::table($tb);
		return intval(self::execute('result_first', 'SELECT COUNT(*) FROM '.$tb.($where?' WHERE '.$where:'')));
	}
	public static function dataCount($tbname, $where = '', $addPrefix = true){
		return self::data_count($tbname, $where, $addPrefix);
	}
	public static function one($tbname, $f='*',$where='',$order='', $forUpdate = false){
		$where && $where=' where '.$where;
		$order && $order=' order by '.$order;
		return self::execute('fetch_first', 'SELECT '.$f.' FROM '.self::table($tbname).$where.$order.' LIMIT 1'.($forUpdate ? ' FOR UPDATE' : ''));
	}
	public static function one_one($tbname,$f='*',$where='',$order='', $forUpdate = false){
		$where && $where=' WHERE '.$where;
		$order && $order=' ORDER BY '.$order;
		return self::execute('result_first', 'SELECT '.$f.' from '.self::table($tbname).$where.$order.' LIMIT 1'.($forUpdate ? ' FOR UPDATE' : ''));
	}
	public static function get_list($tbname,$f='*',$where='',$order='',$page=1,$pagesize=20){
		$where && $where=' WHERE '.$where;
		$order && $order=' ORDER BY '.$order;
		$limit = '';
		$page > 0 && $limit = ' LIMIT '.($page - 1) * $pagesize.','.$pagesize;
		return self::execute('fetch_all', 'SELECT '.$f.' from '.self::table($tbname).$where.$order.$limit);
	}
	public static function get_list2($tbname,$f='*',$where='',$order='',$page=1,$pagesize=20){
		$where && $where=' WHERE '.$where;
		$order && $order=' ORDER BY '.$order;
		$limit = '';
		$page > 0 && $limit = ' LIMIT '.($page - 1) * $pagesize.','.$pagesize;
		if($ids = self::execute('fetch_first_all', 'SELECT id FROM '.self::table($tbname).$where.$order.$limit)){
			return self::execute('fetch_all', 'SELECT '.$f.' FROM '.self::table($tbname).' WHERE id in(\''.implode('\',\'',$ids).'\')'.$order);
		}
	}
	public static function get_ids($tbName, $where = '', $order = '', $pagesize = 0, $page = 0){
		return self::get_keys($tbName, 'id', $where, $order, $pagesize, $page);
	}
	public static function get_keys($tbName, $key, $where = '', $order = '', $pagesize = 0, $page = 0){
		$limit = '';
		$where && $where=' WHERE '.$where;
		$order && $order=' ORDER BY '.$order;
		//$page > 0 && $limit = ' LIMIT '.($page-1)*$pagesize.','.$pagesize;
		$pagesize > 0 && $limit = ' LIMIT '.($page > 0 ? ($page - 1) * $pagesize . ',' : '').$pagesize;
		return self::execute('fetch_first_all', 'SELECT '.$key.' FROM '.self::table($tbName).$where.$order.$limit);
	}
	public static function showCreateTable($tableName, $addPrefix = true){
		$addPrefix && $tableName = self::table($tableName);
		if ($line = self::execute('fetch_first', 'SHOW CREATE TABLE '.$tableName)) {
			return $line['Create Table'];
		}
		return false;
	}
	public static function fieldsAddPrefix($fields, $prefix){
		$rn = '';
		$sp = explode(',', $fields);
		foreach ($sp as $v){
			$rn && $rn .= ',';
			$rn .= $prefix.'.'.$v;
		}
		return $rn;
	}
	public static function sqlSelect($tables, $fields, $wheres = false, $orders = false, $pageSize= 0, $page = 0){
		if (strpos($tables, '|') !== false) {
			$tablesArr = explode('|', $tables);
			$fieldsArr = strpos($fields, '|') !== false ? explode('|', $fields) : $fields;//$fields != '*' ? explode('|', $fields) : $fields;
			$wheresArr = $wheres ? explode('|', $wheres) : array();
			$ordersArr = $orders ? explode('|', $orders) : array();
			$sql = 'SELECT ';
			//初始化字段
			if (is_array($fieldsArr)) {
				$tmpFields = '';
				foreach ($fieldsArr as $k => $v) {
					if ($v) {
						$tmpFields && $tmpFields .= ',';
						$tmpFields .= self::fieldsAddPrefix($v, 't'.$k);
					}
				}
				$sql .= $tmpFields;
			} else {
				$sql .= $fieldsArr;
			}
			$tmpTables = '';
			$tmpTableLastAs = '';
			foreach ($tablesArr as $k => $v) {
				$tmpTableAs = 't'.$k;
				$tmpTables && $tmpTables .= ' LEFT JOIN ';
				if (strpos($v, ':') !== false) {
					$tmpOn = '';
					$sp0 = explode(':', $v);
					$sp1 = explode(',', $sp0[1]);
					$tmpTables .= (substr($sp0[0], 0, 1) == '(' ? $sp0[0] : self::table($sp0[0])) .' AS '.$tmpTableAs;
					foreach($sp1 as $v1) {
						$tmpOn && $tmpOn .= ',';
						list($tmpA, $tmpB) = explode('=', $v1);
						strpos($tmpA, '.') === false && $tmpA = $tmpTableAs.'.'.$tmpA;
						strpos($tmpB, '.') === false && $tmpB = $tmpTableLastAs.'.'.$tmpB;
						$tmpOn .= $tmpA.'='.$tmpB;
					}
					$tmpTables .= ' ON '.$tmpOn;
				} else {
					$tmpTables .= (substr($v, 0, 1) == '(' ? $v : self::table($v)).' AS '.$tmpTableAs;;
				}
				$tmpTableLastAs = $tmpTableAs;
			}
			$sql .= ' FROM '.$tmpTables . '';
			$sql .= ($wheres ? ' WHERE '.$wheres : '').($orders ? ' ORDER BY '.$orders : '');
			$pageSize > 0 && $sql .= ' LIMIT '.($page > 0 ? ($page - 1) * $pageSize . ',' : '').$pageSize;
		} else {
			$sql = 'SELECT '.$fields.' FROM '.(substr($tables, 0, 1) == '(' ? "$tables t0": self::table($tables)).($wheres ? ' WHERE '.$wheres : '').($orders ? ' ORDER BY '.$orders : '');
			$pageSize > 0 && $sql .= ' LIMIT '.($page > 0 ? ($page - 1) * $pageSize . ',' : '').$pageSize;
		}
		return $sql;
	}
	public static function select($tables, $fields = '*', $wheres = false, $orders = false, $pageSize= 0, $page = 0, $call = false){
		$sql = self::sqlSelect($tables, $fields, $wheres, $orders, $pageSize, $page);
		return self::execute('fetch_all', $sql, MYSQL_ASSOC, $call);
	}
	public static function selectFirst($tables, $fields, $wheres = false, $orders = false, $call = false){
		if ($list = self::select($tables, $fields, $wheres, $orders, 1, 1, $call)) {
			return $list[0];
		}
		return array();
	}
	public static function clear($tb, $reset = false){
		self::query('DELETE FROM `'.self::table($tb).'`');
		$reset && self::query('ALTER TABLE `'.self::table($tb).'` AUTO_INCREMENT=1');
	}
	public static function tableExists($tb, $addPrefix = true){
		$tb = qscms::addcslashes($tb, '_');
		$addPrefix && $tb = self::table($tb);
		$rs = self::fetchAll('SHOW TABLES LIKE \''.$tb.'\'');
		return $rs ? true : false;
	}
	public static function insertId(){
		return self::execute('insert_id');
	}
	public static function changeRows(){
		return self::affectedRows();
	}
	public static function lockTable($table, $type, $addPrefix = true){
		$addPrefix && $table = self::table($table);
		$type = strtoupper($type);
		$tps = array('READ', 'WRITE');
		if (!in_array($type, $tps)) return false;
		self::queryUnbuffered('LOCK TABLE `'.$table.'` '.$type);
	}
	public static function lockTableRead($table, $addPrefix = true){
		self::lockTable($table, 'READ', $addPrefix);
	}
	public static function lockTableWrite($table, $addPrefix = true){
		self::lockTable($table, 'WRITE', $addPrefix);
	}
	public static function unlockTables(){
		self::queryUnbuffered('UNLOCK TABLES');
	}
	public static function getRandSql($table, $fields = '*', $count = 1, $key = 'id', $where = ''){
		$tb = self::table($table);
		$where && $where = ' WHERE '.$where;
		$less = $count - 1;
		$sql = 'SELECT '.$fields.' FROM `'.$tb.'` AS t1 JOIN (SELECT ROUND(RAND() * ((SELECT MAX(`'.$key.'`) FROM `'.$tb.'`'.$where.')-(SELECT MIN(`'.$key.'`) FROM `'.$tb.'`'.$where.')'.($less > 0 ? '-'.$less : '').')+(SELECT MIN(`'.$key.'`) FROM `'.$tb.'`'.$where.')) AS `'.$key.'`) AS t2'.($where ? $where.' AND ' : ' WHERE ').'t1.`'.$key.'` >= t2.`'.$key.'` ORDER BY t1.`'.$key.'` LIMIT '.$count;
		return $sql;
	}
	public static function getRand($table, $fields = '*', $count = 1, $key = 'id', $where = '', $call = false){
		$sql = self::getRandSql($table, $fields, $count, $key, $where);
		return self::fetchAll($sql, MYSQL_ASSOC, $call);
	}
	public static function limit($table, $fields = '*', $where = false, $order = false, $pageSize = 20, $page = 0, $key = 'id'){
		$limit = 0;
		$page > 0 && $limit = ($page - 1) * $pageSize;
		$where1 = '';
		$where && $where1 = ' AND '.$where;
		($where && ($where = ' WHERE '.$where)) || $where = '';
		($order && ($order = ' ORDER BY '.$order)) || $order = '';
		$table = self::table($table);
		if (!$limit) return 'SELECT '.$fields.' FROM `'.$table.'`'.$where.$order.($pageSize ? ' LIMIT '.$pageSize : '');
		return 'SELECT '.$fields.' FROM `'.$table.'` WHERE '.$key.'>=(SELECT '.$key.' FROM `'.$table.'` ORDER BY id LIMIT '.$limit.', 1)'.$where1.$order.' LIMIT '.$pageSize;
	}
	public static function getSet($args){
		if (is_array($args)) {
			if (count($args) > 0) {
				$keys='';
				$vals='';
				foreach($args as $k=>$v) {
					$set && $set.=',';
					$set.="`$k`='$v'";
				}
				return $set;
			}
			return '';
		}
		return $args;
	}
	public static function procedure(){
		if (func_num_args() > 0) {
			$args = func_get_args();
			$name = array_shift($args);
			$sql = 'call '.self::formatFunc($name, $args);
			$list = self::execute('call', $sql);
			print_r($list);
		}
	}
	private static function formatFunc($funName, $arr){
		$rn = '';
		foreach ($arr as $v) {
			$rs = '';
			switch(gettype($v)){
				case 'boolean':
					$rs = $v ? "true":"false";
				break;
				case 'integer':
					$rs = $v;
				break;
				case 'double':
					$rs = $v;
				break;
				case 'string':
					$rs = '\''.addcslashes($v,'\'\\').'\'';
				break;
				default:
					$rs = '\'\'';
				break;
			}
			$rn && $rn .= ',';
			$rn .= $rs;
		}
		return "$funName($rn)";
	}
	public static function autocommit($start = true){
		self::query('SET AUTOCOMMIT='.($start ? '0' : '1'));
	}
	public static function commit($closeAutoCommit = false){
		self::query('COMMIT');
		$closeAutoCommit && self::autocommit(false);
	}
	public static function rollback($closeAutoCommit = false){
		self::query('ROLLBACK');
		$closeAutoCommit && self::autocommit(false);
	}
}
db::initialize();
?>