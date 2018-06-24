<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class db_access{
	private $conn;
	public function __construct($dbPath){
		$this->conn = new COM('ADODB.Connection');
		$connStr = 'Provider=Microsoft.Jet.OLEDB.4.0;Data Source='.$dbPath;
		if (!$this->conn) throw new e_qscms('创建ADODB.Connection失败');
		$this->conn->Open($connStr);
	}
	public function query($sql){
		$rs = @new COM("ADODB.Recordset");
		$rs->open($sql, $this->conn, 1, 1);
		return $rs;
	}
	public function fetchAll($sql){
		$rs = $this->query($sql);
		$arr = array();
		$index = 0;
		while (!$rs->EOF) {
			for ($x = 0; $x < $rs->Fields->Count; $x++ ) {
				$arr[ $index ][ $rs->Fields[$x]->Name ] = $rs->Fields[$x]->Value;
			}
			$rs->MoveNext();
			$index++;
		}
		$rs->close();
		$rs = null;
		return $arr;
	}
	public function one($tbName, $fields, $where = '', $order = ''){
		$where && $where = ' WHERE '.$where;
		$order && $order = ' ORDER BY '.$order;
		$sql = 'SELECT TOP 1 '.$fields.' FROM '.$tbName.$where.$order;
		$list = $this->fetchAll($sql);
		if ($list) return $list[0];
		return false;
	}
	public function __destruct(){
		if ($this->conn) {
			$this->conn->close();
			$this->conn = null;
		}
	}
}
?>