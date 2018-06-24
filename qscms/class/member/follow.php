<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class member_follow{
	const TYPE_USER = 1;
	public function __construct($table, $type, $ftype){
		$this->table = $table;
		$this->type  = $type;
		$this->ftype = $ftype;
	}
	public function set($tid, $fid){
		$id = db::one_one($this->table, 'id', "type='$this->type' AND tid='$tid' AND fid='$fid' AND ftype='$this->ftype'");
		if ($id) {//删除
			if (db::del_id($this->table, $id)) {
				return 2;
			}
		} else {//添加
			if (db::insert($this->table, array(
				'type'  => $this->type,
				'tid'   => $tid,
				'fid'   => $fid,
				'ftype' => $this->ftype
			))) {
				return 1;
			}
		}
		return false;
	}
}