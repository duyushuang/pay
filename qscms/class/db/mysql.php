<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
define('MYSQLI', function_exists('mysqli_connect'));
class db_mysql{

	var $version = '';
	var $querynum = 0;
	var $link = null;
	var $connected=false;
	var $query_halt=true;
	function __construct($dbhost, $dbuser, $dbpw, $dbname = '', $dbport = 3306, $pconnect = 0, $halt = TRUE, $dbcharset = 'utf8') {
		$this->query_halt = $halt;
		if (MYSQLI) {
			if(!$this->link = @mysqli_connect($dbhost, $dbuser, $dbpw, $dbname, $port)) {
				$halt && $this->halt('Can not connect to MySQL server');
			} else {
				$this->connected = true;
				if($this->version() > '4.1') {
					$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
					$serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
					$serverset && mysqli_query($this->link, "SET $serverset");
				}
			}
		} else {
			$func = empty($pconnect) ? 'mysql_connect' : 'mysql_pconnect';
			if(!$this->link = @$func($dbhost.':'.$dbport, $dbuser, $dbpw, 1)) {
				$halt && $this->halt('Can not connect to MySQL server');
			} else {
				$this->connected = true;
				if($this->version() > '4.1') {
					$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
					$serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
					$serverset && mysql_query("SET $serverset", $this->link);
				}
				$dbname && @mysql_select_db($dbname, $this->link);
			}
		}
	}

