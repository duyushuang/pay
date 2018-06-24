<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class war_group{
	public $datas, $status;
	public function __construct($id, $isId = true, $uid = 0){
		if ($datas = db::one('war_group', '*', $isId ? "id='$id'" : "name='$id'")) {
			$this->datas = $datas;
			$this->wid = $datas['id'];
			$this->status = true;
			$this->isUser = false;
			if ($uid) {
				if ($item = db::one('war_group_member', '*', "wid='$this->wid' AND uid='$uid'")) {
					if ($item['type'] >= 1) $this->isManager = true;
					if ($item['type'] == 2) $this->isFounder = true;
					$this->isUser = true;
				}
			}
		} else {
			$this->status = false;
		}
	}
	private function _join($uid, $type){
		$datas = array(
			'wid' => $this->wid,
			'uid' => $uid,
			'type' => $type,
			'joinTime' => time()
		);
		if (db::insert('war_group_member', $datas)) return 1;
		return '加入失败，请重试';
	}
	public function joinMember($uid, $type = 0){
		if (db::exists('war_group_member', array('wid' => $this->wid, 'uid' => $uid))) return '你已经是该战队成员，无需重复加入';
		if (db::exists('war_group_verify', array('wid' => $this->wid, 'uid' => $uid))) return '你已经提交过了，请等待管理员验证通过';
		if (!$type && $this->datas['isVerify']) {
			if (db::insert('war_group_verify', array(
				'wid' => $this->wid,
				'uid' => $uid,
				'time' => time()
			))) return 2;
			return '加入失败，请重试';
		} else return $this->_join($uid, $type);
	}
	public function delete(){
		return db::del_id('war_group', $this->wid);
	}
	public function safeDelete(){
		if ($this->isFounder) {
			$this->delete();
			return '删除成功';
		}
		return '没有权限';
	}
	public function getMemberList(){
		$sql = db::sqlSelect('war_group_member', 'uid,type,joinTime,lastActive', "wid='$this->wid'", 'type DESC,lastActive DESC');
		$sql = db::sqlSelect("($sql)|member:id=uid", '*');
		return db::fetchAll($sql);
	}
	public function getVerifyList(){
		$sql = db::sqlSelect('war_group_verify', 'uid,time', "wid='$this->wid'", 'time DESC');
		$sql = db::sqlSelect("($sql)|member:id=uid", '*');
		return db::fetchAll($sql);
	}
	public function isLeaguer($uid){
		return db::exists('war_group_member', array('wid' => $this->wid, 'uid' => $uid));
	}
	public function setManager($uid){
		if (!$this->isFounder) return '很抱歉，您不是队长没有该权利';
		if ($this->isLeaguer($uid)) {
			$type = db::one_one('war_group_member', 'type', "wid='$this->wid' AND uid='$uid'");
			if (!$type) {
				db::update('war_group_member', array('type' => 1), "wid='$this->wid' AND uid='$uid'");
				return '设置成功';
			}
			return '该用户已经是管理员了，无需重复设置';
		}
		return '该用户非本站成员，无法设为管理员';
	}
	public function cancelManager($uid){
		if (!$this->isFounder) return '很抱歉，您不是队长没有该权利';
		if ($this->isLeaguer($uid)) {
			$type = db::one_one('war_group_member', 'type', "wid='$this->wid' AND uid='$uid'");
			if ($type == 1) {
				db::update('war_group_member', array('type' => 0), "wid='$this->wid' AND uid='$uid'");
				return '取消成功';
			}
			return '该用户不是管理员，不能取消';
		}
		return '该用户非本站成员，无法设为管理员';
	}
	public function delMember($uid){
		if (!$this->isManager) return '很抱歉，您不是管理员，无法踢出';
		if ($this->isLeaguer($uid)) {
			$type = db::one_one('war_group_member', 'type', "wid='$this->wid' AND uid='$uid'");
			if ($type == 0) {//此处以后还要判断是否有正在进行中的约战，如果有的话 不能被踢出
				db::delete('war_group_member', "wid='$this->wid' AND uid='$uid'");
				return '踢出成功';
			}
			return '没有权限';
		}
		return '该用户非本站成员，无法踢出';
	}
	public function agreeJoin($uid){
		if (!db::exists('war_group_verify', array('wid' => $this->wid, 'uid' => $uid))) return '该用户没有提交申请，请检查是否已经审核通过';
		db::delete('war_group_verify', "wid='$this->wid' AND uid='$uid'");
		if (!$this->isLeaguer($uid)) $this->_join($uid, 0);
		$m = new member_center($uid);
		$m->sendNotice('战队“'.$this->datas['name'].'”通过加入', '恭喜您，战队“'.$this->datas['name'].'”允许您加入');
		return '操作成功';
	}
	public function rejectJoin($uid){
		if (!db::exists('war_group_verify', array('wid' => $this->wid, 'uid' => $uid))) return '该用户没有提交申请，请检查是否已经审核通过';
		db::delete('war_group_verify', "wid='$this->wid' AND uid='$uid'");
		$m = new member_center($uid);
		$m->sendNotice('战队“'.$this->datas['name'].'”拒绝加入', '很遗憾，战队“'.$this->datas['name'].'”拒绝您加入');
		return '操作成功';
	}
	public static function getAvatarUrl($id, $suffix = '', $type = '_small', $did = 0){
		if (!$suffix) return WEB_URL_S1.cfg::get('web', 'war_def'.$type);
		if ($did > 0) return memory::get('disperse_url_'.$did).'/'.qscms::getArticlePath($id, '/').$type.'.'.$suffix;
		else return qscms::getImgUrl('war').qscms::getArticlePath($id, '/').$type.'.'.$suffix;
	}
}
?>