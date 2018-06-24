<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class member_center extends ext_base{
	//private $vars, $readKeys, $writeKeys;
	const FL_TYPE = 1;
	const FL_TYPE1 = 5;
	public function __construct($uid, $password = '', $isAdmin = false){
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
			/*
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
			*/
			if (!$this->m_keys){//如果没有keys 添加一个
				$keys	   = string::getRandStr(40);
				db::update('member', array('keys' => $keys), "id='$this->m_id'");
				$this->m_keys = $keys;
			}
			
			if ($this->m_vip_endTime < time()){//套餐过期
				db::update('member', 'vip=0,vip_endTime=0', "id='$this->m_id'");
				$this->m_vip = 0;
				$this->m_vip_endTime = 0;
			}
			$this->bl = array();	
			if ($this->m_bl) $this->bl = unserialize($this->m_bl);
			$this->cashMoney = round(db::one_one('pay_cash', 'SUM(money)', "uid='$this->m_id' AND status=1"), 2);
			$this->isCash1 = false;
			$this->isCash = false;
			if ($this->m_card && $this->m_name && $this->m_alipay && $this->m_back_name && $this->m_back_card && $this->m_back_add) $this->isCash = true;//是否填写了提现信息 提现判断
			if ($this->m_wxid) $this->isCash1 = true;
			if ($this->m_wxname) $this->wxname = qscms::stripslashes($this->m_wxname);
			else $this->wxname = '';
			$this->status = true;
		} else {
			$this->status = false;
		}
	}
	public function isOff($type){
		if (db::exists('pay_off', "uid='$this->m_id' AND type='$type'")) return false;//如果在pay_off表里面存在 代表商户关闭了该接口
		else return true;
	}
	public function buyVip($id){//购买套餐
		if ($item = db::one('pay_bl', '*', "id='$id' AND isOff=1")){
			if ($this->m_vip > 0){
				$oldMoney = db::one_one('pay_bl', 'money', "id='$this->m_vip'");
				if ($oldMoney >= $item['money']){
					return '不能购买小于当前级别接口套餐';	
				}
			}
			if ($item['money'] > 0){
				if ($this->m_money >= $item['money']){
					db::autocommit();
					if ($this->addMoney(-$item['money'], 0, '购买'.$item['name'], 1)){
						$time = time();
						$vip_endTime = $time + (86400 * 365);
						if(db::update('member', "vip='$item[id]',vip_endTime=$vip_endTime", "id='$this->m_id'")){
							db::commit(true);
							return true;	
						}
					}else{
						db::rollback(true);	
						return '购买失败';
					}
					db::autocommit(false);
				}
			}	
		}
		return '操作失败';
	}
	public function wx_relieve($datas){//解除微信绑定
		$datas = qscms::filterArray($datas, array('code'), true);
		$rs = member_base::checkVcode($this->m_mobile, $datas['code'], false);
		if ($rs !== true) return $rs;
		db::update('member', "wxid='',wxname='',wximg=''", "id='$this->m_id'");
		return true;
	}
	public function qrcode_url(){
		$member = $this;
		if (empty($member) || $member->status == false || $member->m_wxid) return false;
		$auth = false;
		//echo 123123;exit;
		$weObj = new weixin($auth);
		$access_token = $weObj->get_token($auth);
		
		//if (!file_exists($qrcodeDir)){//生成推荐二维码
			$json_arr = array(
				'action_name' => 'QR_LIMIT_SCENE',
				'action_info' => array('scene' => array('scene_id' => $member->m_id))
			);
			$data = json_encode($json_arr);
			//return $data;
			$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
			//$res_json = $auth -> curl_grab_page($url, $data);
			$res_json = winsock::curl($url, $data);
			$json = json_decode($res_json);
			if (!empty($json->ticket)){
				$json->ticket = iconv(ENCODING, 'GBK', $json->ticket);
				$ticket_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($json->ticket);
				return $ticket_url;
			}
		//}
		return false;	
	}
	public function edit_mobile($datas){
		$datas = qscms::filterArray($datas, array('code', 'mobile', 'code1'), true);
		$rs = member_base::checkVcode($this->m_mobile, $datas['code'], false);
		if ($rs !== true) return '旧'.$rs;
		$rs = member_base::checkVcode($datas['mobile'], $datas['code1']);
		if ($rs !== true) return '新'.$rs;
		db::update('member', array('mobile' => $datas['mobile']), "id='$this->m_id'");
		return true;
	}
	public function editInfo($datas){
		$datas = qscms::filterArray($datas, array('qq', 'name', 'card', 'alipay', 'back_name', 'back_card', 'back_add', 'sitename', 'siteurl'), true);
		/*以前填了的信息资料不能修改 处了联系方式和站点地址*/
		
		//if ($datas['name'] && !form::checkRealname($datas['name'])) return '请填写中文姓名';	
		//if ($datas['name'] && !form::checkCnId($datas['card'])) return '身份证格式错误';	
		//if ($datas['alipay'] && !form::checkAlipay($datas['alipay'])) return '支付宝账号格式错误';
		//if ($datas['back_name'] && !$datas['back_name']) return '请填写收款银行';
		//if ($datas['back_card'] && !form::checkBankId($datas['back_card'])) return '银行卡格式错误';
		//if ($datas['back_add'] && !$datas['back_add']) return '请填写银行开户地址';
		if ($datas['qq'] && !form::check_qq($datas['qq'])) return '联系QQ格式错误';
		
		if ($datas['siteurl']){
			$datas['siteurl'] = trim($datas['siteurl']);
			if (substr($datas['siteurl'], 0, 7) == 'http://' || substr($datas['siteurl'], 0, 8) == 'https://'){
				if (substr($datas['siteurl'], -1, 1) == '/'){//最后一个字符/ 就去掉
					$datas['siteurl'] = substr($datas['siteurl'], 0 ,strlen($datas['siteurl'])-1);
				}
			}else return '网址地址请以http://或https://开头';
		}
		if (db::update('member', $datas, "id='$this->m_id'")) return true;
		else return '操作失败';
		return $rs;
	}
	public function transfer($datas){
		
		$cashNum = cfg::getInt('pay', 'transferNum');//每日提现次数
		$todayStart = time::$todayStart;
		
		if ($cashNum && db::dataCount('pay_cash', "uid=$this->m_id AND is_transfer=1 AND addTime>$todayStart") >= $cashNum) return '已到达今日转账上限';
		$datas = qscms::filterArray($datas, array('money', 'name', 'type', 'code', 'alipay', 'back_name', 'back_card', 'back_add'), true);
		$minMoney = member_base::minMoney();
		$datas['money'] = (float)$datas['money'];
		if ($datas['money'] > $this->m_money) return '余额不足';
		if ($datas['money'] < $minMoney) return '转账金额必须大于'.$minMoney;
		if (!in_array($datas['type'], array(/*0, */1))) return '没有该转账方式'; //0支付宝 1银行卡
		
		if (!form::checkRealname($datas['name'])) return '请填写中文姓名';	
		
		
		
		switch($datas['type']){
			case 0://支付宝
				if (!$datas['alipay'] || !form::checkAlipay($datas['alipay'])) return '支付宝账号格式错误';
			case 1://银行卡
				if (!$datas['back_name']) return '请填写收款银行';
				if (!$datas['back_card'] || !form::checkBankId($datas['back_card'])) return '银行卡格式错误';
				if (!$datas['back_add']) return '请填写银行开户地址';
			break;
		}
		$maxMoney = cfg::getMoney('pay', 'maxMoney1');
		if ($maxMoney > 0){
			if ($datas['money'] > $maxMoney) return '超出单笔转账最高金额';
		}
		$rs = member_base::checkVcode($this->m_mobile, $datas['code']);
		if ($rs !== true) return $rs;
		$blMoney = 0;
		$cashMinMoney = cfg::getMoney('pay', 'cashMoney');
		if ($datas['money'] <= $cashMinMoney){
			$blMoney = cfg::getMoney('pay', 'moneyCash');
			$blCash = 0;
		}else{
			$blCash  = member_base::blCash();//手续费比例
			$blMoney = $datas['money'] * $blCash;
		}
		
		db::autocommit();
		
		$blCash = member_base::blCash();//手续费比例
		$insert = array(
			'uid' => $this->m_id,
			'cashMoney' => $datas['money'],//提现金额
			'money'  => ($datas['money'] - $blMoney),//实际到账金额
			'sxf'    => $blCash,
			'type'   => $datas['type'],
			'is_transfer' => 1,//转让
			'status' => 0,
			'name'   => $datas['name'],//$this->m_name,
			'card'   => '',//$this->m_card,
			'addTime' => time()
		);
		switch($datas['type']){
			case 0://支付宝
				$insert['alipay'] = $datas['alipay'];
			break;
			case 1://银行卡
				$insert['back_name'] = $datas['back_name'];
				$insert['back_card'] = $datas['back_card'];
				$insert['back_add'] = $datas['back_add'];
			break;
			default:
				db::autocommit(false);
				return '请选择转账类型';
			break;	
		}
		if (!$this->addMoney(-$datas['money'], 0, '提现', 11)){
			db::autocommit(false);
			return '余额不足';
		}
		if (!db::insert('pay_cash', $insert)){
			db::rollback(true);
			return '未知错误';	
		}
		db::commit(true);
		if (cfg::get('web', 'sms') == 1){//阿里大于
			message::cash_sendOne($this->m_mobile);
		}else{
			message::sendOne($this->m_mobile, '尊敬的'.qscms::v('_G')->webName.'用户，您的账户于'.date('d日H时i分').'有一笔提现申请，如您本人操作请忽略该次提醒。');
		}
		return true;
	
	}
	public function cash($datas){//提现
		if (!$this->isCash && !$this->isCash1) return '请先填写提现信息';
		$cashNum = cfg::getInt('pay', 'cashNum');//每日提现次数
		$todayStart = time::$todayStart;
		if ($this->m_cashT){
			if ($this->m_cashT == -1){//不限制提现次数
				$cashNum = '';
			}else{
				$cashNum = $this->m_cashT;
			}
		}
		
		if ($member->m_cashNum > 0) $cashNum = $member->m_cashNum;
		
		if ($cashNum && db::dataCount('pay_cash', "uid=$this->m_id AND addTime>$todayStart") >= $cashNum) return '已到达今日提现上限';
		$datas = qscms::filterArray($datas, array('money', 'type', 'code'), true);
		$minMoney = member_base::minMoney();
		$datas['money'] = (float)$datas['money'];
		if ($datas['money'] > $this->m_money) return '余额不足';
		if ($datas['money'] < $minMoney) return '提现金额必须大于'.$minMoney;
		if (!in_array($datas['type'], array(0, 1, 2))) return '没有该提现方式'; //0支付宝 1银行卡 2微信提现
		//if ($this->m_id != '11073' || $this->m_id != '11069'){
		
		//}
		switch($datas['type']){
			case 0://支付宝
			case 1://银行卡
				if (!$this->isCash) return '请先填写支付宝和银行卡才能使用该方式提现';
			break;
			case 2://微信
				if (!$this->isCash1) return '请先绑定微信号才能使用该方式提现';
			break;	
		}
		$maxMoney = cfg::getMoney('pay', 'maxMoney');
		if ($maxMoney > 0){
			if ($datas['money'] > $maxMoney) return '超出单笔提现最高金额';
		}
		$rs = member_base::checkVcode($this->m_mobile, $datas['code']);
		if ($rs !== true) return $rs;
		
		$blMoney = 0;
		$cashMinMoney = cfg::getMoney('pay', 'cashMoney');
		if ($datas['money'] <= $cashMinMoney){
			$blMoney = cfg::getMoney('pay', 'moneyCash');
			$blCash = 0;
		}else{
			$blCash  = member_base::blCash();//手续费比例
			$blMoney = $datas['money'] * $blCash;
		}
		
		db::autocommit();
		$insert = array(
			'uid' => $this->m_id,
			'cashMoney' => $datas['money'],//提现金额
			'money'  => ($datas['money'] - $blMoney),//实际到账金额
			'sxf'    => $blCash,
			'type'   => $datas['type'],
			'status' => 0,
			'name'   => $this->m_name,
			'card'   => $this->m_card,
			'addTime' => time()
		);
		switch($datas['type']){
			case 0://支付宝
				$insert['alipay'] = $this->m_alipay;
			break;
			case 1://银行卡
				$insert['back_name'] = $this->m_back_name;
				$insert['back_card'] = $this->m_back_card;
				$insert['back_add'] = $this->m_back_add;
			break;
			case 2:
				$insert['wxid'] = $this->m_wxid;
				$insert['wxname'] = $this->m_wxname;
				$insert['wximg'] = $this->m_wximg;
			break;
			default:
				db::autocommit(false);
				return '请选择提现类型';
			break;	
		}
		if (!$this->addMoney(-$datas['money'], 0, '提现', 11)){
			db::autocommit(false);
			return '余额不足';
		}
		if (!db::insert('pay_cash', $insert)){
			db::rollback(true);
			return '未知错误';	
		}
		db::commit(true);
		if (cfg::get('web', 'sms') == 1){//阿里大于
			message::cash_sendOne($this->m_mobile);
		}else{
			message::sendOne($this->m_mobile, '尊敬的'.qscms::v('_G')->webName.'用户，您的账户于'.date('d日H时i分').'有一笔提现申请，如您本人操作请忽略该次提醒。');
		}
		return true;
	}
	public static function confirmPay($sn, $trade_no){//确认充值  
		//订单号，类型，接口返回的交易号
		$time = time();
		db::autocommit();
		if ($userInfo = db::one('pay_payment', '*', "sn='$sn' AND status=0", false, true)){
			$set  = '';//用户充值 要给商户把比例和 最终拿的钱 加进去
			if ($userInfo['uid']){//没有UID就是测试数据
				$m = new member_center($userInfo['uid']);
				if ($userInfo['types'] == 0 || $userInfo['types'] == 3){
					$bl = 0;//初始默认 0 
					$systemMoney = 0;//初始化系统收入
					if (!empty(pay::$bdpay[$userInfo['type']])) $userInfo['type'] = 'bdpay';//如果是这些类型 就按百度网银比例算
					
					if ($blArr = $m->bl){//查看是否后台设置了单独比率
						if (!empty($blArr[$userInfo['type']])){//设置了这个比率
							$bl = $blArr[$userInfo['type']];
							if ($bl < 0 || $bl >= 1){//费率不能低于0% 大于等于100%
								$bl = 0;
								pay::log('member->confirmPay ' .$userInfo['uid'].'用户后台独立费率设置超过1或者低于0,请修改。（现已跳过后台设置比例，按套餐比例计算）');
								//db::rollback(true);		
							}
						}
					}
					
					if (!$bl && $vid = $m->m_vip){//购买了套餐的
						if ($pay_bl = db::one('pay_bl', '*', "id='$vid' AND isOff=1")){//获取商户购买套餐的 比例
							if (!empty($pay_bl[$userInfo['type']])) $bl = $pay_bl[$userInfo['type']];
						}
					}
					
					if (!$bl && $pay_bl = db::one('pay_bl', '*', "isOff=1 AND money=0")){//获取免费套餐的 比例
						if (!empty($pay_bl[$userInfo['type']])) $bl = $pay_bl[$userInfo['type']];
					}
					//$bl || $bl = 0.05;//固定一个默认比例 5%
					
					/*系统赚的给商户上级提成 */
					if ($userInfo['money'] >= 1){//至少要0.1元才扣商户金额
						$systemMoney = $userInfo['money'] * $bl;//系统赚的
						if ($systemMoney){
							$banckMoney = $m->fc($userInfo, $systemMoney);//给上级反了多少钱
							$systemMoney -= $banckMoney;
						}
						if ($systemMoney > 0){//如果是提成金额超过100%而出现负数 会出错
							$money = $userInfo['money'] - $systemMoney;//返给商户的
						}else{
							$money = $userInfo['money'] - $userInfo['money'] * $bl;
						}
						if ($li = $money - round($money, 2) > 0) $systemMoney += $li;//把第三位小数金额也计算到 系统利润里面去 
						if ($systemMoney != 0){
							if (!db::insert('system_log', array(
								'uid'      => $userInfo['uid'],
								'sn'       => $userInfo['sn'],
								'allMoney' => $userInfo['money'],
								'money'    => $systemMoney,
								'bl'       => $bl,
								'addTime'  => $time 
							))){//插入系统收入表
								pay::log('member->confirmPay ' .$sn.'添加系统收入失败');
							}
						}
					}else{
						$bl = 0;
						$money	 = $userInfo['money'];
					}
					if (!$m->addMoney($money, 0, '用户充值 反馈单号'.$trade_no, 9, $sn)){//商户API 用户充值
						pay::log('member->confirmPay ' .$sn.'用户充值商户加钱失败');
						db::rollback(true);
						return '未知错误1';
					}
					$set = ",bl='$bl',money1=$money";
					
					if ($userInfo['types'] == 0 && $userInfo['notify_url']){
						$datas = array(
							'pid'          => $userInfo['uid'],//商户ID
							'out_trade_no' => $userInfo['out_trade_no'],//用户提交的订单号
							'trade_no'     => $sn,//返回系统生成的交易成功订单号 不是微信支付宝生成的订单号
							'subject'      => $userInfo['subject'],
							'money'  	   => $userInfo['money'],
							'type'         => $userInfo['type'],
							'trade_status' => 'SUCCESS',
							'sign_type'    => 'MD5'
						);
						$datas['sign'] = string::keymd5Sign($datas, $m->m_keys);
						
						
						for($ii = 0; $ii < 3; $ii++){
							$html = winsock::open($userInfo['notify_url'].'?'.string::createLinkstring($datas));
						  	$html || $html = file_get_contents($userInfo['notify_url'].'?'.string::createLinkstring($datas));
							if (strtoupper($html) == 'SUCCESS'){
								break;	
							}
						}
					}
				}else{
					if (!$m->addMoney($userInfo['money'], 0, '充值成功 反馈单号'.$trade_no, 10, $sn)){
						db::rollback(true);
						pay::log('member->confirmPay ' .$sn.'商户充值商户加钱失败');
						return '未知错误2';		
					}//商户充值
				}
			}
			if (!db::update('pay_payment', "status=1, trade_no='$trade_no',payTime='$time'".$set, "sn='$sn' AND status=0")){
				db::rollback(true);
				return '未知错误3';	
			}
		}else{
			return '没有找到该订单';	
		}
		db::commit(true);
		
	}
	
	public static function fcList(){
		static $list;
		if ($list) return $list;
		$list = db::select('fc', '*', '', 'id');
		return $list;
	}
	public static function fcCount(){
		static $count;
		if ($count) return $count;
		$count = db::dataCount('fc');
		return $count;	
	}
	public function fc($sn, $money){//系统赚取的 金额 * 100
		
		if ($money < 1) return false; 
		$item = '';
		if (is_array($sn)) $item = $sn;
		if (!cfg::get('fc', 'status')) return false;//没开启分成
		$list = self::fcList();
		$Nm = $Nc = '';
		$isN = cfg::get('fc', 'n');//是否开启无限提成
		if ($isN){
			$Nm = cfg::get('fc', 'm');//无限金额提成比例
			$Nc = cfg::get('fc', 'c');//无限积分提成比例
		}
		if (!$list && !$Nc && !$Nm) return false; //有限级别 和 无限级别  都没有的话 不提成
		$p_member = treeDB::parents('member', $this->m_id, 'id,money,credit,agent', '');
		if (!$p_member) return false;//没有上级
		$item || $item = db::one('pay_payment', '*', "sn='$sn' AND uid='$this->m_id'");//获取该订单
		if (!$item) return false;
		
		$money  = $money * cfg::get('fc', 'm_bl');
		$credit = $money * cfg::get('fc', 'c_bl');
		$returnMoney = 0;
		/*设置的级别提成*/
		foreach ($list as $k => $v){
			if (!empty($p_member[$k]['id']) && $p_member[$k]['agent'] == 1){//要有这一级用户才提成 还必须是代理才有提成
			
				$m = $c = 0;//分成金额 积分
				if ($money  > 0 && $v['m'] > 0) $m = round($v['m'] * $money,  2);
				if ($credit > 0 && $v['c'] > 0) $c = round($v['c'] * $credit, 0);//积分不要小数(四舍五入)
				
				if ($m > 0 || $c > 0){
					self::uid_addMoney($p_member[$k]['id'], $m, $c, '提成', 5);
					$insert = array(
						'uid'      => $p_member[$k]['id'],
						'fuid'     => $item['uid'],
						'sn'       => $item['sn'],
						'pay_id'   => $item['id'],
						'money'    => $m,//提成金额
						'credit'   => $c,
						'fc_m'     => $v['m'],//金额提成的比例 
						'fc_c'     => $v['c'],
						'blMoney'  => $money,//拿来算提成的总额
						'bl'       => $item['bl'],//系统收取的费率
						'payMoney' => $item['money'],//支付的金额
						'addTime'  => time(),//提成时间
						
					);
					db::insert('fc_log', $insert);
					$returnMoney += $m;
				}
				//echo $m.'---------'.$c.'<br />';
			}
		}
		/*无限级别提成*/
		if ($isN){
			$m_count = count($p_member);//上级用户数量
			$fc_count = count($list);//有限级分成数量
			if ($m_count > $fc_count){//判断上级数量 大于 有限级别数量
				$m = $c = 0;//分成金额 积分
				if ($money  > 0 && $Nm > 0) $m = round($Nm * $money,  2);
				if ($credit > 0 && $Nc > 0) $c = round($Nc * $credit, 0);//积分不要小数(四舍五入)
				if ($m > 0 || $c > 0){
					for($i = $fc_count; $i < $m_count; $i++){
						if ($p_member[$i]['agent'] == 1){//上级是否是代理
							self::uid_addMoney($p_member[$i]['id'], $m, $c, '提成', 5);
							$insert = array(
								'uid'      => $p_member[$i]['id'],
								'fuid'     => $item['uid'],
								'sn'       => $item['sn'],
								'pay_id'   => $item['id'],
								'money'    => $m,
								'credit'   => $c,
								'fc_m'     => $Nm,//金额提成的比例 
								'fc_c'     => $Nc,
								'bl'       => $item['bl'],//系统收取的费率
								'blMoney'  => $money,//拿来算提成的总额
								'payMoney' => $item['money'],//支付的金额
								'addTime'  => time(),//提成时间
								
							);
							db::insert('fc_log', $insert);
							$returnMoney += $m;
						}
					}
				}
			}
		}
		return $returnMoney;
	}
	public static function uid_addMoney($uid = '', $money = 0, $credit = 0, $remark = '', $type = 0){
		if (is_array($uid)) $m = $uid;
		else $m = db::one('member', 'id,money,credit', "id='$uid'");
		if (!$m) return '操作失败';
		
		if ($m['money']  + $money < 0) return '账户余额不足';
		if ($m['credit'] + $credit < 0) return '账户积分不足';
		if (db::update('member', "money=money+$money,credit=credit+$credit", "id='$m[id]'")){
			return db::insert('log_money', array('uid' => $m['id'], 'type' => $type, 'money' => $money, 'credit' => $credit, 'remark' => $remark, 'time' => time::$timestamp));
		}
		return false;
	}
	public function addMoney($money = 0, $credit = 0, $remark = '', $type = 0, $sn = ''){
		$credit || $credit = 0;
		$money  || $money  = 0;
		//if ($this->m_money + $money < 0) return false;//return '账户余额不足';
		//if ($this->m_credit + $credit < 0) return false;//return '账户积分不足';
		/*不限制 金额积分小于0的
		*/
		$set = '';
		/*
		
		if ($type == 10){
			$set.= ",addMoney=addMoney+$money";
		}
		if ($type == 11){
			$set .= ",cashMoney=cashMoney+".(-$money);//负负的正 - -	
		}
		*/
		if (db::update('member', "money=money+$money,credit=credit+$credit".$set, "id=$this->m_id")){
			$this->m_money += $money;
			$this->m_credit += $credit;
			return db::insert('log_money', array('uid' => $this->uid, 'type' => $type, 'money' => $money, 'credit' => $credit, 'sn' => $sn, 'remark' => $remark, 'time' => time::$timestamp));
		}
		return false;
	}
	
	
	
	
	
	
	
	public function getSmsCode($datas){
		if ($datas['mobile'] && form::checkMobilephone($datas['mobile'])){
			$ipint = qscms::ipint();
			if ($datas['verify_num'] && db::exists('scroll_verify', "ip='$ipint' AND code='$datas[verify_num]'")){
				if (!db::exists('member', "mobile='$datas[mobile]'")){
					$msg = '您的验证码是：{vcode}。请不要把验证码泄露给其他人。如非本人操作，可不用理会！';
					//$msg = '您的手机号：{mobile}，修改手机号码验证码：{vcode}，请不要把验证码泄露给其他人。如非本人操作，可不用理会！';
					$rs = member_base::message($datas['mobile'], $msg);
					if ($rs === true) return true;
					elseif ($rs) return $rs;
					else return '验证码发送失败,请联系客服';
				} else return '手机号码已被使用';
			} elseif ($datas['verify_num']) return '推动验证码失效,请刷新页面重试';
			else return '拖动验证未能通过，请按住滑块左侧向右滑到底';
		} else return '手机格式不正确';	
	}
	public function bandPhone($datas){//修改手机号 或设置初始密码
		if ($datas['mobile'] && form::checkMobilephone($datas['mobile'])){
			if (!db::exists('member', "mobile='$datas[mobile]'")){
				if ($datas['yanzhengma']){
					$rs = member_base::checkVcode($datas['mobile'], $datas['yanzhengma']);
					if ($rs === true && !$this->m_password) $rs = $this->set_password($datas['password']);
					if ($rs === true) {
						db::update('member', "mobile='$datas[mobile]'", "id='$this->m_id'");
						return true;
					} else return $rs;
				} else return '验证码错误';
			} else return '手机号码已被使用';
		} else return '手机格式不正确';
	}
	
	public function set_password($password){
		if (!is_array($password)) $datas['password'] = $password;
		else $datas = $password;
		$datas = qscms::filterArray($datas, array('password'), true);
		$rs = form::checkData($datas, array(
			'null' => array('password'),
			'minLength' => array(
				'password' => 6 
			),
			'maxLength' => array(
				'password' => 16
			)
		), array(
			'password'    => '密码'
		));
		if ($rs === true){
			$salt = $this->m_salt;
			if (!$salt){
				$salt = qscms::salt();
			}
			//$oldpwd  = qscms::saltPwd($salt, $datas['oldPassword']);
			//if ($oldpwd != $this->m_password) return '旧密码错误';
			$pwd = qscms::saltPwd($salt, $datas['password']);
			db::update('member', "password='$pwd',salt='$salt'", "id='$this->m_id'");
			return true;
		}else return $rs;
	}
	public function edit_password($datas){
		$datas = qscms::filterArray($datas, array('oldPassword', 'password'), true);
		$rs = form::checkData($datas, array(
			'null' => array('oldPassword', 'password'),
			'minLength' => array(
				'oldPassword' => 6,
				'password' => 6 
			),
			'maxLength' => array(
				'oldPassword' => 16,
				'password' => 16
			)
		), array(
			'oldPassword' => '旧密码',
			'password'    => '新密码'
		));
		if ($rs === true){
			$salt = $this->m_salt;
			$oldpwd  = qscms::saltPwd($salt, $datas['oldPassword']);
			if ($oldpwd != $this->m_password) return '旧密码错误';
			$salt = qscms::salt();
			$pwd = qscms::saltPwd($salt, $datas['password']);
			db::update('member', "password='$pwd',salt='$salt'", "id='$this->m_id'");
			return true;
		}else return $rs;
	}
	public function setPwd($datas){
		if (!$this->isAdmin) {
			$datas = qscms::filterArray($datas, array('old_password', 'new_password', 'confim_password'), true);
			if ($datas['old_password'] && $datas['new_password'] && $datas['confim_password']) {
				if ($datas['new_password'] == $datas['confim_password']) {
					$len = mb_strlen($datas['new_password']);
					if ($len < 6 || $len > 16) return '密码长度为6~16个字符';
					if (qscms::saltPwdCheck($this->m_salt, $datas['old_password'], $this->m_password)) {
						$salt = qscms::salt();
						$pwd = qscms::saltPwd($salt, $datas['new_password']);
						$this->m_salt = $salt;
						$this->m_password = $pwd;
						db::update('member', array('salt' => $salt, 'password' => $pwd), "id='$this->m_id'");
						qscms::setcookie('memberAuth', $this->m_id.'|'.$this->m_password);
						return true;
					}
					return '原密码错误';
				}
				return '两次密码输入不一致';
			}
			return '请填写完整';
		} else {
			$datas = qscms::filterArray($datas, array('password'), true);
			$len = mb_strlen($datas['password']);
			if ($len < 6 || $len > 16) return '密码长度为6~16个字符';
			$salt = qscms::salt();
			$pwd = qscms::saltPwd($salt, $datas['password']);
			$this->m_salt = $salt;
			$this->m_password = $pwd;
			db::update('member', array('salt' => $salt, 'password' => $pwd), "id='$this->m_id'");
			return true;
		}
	}
	public function setPwdToForm(){
		if (form::hash()) {
			return $this->setPwd($_POST);
		}
		return false;
	}
	public function getPwdToEmail(){
		if ($this->status) {
			if (cfg::getBoolean('email', 'isGetPwd')) {
				$code0 = md5(string::getRandStr(32));
				$code1 = qscms::authcode($this->uid.'|'.$code0);
				$webName = cfg::get('sys', 'webName');
				$mail_server   = cfg::get('email', 'server');
				$mail_port     = cfg::get('email', 'port');
				$mail_username = cfg::get('email', 'username');
				$mail_email    = cfg::get('email', 'email');
				$mail_password = cfg::get('email', 'password');
				$str = cfg::get('email', 'getPwdStr');
				$url = qscms::getUrl('/member/resetpwd?code='.urlencode($code1));
				$arr = array(
					'nickname' => $this->m_nickname,
					'url'      => $url,
					'webName'  => $webName,
					'webUrl'   => WEB_URL.'/'
				);
				$str = qscms::replaceVars($str, $arr);
				db::update('member', array('code' => $code0), "id='$this->m_id'");
				$smtp = new email($mail_server, $mail_port, true, $mail_username, $mail_password);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
				//$smtp->debug = TRUE;//是否显示发送的调试信息
				$smtp->sendmail($this->m_email, $mail_email, $webName.'密码找回', $str, 'HTML');
				return true;
			}
			return '系统禁用邮箱找回密码';
		}
		return '用户不存在';
	}
	public function ltMoney($money){
		return $this->c_money < $money;
	}
	public function lteMoney($money){
		return $this->c_money <= $money;
	}
	public function gtMoney($money){
		return $this->c_money > $money;
	}
	public function gteMoney($money){
		return $this->c_money >= $money;
	}
	public function checkVcode($datas){
		if ($this->status) {
			extract(qscms::filterArray($datas, array('mobilephone', 'vcode'), true));
			if ($this->isActive()) $datas = array('uid' => $this->uid, 'mobilephone' => $this->m_mobilephone, 'vcode' => $vcode, 'status' => 0);
			else $datas = array('uid' => $this->uid, 'mobilephone' => $mobilephone, 'vcode' => $vcode, 'status' => 0);
			if (db::exists('log_vcode', $datas)) {
				db::update('log_vcode', array('status' => 1), sql::getWhere($datas));
				if (!$this->isActive()) {
					$this->m_mobilephone = $mobilephone;
					$this->m_verifyMobile = 1;
					db::update('member', array('mobilephone' => $mobilephone, 'verifyMobile' => 1), "id='$this->uid'");
				}
				return true;
			}
			return '验证码错误';
		}
		return '用户无效，或者未登录';
	}
	
	
}
?>