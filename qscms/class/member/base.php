<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class member_base extends ext_base{
	/**
	 * 随机登录机器人
	 */
	public static function robotLoginRand(){
		$max = db::dataCount('member', "robot='1'");
		$max <= 0 && exit('error');
		$id = rand(1, $max);
		db::query('SET @i = 0;');
		$sql = db::sqlSelect('member', 'id', "robot='1'");
		$sql = "SELECT @i:=@i+1 id,id uid FROM ($sql) t";
		$uid = db::resultFirst("SELECT uid FROM ($sql) t WHERE id='$id'");
		return new member_center($uid);
	}
	public static function memberIdExists($uid){
		return db::exists('member', array('id' => $uid));
	}
	public static function emailExists($email){
		if (form::checkEmail($email)) {
			return db::exists('member', array('email' => $email));
		}
		return 'E-Mail格式错误';
	}
	public static function mobileExists($mobile){
		if (form::checkMobilephone($mobile)) {
			return db::exists('member', array('mobile' => $mobile));
		}
		return '手机号码格式错误';
	}
	public static function nicknameExists($nickname){
		$len = mb_strlen($nickname);
		if ($len >= 4 && $len <= 32) {
			return db::exists('member', array('nickname' => $nickname));
		}
		return '昵称长度为4~32';
	}
	
	public static function message($mobile, $msg){
		$vcode = string::getRandStr(6, 1);
		$time = time();
		
		if (!$msg){
			$msg || $msg = '您的手机号：'.$mobile.'，验证码：'.$vcode.'。请不要把验证码泄露给其他人。如非本人操作，可不用理会！';
		}else{
			$msg = qscms::replaceVars($msg, array('mobile' => $mobile, 'vcode' => $vcode));
		}
		$arr = array(
			'name' => $mobile,
			'type' => 0,
			'vcode' => $vcode,
			'time' => $time,
			'status' => 0
		);
		if ($lastTime = db::one_one('vcode', 'time', "name='$mobile'", 'id DESC')){
			$s = message::sms_time();
			if ($time < $lastTime + $s){
				//return '操作频繁，请稍后尝试';
			}
		}
		if (db::insert('vcode', $arr)){
			if (cfg::get('web', 'sms') == 1){//使用阿里大于
				$rs = message::sendOne1($mobile, $vcode);
			}else{//默认的
				$rs = message::sendOne($mobile, $msg);
			}
			if ($rs === true) return true;
			else return $rs;
		}else return false;
	}
	public static function checkVcode1($mobile, $vcode){
		if (db::exists('vcode', 'id,time', "name='$mobile' AND vcode='$vcode' AND status=0")) return true;
		else return false;
	}
	public static function checkVcode($mobile, $vcode, $isUpdate = true){
		if ($item = db::one('vcode', 'id,time', "name='$mobile' AND vcode='$vcode' AND status=0")){
			$lastTime = $item['time'];
			$time = time();
			//$s = message::sms_time();
			$s = 15 * 60;//默认15分钟
			if ($time > $lastTime + $s) {
				return '手机验证码过期，请重新获取';
			}else {
				if ($isUpdate) {
					db::update('vcode', 'status=1', "id='$item[id]'");
				}
				return true;
			}
		}else{
			return '手机验证码错误';	
		}
	}
	public static function wx($wxid, $id = ''){
		if ($wxid){
			//return $wxid;
			if (db::exists('member', "wxid='$wxid'")) return '该微信已绑定其他商户';
			if (db::exists('member', "id='$id' AND wxid!=''")) return '该商户已绑定其他微信号';
			db::autocommit();
			$fans_info = self::get_fans_info($wxid);
			if (!$fans_info) return '未获取到该微信信息';//判断如果是随意填写的wxid 就直接跳出
			$datas['wxid']   = $wxid;
			$datas['wxname'] = qscms::addslashes($fans_info['wxname']);
			$datas['wximg']  = $fans_info['wximg'];
			if (db::update('member', $datas, "id='$id'")) {
				$mobile = db::one_one('member', 'mobile', "id='$id'");
				db::commit(true);
				return "欢迎关注! 您绑定的手机号为：".$mobile;
			}
			db::rollback(true);
		}
		return '操作失败';
	}
	public static function get_fans_info($wxid){
		$weixin = new  weixin();
		$authorizer_access_token = $weixin->get_token();
		$this_info = string::json_decode(winsock::open("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$authorizer_access_token."&openid=".$wxid."&lang=zh_CN"));
		$this_info || $this_info = string::json_decode(winsock::curl("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$authorizer_access_token."&openid=".$wxid."&lang=zh_CN"));
		$this_nick = $this_info['nickname'];
		//$this_touxiang = self::down_headpic($this_info['headimgurl']);
		$this_touxiang = $this_info['headimgurl'];
		return array("wxname"=>$this_nick,"wximg"=>$this_touxiang);
	}
	private static function down_headpic($pic){
		$saveDir = d('./img/wx/');
		if(!file_exists(d('./img/wx/'))) file::createFolder($saveDir);
		$temname = upload::tempname($saveDir,'gif');
		while(winsock::downloadFull($pic,$temname)){
			return u($temname);break;
		}
	}
	public static function minMoney(){
		static $minMoney;
		if ($minMoney) return $minMoney;
		$minMoney = cfg::getMoney('pay', 'minMoney');	
		if ($minMoney < 1) $minMoney = 1;
		return $minMoney;
	}
	public static function blCash(){
		static $blCash;
		if ($blCash) return $blCash;
		$blCash = cfg::getMoney('pay', 'blCash');	
		if ($blCash < 0) $blCash = 0;
		return $blCash;	
	}
	public static function edit_password($datas){
		$datas = qscms::filterArray($datas, array('mobile', 'password', 'code'), true);
		$rs = form::checkData($datas, array(
			'null' => array('mobile', 'password'),
			'minLength' => array(
				'password' => 6 ,
				'code' => 6
			),
			'maxLength' => array(
				'password' => 16,
				'code' => 6
			)
		), array(
			'password'    => '新密码',
			'mobile'	  => '手机号',
			'code'    => '验证码'
		));
		if ($rs === true){
			$rs = member_base::checkVcode($datas['mobile'], $datas['code']);
			if ($rs !== true) return $rs;
			if ($item = db::one('member', '*', "mobile='$datas[mobile]'")){
				$salt = $item['salt'];
				$pwd = qscms::saltPwd($salt, $datas['password']);
				db::update('member', "password='$pwd'", "id='$item[id]'");
				return true;
			}
		}
		return $rs;
	}
	public static function reg($datas){
		$datas = qscms::filterArray($datas, array('mobile', 'password', /*'code',*/ 'pid'), true);
		$rs = form::checkData($datas, array(
			'null' => array('password', 'mobile'/*, 'code'*/),
			'minLength' => array(
				'password' => 6,
				//'code' => 6
			),
			'maxLength' => array(
				'password' => 16,
				//'code' => 6
			),
			'function' => array(
				'mobile' => 'form::checkMobilephone'
			)
		), array(
			'password'    => '密码',
			'mobile'    => '手机号',
			'code'    => '验证码'
		));
		if ($rs === true){
			//$rs = self::checkVcode($datas['mobile'], $datas['code']);
			//if ($rs !== true) return $rs;
			$salt = qscms::salt();
			$pwd  = qscms::saltPwd($salt, $datas['password']);
			if (db::exists('member', "mobile='$datas[mobile]'"))  return '该手机号已被使用';
			$datas['salt']     = $salt;
			$datas['password'] = $pwd;
			$datas['regTime']  = time::$timestamp;
			$datas['keys']	   = string::getRandStr(40);
			$pid = '';
			if ($datas['pid'] && db::exists('member', "id='$datas[pid]'")){
				$pid = $datas['pid'];
			}
			unset($datas['pid']);
			unset($datas['code']);
			if ($id = treeDB::insert('member', $datas, $pid, true)){
				qscms::setcookie('memberAuth', $id.'|'.$datas['password']);
				return true;
			}else return '未知错误，请联系客服';
		}
		return $rs;
	}
	public static function qrcode($wxid, $returnImg = false){
		$member = new member_center($wxid);
		if (empty($member) || $member->status == false) return false;
		$dir = d(qscms::getCfgPath('/system/imgRoot').'userqrcode/');
		$url = u(qscms::getCfgPath('/system/imgRoot').'userqrcode/', true);
		$qrcodeDir = $dir.$member->m_id.'.jpg';
		$qrcodeUrl = $url.$member->m_id.'.jpg';
		file::createFolder($dir);//没有这个目录就创建
		$auth = false;
		$weObj = new weixin($auth);
		$access_token = $weObj->get_token($auth);
		if (!file_exists($qrcodeDir)){//生成推荐二维码
			$json_arr = array(
				'action_name' => 'QR_LIMIT_SCENE',
				'action_info' => array('scene' => array('scene_id' => $member->m_id))
			);
			$data = json_encode($json_arr);
			//return $data;
			$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
			$res_json = $auth -> curl_grab_page($url, $data);
			$json = json_decode($res_json);
			$ticket = $json->ticket;
			if ($ticket){
				$ticket_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket);
				winsock::downloadFull($ticket_url,$qrcodeDir);
			}
		}
		$media_id = $member->c_media_id;
		$media_endTime = $member->c_media_endTime;
		$time = time::$timestamp;
		if ($member->c_media_id && $member->c_media_endTime > $time/* && false*/){//如果数据库的media_id没有过期就不用上传了
			return $member->c_media_id;
		}else{//上传二维码
			if (file_exists($qrcodeDir)){
				$dfile = cfg::get('web', 'qrcode_logo');//获取水银图片
				if ($dfile){//加水印
					$rs = image::watermark($qrcodeDir, $dfile, 5);
				}
				$data = $qrcodeDir;
				$filedata=array("media"=>"@".$data);
				if(strlen($access_token) >= 64) {
					$url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$access_token.'&type=image';					
					$res_json = $auth -> https_request($url, $filedata);
					$json = string::json_decode($res_json);	
				}
				$media_id = $json['media_id'];
				$media_endTime = $time + (86400 * 1.5);
				db::update('member_count', "media_id='$media_id',media_endTime='$media_endTime'", "uid='$member->m_id'");
				//return "media_id='$media_id',media_endTime='$media_endTime'"."uid='$member->m_id'";
				//return db::error();
				return $media_id;
			}return false;
		}return false;	
	}
	
	
	
	
	public static function wxReg($wxid, $pid = '', $login = false){
		if ($wxid){
			if (db::exists('member', "wxid='$wxid'")) return false;
			db::autocommit();
			$password = mt_rand(100000,999999);
			$salt = qscms::salt();
			$pwd  = qscms::saltPwd($salt, $password);
			$datas['salt'] = $salt;
			$datas['password'] = $pwd;
			$datas['regTime']  = time::$timestamp;
			$datas['wxid'] = $wxid;
			$fans_info =self::get_fans_info($wxid);
			if (!$fans_info) return false;//判断如果是随意填写的wxid 就直接跳出
			$datas['wxname'] = qscms::addslashes($fans_info['wxname']);
			$datas['wximg']  =  $fans_info['wximg'];
			//return $pid.'------';
			if ($id = treeDB::insert('member', $datas, $pid, true)) {
				if ($login) {
					qscms::setcookie('memberAuth', $id.'|'.$pwd);
				}
				$p_str = '';
				if ($pid && $p_member = db::one('member', 'username,wxname,email,mobile', "id='$pid'")){
					$p_str = "恭喜您由 ".($p_member['username'] ? $p_member['username'] : ($p_member['wxname'] ? $p_member['wxname'] : $p_member['mobile']))." 推荐成为".cfg::get('sys', 'webName')."的下级";
				}
				db::commit(true);
				return '欢迎关注!'.($p_str ? $p_str : "").'您的用户ID：'.$id;
			}
			db::rollback(true);
		}
		return false;
	}
	public static function regToForm(){
		if (form::hash()) {
			if (securimage::checkForm()) {
				return self::reg($_POST);
			} else return '验证码错误';
		}
		return false;
	}
	public static function idExists($id){
		return db::exists('member', array('id' => $id));
	}
	public static function getNick($id){
		return db::one_one('member', 'nickname', "id='$id'");
	}
	public static function wxLogin($wxid){
		$member = db::one('member', 'id,salt,password', "wxid='$wxid'");
		if ($member) {
			$timestamp = time::$timestamp;
			$ip = qscms::ipint();
			db::update('member', "lastLoginTime='$timestamp',loginTimes=loginTimes+1,lastLoginIp=$ip", "id='$member[id]'");
			qscms::setcookie('memberAuth', $member['id'].'|'.$member['password']);
			return true;
		}
		return false;
	}
	public static function login($datas){
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
				$member = db::one('member', 'id,salt,password,mobile', "mobile='$datas[username]'");
			} elseif (is_numeric($datas['username'])){
				$member = db::one('member', 'id,salt,password,mobile', "id='$datas[username]'");
			} elseif (form::checkEmail($datas['username'])){
				$member = db::one('member', 'id,salt,password,mobile', "email='$datas[username]'");
			}
			if ($member) {
				if (qscms::saltPwdCheck($member['salt'], $datas['password'], $member['password'])) {
					$timestamp = time::$timestamp;
					$ip = qscms::ipint();
					db::update('member', "lastLoginTime='$timestamp',loginTimes=loginTimes+1,lastLoginIp=$ip", "id='$member[id]'");
					$cookieTime = $datas['cookieTime'];
					$cookieTime < 0 && $cookieTime = 0;
					if ($datas['remember']) $cookieTime = 24 * 30 * 3600;
					qscms::setcookie('memberAuth', $member['id'].'|'.$member['password'], $cookieTime);
					$loginArr = array(
						'uid' => $member['id'],
						'username' => $datas['username'],
						'ip'   => qscms::ipint(),
						'time' => time()
 					);
					db::insert('login_log', $loginArr);
					$todayStart = time::$todayStart;
					$num = db::dataCount('login_send', "addTime>$todayStart");
					if (cfg::get('web', 'login_sms') && $num < cfg::getInt('web', 'login_send_num')){
						db::insert('login_send', array('uid' => $member['id'], 'mobile' => $member['mobile'], 'addTime' => $timestamp));
						if (cfg::get('web', 'sms') == 1){//阿里大于
							message::login_sendOne($member['mobile']);
						}else{
							message::sendOne($member['mobile'], '您的'.qscms::v('_G')->webName.'账户于'.date('d日H时i分').'登录，如非本人操作，请登录官网修改密码。');
						}
						
					}
					return true;
				}
				return '密码错误';
			}
			return '该用户不存在';
		}
		return $rs;
	}
	public static function logout(){
		qscms::unsetcookie('memberAuth');
	}
	public static function memberEdit($datas, $uid){
		$item = db::one('member', 'name,mobile,qq', "id='$uid'");
		if ($item['name']) unset($datas['name']);
		if (!form::checkMobilephone($datas['mobile'])) return '手机号格式不正确';
		if (!form::checkQQ($datas['qq'])) return 'QQ格式不正确';
		db::update('member', $datas, "id='$uid'");
		return true;
	}
	public function bind($datas){
		$datas = qscms::filterArray($datas, array('email', 'password', 'open'), true, '');
		$openArr = $datas['open'];
		unset($datas['open']);
		if ($openArr && is_array($openArr)) {
			foreach ($openArr as $k => $v) {
				$v = qscms::authcode($v, false);
				switch ($k) {
					case 'qq':
						$datas['qq_openid'] = $v;
					break;
				}
			}
		}
		if (!empty($datas['qq_openid'])) {
			$rs = self::login($datas, true);
			if ($rs === false) return '登录失败，请检查您的帐号密码';
			elseif (is_numeric($rs)) {
				db::update('member', array('qq_openid' => $datas['qq_openid']), "id='$rs'");
				$password = db::one_one('member', 'password', "id='$rs'");
				qscms::setcookie('memberAuth', $rs.'|'.$password);
				return true;
			} else return $rs;
		}
		return '参数错误';
	}
	public function bindToForm(){
		if (form::hash()) {
			$rs = self::bind($_POST);
			if ($rs === true) qscms::unsetcookie('qq_openid');
			return $rs;
		}
		return false;
	}
	public static function loginCookie($cookie = ''){
		$cookie || (isset($_COOKIE['memberAuth']) && ($cookie = $_COOKIE['memberAuth']));
		$member = false;
		if ($cookie) {
			list($uid, $password) = explode('|', $cookie);
			$member = new member_center($uid, $password);
			if (!$member->status) {
				$member = false;
			} else {
				//处理每日领取积分
				//$rs = $member->addCredit1('login', 0, '每日登录奖励');
				//if ($rs) qscms::v('_G')->_showmessage = '每日登录：'.$rs;
			}
		}
		@qscms::v('_G')->member = $member;
		@qscms::v('_G')->member->status = $member ? true : false;
		@qscms::v('_G')->memberLogin = $member ? true : false;
	}
	public static function loginToForm(){
		if (form::hash()) {
			$rs = self::login($_POST);
			if ($rs === true) qscms::unsetcookie('qq_openid');
			return $rs;
		}
		return false;
	}
	public static function loginQQ($openid){
		$member = db::one('member', 'id,salt,password', "qq_openid='$openid'");
		if ($member) {
			qscms::setcookie('memberAuth', $member['id'].'|'.$member['password']);
			return $member['id'];
		}
		return false;
	}
}
?>