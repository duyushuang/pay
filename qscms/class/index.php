<?php
class member extends ext_base{
	public function __construct($id = false){
		$var = qscms::v('_G');
		$this->var = $var;
		$this->lan = $var->lan;
		$this->id = 0;
		$this->isCheck = $this->isCheck();
		$this->isLogin = $this->loginCookie();
		$this->settings = qscms::getConfig('settings');
		$this->p_on = false;//上级是否存在
		if ($this->isLogin || $id) {
			if ($id){
				$this->id = $id;
			}
			$this->load();
		}
	}
	function isCheck(){//是否已经验证了首页验证码
		if (isset($_COOKIE['isCheck']) && $_COOKIE['isCheck'] == 'yes') return true;
		return false;
	}
	function checkVcode(){
		
	}
	function checkMobileVcode(){
		$hash = qscms::v('_G')->sys_hash;
		$mobile = !empty($_POST['mobile']) ? $_POST['mobile'] : false;
		$vcode = !empty($_POST['phoneVcode']) ? $_POST['phoneVcode'] : false;
		if ($mobile && $vcode) {
			if ($id = db::one_one('log_vcode', 'id', "hash='$hash' AND mobile='$mobile' AND vcode='$vcode' AND status='0'")) return $id;
		}
		return false;
	}
	function reg($datas){
		$isReg = cfg::get('web', 'isReg');
		if (!$isReg){
			return '不允许注册';
		}
		$var = qscms::v('_G');
		$start = $var->todayStart;
		$end   = $var->todayEnd;
		$everyDayRegCount = cfg::getInt('web', 'everyDayRegCount');//每日注册数量限制
		if ($everyDayRegCount > 0) {//大于0 代表限制注册数量
			$closeRegInfo = cfg::get('web', 'closeRegInfo1');
			$closeRegInfo || $closeRegInfo = '每天只能注册'.$everyDayRegCount.'个会员';
			if (db::dataCount('member', "regTime>='$start' AND regTime<='$end'") >= $everyDayRegCount) return $closeRegInfo;
			$notTime = $start + 9 * 3600;
			if (time() < $notTime) return '今日注册暂未开放，请耐心等待。';
		}
		if (!vcode3::check()) return '验证码错误';
		$datas = form::get4($datas, array(
			'name', 'email', 'mobile', 'password', 'safePassword', 'invite', 'parent_email', 'parent_mobile', 'from', 'from_content'
		));
		$datas = qscms::trim($datas);
		if (form::checkEmail($datas['email'])) {
			if (db::exists('member', "email='{$datas['email']}'")) return '邮箱已存在';
			if (db::exists('member', "mobile='{$datas['mobile']}'")) return '手机号已存在';
			if ($datas['password'] == $datas['safePassword']) return '安全码不能和密码相同';
			
			$vcodeId = $this->checkMobileVcode();
			if ($vcodeId === false) return '手机验证码错误';
			
			
			
			$fromOther = $var->lan->lan == 'cn' ? '另一个' : 'others';
			if ($datas['from'] == $fromOther && $datas['from_content'] != '') $datas['from'] = $datas['from_content'];
			$datas['invite'] = $this->getUid($datas['invite']);
			$puid = false;
			if (!$puid && $datas['parent_email']) {
				$puid = $this->getUid($datas['parent_email']);
			}
			if (!$puid && $datas['parent_mobile']) {
				$puid = $this->getUid($datas['parent_email'], 'mobile');
			}
			
			$datas['ge'] = 1;
			$datas['ges'] = 1;
			$datas['tz'] = 26;
			$datas['il'] = 315;
			$datas['ic'] = 156;
			$datas['code'] = 35;
			$puid || $puid = 0;
			$datas['tuid'] = $datas['invite'] ? $datas['invite'] : 0;
			$datas['tuid'] = $puid;//邀请人和领导人一致
			$from_content = $datas['from'];
			unset($datas['invite'], $datas['parent_email'], $datas['parent_mobile'], $datas['from'], $datas['from_content']);//, 
			$datas['regTime'] = time();
			$time = time();
			$datas['regIp']   = $var->ipint;
			$ip = $var->ipint;
			$salt = qscms::salt();
			$datas['password'] = qscms::saltPwd($salt, $datas['password']);
			$datas['safePassword'] = qscms::saltPwd($salt, $datas['safePassword']);
			$datas['salt']     = $salt;
			if ($datas['tuid'] && $puid) {
				$inviteEqualRecommend = cfg::getBoolean('web', 'inviteEqualRecommend');
				if ($inviteEqualRecommend && $datas['tuid'] != $puid) return '推荐人必须和领导人一致';
			}
			
			if (cfg::get('sys', 'is_parent')){
				if (!$puid) return '必须填写你的领导人';
				if (!cfg::get('sys', 'is_use')) $datas['is_use'] = 1;
			}else $datas['is_use'] = 1;
			if ($puid && db::exists('member', "id='$puid' AND is_use=0")) return '你的领导人还未审核通过';
			if (db::exists('member', "email='{$datas['email']}'")) return '邮箱已存在';
			if (db::exists('member', "mobile='{$datas['mobile']}'")) return '手机号已存在';
			//$puid = 0;//强制上级为0
			if ($uid = treeDB::insert('member', $datas, $puid)) {
				db::update('log_vcode', "uid='$uid',status='1'", "id='$vcodeId'");//
				if ($puid) {
					db::update('member', "firstTotal=firstTotal+1,activeTime='$time'", "id='$puid'");//增加上级活跃时间和直接推荐人数
					
					/**
					 * 推荐人数烧伤处理
					 */
					if (!cfg::getBoolean('rewordSS', 'close')) {
						$triggerTime = cfg::getInt('rewordSS', 'firstTriggerDay') * 86400;//触发间隔时间
						$time = time();
						$leftTime = $time - $triggerTime;
						$childMoreThan = cfg::getInt('rewordSS', 'childMoreThan');//下级超过上级多少倍
						if ($childMoreThan > 0) {
							$parent = db::one('member', 'l,r,firstTotal', "id='$puid'");
							qscms::setType($parent, 'int');
							$v = floor($parent['firstTotal'] / $childMoreThan * 10) / 10;
							db::update('member', "rewordWither=rewordWither+1,rewordWitherTime='$time'", "l<$parent[l] AND r>$parent[r] AND firstTotal<$v AND rewordWitherTime<$leftTime");//更新烧伤次数
							
						}
					}
				}
				db::insert('member_from', array('uid' => $uid, 'content' => $from_content));
				
				/**
				 * IP重复检测
				 */
				$ipTotal = db::dataCount('member', "regIp='$ip' OR lastLoginIp='$ip'");
				if ($ipTotal > 1) {
					db::insert('cheat', array(
						'type'   => 0,//IP
						'uid'    => $uid,
						'val'    => $ip,
						'remark' => '注册IP重复',
						'count'  => $ipTotal,
						'time'   => time()
					));
				}
				
				/**
				 * 手机重复检测
				 */
				$mobileTotal = db::dataCount('member', "mobile='$datas[mobile]'");
				if ($mobileTotal > 1) {
					db::insert('cheat', array(
						'type'   => 1,//手机
						'uid'    => $uid,
						'val'    => $datas['mobile'],
						'remark' => '手机号重复',
						'count'  => $mobileTotal,
						'time'   => time()
					));
				}
				return true;
			}
			return '注册失败，请重试';
		}
		return '邮箱格式错误';
	}
	function edit($datas) {
		if (!$this->isLogin) return $this->lan->get('登录超时');
		$arr = array();
		$keys = array('temail', 'name', 'xin', false, false, false, false, 'quyu', 'city', false, 'birthday', 'skype', 'yahu', 'webUrl', 'facebook', 'google', 'twitter', 'info', 'contact_info', 'tz' => 'tz', 'il' => 'il', 'ic' => 'ic', 'ge' => 'ge', 'ges' => 'ges', 'asn' => 'asn', 'ase' => 'ase', 'hmm' => 'hmm', 'v' => 'v');
		$arr = array('ge' => 0, 'ges' => 0, 'asn' => 0, 'ase' => 0, 'hmm' => 0);
		foreach($datas as $k => $v) {
			if (/*is_numeric($k) && */isset($keys[$k]) && $keys[$k] && $v != 'undefined') {
				if (!empty($arr[$k])) $v = 1;
				$arr[$keys[$k]] = $v;
			}
		}
		if (!empty($arr['v'])) $arr['v'] = $arr['v'] ? 1 : 0;
		if ($this->p_on) unset($arr['v']);
		if (!empty($arr['temail'])) {
			$tuid = $this->getUid($arr['temail']);
			if (!$tuid) return 1005;//不存在 其实显示的是已存在
			unset($arr['temail']);
			$arr['tuid'] = $tuid;
		}
		if (isset($arr['quyu'])) $arr['quyu'] = intval($arr['quyu']);
		
		if (isset($arr['birthday'])) {
			
			if (preg_match('/^(\d{1,2}\/(\d{1,2})\/(\d{4}))$/', $arr['birthday'], $ms)) {
				$arr['birthday'] = strtotime($arr['birthday']);
				//$arr['birthday'] = time::getGeneralTimestamp($ms[3].'-'.$ms[1].'-'.$ms[2]);
			} elseif (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $arr['birthday'])) {
				//$arr['birthday'] = time::getGeneralTimestamp($arr['birthday']);
				$arr['birthday'] = strtotime($arr['birthday']);
			} else return 1040;
		}
		if ($arr) {
			if (db::update('member', $arr, "id='$this->id'")) return true;
			return $this->lan->get('修改失败，请重试');
		}
		return $this->lan->get('未知错误');
	}
	function editValue($datas){
		if (!$this->isLogin) return $this->lan->get('登录超时');
		if (!empty($datas['t'])) {
			switch ($datas['t']) {
				case 'mobile':
					$datas = form::get4($datas, array('v', array('im', 'int')));
					$arr = array(
						'code' => $datas['im'],
						'mobile' => $datas['v']
					);
					if (db::exists('member', "code='$arr[code]' AND mobile='$arr[mobile]' AND id<>'$this->id'")) return 1004;
					if (db::update('member', $arr, "id='$this->id'")) {
						$this->_code = $arr['code'];
						$this->_mobile = $arr['mobile'];
						$this->formatData();
						return true;
					}
					return $this->lan->get('修改失败，请重试');
				break;
			}
		}
		return $this->lan->get('非法操作');
	}
	function getUid($data, $key = 'email'){
		return db::one_one('member', 'id', "$key='$data'");
	}
	public static function loginSys($uid){
		if ($line = db::one('member', 'id,salt,password,lastLoginIp', "id='$uid'")){
			qscms::setcookie('memberAuth', $line['id'].'|'.$line['password'], 1200);
		}
	}
	function login($datas){
		$datas = form::get4($datas, array('email', 'password', 'lan'));
		$var = qscms::v('_G');
		if ($datas['lan']) $var->lan->set($datas['lan']);
		$rs = false;
		if ($datas['email'] && $datas['password']) {
			$where = false;
			if (is_numeric($datas['email'])) $where = "mobile='{$datas['email']}'";
			elseif (form::checkEmail($datas['email'])) $where = "email='{$datas['email']}'";
			if ($where && $line = db::one('member', 'id,salt,password,lastLoginIp,isMove', $where)) {
				if (qscms::saltPwdCheck($line['salt'], $datas['password'], $line['password'])) {
					//if (!$line['isMove']) return '用户审核中，请稍后登录';
					$time = time();
					$ip   = $var->ipint;
					$lastLoginIp = $line['lastLoginIp'];
					db::update('member', "lastLoginTime='$time',lastLoginIp='$ip',loginTimes=loginTimes+1", "id='$line[id]'");
					qscms::setcookie('memberAuth', $line['id'].'|'.$line['password'], 1200);
					$rs = true;
					$this->id = $line['id'];
					$this->isLogin = true;
					
					/**
					 * IP重复检测
					 */
					$ipTotal = db::dataCount('member', "regIp='$ip' OR lastLoginIp='$ip'");
					if ($ipTotal > 1) {
						db::insert('cheat', array(
							'type'   => 0,//IP
							'uid'    => $line['id'],
							'val'    => $ip,
							'remark' => '登录IP重复',
							'count'  => $ipTotal,
							'time'   => time()
						));
					}
					
					/**
					 * 异地登录检测
					 */
					if ($lastLoginIp != $ip) {
						$lastInfo = ip::info(qscms::intip($lastLoginIp));
						$info     = ip::info(qscms::intip($ip));
						if ($lastInfo['coutry'] != $info['coutry']) {
							db::insert('cheat', array(
								'type'   => 3,//IP
								'uid'    => $line['id'],
								'val'    => $ip,
								'remark' => '异地登录',
								'count'  => 1,
								'time'   => time()
							));
						}
					}
					
				} else $rs = '密码错误';
			} else $rs = '帐号不存在';
		} else $rs = '邮箱或密码不能为空';
		$rs !== true && $rs = $var->lan->get($rs);
		return $rs;
	}
	function loginCookie(){
		if (isset($_COOKIE['memberAuth'])) {
			list($uid, $pwd) = explode('|', $_COOKIE['memberAuth']);
			if (db::exists('member', array('id' => $uid, 'password' => $pwd))) {
				$this->id = $uid;
				qscms::setcookie('memberAuth', $_COOKIE['memberAuth'], 1200);
				return true;
			}
		}
		return false;
	}
	function load(){
		if ($line = db::one('member', '*', "id='$this->id'")) {
			foreach ($line as $k => $v) {
				$this->{'_'.$k} = $v;
			}
			$level = cfg::getInt('m', 'level');
			$levelAll = cfg::getInt('m', 'levelAll');
			
			$this->_ifManager = 0;
			/*
			if ($this->_isManager == 0 && !db::exists('manager', "uid='{$this->_id}'") && treeDB::childsCount('member', $this->_id) > $level || treeDB::childsCount_all('member', $this->_id) > $levelAll){//是否具备申请成为领导人的限制
				$this->_ifManager = 1;
			}
			*/
			$this->_reg_url = WEB_URL.'/new/cn/registration/&i='.urlencode($this->_email);
			$this->formatData();
		}
		if (isset($_COOKIE['memberAuthParent'])) {
			list($uid, $pwd) = explode('|', $_COOKIE['memberAuthParent']);
			$parent = categories::cate_parent('member', $this->_id, 'id');
			if ($parent['id'] == $uid && $this->_v){//上级是这个cookieUID 和 用户设置了让上级管理
				if (db::exists('member', array('id' => $uid, 'password' => $pwd, 'status' => 0))) {
					$parent = db::one('member', '*', "id='$uid'");
					$this->p_on = true;
					foreach ($parent as $k => $v){
						$this->{'p_'.$k} = $v;
					}
				}
			}else{
				qscms::unsetcookie('memberAuthParent', 'isCheck');	
			}
		}
	}
	function formatData(){
		$arr = $this->settings['countries-mobiles'];
		foreach ($arr as $v) {
			if ($v[0] == $this->_code) {
				$this->mobile = '+'.$v[2].$this->_mobile;
				break;
			}
		}
	}
	function logout(){
		qscms::unsetcookie('memberAuth', 'isCheck');
		$this->isLogin = false;
	}
	function getUrl($action = false){
		//return qscms::getUrl('/'.$this->id.'/'.($action ? $action.'.html' : ''));
		//return u('./'.$this->id.'/'.($action ? $action.'.html' : ''));
		$num = mt_rand(123472713, 999999999);
		return u('./'.$num.'/'.($action ? $action.'.html' : ''));
	}
	function config(){
		$params = array(
			'self_change_mobile_no_sms' => array(
				'all' => 100
			),
			'self_set_mobile_login' => array(
				'all' => 100
			),	
			'trust' => array(
				'all' => 100
			),	
			'members' => array(
				'all' => 100
			),		
			'members_secondline' => array(
				'view' => 1,
				'add' => 2
			),	
			'tickets' => array(
				'add' => 2,
				'view' => 1
			),	
			'login' => array(
				'all' => 100
			),		
			'request_in' => array(
				'view' => 1,
				'edit' => 3,
				'add' => 2
			),
			'storno' => array(
				'edit' => 3
			),
			'news' => array(
				'view' => 1
			),		
			'mavro' => array(
				'view' => 1
			),		
			'referers' => array(
				'view' => 1
			),		
			'request_out' => array(
				'view' => 1,
				'add' => 2
			),	
			'preview_registrations' => array(
				'all' => 100
			),		
			'chats' => array(
				'view' => 1,
				'add' => 2
			),	
			'messages_happy' => array(
				'add' => 2,
				'view' => 1,
				'edit' => 3
			),
			'private_accounts' => array(
				'all' => 100
			),		
			'self_go_to_es' => array(
				'all' => 100
			),		
			'id' => $this->_id,
			'id_position' => 20,
			'id_country' => 35
		);
		$settings = array(
			'desktop' => array(
				'current_arrg_rows' => 50,
				'show_expired_arrangements' => 1,
				'show_archived_orders' => 1,
				'current_orders_rows' => 10
			),
			'refs' => array(
				'per_page' => 100
			),
			'members' => array(
				'per_page' => 50
			),
			/*
			 * 以下参数控制用户资料页每个栏目的展开
			 * 麻痹 1 是展开还是 0 是展开
			 * 艹 1 是关闭 0 是展开
			 * 太神奇了，勿动
			 * 草泥马奔腾而过
			 * 作死
			 * 对了 可以直接删掉数组
			 * 里面的3 代表第四组，从0 开始
			 * 艹艹艹艹艹
			 * 总共耗时 1小时3分钟
			 */
			'profile' => array(
				3 => 0
			)
		);
		$arr = array(
			'var' => array(
				'new_ref' => 1,
				'REFERAL_LINK' => (WEB_URL.'/?i='.$this->_email),// 'http://china-mmm.net/?i=tuoqiao834269@163.com',
				'id_structure' => 14,
				'desktop_theme' => 0,
				'allow_to_copy_chat' => 0,
				'user_cid' => $this->_id,
				'_cs' => 1,
				'user_lang' => 'chi',
				'is_dec' => true,
				'cache_id_order_happy_message' => 0,
				'is_block_form' => ''
			),
			'data' => array(),
			'json' => array(
			'user_perms' => string::json_encode($params),
			'user_settings' => string::json_encode($settings)
			)
		);
		return string::json_encode($arr);
	}
	function getNotice($isNew = true){
		$arr = array();
		if ($this->isLogin) {
			$arr['status'] = true;
			$arr['total'] = 0;
		} else {
			$arr['status'] = false;
		}
		return string::json_encode($arr);
	}
	function getInfoList($pagesize, $page, $json = false){
		$list = array(
			8 => 0,
			'orders' => array(
				'rows' => array(
					array(
						'id' => '32266096938',
						'idu' => $this->_id,
						'fio' => '酒 九18969924519',
						'amount' => '100,
						00',
						'currency' => 'CNY',
						'date' => '2016-03-17',
						'date_exptime' => '2016-03-24 00:13',
						's' => '100',
						'status' => 'STATUS_ORD_NEW',
						'd_status' => '100',
						'type' => 'in',
						'can_remove' => true,'can_release' => 0,
						'r' => '100,00',
						'is_avto' => false,'is_show_mh' => false,
						'for_admin_comment_in_order_list' => '',
						'for_user_comment' => ''
					),
					array(
						'id' => '32981967680',
						'idu' => $this->_id,
						'fio' => '酒 九18969924519',
						'amount' => '236 150,00',
						'currency' => 'CNY',
						'date' => '2016-03-01',
						'date_exptime' => '2016-03-08 00:07',
						's' => '430',
						'status' => 'STATUS_ORD_DONE_MH_GOOD',
						'd_status' => '430',
						'type' => 'out',
						'can_remove' => 0,
						'can_release' => 0,
						'r' => '0,
						00',
						'is_avto' => false,
						'is_show_mh' => false,
						'for_admin_comment_in_order_list' => '',
						'for_user_comment' => ''
					),
					array(
						'id' => '37407322745',
						'idu' => $this->_id,
						'fio' => '酒 九18969924519',
						'amount' => '0,
						00',
						'currency' => 'CNY',
						'date' => '2016-02-28',
						'date_exptime' => '2016-03-06 00:31',
						's' => '3',
						'status' => 'STATUS_DELETED',
						'd_status' => '3',
						'type' => 'in',
						'can_remove' => 0,
						'can_release' => 0,
						'r' => '0,
						00',
						'is_avto' => false,
						'is_show_mh' => false,
						'for_admin_comment_in_order_list' => '',
						'for_user_comment' => ''
					)
				),
				'total' => '73'
			),
			'arrangements' => array(
				'rows' => array(
					array(
						'id' => '35831969264',
						'ident_agent' => 's',
						'auser' => $this->_id,
						'uin' => $this->_id,
						'uout' => '30004190598',
						'uin_uout' => '0',
						'folder' => '',
						'ustatus' => 'in',
						'amount' => '59 000 CNY',
						'btcv' => 0,
						'btcvperc' => 0,
						'status_name' => 'arrgst_900_s',
						'status' => '900',
						'status_img' => su('./images/block.png'),
						'red' => 0,
						'user_in' => 'You',
						'user_out' => ' 土豆',
						'bank_in' => 'Other bank',
						'bank_out' => 'Other bank',
						'can_print' => '1',
						'date' => '2016-03-04',
						'files' => array(
							array(
								'file' => 'https://f.mmm-office.com/201603/04/15/10938213761u_Screenshot_2016-03-04-23-51-29.jpeg',
								'icon' => 'jpg'
							)
						),
						'variants' => array(),
						'chat_show' => 2,
						'order_in' => '37407322745',
						'order_out' => '',
						'is_transfer' => false,
						'is_control' => '',
						'is_control_info' => 0,
						'chat_count_messages' => 0,
						'chat_unread_messages' => 0
					),
					array(
						'id' => '37819785285',
						'ident_agent' => 'd',
						'auser' => $this->_id,
						'uin' => '29377076336',
						'uout' => $this->_id,
						'uin_uout' => '0',
						'folder' => '',
						'ustatus' => 'out',
						'amount' => '19 910 CNY',
						'btcv' => 0,
						'btcvperc' => 0,
						'status_name' => 'arrgst_800_d',
						'status' => '800',
						'status_img' => su('./images/ok.png'),
						'red' => 0,
						'user_in' => ' 向往',
						'user_out' => 'You',
						'bank_in' => 'Other bank',
						'bank_out' => 'Other bank',
						'can_print' => '0',
						'date' => '2016-03-01',
						'files' => array(),'variants' => array(),'chat_show' => 2,
						'order_in' => '28726532260',
						'order_out' => '32981967680',
						'is_transfer' => false,'is_control' => '',
						'is_control_info' => 0,
						'chat_count_messages' => 0,
						'chat_unread_messages' => 0
					),
					array(
						'id' => '39647615983',
						'ident_agent' => 'd',
						'auser' => $this->_id,
						'uin' => '11253024387',
						'uout' => $this->_id,
						'uin_uout' => '0',
						'folder' => '',
						'ustatus' => 'out',
						'amount' => '50 000 CNY',
						'btcv' => 0,
						'btcvperc' => 0,
						'status_name' => 'arrgst_800_d',
						'status' => '800',
						'status_img' => su('./images/ok.png'),
						'red' => 0,
						'user_in' => ' 名1977',
						'user_out' => 'You',
						'bank_in' => 'Other bank',
						'bank_out' => 'Other bank',
						'can_print' => '0',
						'date' => '2016-03-01',
						'files' => array(
						array(
						'file' => 'https://f.mmm-office.com/201603/01/05/11253024387u_mmexport1456811867595.jpg',
						'icon' => 'jpg')),'variants' => array(),'chat_show' => 2,
						'order_in' => '21295460060',
						'order_out' => '32981967680',
						'is_transfer' => false,'is_control' => '',
						'is_control_info' => 0,
						'chat_count_messages' => '1',
						'chat_unread_messages' => 0
					),
					array(
						'id' => '24242677059',
						'ident_agent' => 's',
						'auser' => $this->_id,
						'uin' => $this->_id,
						'uout' => '12354287624',
						'uin_uout' => '0',
						'folder' => '',
						'ustatus' => 'in',
						'amount' => '19 430 CNY',
						'btcv' => 0,
						'btcvperc' => 0,
						'status_name' => 'arrgst_800_s',
						'status' => '800',
						'status_img' => su('./images/ok.png'),
						'red' => 0,
						'user_in' => 'You',
						'user_out' => ' 赵聪',
						'bank_in' => 'Other bank',
						'bank_out' => 'Other bank',
						'can_print' => '1',
						'date' => '2016-02-25',
						'files' => array(
						array(
						'file' => 'https://f.mmm-office.com/201602/26/12/10938213761u_mmexport1456482534950.jpg',
						'icon' => 'jpg')),'variants' => array(),'chat_show' => 2,
						'order_in' => '22546566388',
						'order_out' => '20598599668',
						'is_transfer' => false,'is_control' => '',
						'is_control_info' => 0,
						'chat_count_messages' => '1',
						'chat_unread_messages' => 1
					),
					array(
						'id' => '20528675414',
						'ident_agent' => 's',
						'auser' => $this->_id,
						'uin' => $this->_id,
						'uout' => '29556399523',
						'uin_uout' => '0',
						'folder' => '',
						'ustatus' => 'in',
						'amount' => '39 950 CNY',
						'btcv' => 0,
						'btcvperc' => 0,
						'status_name' => 'arrgst_800_s',
						'status' => '800',
						'status_img' => su('./images/ok.png'),
						'red' => 0,
						'user_in' => 'You',
						'user_out' => ' 土土',
						'bank_in' => 'Other bank',
						'bank_out' => 'Other bank',
						'can_print' => '1',
						'date' => '2016-02-24',
						'files' => array(
						array(
						'file' => 'https://f.mmm-office.com/201602/25/04/10938213761u_Screenshot_2016-02-25-12-01-44.jpeg',
						'icon' => 'jpg')),'variants' => array(),'chat_show' => 2,
						'order_in' => '21725394797',
						'order_out' => '29832282457',
						'is_transfer' => false,'is_control' => '',
						'is_control_info' => 0,
						'chat_count_messages' => '1',
						'chat_unread_messages' => 1
					)
				),
				'total' => '441'
			)
		);
		return $list;
	}
	function addAccount($datas){
		if (!$this->checkSafePassword()) return '安全码错误，操作失败';
		$datas = form::get4($datas, array('name', 'code', 'bank_name', 'st_number', 'holder_firstname', 'alipay', array('i18', 'int')));
		$id = $datas['i18'];
		unset($datas['i18']);
		if (!is_numeric($datas['st_number'])) return $this->lan->get('银行卡号只能是数字');
		if (preg_match('/\d+/', $datas['bank_name'])) return $this->lan->get('银行名称中不能有数字');
		if ($datas['alipay'] && !form::checkMobilephone($datas['alipay']) && !form::checkEmail($datas['alipay'])) return $this->lan->get('请填写正确的支付宝帐号');
		if (preg_match('/\d+/', $datas['holder_firstname'])) return $this->lan->get('请填写中文姓名');
		if (!form::chack_hanzi($datas['holder_firstname'])) return $this->lan->get('请填写中文姓名');
		if (!$id) {
			$datas['uid'] = $this->id;
			if (db::exists('account', "holder_firstname='$datas[holder_firstname]' AND uid!={$this->id}")) return $this->lan->get('该姓名已被使用，如有疑问请反馈进行身份核验');
			if ($datas['alipay'] && db::exists('account', "alipay='$datas[alipay]' AND uid!={$this->id}")) return $this->lan->get('该支付宝已被使用');
			if (db::exists('account', "st_number='$datas[st_number]' AND uid!={$this->id}")) return $this->lan->get('该银行卡已被使用');
			if (db::dataCount('account', "uid='$this->id'") > 0) {
				if (!db::exists('account', "uid='$this->id' AND holder_firstname='$datas[holder_firstname]'")) return $this->lan->get('只能添加同一个人收款姓名');
			}
			if (db::insert('account', $datas)) {
				return true;
			}
			return $this->lan->get('添加失败');
		} else {
			if ($id) return '不可修改';
			if (db::dataCount('account', "uid='$this->id'") > 1) {
				if (!db::exists('account', "uid='$this->id' AND holder_firstname='$datas[holder_firstname]'")) return $this->lan->get('只能添加同一个人收款帐号');
			}
			if (db::exists('account', "uid='$this->id' AND holder_firstname!='$datas[holder_firstname]'")) return $this->lan->get('不能修改收款人姓名');
			if (db::exists('shop', "bank='$id' AND type='1' AND status IN('200', '201')")) return '该账户正在提款，禁止修改';
			if (db::update('account', $datas, "id='$id' AND uid='$this->id'")) {
				if (db::dataCount('shop', "type='1' AND uid='$this->id' AND status='201'") > 0) {//如果有正在卖出的修改银行资料那么冻结
					//db::update('member', 'status=1', "id='$this->id'");
					member::freeze($this->id);
				}
				return true;
			} else return $this->lan->get('修改失败');
		}
	}
	function accountList(){
		$list = array();
		foreach (db::select('account', '*', "uid='$this->id'") as $v) {
				$arr = array(
					'id'          => $v['id'],
					'name'        => $v['name'],
					'id_currency' => 156,
					'id_bank'     => 6005,
					'balance'     => '0.00',
					'bankname'    => $v['code'],
					'real_bankname' => $v['code'].' '.$v['bank_name'],
					'bank_name'     => $v['bank_name'],
					'number'        => $v['st_number'],
					'st_number'     => $v['st_number'],
					'alipay'        => $v['alipay'],
					'holder_firstname' => $v['holder_firstname'],
					'holder_name'      => null,
					'holder_lastname'  => null,
					'fulldata'         => "银行名称*：$v[bank_name] 银行帐号*: $v[st_number] 姓名*: $v[name]",
					'holder'           => $v['holder_firstname'],
					'cursname'         => 'CNY: Chinese yuan'
					
				);
			$list[] = $arr;
		}
		return $list;
	}
	function getAccount($id){
		return db::one('account', '*', "id='$id' AND uid='$this->id'");
	}
	function delAccount($id){
		return '禁止删除';
		if (db::exists('shop', "bank='$id' AND type='1' AND uid='$this->id' AND status IN('200', '201')")) return '该账户正在提款，禁止删除';
		if (db::delete('account', "id='$id' AND uid='$this->id'")) return true;
		return false;
	}
	function checkSafePassword(){
		$pwd = !empty($_POST['safePassword']) ? $_POST['safePassword'] :false;
		if ($pwd) {
			return qscms::saltPwdCheck($this->_salt, $pwd, $this->_safePassword);
		}
		return false;
	}
	function allowBuy(){
		$msg = false;
		$goto = false;
		if (!$total = db::dataCount('account', "uid='$this->id'")) {
			$msg = '请先完善银行资料再提供帮助';
			$goto = array('url' => $this->getUrl('private_accounts'), 'name' => '完善银行资料');
		}
		if (!$msg && db::dataCount('shop', "type='0' AND complateTime=0 AND uid='$this->id' AND isSys=0 AND status!=300") > 0) {
			$msg = '您有未完成的订单';
		}
		if (!$msg) return true;
		return array('msg' => $msg, 'goto' => $goto);
	}
	function allowPutMoney(){
		/**
		 * 这是一段奇葩的代码 很是让人着急
		 */
		$var = qscms::v('_G');
		$start = $var->todayStart;
		$end   = $var->todayEnd;
		$everyDayHelpMoney = cfg::getMoney('web', 'everyDayHelpMoney');//每日最大捐助金额 0 为不限制
		if ($everyDayHelpMoney > 0) {
			$todayHelpMoney = qscms::formatMoney(db::one_one('shop', 'SUM(allMoney)', "type='0' AND isSys='0' AND status<>300 AND addTime>='$start' AND addTime<='$end'"));
		} else $todayHelpMoney = 0;
		
		/**
		 * 捐助次数 限额检测
		 */
		$reword = cache::get_array('reword');
		$cfg = $reword[3];//配置
		$index = $this->_buyCount;
		
		
		$limitMoney = isset($cfg['list'][$index]) ? $cfg['list'][$index] : $cfg['overflow'];
		
		return array('allow' => $everyDayHelpMoney, 'used' => $todayHelpMoney, 'leftOver' => $everyDayHelpMoney - $todayHelpMoney, 'limitMoney' => $limitMoney);
	}
	function buy($datas){
		if (!$this->checkSafePassword()) return '安全码错误，操作失败';
		$var = qscms::v('_G');
		$start = $var->todayStart;
		$end   = $var->todayEnd;
		if (!$total = db::dataCount('account', "uid='$this->id'")) return '请先完善银行资料再提供帮助';
		
		if (db::dataCount('shop', "type='0' AND uid='$this->id' AND status IN('100', '101')") > 0) return '您还有捐助未完成';
		$datas = form::get4($datas, array('cry', 'bank', 'stotal', 'scry', array('complate', '01')));
		qscms::setType($datas, 'int');
		
		/**
		 * 每日捐助金额限制
		 * SB勿动 4/2/2016
		 * 雨雨是个傻吊 4/5/2016
		 */
		$everyDayHelpMoney = cfg::getMoney('web', 'everyDayHelpMoney');//每日最大捐助金额 0 为不限制
		if ($everyDayHelpMoney > 0) {
			$todayHelpMoney = qscms::formatMoney(db::one_one('shop', 'SUM(allMoney)', "type='0' AND isSys='0' AND status<>300 AND addTime>='$start' AND addTime<='$end'"));
			$ifTodayMoney = $todayHelpMoney + $datas['stotal'];
			if ($ifTodayMoney > $everyDayHelpMoney) return '帮助金额已达到今日额度上限';//return '每天最多捐助金额：'.$everyDayHelpMoney.'，您还可以提供捐助金额：'.($everyDayHelpMoney - $todayHelpMoney);
		}
		
		/**
		 * 捐助次数 限额检测
		 */
		$reword = cache::get_array('reword');
		$cfg = $reword[3];//配置
		$index = $this->_buyCount;
		$limitMoney = isset($cfg['list'][$index]) ? $cfg['list'][$index] : $cfg['overflow'];
		if ($datas['stotal'] > $limitMoney) return '您当前最大捐助金额：'.$limitMoney;
		
		$bei = cfg::getInt('m', 'bei');
		$low = cfg::getInt('m', 'low');
		$buyDay = cfg::getInt('m', 'buyDay');
		$sellDay = cfg::getInt('m', 'sellDay');
		$lixiDay = cfg::getInt('m', 'lixiDay');
		$lixiFreezeDay = cfg::getInt('m', 'lixiFreezeDay');//利息解冻天数
		$lixiPercent = qscms::percentDecimal(cfg::get('m', 'lixiPercent'));
		if ($datas['stotal'] % $bei != 0) return '买入数量必须是'.$bei.'的倍数';
		if ($datas['stotal'] < $low) return '买入数量最少'.$low;
		if ($this->_isNew) {
			$maxMoney = cfg::getMoney('web', 'newMaxMoney');//新手最大捐助金额
			if ($datas['stotal'] > $maxMoney) return '新手会员最大捐助金额：'.$maxMoney;
		} else {
			/*
			$min = cfg::getMoney('web', 'childMaxMoney');//买入大于等于该金额可忽略下级最大投入金额
			if ($datas['stotal'] < $min) {
				$l = $this->_l;
				$r = $this->_r;
				$sql = db::sqlSelect('member', 'id,l,r', "l>$l AND r<$r");
				$sql = db::sqlSelect("($sql)|shop:uid=id", "|allMoney");
				$sql = "SELECT MAX(allMoney) FROM ($sql) t";
				$max = intval(db::resultFirst($sql));
				$childMaxMoneyPercent = cfg::getMoney('web', 'childMaxMoneyPercent');
				if ($childMaxMoneyPercent > 0) {
					$max *= $childMaxMoneyPercent;
					if ($datas['stotal'] < $max) return '援助金额不能小于下级最大投入金额：'.$max.' 的'.cfg::get('web', 'childMaxMoneyPercent');
				} else {
					if ($datas['stotal'] < $max) return '援助金额不能小于下级最大投入金额：'.$max;
				}
			}
			*/
		}
		$time = time();
		$afterTime = time::getSecond(cfg::get('m', 'afterTime'));//当天分隔时间
		$isToday = $time < $start + $afterTime;//是否为当天
		//!$isToday && $lixiDay++;
		$arr = array(
			'uid'            => $this->id,
			//'buyCry'       => $datas['cry'],
			//'buyBank'      => $datas['bank'],
			'allMoney'       => $datas['stotal'],
			'yuMoney'        => $datas['stotal'],
			'lixi'           => qscms::formatMoney($datas['stotal'] * (1 +  $lixiPercent * $lixiDay)),//qscms::formatMoney($datas['stotal'] * pow(1 +  $lixiPercent, $lixiDay)),
			'dayLixi'        => $lixiDay,
			'dayStart'       => $buyDay,
			'addTime'        => $time,
			'startTime'      => $time + $buyDay * 86400,//解冻时间
			'lixiTime'       => $time + $lixiFreezeDay * 86400,
			'addLixiTime'       => $time + $lixiFreezeDay * 86400
		);
		if (!$isToday) {//如果不是当天延迟一天
			//$arr['startTime'] += 86400;
			//$arr['lixiTime']  += 86400;
		}
		$arr['status'] = $arr['addTime'] == $arr['startTime'] ? 101 : 100;//如果解冻时间可添加时间一样 就不用等待解冻
		$arr['lixiStatus'] = $arr['addTime'] == $arr['lixiTime'] ? 1 : 0;//如果添加时间和利息解冻时间一样就直接解冻利息
		if ($datas['complate'] && $this->_isDebug) {
			$arr['status'] = 102;//如果是调试员
			$arr['lixiStatus'] = 1;//利息解冻
			$arr['complateTime'] = time();
		}
		$arr['lixiYu'] = $arr['lixi'];
		
		/**
		 * 擦尼玛 加的新东西 20160509
		 */
		$isFirstLevel = false;
		if (db::exists('shop', "type='0' AND uid='$this->id' AND status='102' AND sellStatus='0'")) {
			$isFirstLevel = true;
		}
		if (!$isFirstLevel) {
			//$arr['isError'] = 2;//如果当前没有新排单 设为错误为2；
		}
		if ($sid = db::insert('shop', $arr, true)) {
			
			/**
			 * 优质度增加
			 */
			if ($isFirstLevel) {
				db::update('member', 'firstLevel=firstLevel+1', "id='$this->id'");
			}
			
			/**
			 * 排单金额递减烧伤检测
			 */
			if ($arr['allMoney'] < $this->_lastBuyMoney) {
				db::update('member', 'buyDescCount=buyDescCount+1', "id='$this->id'");
				$this->_buyDescCount++;
				if (!cfg::getBoolean('rewordSS', 'close')) {
					$triggerTime = cfg::getInt('rewordSS', 'firstTriggerDay') * 86400;//触发间隔时间
					$time = time();
					$leftTime = $time - $triggerTime;
					$helpMoneyDesc = cfg::getInt('rewordSS', 'helpMoneyDesc');
					if ($helpMoneyDesc > 0 && $helpMoneyDesc <= $this->_buyDescCount) {
						db::update('member', "rewordWither=rewordWither+1,rewordWitherTime='$time'", "id='$this->id' AND rewordWitherTime<$leftTime");
					}
				}
			} else {
				db::update('member', 'buyDescCount=0', "id='$this->id'");
			}
			
			$time = time();
			db::update('member', "activeTime='$time',lastBuyMoney='$arr[allMoney]',lastBuyTime='$time'", "id='$this->id'");//修改活跃时间
			member_shop::reword($sid);//提成奖励
			return true;
		}
		return '添加失败，请重试';
	}
	function sell($datas){
		$useCommit = true;
		$yu = $this->_money;//账户余额;
		if (!$this->checkSafePassword()) return '安全码错误，操作失败';
		extract(form::get4($datas, array('bank', 'sell')));
		if (!db::exists('account', array('id' => $bank, 'uid' => $this->id))) return '收款账户不存在，请检查是否已删除';
		$allMoney = 0;
		$shops = array();
		
		$useCommit && db::autocommit();
		
		foreach ($sell as $sid => $money) {
			if ($line = db::one('shop', 'lixi,lixiYu', "id='$sid' AND uid='$this->id' AND status='102' AND lixiStatus='1' AND isFreeze='0' AND isLock='0' AND cid=0 AND lixiYu>0 AND (isSys='1' OR complateTime>0)", false, $useCommit)) {
				if ($money > $line['lixiYu']) {
					$useCommit && db::autocommit(false);
					return '所选卖出可用余额不足';
				}
				$shops[$sid] = $line;
			} else {
				$useCommit && db::autocommit(false);
				return '所选卖出无效';
			}
			$allMoney += $money;
		}
		$allMoney0 = $allMoney;
		$allMoney += $yu;//加上账户余额
		$bei = cfg::getInt('m', 'bei');
		$low = cfg::getInt('m', 'low');
		$buyDay = cfg::getInt('m', 'buyDay');
		$sellDay = cfg::getInt('m', 'sellDay');
		if ($allMoney < $bei) return '卖出不能小于'.$bei;
		
		/**
		 * 阻断金额
		 */
		$allowMoney = qscms::formatMoney(db::one_one('shop', 'SUM(lixiYu)', "uid='$this->id' AND status='102' AND lixiStatus='1' AND isFreeze='0' AND isLock='0' AND cid=0 AND lixiYu>0"));
		//$blockMoney = qscms::formatMoney(db::one_one('shop', 'SUM(allMoney)', "type='1' AND uid='$this->id' AND complateTime=0 AND status<>300"));
		$sql = db::sqlSelect('shop', 'id', "type='1' AND uid='$this->id' AND complateTime=0 AND status<>300");
		$sql = db::sqlSelect("($sql)|shop_order:tsid=id", 'SUM(money)', "t1.status NOT IN(13,20)");
		$blockMoney = db::resultFirst($sql);

		if ($blockMoney > 0){
			$allow = $allowMoney - $blockMoney;
			
			if ($allMoney > $allow) return "有{$blockMoney}正在提出，".($allow > 0 ? "还可以提出：$allow" : "不可卖出");
		}
		
		
		$time = time();
		$useMoney = floor($allMoney / $bei) * $bei;//整倍数卖出
		$thisYu   = $allMoney - $useMoney;//剩余金额
		
		/**
		 * 提现后再排单烧伤
		 */
		$rewordSS = false;
		if (!cfg::getBoolean('rewordSS', 'close')) {
			if (cfg::getBoolean('rewordSS', 'sellAfterBuy')) {
				if (!db::exists('shop', "type='0' AND uid='$this->id' AND status<='101'")) {
					$rewordSS = true;
				}
			}
		}
		
		$arr = array(
			'type' => 1,//卖出
			'uid' => $this->id,
			'allMoney' => $useMoney,
			'yuMoney' => $useMoney,
			'yuMoney1' => $useMoney,
			'useYu'   => $useMoney - $allMoney0,//使用多少账户余额
			'bank'    => $bank,//收款银行
			'dayStart' => $sellDay,
			'addTime'  => $time,
			'startTime' => $time + $sellDay * 86400
		);
		$arr['status'] = $arr['addTime'] == $arr['startTime'] ? 201 : 200;//如果解冻时间可添加时间一样 就不用等待解冻
		foreach ($sell as $sid => $money) {
			if ($line = db::one('shop', 'lixi,lixiYu', "id='$sid' AND uid='$this->id' AND status='102' AND lixiStatus='1' AND isFreeze='0' AND isLock='0' AND cid=0 AND lixiYu>0 AND (isSys='1' OR complateTime>0)")) {
				if ($money > $line['lixiYu']) return '所选卖出可用余额不足';
			} else return '所选卖出无效';
		}
		/**
		 * 阻断 排单
		 */
		$isZD = false;
		$dMoney = cfg::getMoney('m', 'zdMoney');
		if (!$dMoney) $dMoney = 5000;
		if ($dMoney){
			if (!db::exists('shop', "type='0' AND isSys='0' AND uid='$this->id' AND status<='102' AND complateTime='0' AND allMoney>=$dMoney")) {
				$arr['isError'] = 2;
				$arr['yuMoney1'] = 0;
				$isZD = true;
			}
		}
		if (!$isZD) {
			$arr['yuMoney1'] = $arr['yuMoney'] * mt_rand(30, 70) / 100;
			$arr['yuMoney1'] = floor($arr['yuMoney1'] / $bei) * $bei;
		}
		
		if ($id = db::insert('shop', $arr, true)) {
			$error = false;
			if ($rewordSS) {
				$triggerTime = cfg::getInt('rewordSS', 'firstTriggerDay') * 86400;//触发间隔时间
				$time = time();
				$leftTime = $time - $triggerTime;
				db::update('member', "rewordWither=rewordWither+1,rewordWitherTime='$time'", "id='$this->id' AND rewordWitherTime<$leftTime");//更新烧伤次数
			}
			db::update('member', "money=$thisYu", "id='$this->id'");//更新账户余额
			foreach ($sell as $sid => $money) {
				if (!$money) continue;
				$shop = $shops[$sid];
				$shop['lixiYu'] -= $money;
				if ($shop['lixiYu'] <= 0) {
					$shop['status'] = 103;
					$shop['complateTime'] = time();
				}
				$shop['sellStatus'] = 1;//标记已经卖出过
				if (!db::update('shop', $shop, "id='$sid'")) {
					if ($useCommit) {
						$error = true;
						break;
					}
				}
				
				/**
				 * 拆分处理
				 */
				$newShop = db::one('shop', '*', "id='$sid'");
				unset($newShop['id']);
				$newShop['addTime'] = time();
				$newShop['lixi'] = $money;
				$newShop['lixiYu'] = $money;
				$newShop['cid'] = $sid;
				$newShop['status'] = 102;
				$newSid = db::insert('shop', $newShop, true);
				
				if (!db::insert('shop_sell', array(
					'fid' => $id,
					'sid' => $newSid,
					'money' => $money
				))) {
					if ($useCommit) {
						$error = true;
						break;
					}
				}
			}
			
			/**
			 * 检查是否有重复卖出 如果有设为错误订单拒绝匹配
			 */
			/*foreach ($sell as $sid => $money) {//遍历卖出的马夫罗
				$money1 = db::one('shop', 'lixi,lixiYu', "id='$sid'");//获取该马夫罗利息
				$money1 = $money1['lixi'];// - $money1['lixiYu'];
				$money2 = db::one_one('shop', 'SUM(lixi)', "cid='$sid'");//获取拆分的总额
				if ($money2 > $money1) {//如果拆分总额大于马夫罗利息 表示错误 可能重复了 那么设为错误
					foreach (db::select('shop', 'id', "cid='$sid'") as $v) {//获取拆分的ID
						foreach (db::select('shop_sell', 'fid', "sid='$v[id]'") as $v1) {//获取最终对应的得到帮助ID
							db::update('shop', "isError='1'", "id='$v1[fid]'");
						}
					}
				}
			}*/
			if ($useCommit) {
				if (!$error) {
					db::commit(true);
					return true;
				}
			}
			return true;
		}
		$useCommit && db::rollback(true);
		return '添加失败，请重试';
	}
	function getShops($pagesize, $page, $where = '', $hide = 0){
		$hide && qscms::setcookie('hide1', $hide);
		$item = array();
		$list = array();
		$wh = '';
		if ($hide == 2) $wh = 'status not in(103, 202, 300, 400) AND '; 
		if ($total = db::dataCount('shop', $wh."(type='0' OR type='1') AND uid='$this->id' AND isSys=0 AND cid=0")) {
			$sql = db::sqlSelect('shop', '*', $wh."(type='0' OR type='1') AND uid='$this->id' AND isSys=0 AND cid=0", 'id DESC', $pagesize, $page);
			$list = db::select("($sql)|member:id=uid", '*|name,xin');
		}
		$item = array('total' => $total, 'list' => $list);
		return $item;
	}
	function getOrders($pagesize, $page, $where = '', $hide = false){
		$hide && qscms::setcookie('hide', $hide);
		$list = array();
		$wh = '';
		if ($hide == 2) $wh = 'status not in(13, 20) AND '; //$wh = ' AND status not in(103, 202, 300, 400)'; 
		$uid = $this->id;
		if ($total = db::dataCount('shop_order', $wh."(fuid='$uid' OR tuid='$uid')")) {
			$sql = db::sqlSelect('shop_order', '*', $wh."(fuid='$uid' OR tuid='$uid')", 'id DESC', $pagesize, $page);
			$list = db::select("($sql)|member:id=fuid|member:id=t0.tuid", '*|name fname|name tname');
		}
		//print_r($list);exit;
		return array('total' => $total, 'list' => $list);
	}
	function getOrder($id){
		$uid = $this->id;
		$sql = db::sqlSelect('shop_order', '*', "id='$id' AND (fuid='$uid' OR tuid='$uid')");
		return db::selectFirst("($sql)|member:id=fuid|member:id=t0.tuid", '*|name fname|name tname');
	}
	function deleteShop($datas){
		return false;
		$useCommit = true;
		$useCommit && db::autocommit();
		$var = qscms::v('_G');
		$datas = form::get4($datas, array(array('id', 'int'), 'remark'));
		if ($line = db::one('shop', 'id,type,status,buyTotal,sellTotal,allMoney', "id='$datas[id]' AND uid='$this->id'", false, $useCommit)) {
			if ($line['type'] == 0) {
				$allowDelCount = cfg::getInt('web', 'monthCancelCount');//每个月允许删除订单次数 0为不限制
				if ($allowDelCount > 0) {
					$start = $var->tsmStart;//本月开始时间
					$end   = $var->tsmEnd;//本月结束时间
					$delCount = db::dataCount('shop', "uid='$this->id' AND status='300' AND addTime>=$start AND addTime<=$end");
					if ($delCount >= $allowDelCount) return '每个月只能删除'.$allowDelCount.'次';
				}
				if ($line['status'] <= 101 && $line['buyTotal'] == 0) {
					if (db::update('shop', array('status' => 300, 'delRemark' => $datas['remark']), "id='$datas[id]'")) {
						member_shop::rewordDelete($datas['id']);//删除提成
						$useCommit && db::commit(true);
						return true;
					}
					$useCommit && db::autocommit(false);
					return '删除失败';
				}
				$useCommit && db::autocommit(false);
				return '该状态不可删除';
			} elseif ($line['type'] == 1) {
				if ($line['status'] <= 201 && $line['sellTotal'] == 0) {
					$allMoney = 0;
					$error = false;
					foreach (db::select('shop_sell', 'sid', "fid='$line[id]'") as $v) {
						$sid = $v['sid'];
						$sellShop = db::one('shop', '*', "id='$sid'", false, $useCommit);
						//print_r($sellShop);
						if (db::del_id('shop', $sid) <= 0) {//删除等待提出
							if ($useCommit) {
								$error = true;
								break;
							}
						}
						$cid = $sellShop['cid'];
						$buyShop = db::one('shop', 'lixi,lixiYu', "id='$cid'");
						$buyShop['lixiYu'] += $sellShop['lixi'];//恢复之前扣除
						$allMoney += $sellShop['lixi'];//总共恢复金额
						if ($buyShop['lixi'] == $buyShop['lixiYu']) $buyShop['sellStatus'] = 0;
						$buyShop['status'] = 102;
						//$buyShop['complateTime'] = 0;//完成时间设为0
						if (!db::update('shop', $buyShop, "id='$cid'")){//恢复扣除前
							if ($useCommit) {
								$error = true;
								break;
							}
						}
					}
					if ($error && $useCommit) {
						db::rollback(true);
						return '删除失败，请重试';
					}
					$more = $line['allMoney'] - $allMoney;//多出的钱
					db::update('member', "money=money+$more", "id='$this->id'");//更新账户余额
					if (db::update('shop', array('status' => 300, 'delRemark' => $datas['remark']), "id='$datas[id]'")) {
						if ($useCommit) {
							db::commit(true);
						}
					}
					return true;
				}
				$useCommit && db::autocommit(false);
				return '该状态不可删除';
			}
		}
		return '非法操作';
	}
	function getSellShop($order = false){
		$list = array();
		$total = 0;
		/**
		 * 检查下面的人是否有冻结的，如果有的话就不列出奖励的项目，只列出捐助完成的
		 */
		$l = $this->_l;
		$r = $this->_r;
		/*$freeze = db::exists("member", "l>$l AND r<$r AND status='1'");//是否存在已冻结的用户
		
		if ($total = db::dataCount('shop', ($freeze ? "isSys='0' AND " : '')."uid='$this->id' AND status='102' AND lixiStatus='1' AND isFreeze='0'")) {
			$list = db::select('shop', '*', ($freeze ? "isSys='0' AND " : '')."uid='$this->id' AND status='102' AND lixiStatus='1' AND isFreeze='0'");
		}*/
		foreach (db::select('shop', '*', "uid='$this->id' AND status='102' AND lixiStatus='1' AND isFreeze='0' AND isLock='0' AND cid=0 AND lixiYu>0 AND (isSys='1' OR complateTime>0)", $order) as $v) {
			if ($v['isSys'] && $v['fuid']) {
				if (db::exists('member', "id='$v[fuid]' AND status='1'")) continue;
			}
			$list[] = $v;
			$total++;
		}
		return array('total' => $total, 'list' => $list);
	}
	function isAllowSell(){
		//if (db::dataCount('shop', "type='0' AND uid='$this->id' AND isSys='0' AND status IN(100,101,102) AND complateTime=0 AND buyTotal>0"))
		if ($this->_is_sys == 0){
			if (!db::dataCount('shop', "type='0' AND uid='$this->id' AND isSys='0' AND status IN(102,103) AND (isSys='1' OR complateTime>0)")) return '<span style="font-weight: bold;">抱歉，但此刻您的马夫罗没有得到证实（或冻结）。</span>
	<br>
	因此，不可能提取。
	<br>
	为了证实自己的马夫罗，首先您必须有一个人帮助。';//'您还有捐助未完成';
			if (db::dataCount('shop', "type='1' AND uid='$this->id' AND status='202' AND complateTime>0 AND isXfx='0'")) return '您有得到帮助还没写幸福信';
		}
		return true;
	}
	function orderDelay($id){//延迟付款时间
		if ($one = db::one('shop_order', '*', "id='$id' AND tuid='$this->id' AND is_delay=0 AND status IN('10', '11','12')")) {
			$endTime = $one['endTime'] + (3600 * 24);
			if (db::update('shop_order', "endTime='$endTime',is_delay=1", "id='$id'")) return true;
		}
		return '非法操作';
	}
	function orderCancel($id, $chack = true){
		$useCommit = true;
		$useCommit && db::autocommit();
		if ($one = db::one('shop_order', '*', "id='$id' AND fuid='$this->id' AND status IN('10', '11')", false, $useCommit)) {
			$time = time();
			if ($chack){
				$cancel = cfg::getInt('web', 'matchCancel');//允许取消订单次数
				if ($cancel > 0) {
					$var = qscms::v('_G');
					$start = $var->tsmStart;
					$end   = $var->tsmEnd;
					$count = db::dataCount('shop_order', "fuid='$this->id' AND status='13' AND cancelTime>='$start' AND cancelTime<='$end'");
					if ($count >= $cancel) {
						$useCommit && db::autocommit(false);
						return '匹配成功的订单每月只能拒绝付款'.$cancel.'次';
					}
					//return '匹配成功的订单不允许拒绝付款';
				}
			}
			if (db::update('shop_order', "cancelTime='$time',status='13'", "id='$id'")) {
				$error = false;
				$error || $error = !db::update('shop', "yuMoney=yuMoney+$one[money],status='101'", "id='$one[fsid]'");
				$error || $error = !db::update('shop', "yuMoney=yuMoney+$one[money],yuMoney1=yuMoney1+$one[money],status='201'", "id='$one[tsid]'");
				if (!$error) {
					$useCommit && db::commit(true);
				}
				return true;
			}
			$useCommit && db::rollback(true);
		}
		return '非法操作';
	}
	function orderCancel4($id, $chack = true){
		$useCommit = true;
		$useCommit && db::autocommit();
		if ($one = db::one('shop_order', '*', "id='$id' AND fuid='$this->id' AND status IN('10', '11')", false, $useCommit)) {
			$time = time();
			if ($chack){
				$cancel = cfg::getInt('web', 'matchCancel');//允许取消订单次数
				if ($cancel > 0) {
					$var = qscms::v('_G');
					$start = $var->tsmStart;
					$end   = $var->tsmEnd;
					$count = db::dataCount('shop_order', "fuid='$this->id' AND status='13' AND cancelTime>='$start' AND cancelTime<='$end'");
					if ($count >= $cancel) {
						$useCommit && db::autocommit(false);
						return '匹配成功的订单每月只能拒绝付款'.$cancel.'次';
					}
					//return '匹配成功的订单不允许拒绝付款';
				}
			}
			if (db::update('shop_order', "cancelTime='$time',status='13'", "id='$id'")) {
				$error = false;
				$oneItem = db::one('shop', 'id,allMoney,yuMoney,status', "id='$one[fsid]'", false, $useCommit);
				if ($oneItem['status'] != 300){
					$error || $error = !db::update('shop', "yuMoney=yuMoney+$one[money],status='101'", "id='$one[fsid]'");
				}else {
					$error || $error = !db::update('shop', "yuMoney=yuMoney+$one[money]'", "id='$one[fsid]'");	
				}
				$error || $error = !db::update('shop', "yuMoney=yuMoney+$one[money],yuMoney1=yuMoney1+$one[money],status='201'", "id='$one[tsid]'");
				if (!$error) {
					$useCommit && db::commit(true);
				}
				return true;
			}
			$useCommit && db::rollback(true);
		}
		return '非法操作';
	}
	function orderAgree($id){
		$useCommit = true;
		$useCommit && db::autocommit();
		if ($one = db::one('shop_order', '*', "id='$id' AND fuid='$this->id' AND status IN('10')", false, $useCommit)) {
			$time = time();
			if ($time <= $one['endTime']){
				if (db::update('shop_order', "status='11'", "id='$id'")) {
					$useCommit && db::commit(true);
					return true;

				}
				$useCommit && db::rollback(true);
			}else {
				$useCommit && db::rollback(true);
				return '已超时';
			}
		}
		$useCommit && db::rollback(true);
		return '非法操作';
	}
	function orderPayfor($id){
		$time = time();
		$useCommit = true;
		$useCommit && db::autocommit();
		if ($one = db::one('shop_order', '*', "id='$id' AND fuid='$this->id' AND status IN('10','11') AND endTime>=$time", false, $useCommit)) {
			$endTime = $time + (3600 * 48);
			$calTime = $time + (3600 * 24);
			
			if (db::update('shop_order', "status='12',endTime=$endTime,calTime=$calTime", "id='$id'")) {
				$tmobile = db::one_one('member', 'mobile', "id='$one[tuid]'");
				
				message::addSendVcodeInfo($tmobile, '已付款');//已付款 已确认
				
				$useCommit && db::commit(true);
				return true;
			}
		}
		$useCommit && db::rollback(true);
		return '非法操作';
	}
	function orderConfirm($id){
		$bei = cfg::getInt('m', 'bei');//整倍数 10
		$useCommit = true;
		$useCommit && db::autocommit();
		if ($one = db::one('shop_order', '*', "id='$id' AND tuid='$this->id' AND status='12'", false, $useCommit)) {
			$time = time();
			$allMoney = qscms::formatMoney(db::one_one('shop', 'allMoney', "id='$one[fsid]'"));
			$comMoney = qscms::formatMoney(db::one_one('shop_order', 'SUM(money)', "fsid='$one[fsid]' AND status='20'"));
			if (db::update('shop_order', "status='20',comTime='$time'", "id='$id'")) {
				/**
				 * 买入
				 */
				$allMoney = qscms::formatMoney(db::one_one('shop', 'allMoney', "id='$one[fsid]'", false, $useCommit));
				$allMoney = floor($allMoney / $bei) * $bei;
				$comMoney = qscms::formatMoney(db::one_one('shop_order', 'SUM(money)', "fsid='$one[fsid]' AND status='20'"));
				
				/**
				 * 卖出
				 */
				$allMoney1 = qscms::formatMoney(db::one_one('shop', 'allMoney', "id='$one[tsid]'", false, $useCommit));
				$allMoney1 = floor($allMoney1 / $bei) * $bei;
				$comMoney1 = qscms::formatMoney(db::one_one('shop_order', 'SUM(money)', "tsid='$one[tsid]' AND status='20'"));
				
				$bank = db::one_one('shop', 'bank', "id='$one[tsid]'");
				if ($acc = db::one('account', '*', "id='$bank'")) {
					db::update('account', 'money=money+$one[money]', "id='$bank'");//更新卖家使用的银行总收入
					/**
					 * 收款银行重复检测
					 */
					$bankTotal = db::dataCount('account', "code='$acc[code]' AND bank_name='$acc[bank_name]' AND st_number='$acc[st_number]'");
					if ($bankTotal > 1) {
						db::insert('cheat', array(
							'type'   => 2,//银行
							'uid'    => $one['tuid'],
							'val'    => $acc['code'].':'.$acc['bank_name'].':'.$acc['st_number'],
							'remark' => '收款银行重复使用',
							'count'  => $bankTotal,
							'time'   => time()
						));
					}
				}
				db::update('member', "buyMoney=buyMoney+$one[money]", "id='$one[fuid]'");//更新买入金额
				db::update('member', "sellMoney=sellMoney+$one[money]", "id='$one[tuid]'");//更新卖出金额
				$fmobile = db::one_one('member', 'mobile', "id='$one[fuid]'");
				message::addSendVcodeInfo($fmobile, '已确认');//已付款 已确认
				
				/**
				 * 检测排单金额烧伤处理
				 */
				if (!cfg::getBoolean('rewordSS', 'close')) {
					$triggerTime = cfg::getInt('rewordSS', 'firstTriggerDay') * 86400;//触发间隔时间
					$time = time();
					$leftTime = $time - $triggerTime;
					$childHelpMoneyMoreThan = cfg::getInt('rewordSS', 'childHelpMoneyMoreThan');
					if ($childHelpMoneyMoreThan > 0) {
						$buyer = db::one('member', 'l,r,buyMoney', "id='$one[fuid]'");
						$buyMoney = qscms::formatMoney($buyer['buyMoney']);//买入总金额
						$v = floor($buyMoney / $childHelpMoneyMoreThan * 100) / 100;
						db::update('member', "rewordWither=rewordWither+1,rewordWitherTime='$time'", "l<$buyer[l] AND r>$buyer[r] AND buyMoney<$v AND rewordWitherTime<$leftTime");//更新烧伤次数
						/**
						 * 后台配置目前是间隔14天才烧伤一次 晓得不傻吊 20160429
						 * 雨雨个瓜麻批
						 */
					}
				}
				
				$time = time();
				$error = false;
				if ($allMoney1 == $comMoney1) {//卖出完成
					if (!db::update('shop', "complateTime='$time'", "id='$one[tsid]'")) {//设置完成时间 卖家状态 表示已经卖完
						if ($useCommit) {
							$error = true;
						}
					}
					if (!$useCommit || !$error) {
						foreach (db::select('shop_sell', 'sid', "fid='$one[tsid]'") as $v) {
							if (!db::update('shop', "complateTime='$time',status='103'", "id='$v[sid]'")) {
								if ($useCommit) {
									$error = true;
									break;
								}
							}
						}
					}
					if ($useCommit && $error) {
						db::rollback(true);
						return '操作失败，请重试';
					}
				}
				
				if ($allMoney == $comMoney) {//买入代表完成了，分发奖励
					$error = false;
					db::update('member', 'isNew=0,buyCount=buyCount+1', "id='$one[fuid]'");//设为老手
					$error || $error = !db::update('shop', "complateTime='$time'", "id='$one[fsid]'");//设置完成时间
					
					if (!$useCommit || !$error) {
						/*
						$reHour = cfg::getInt('web', 'shopRewordHour');//多少小时内完成
						$rePer  = cfg::getMoney('web', 'shopRewordPercent');//奖励百分比
						$lixiDay = cfg::getInt('web', 'shopRewordLixiTime');//诚信解冻天数
						if ($reHour > 0 && $rePer > 0) {
							$t = db::one_one('shop', 'startTime', "id='$one[fsid]'");//开始时间
							if ($time - $t <= $reHour * 3600) {
								member_shop::rewordShop($one['fsid'], $rePer, ($lixiDay > 0) ? ($time + $lixiDay * 86400): 1);//如果是1直接解冻
							}
						}
						*/
						//member_shop::reword($one['fsid']);//提成奖励
						member_shop::rewordConfirm($one['fsid']);//解冻提成
					}
					if ($useCommit && $error) {
						db::rollback(true);
						return '操作失败，请重试';
					}
				}
				/*雨大爷改*/
				$reHour = cfg::getInt('web', 'shopRewordHour');//多少小时内完成
				$rePer  = cfg::getMoney('web', 'shopRewordPercent');//讲诚奖金百分比
				if ($reHour > 0 && $rePer > 0) {
					$t = db::one_one('shop_order', 'addTime', "id='$one[id]'");//开始时间
					if ($time - $t <= $reHour * 3600) {
						member_shop::rewordShop_order($one['id'], $rePer);//如果是1直接解冻
					}
				}
				
				$useCommit && db::commit(true);
				return true;
			}
		}
		$useCommit && db::autocommit(false);
		return '非法操作';
	}
	function orderError($id){
		$useCommit = true;
		$useCommit && db::autocommit();
		
		if ($one = db::one('shop_order', '*', "id='$id' AND tuid='$this->id' AND status='12'", false, $useCommit)) {
			$time = time();
			if ($one['calTime'] && $one['calTime'] < $time) return '由于银行到账时间不一致请在24小时后确认未到账后点击';
			if (db::update('shop_order', "status='21'", "id='$id'")) {
				if (!db::dataCount('shop_order', "(fuid='$one[fuid]' OR tuid='$one[fuid]') AND status='20'")) {//如果买家没有交易，那么冻结他
					//db::update('member', "status='1'", "id='$one[fuid]'");
					member::freeze($one['fuid']);
				}
				$useCommit && db::commit(true);
				return true;
			}
		}
		$useCommit && db::autocommit(false);
		return '非法操作';
	}
	function orderInfo($id){
		//$sql = db::sqlSelect('shop_order', '*', "id='$id' AND (fuid='$this->id' OR tuid='$this->id')");
		
		if ($item = db::one('shop_order', '*', "id='$id' AND (fuid='$this->id' OR tuid='$this->id')")) {
			
			$fuser = db::one('member', '*', "id='$item[fuid]'");//买
			$tuser = db::one('member', '*', "id='$item[tuid]'");//卖
			
			$fpuser = treeDB::parents_one('member', $fuser['id']);
			$tshop = db::one('shop', '*', "id='$item[tsid]'");
			$tpuser = treeDB::parents_one('member', $tuser['id']);
			
			$tbank = db::one('account', '*', "id='$tshop[bank]'");
			$item['f'] = array(
				'user' => $fuser,
				'puser'=> $fpuser,
			);
			$item['t'] = array(
				'user' => $tuser,
				'puser'=> $tpuser,
				'bank' => $tbank
			);
			return $item;
		}
		return false;
	}
	function shopInfo($id){
		if ($shop = db::one('shop', '*', "id='$id' AND uid='$this->id'")) {//先获取商品
			$user = db::one('member', '*', "id='$shop[uid]'");
			$shop['user'] = $user;
			$shops = array();//关于哪些商品 也就是马夫罗包
			$orders = array();
			if ($shop['type'] == 0) {//买 援助别人
				$shops[] = array(
					'time'  => $shop['addTime'],
					'money' => $shop['allMoney']
				);
				$sql = db::sqlSelect('shop_order', '*', "fsid='$shop[id]'");
			} elseif ($shop['type'] == 1) {//卖 卖出提现
				$sql = db::sqlSelect('shop_sell', '*', "fid='$shop[id]'");
				foreach (db::select("($sql)|shop:id=sid", 'money|addTime') as $v) {
					$shops[] = array(
						'time' => $v['addTime'],
						'money' => $v['money'],
					);
				}
				$sql = db::sqlSelect('shop_order', '*', "tsid='$shop[id]'");
			}
			$orders = db::select("($sql)|member:id=fuid|member:id=t0.tuid", '*|name fname|name tname');
			return array('shop' => $shop, 'shops' => $shops, 'orders' => $orders);
		}
		return false;
	}
	static function recover($datas){
		$datas = form::get4($datas, array('user', 'vcode', 'password'));
		if ($datas['user'] && $datas['vcode'] && $datas['password']){
			$next = false;	
			if (form::checkEmail($datas['user']) && $one = db::one('member', 'id,salt,password,safePassword', "email='$datas[user]'")){
				$uid  = $one['id'];
				$salt = $one['salt'];
				$hash = qscms::v('_G')->sys_hash;
				$timestamp = time::$timestamp;
				$vcode_time  = cfg::getInt('email', 'time');//验证码有效时间 分钟
				
				if ($sendLog = db::one('log_email_vcode', '*', "hash='$hash' AND email='$datas[user]'", 'time desc')) {
					if ($sendLog['status'] == 1) return '操作失败';
					if ($datas['vcode'] != $sendLog['vcode']) return '验证码错误';
					if ($sendLog['time'] + ($vcode_time * 60) < $timestamp) return '验证码过期，请重新获取';
					db::update('log_email_vcode', 'status=1', "id='$sendLog[id]'");
					$password = qscms::saltPwd($salt, $datas['password']);
					db::update('member', "password='$password'", "id='$uid'");
					return true;
				}
			}elseif (form::checkMobilephone($datas['user']) && $one = db::one('member', 'id,salt,password,safePassword', "mobile='$datas[user]'")){
				$uid  = $one['id'];
				$salt = $one['salt'];
				$hash = qscms::v('_G')->sys_hash;
				$timestamp = time::$timestamp;
				$vcode_time  = cfg::getInt('sms', 'sms_time');//验证码有效时间 分钟
				if ($sendLog = db::one('log_vcode', '*', "hash='$hash' AND mobile='$datas[user]'", 'time desc')) {
					if ($sendLog['status'] == 1) return '验证码过期，请重新获取';
					if ($datas['vcode'] != $sendLog['vcode']) return '验证码错误';
					if ($sendLog['time'] + ($vcode_time * 60) < $timestamp) return '验证码过期，请重新获取';
					db::update('log_vcode', 'status=1', "id='$sendLog[id]'");
					$safePassword = qscms::saltPwd($salt, $datas['password']);
					db::update('member', "safePassword='$safePassword'", "id='$uid'");
					return true;
				}
			}
		}
		return '操作失败';
	}
	public static function sendVcode2($email){
		if (!cfg::getBoolean('email', 'isGetPwd')) return '未启动短信方式';
		$hash = qscms::v('_G')->sys_hash;
		if ($time= db::one_one('log_email_vcode', 'time', "hash='$hash'", 'id DESC')) {
			$time = intval($time);
			$spaceTime = cfg::getInt('email', 'time_vcode');
			if (time() - $time < $spaceTime) return '请在'.($time + $spaceTime - time()).'秒后在发送';
		}
		if (!$item = db::one('member', '*', "email='$email'")) {
			$msg = '邮箱不存在1';
		}
		$var = qscms::v('_G');
		$vcode = string::getRandStr(6, 1);
		$msg = cfg::get('email', 'getPwdStr');
		$mail_server   = cfg::get('email', 'server');
		$mail_port     = cfg::get('email', 'port');
		$mail_username = cfg::get('email', 'username');
		$mail_email    = cfg::get('email', 'email');
		$mail_password = cfg::get('email', 'password');
		$msg_vcode_time = cfg::get('email', 'time_vcode');
		$timestamp = time();
		if ($sendLog = db::one('log_email_vcode', '*', "hash='$hash'", 'time desc')) {
			if (!$sendLog['status'] && $timestamp - $sendLog['time'] <= $msg_vcode_time) {
				return '对不起，激活码'.$msg_vcode_time.'秒内只能发送一次';
			}
		}
		$datas = array('nickname' => $item['xin'].' '.$item['name'].' ('.$item['email'].')', 'webName' => $var->webName, 'vcode' => $vcode, 'webUrl' => WEB_URL);
		$msg = qscms::replaceVars($msg, $datas);
		$smtp = new email($mail_server, $mail_port, true, $mail_username, $mail_password);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
		//$smtp->debug = TRUE;//是否显示发送的调试信息
		
		$rs = $smtp->sendmail($email, $mail_email, $var->webName.'密码找回', $msg, 'HTML');
		//print_r($rs);exit;
		if ($rs === true) {//发送成功
			$datas = array(
				'uid'    => $item['id'],
				'email'  => $email,
				'hash'   => $hash,
				'vcode'  => $vcode,
				'time'   => time()
			);
			if (db::insert('log_email_vcode', $datas)) {
				return true;
			}
			return '发送失败，请重试！';
		} else return '发送失败，请稍后重新尝试';
	}
	static function level($uid){
		$total = treeDB::childsCount_all('member', $uid);
		$name = '';
		if     ($total > 0 && $total < 9) $name = '1+';
		elseif ($total > 9 && $total < 99) $name = '10+';
		elseif ($total > 99 && $total < 999) $name = '100+';
		elseif ($total > 999 && $total < 9999) $name = '1K';
		elseif ($total > 9999 && $total < 99999) $name = '10K';
		elseif ($total > 99999 && $total < 999999) $name = '1M';
		elseif ($total > 999999 && $total < 9999999) $name = '10M';
		elseif ($total > 9999999) $name = '10M';
		$name = '';
		if (db::exists('member', "id='$uid' AND isManager=1")) $name .= ' 领袖';
		else $name = '领导'; 
		return $name;
	}
	static function sendVcode1($mobile){
		$timestamp = time::$timestamp;
		$msg_vcode_time = cfg::getInt('sms', 'time_vcode');
		$vcodeStr        = cfg::get('sms', 'vcodeStr');
		if (form::checkMobilephone($mobile)) {
			$next = true;
			if (!$item = db::one('member', '*', "mobile='$mobile'")) {
				$next = false;
				$msg = '该手机号码不存在';
			}
			$hash = qscms::v('_G')->sys_hash;
			if ($sendLog = db::one('log_vcode', '*', "hash='$hash'", 'time desc')) {
				if (!$sendLog['status'] && $timestamp - $sendLog['time'] <= $msg_vcode_time) {
					$next = false;
					$msg = '对不起，验证码'.$msg_vcode_time.'秒内只能发送一次';
				}
			}
			if ($next) {
				$salt = string::getRandStr(6, 1);//qscms::salt();
				$str = qscms::replaceVars($vcodeStr, array('webName' => qscms::v('_G')->webName, 'vcode' => $salt));
				$rs = message::sendVcode1($mobile, $salt);
				if ($rs === true){
					db::insert('log_vcode', array('hash' => $hash, 'uid'=> $item['id'], 'mobile' => $mobile, 'vcode' => $salt, 'time' => $timestamp));
					return true;
				} else {
					return $rs;
				}
			}
			if (!$next) return $msg;
		} else {
			return '很抱歉，您的手机号码格式错误，请联系管理员！';
		}
	}
	function sendVcode($datas){
		$isReg = cfg::get('web', 'isReg');
		if (!$isReg){
			return '不允许注册';
		}
		$var = qscms::v('_G');
		$start = $var->todayStart;
		$end   = $var->todayEnd;
		$everyDayRegCount = cfg::getInt('web', 'everyDayRegCount');//每日注册数量限制
		if ($everyDayRegCount > 0) {//大于0 代表限制注册数量
			$closeRegInfo = cfg::get('web', 'closeRegInfo1');
			$closeRegInfo || $closeRegInfo = '每天只能注册'.$everyDayRegCount.'个会员';
			if (db::dataCount('member', "regTime>='$start' AND regTime<='$end'") >= $everyDayRegCount) return $closeRegInfo;
			$notTime = $start + 9 * 3600;
			if (time() < $notTime) return '注册暂未开放，请耐心等待。';//'当天9：00后注册新用户';
		}
		
		$datas = form::get4($datas, array('mobile'));
		$timestamp = time::$timestamp;
		$msg_vcode_time = cfg::getInt('sms', 'time_vcode');
		$vcodeStr        = cfg::get('sms', 'vcodeStr');
		$mobile = $datas['mobile'];
		if (form::checkMobilephone($mobile)) {
			$next = true;
			if (db::exists('member', "mobile='$mobile'")) {
				$next = false;
				$msg = '该手机号码已经存在了';
			}
			$hash = qscms::v('_G')->sys_hash;
			if ($sendLog = db::one('log_vcode', '*', "hash='$hash'", 'time desc')) {
				if (!$sendLog['status'] && $timestamp - $sendLog['time'] <= $msg_vcode_time) {
					$next = false;
					$msg = '对不起，激活码'.$msg_vcode_time.'秒内只能发送一次';
				}
			}
			if ($next) {
				$salt = string::getRandStr(6, 1);//qscms::salt();
				$str = qscms::replaceVars($vcodeStr, array('webName' => qscms::v('_G')->webName, 'vcode' => $salt));
				$rs = message::sendVcode1($mobile, $salt);
				if ($rs === true){
					db::insert('log_vcode', array('hash' => $hash, 'mobile' => $mobile, 'vcode' => $salt, 'time' => $timestamp));
					return true;
				} else {
					return $rs;
				}
			}
			if (!$next) return $msg;
		} else {
			return '很抱歉，您的手机号码格式错误，请联系管理员！';
		}
	}
	function getFreezeTip(){
		$notBuyFreezeDay = cfg::getInt('web', 'notBuyFreezeDay');
		if ($notBuyFreezeDay > 0) {
			$__time = time();
			if ($this->_lastBuyTime == 0) {
				$actTime = $this->_regTime;
			} else {
				$actTime = $this->_lastBuyTime;
			}
			$buyTime = $actTime + $notBuyFreezeDay * 86400;
			if ($buyTime > $__time) {
				$t = $buyTime - $__time;
				if ($t < 7 * 86400) return '剩余捐助时间：'.time::times($t).'，如果届时还未提供捐助您的帐号将被冻结';
			}
		}
		return false;
	}
	public static function overTime(){//超时订单处理
		$time = time();
		//echo db::Sqlselect('shop_order', 'id,status', "status in (10,11,12) AND $time>endTime");exit;
		if ($list = db::select('shop_order', 'id,status', "status in (10,11,12) AND endTime<$time", false, 200)){
			foreach($list as $v){
				self::orderCancel1($v['id']);
			}
		}
	}
	public static function news($uid, $fuid = '', $name = '', $key, $val, $is_show = true){
		return db::insert('news', array(
			'uid' => ($uid ? $uid : ''),
			'fuid' => ($fuid ? $fuid : ''),
			'name' => ($name ? $name: ''),
			'key' => $key,
			'val' => $val,
			'is_show' => $is_show,
			'addTime' => time()
		));
	}
	public static function orderCancel1($id){
		$useCommit = true;
		$useCommit && db::autocommit();
		if ($one = db::one('shop_order', '*', "id='$id' AND status IN('10', '11', '12')", false, $useCommit)) {
			
			if ($one['status'] == 12){
				db::update('shop_order', "status='21'", "id='$id'");
				$useCommit && db::commit(true);
				return true;
			}else{
				db::update('member', "status=1,is_status=1,dinfo='超时付款'", "id='$one[fuid]'");
				if (db::update('shop_order', "status='13',is_cs=1", "id='$id'")) {
					$error = false;
					$error || $error = !db::update('shop', "yuMoney=yuMoney+$one[money],status='101'", "id='$one[fsid]'");
					if (!db::exists('shop_order', "fuid='$one[fuid]' AND (status=11 or status=12 or status=20 or status=21)")){
						
						/**
						 * 以下代码有问题 为啥要改成300？别个已经完成了喃 我艹 禁用了之后还会去匹配吗 改鸡巴
						 */
						/*$list1 = db::select('shop', 'id', "uid='$one[fuid]' AND status<>300");
						if ($list1){
							foreach($list1 as $v){
								db::update('shop', 'status=300', "id='$v[id]'");	
							}
						}*/
						$list = db::select('shop_order', 'id', "(fuid='$one[fuid]' OR tuid='$one[fuid]') AND status!=21 AND status!=13");
						if ($list){
							foreach($list as $v){
								self::orderCancel2($v['id'], false);
							}
						}
					}
					$error || $error = !db::update('shop', "yuMoney=yuMoney+$one[money],yuMoney1=yuMoney1+$one[money],status='201'", "id='$one[tsid]'");
					if ($useCommit) {
						if (!$error) db::commit(true);
						else db::rollback(true);
					}
					return true;
				}
			}
			
		}	
		$useCommit && db::autocommit(false);
	}
	public static function orderCancel2($id, $setCommit = true){
		$useCommit = true;
		if ($setCommit) {
			$useCommit && db::autocommit();
		}
		if ($one = db::one('shop_order', '*', "id='$id' AND status IN('10', '11', '12')", false, $useCommit)) {
			if ($one['status'] == 12){
				db::update('shop_order', "status='21'", "id='$id'");
				if ($setCommit) {
					$useCommit && db::commit(true);
				}
				return true;
			}else{
				if (db::update('shop_order', "status='13'", "id='$id'")) {
					$error = false;
					$error || $error = !db::update('shop', "yuMoney=yuMoney+$one[money],status='101'", "id='$one[fsid]'");
					$error || $error = !db::update('shop', "yuMoney=yuMoney+$one[money],yuMoney1=yuMoney1+$one[money],status='201'", "id='$one[tsid]'");
					if ($setCommit) {
						if ($useCommit) {
							if (!$error) db::commit(true);
							else db::rollback(true);
						}
					}
					return true;
				}
			}
			
		}	
		if ($setCommit) {
			$useCommit && db::autocommit(false);
		}
	}
	public static function contacts($datas){
		$datas = form::get4($datas, array(
			'name', 'isMember', 'theme', 'email', 'mobile', 'message', 'skype'
		));
		if (!vcode3::check()) return '验证码错误';
		if (!form::checkEmail($datas['email'])) return '邮箱格式错误';
		if ($datas['message'] && $datas['name'] && $datas['theme']){
			$arr = array(
				'name' => $datas['name'], 
				'isMember' => ($datas['isMember'] ? 1 : 0), 
				'theme' => $datas['theme'],
				'email' => $datas['email'],
				'mobile' => $datas['mobile'],
				'message' => $datas['message'],
				'skype' => $datas['skype'],
				'addTime' => time()
			);
			db::insert('contacts', $arr); return true;
		}
		return true;
	}
	static function freeze($uid){
		db::update('member', "status='1',freezeCount=freezeCount+1", "id='$uid'");
	}
	static function freezePunish($uid){
		$count = db::one_one('member', 'freezeCount', "id='$uid'");
		if ($count == 1) {//归还本金
			db::update('shop', 'lixi=allMoney,lixiYu=lixi', "type='0' AND uid='$uid' AND status IN(100,101,102)");
		} elseif ($count == 2) {//冻结所有资金
			db::update('shop', "isFreeze='1'", "uid='$uid'");
		} else {//封号
			db::update('member', "status='1'", "id='$uid'");
		}
	}
}
?>