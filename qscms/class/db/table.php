<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class db_table{
	public function __construct($table, $fields= '*', $where = false, $order = false, $pagesize = 0, $page = 0){
		$this->table = $table;
		$this->fields = $fields;
		$this->where = $where;
		$this->order = $order;
		$this->pagesize = $pagesize;
		$page <= 0 && $page = 1;
		$this->page = $page;
		$this->cache = array();
	}
	public function where(){
		if (isset($this->cache['where'])) return $this->cache['where'];
		if ($this->where) {
			$where = sql::getWhere($this->where);
		} else $where = '';
		$this->cache['where'] = $where;
		return $where;
	}
	public function sql(){
		if (isset($this->cache['sql0'])) return $this->cache['sql0'];
		$sql = db::sqlSelect($this->table, $this->fields, $this->where(), $this->order);
		$this->cache['sql0'] = $sql;
		return $sql;
	}
	public function sqlSize(){
		if (isset($this->cache['sql1'])) return $this->cache['sql1'];
		$sql = db::sqlSelect($this->table, $this->fields, $this->where(), $this->order, $this->pagesize, $this->page);
		$this->cache['sql1'] = $sql;
		return $sql;
	}
	public function total(){
		if (isset($this->cache['total'])) return $this->cache['taotal'];
		$sql = db::sqlSelect($this->table, 'COUNT(*)', $this->where());
		$total = intval(db::resultFirst($sql));
		//$total = db::dataCount($this->table, $this->where());
		$this->cache['total'] = $total;
		return $total;
	}
	public function fetch(){
		return db::fetchAll($this->sqlSize());
	}
	public function fetchAll(){
		return db::fetchAll($this->sql());
	}
	public function fetchOne(){
		return db::fetchFirst($this->sql());
	}
	public function leftJoin($tb, $joinWhere, $pagesize = 0, $page = 0){
		$sql = $this->sqlSize();
		$where = '';
		foreach ($joinWhere as $k => $v) {
			$where && $where .= ',';
			$where .= "$k=$v";
		}
		return new self("($sql)|$tb->table:$where", '*|'.$tb->fields, $tb->where, $tb->order, $tb->pagesize, $tb->page);
	}
}
?>