	function select_db($dbname) {
		return MYSQLI ? mysqli_select_db($this->link, $dbname) : mysql_select_db($dbname, $this->link);
	}
	function fetch_array_first($query){
		if ($query) {
			$line = MYSQLI ? mysqli_fetch_array($query, MYSQL_NUM) : mysql_fetch_array($query, MYSQL_NUM);
			return $line[0];
		}
		return '';
	}
	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		if ($query) {
			return MYSQLI ? mysqli_fetch_array($query, $result_type) : mysql_fetch_array($query, $result_type);
		}
		return false;
	}
	function fetch_all($sql,$result_type = MYSQL_ASSOC, $call = false){
		$list  = array();
		$query = $this->query($sql);
		while($line = $this->fetch_array($query,$result_type)){
			$call && $call($line);
			if ($line !== false) $list[] = $line;
		}
		return $list;
	}
	function fetch_first_all($sql){
		$list=array();
		$query=$this->query($sql);
		while($line=$this->fetch_array($query,MYSQL_NUM)){
			$list[]=$line[0];
		}
		return $list;
	}
	function print_all($sql,$tpl,$result_type = MYSQL_ASSOC){
		$query=$this->query($sql);
		$rn='';
		while($line=$this->fetch_array($query,$result_type)){
			$rn.=preg_replace('/\{([a-zA-Z0-9_]+?)\}/e','$line[$1]',$tpl);
		}
		return $rn;
	}
	function fetch_first($sql, $resultType = MYSQL_ASSOC) {
		return $this->fetch_array($this->query($sql), $resultType);
	}

	function result_first($sql) {
		return $this->result($this->query($sql), 0);
	}

	function query($sql, $type = '') {
		if (MYSQLI) {
			if(!($query = mysqli_query($this->link, $sql))) {
				if($this->query_halt){
					if(in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
						$this->close();
						$this->halt('连接超时', $sql);
						return $this->query($sql, 'RETRY'.$type);
					} elseif($type != 'SILENT' && substr($type, 5) != 'SILENT') {
						$this->halt('MySQL Query Error', $sql);
					}
				}
			}
		} else {
			$func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query')?'mysql_unbuffered_query' : 'mysql_query';
			if(!($query = $func($sql, $this->link))) {
				if($this->query_halt){
					if(in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
						$this->close();
						$this->halt('连接超时', $sql);
						return $this->query($sql, 'RETRY'.$type);
					} elseif($type != 'SILENT' && substr($type, 5) != 'SILENT') {
						$this->halt('MySQL Query Error', $sql);
					}
				}
			}
		}
		$this->querynum++;
		return $query;
	}
	function multi_query($sql){
		return mysqli_multi_query($this->link, $sql);
	}
	function call($sql){
		$list = array();
		if ($query = $this->multi_query($sql)) {
			do {
				if ($result = mysqli_store_result($this->link)) {
					$arr = array();
					while ($row = $result->fetch_row()) {
						$arr[] = $row;
					}
					$result->close();
					$arr && $list[] = $arr;
				}
			} while (mysqli_next_result($this->link));
		}
		return $list;
	}
	function query_unbuffered($sql){
		$this->query($sql,'UNBUFFERED');
		return $this->affected_rows();
	}
	public function is_resource($q){
		return is_resource($q) || is_object($q);
	}
	public function get_fields_name($query){
		if($this->is_resource($query)){
			$rn = array();
			if (MYSQLI) {
				if($num=mysqli_num_fields($query)){
					for($i=0;$i<$num;$i++){
						$v = mysqli_fetch_field($query);
						$rn[] = $v->name;
					}
				}
			} else {
				if($num=mysql_num_fields($query)){
					for($i=0;$i<$num;$i++){
						$rn[]=mysql_field_name($query,$i);
					}
				}
			}
			return $rn;
		}
	}
	function affected_rows($link=NULL) {
		$link || $link=$this->link;
		return MYSQLI ? mysqli_affected_rows($link) : mysql_affected_rows($link);
	}

	function error() {
		if (MYSQLI) return (($this->link) ? mysqli_error($this->link) : mysqli_error());
		return (($this->link) ? mysql_error($this->link) : mysql_error());
	}

	function errno() {
		if (MYSQLI) return intval(($this->link) ? mysqli_errno($this->link) : mysqli_errno());
		return intval(($this->link) ? mysql_errno($this->link) : mysql_errno());
	}

	function result($query, $row = 0) {
		if (MYSQLI) {
			mysqli_data_seek($query, $row);
			if ($line = mysqli_fetch_row($query)) return $line[0];
			return false;
		} else {
			$query = @mysql_result($query, $row);
			return $query;
		}
	}

	function num_rows($query) {
		if (MYSQLI) return mysqli_num_rows($query);
		$query = mysql_num_rows($query);
		return $query;
	}

	function num_fields($query) {
		return MYSQLI ? mysqli_num_fields($query) : mysql_num_fields($query);
	}

	function free_result($query) {
		return $query ? (MYSQLI ? mysqli_free_result($query) : mysql_free_result($query)) : false;
	}

	function insert_id() {
		if (MYSQLI) return ($id = mysqli_insert_id($this->link)) > 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
		return ($id = mysql_insert_id($this->link)) > 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}

	function fetch_row($query) {
		$query = MYSQLI ? mysqli_fetch_row($query) : mysql_fetch_row($query);
		return $query;
	}

	function fetch_fields($query) {
		return MYSQLI ? mysqli_fetch_field($query) : mysql_fetch_field($query);
	}

	function version() {
		if(empty($this->version)) {
			$this->version = MYSQLI ? mysqli_get_server_info($this->link) : mysql_get_server_info($this->link);
		}
		return $this->version;
	}
	
	function __destruct(){
		$this->close();
	}
	
	function close() {
		if ($this->connected) {
			return MYSQLI ? mysqli_close($this->link) : mysql_close($this->link);
		}
	}

	public function halt($message, $sql=''){
		$t = time();
		$logDir = d('./cache/error/sql/'.date('Y/m', $t).'/');
		!file_exists($logDir) && file::mkdir($logDir);
		$logFile = $logDir.date('d', $t).'.log';
		error_log(date('Y-m-d H:i:s', $t)."\r\nSQL:\r\n$sql\r\nmessage:\r\n".$this->error()."\r\n\r\n", 3, $logFile);
		echo $message.($sql?' <br />'.$sql:'').'<br />'.$this->error();exit;
		//qscms::showMessage($message.($sql?' <br />'.$sql:'').'<br />'.$this->error());
	}
}
?>