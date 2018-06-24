<?php
/**
 * @author    溺水的狗 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class web_user extends ext_base{
	public function __construct($uid = false, $password = false){
		if ($uid) {
			if ($item = db::one('web_user', '*', "id='$uid'".($password ? " AND password='$password'" : ''))) {
				$this->isLogin = true;
				foreach ($item as $k => $v) {
					$this->{'u_'.$k} = $v;
				}
				$this->loadCfg();
			} else {
				$this->isLogin = false;
			}
		} else $this->isLogin = false;
	}
	
	function loadCfg(){
		if ($item = db::one('web_cfg', '*', "wid='$this->u_id'")) {
			foreach ($item as $k => $v) {
				$this->{'c_'.$k} = $v;
			}
		}
	}
	
	/**
	 * 创建用户
	 */
	public function create($datas){
		$datas = form::get4($datas, array('username', 'email', 'password'));
		$rs = form::checkData($datas, array(
			'null' => array('username', 'email', 'password'),
			'preg' => array(
				'username' => '/^[a-z][a-z0-9_]{5,16}$/i',
				'password' => '/.{6,16}/'
			),
			'function' => array(
				'email' => 'form::checkEmail'
			),
			'dbFind' => array(
				'username' => 'web_user',
				'email'    => 'web_user'
			)
		), array('username' => '用户名', 'email' => '邮箱号', 'password' => '密码'));
		if ($rs === true) {
			$datas['salt'] = qscms::salt();
			$datas['password'] = qscms::saltPwd($datas['salt'], $datas['password']);
			$datas['regTime'] = time();
			$datas['regIp'] = qscms::v('_G')->ipint;
			if ($id = db::insert('web_user', $datas, true)) {
				return $id;
			}
			return '注册失败，请重试';
		}
		return $rs;
	}
	
	/**
	 * 信息修改
	 */
	public function edit($datas){
		$datas = form::get4($datas, array('password', 'rpassword'));
		if ($datas['password'] != '') {
			if (!preg_match('/^.{6,16}$/', $datas['password'])) return '密码长度必须是6~16位';
			if ($datas['password'] != $datas['rpassword']) return '两次密码输入不一致';
			$arr = array();
			$arr['salt'] = qscms::salt();
			$arr['password'] = qscms::saltPwd($arr['salt'], $datas['password']);
			db::update('web_user', $arr, "id='$this->u_id'");
		}
		return true;
	}
	
	/**
	 * IFRAME 图片上传
	 */
	public function ifUpload($datas){
		$datas = form::get4($datas, array('upName'));
		$upName = $datas['upName'];
		$u = new upload();
		$rs = $u->toupload($upName, 'image');
		if ($rs['count'] == 1) {
			return $rs['info'][$upName]['db_id'];
		}
		return '上传失败';
	}
	
	/**
	 * 设置分站配置
	 */
	function setCfg($datas){
		$var = qscms::v('_G');
		if ($var->menuAjax) {//异步
			$datas = form::get4($datas, array('name', 'logo'), true);
			if (is_numeric($datas['logo'])) {//新上传的 处理图片ID
				$u = new upload();
				$saveDir = qscms::getImgDir('webLogo');
				file::createFolder($saveDir);
				$rs = $u->move2($datas['logo'], $saveDir, true);
				if ($rs) {
					$datas['logo'] = $rs['basename'];
				}
			} else unset($datas['logo']);
			db::update('web_cfg', $datas, "wid='$this->u_id'");
			return true;
		} else {
			$datas = form::get4($datas, array('name'), true);
			$saveDir = qscms::getImgDir('webLogo');
			$rs = upload::uploadImage('logo', $saveDir);
			$datas['logo'] = $rs;
			db::update('web_cfg', $datas, "wid='$this->u_id'");
			return true;
		}
	}
	
	public static function login($datas){
		$datas = form::get4($datas, array('username', 'password', array('remember', '01')));
		if (form::checkEmail($datas['username'])) {
			$user = db::one('web_user', '*', "email='$datas[username]'");
		} else {
			$user = db::one('web_user', '*', "username='$datas[username]'");
		}
		if ($user) {
			if (qscms::saltPwdCheck($user['salt'], $datas['password'], $user['password'])) {
				if (self::loginUid($user['id']) === true) {
					if ($datas['remember']) {
						qscms::setcookie('webUserLoginName', $datas['username'], 30 * 86400);
					}
					return true;
				} else return '登录失败';
			}
		} else return '登录失败';
	}
	public static function checkLogin(){
		$var = qscms::v('_G');
		if (!$var->webUserLogin) {
			$wUser = new self();
			$rs = $wUser->createForm_register();
			if ($rs !== false) {
				if (is_numeric($rs)) {//返回的用户ID
					self::loginUid($rs);
					qscms::refresh();
				} else qscms::showMessage($rs);
			}
			$rs = self::formCall('loginForm_login');
			if ($rs !== false) {
				if ($rs === true) qscms::refresh();
				else qscms::showMessage($rs);
			}
			include(template::load('login'));
			exit;
		}
	}
	public static function loginUid($uid){
		$var = qscms::v('_G');
		$user = new self($uid);
		$time = time();
		$ip = $var->ipint;
		if ($user->isLogin) {
			qscms::setcookie('webUserAuth', $user->u_id.'|'.$user->u_password, 0);
			db::update('web_user', "lastLoginTime='$time',lastLoginIp='$ip',loginTimes=loginTimes+1", "id='$user->u_id'");
			return true;
		}
		return '登录失败';
	}
	public static function loginCookie($cookie = ''){
		$cookie || (isset($_COOKIE['webUserAuth']) && ($cookie = $_COOKIE['webUserAuth']));
		$member = false;
		$user = false;
		if ($cookie) {
			list($uid, $password) = explode('|', $cookie);
			$user = new self($uid, $password);
			if (!$user->isLogin) {
				$user = false;
			} else {
				//处理每日领取积分
				//$rs = $member->addCredit1('login', 0, '每日登录奖励');
				//if ($rs) qscms::v('_G')->_showmessage = '每日登录：'.$rs;
			}
		}
		qscms::v('_G')->webUser = $user;
		qscms::v('_G')->webUserLogin = $user ? true : false;
	}
	public static function logout(){
		qscms::unsetcookie('webUserAuth');
		qscms::gotoUrl('/webAdmin');
	}
}
?>