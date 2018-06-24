<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class member_agent extends ext_base{
	//private $vars, $readKeys, $writeKeys;
	const FL_TYPE = 1;
	const FL_TYPE1 = 5;
	public function __construct($uid, $password = '', $isAdmin = false){
		//$this->vars = new vars();
		//$this->readKeys = array('status', 'info');
		//$this->writeKeys = array('info');
		$this->isAdmin = $isAdmin;
		$this->safePwdCheck = false;
		$where = '';
		if (is_numeric($uid)) $where = "id='$uid'";
		elseif (form::checkEmail($uid)) $where = "email='$uid'";
		elseif (form::checkMobilephone($uid)) $where = "mobile='$uid'";
		elseif ($uid) $where = "wxid='$uid'";
		if ($where) $member = db::one('member', '*', $where.($password ? " AND password='$password'" : ''));
		else $member = false;
		$this->sql = db::sqlSelect('member', '*', $where.($password ? " AND password='$password'" : ''));
		if ($member) {
			$member['gender'] = $member['sex'] == 1 ? '男' : ($member['sex'] == 2 ? '女' : '保密');
			
			foreach ($member as $k => $v) {
				$k = 'm_'.$k;
				$this->$k = $v;
			}
			$this->uid = $this->m_id;
			$member_count = db::one('member_count', '*', "uid='$this->uid'");
			if ($member_count){
				if ($member_count['province'] && $member_count['city'] && $member_count['county']){
					$member_count['seat'] = qscms::area_seat($member_count['province'], $member_count['city'], $member_count['county']);
				}else $member_count['seat'] = '';
				foreach ($member_count as $k => $v) {
					$k = 'c_'.$k;
					$this->$k = $v;
				}
			}
			$this->status = true;
		} else {
			$this->status = false;
		}
	}
	public static function loginCookie($cookie = ''){
		$cookie || (isset($_COOKIE['agentAuth']) && ($cookie = $_COOKIE['agentAuth']));
		$agent = false;
		if ($cookie) {
			list($uid, $password) = explode('|', $cookie);
			$agent = new member_agent($uid, $password);
			if (!$agent->status || $agent->m_agent != 1) {
				qscms::unsetcookie('agentAuth');
				$agent = false;
			}
		}
		qscms::v('_G')->agent = $agent;
	}
	public static function login($datas){
		//if (!vcode::check()) return '验证码错误';
		$datas = qscms::filterArray($datas, array('username', 'remember', 'password', 'cookieTime', 'vcode'), true, '');
		$datas['cookieTime'] = intval($datas['cookieTime']);
		$rs = form::checkData($datas, array(
			'null' => array('password', 'username'),
			'minLength' => array(
				'password' => 6
			),
			'maxLength' => array(
				'password' => 16
			)
		), array(
			'username'    => '用户帐号',
			'password' => '用户登录密码'
		));
		if ($rs === true) {
			
			if (form::checkMobilephone($datas['username'])){
				$member = db::one('member', 'id,salt,password,agent', "mobile='$datas[username]'");
			} elseif (is_numeric($datas['username'])){
				$member = db::one('member', 'id,salt,password,agent', "id='$datas[username]'");
			} elseif (form::checkEmail($datas['username'])){
				$member = db::one('member', 'id,salt,password,agent', "email='$datas[username]'");
			}
			
			if (!empty($member)) {
				if (qscms::saltPwdCheck($member['salt'], $datas['password'], $member['password'])) {
					if (!$member['agent']) return '该商户不是代理';
					$timestamp = time::$timestamp;
					$ip = qscms::ipint();
					db::update('member', "lastLoginTime='$timestamp',loginTimes=loginTimes+1,lastLoginIp=$ip", "id='$member[id]' AND agent=1");
					$cookieTime = $datas['cookieTime'];
					$cookieTime < 0 && $cookieTime = 0;
					if ($datas['remember']) $cookieTime = 24 * 30 * 3600;
					qscms::setcookie('agentAuth', $member['id'].'|'.$member['password'], $cookieTime);
					return true;
				}
				return '密码错误';
			}
			return '该用户不存在';
		}
		return $rs;
	}
}
?>