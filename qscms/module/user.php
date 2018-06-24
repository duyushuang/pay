<?php



(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');

qscms::gotoUrl('/users/');
exit;
!qscms::defineTrue('INSTALL') && qscms::gotoUrl('/install/');



$noLoginArr = array('reg', 'login', 'regcode', 'forgot', 'forgotcode', 'cs');//登录不允许访问



if (!$member && !in_array($var->p1, $noLoginArr)){



	qscms::gotoUrl('/user/login.html');



}



if ($member && in_array($var->p1, $noLoginArr)){



	qscms::gotoUrl('/user/index.html');



}



template::addPath($var->p0, $var->p0);



$op = $var->p1;



$var->tplName = $op;



$arr = array('status' => false, 'msg' => '', 'is_login' => $member ? true : false, 'url' => '');



$time = time();



$list = array();



$multipage = '';



switch ($op){



	case 'index'://个人中心首页



		$todayEnd = time::$todayEnd;



		$todayStart = time::$todayStart;



		$cashMoney = round(db::one_one('pay_cash', 'SUM(cashMoney)', "status=1 AND uid='$member->m_id'"));



		$allPayMoney = round(db::one_one('pay_payment', 'SUM(money)', "uid='$member->m_id' AND types=0 AND status=1"), 2);



		$dayTotal = (int)db::dataCount('pay_payment', "uid='$member->m_id' AND types=0");



		$dayMoney = round(db::one_one('pay_payment', 'SUM(money)', "uid='$member->m_id' AND types=0 AND status=1 AND addTime>=$todayStart"), 2);// 没扣出费率  money1 就是扣除费率的



	break;



	case 'qrcode':



		$dir = d(qscms::getCfgPath('/system/imgRoot').'phpqrcode/');



		$url = u(qscms::getCfgPath('/system/imgRoot').'phpqrcode/');



		$qrcodeDir = $dir.$member->m_id.'.png';



		$qrcodeUrl = $url.$member->m_id.'.png';



		



		if (!file_exists($qrcodeDir)){



			$tplRoot = qd(qscms::getCfgPath('/system/tplRoot_phpqrcode'));



			include_once($tplRoot.'phpqrcode.php');



			$url = WEB_URL.'/recharge/qrcode/'.$member->m_id;//



			$errorCorrectionLevel = 'L';  // 纠错级别：L、M、Q、H  



			$matrixPointSize = 10; // 点的大小：1到10  



			QRcode::png($url, $qrcodeDir, $errorCorrectionLevel, $matrixPointSize, 2);



		}



	break;



	case 'notice'://弹出公告



		$id = $var->p2;



		$item = db::one('cms_notice', '*', "id='$id'");



		!$item && db::one('cms_notice', '*', '', 'sort,id DESC');



	break;



	case 'login':



		if(form::hash()){



			$rs = member_base::login($_POST);



			if ($rs === true){



				$arr['status'] = true;



				$arr['msg']    = '登录成功';



				$arr['url']    = qscms::getUrl('/user');



			}else $arr['msg'] = $rs;

			

			echo string::json_encode($arr);

			exit;



		}



	break;



	case 'recharge'://充值金额



		if(form::hash()){



			$datas = form::get3(array('money', 'money'));



			if ($datas['money'] >= 1){//充值最低1元



				db::autocommit();



				do {



					$sn = db::createId();



				} while(db::exists('pay_payment', array('sn' => $sn), '', true));



				if (db::insert('pay_payment', array(



					'sn' => $sn,



					'uid' => $member->m_id,



					'type' => 'wxpay',//默认一个



					'types' => 1,//商户充值的



					'status' => 0,



					'money'  => $datas['money'],



					'addTime' => time()



				))){



					db::commit(true);



					qscms::gotoUrl("/recharge/user?out_trade_no=$sn");



				}



				db::autocommit(false);



				qscms::showMessage('操作失败，请重新尝试', qscms::getUrl('/user/recharge'));



			}else qscms::showMessage('充值金额最低1元', qscms::getUrl('/user/recharge'));



		}



	break;



	case 'qrorders'://交易记录



		$where = "uid='$member->m_id' AND types=3"; //使用商户API充值的数据



		$urlVar = '';



		$vars = array();



		$queryDay = cfg::getInt('pay', 'queryDay');//最多查询多少天的数据



		$queryDay || $queryDay = 1;//默认最少1天



		$queryTime = time::$todayEnd - (86400 * $queryDay);//最多查询多少天的数据



		



		$allTotal = db::dataCount('pay_payment', $where." AND addTime>=$queryTime");//总订单



		$payTotal = db::dataCount('pay_payment', $where." AND status=1 AND addTime>=$queryTime");//已付款总数量



		$notTotal = $allTotal - $payTotal;//没付款的数量



		$allMoney = round(db::one_one('pay_payment', 'SUM(money)', $where." AND addTime>=$queryTime"), 2);//总金额



		$payMoney = round(db::one_one('pay_payment', 'SUM(money)', $where." AND status=1 AND addTime>=$queryTime"), 2);//已付款总金额



		$notMoney = $allMoney - $payMoney;//没付款的总金额



		



		$kyes = array('status', 'type', 'sn', 'startDate', 'endDate');



		foreach($kyes as $v){



			$val = $var->{'gp_'.$v};



			$vars[$v] = $val;



			!is_null($val) && $val !== '' && (($urlVar && $urlVar .= '&') || !$urlVar) && $urlVar .= $v.'='.urlencode($val);



		}



		extract($vars);



		if (!is_null($status) && ($status == 0 || $status == 1)){



			$where && $where .= ' AND ';



			$where .= "status='$status'";



		}else $status = -1;



		if (!is_null($type) && $type !== '' && pay::ename($type)){



			$where && $where .= ' AND ';



			$where .= "type='$type'";



		}



		if (!is_null($out_trade_no) && $out_trade_no !== ''){



			$where && $where .= ' AND ';



			$where .= "out_trade_no='$out_trade_no'";



		}



		if (!is_null($sn) && $sn !== ''){



			$where && $where .= ' AND ';



			$where .= "sn='$sn'";



		}



		if (!is_null($startDate) && $startDate !== '') {



			$where && $where .= ' AND ';



			$startTime = time::getGeneralTimestamp($startDate);



			if ($startTime + (86400 * $queryDay) < time::$todayEnd){//如果查询时间大于系统设置的返回 默认为最高



				$startTime = $queryTime;



				$startDate = date('Y-m-d', $startTime);



			}



			$where .= 'addTime>=\''.$startTime.'\'';



		}else{



			$where && $where .= ' AND ';



			$startDate = date('Y-m-d');



			$startTime = time::getGeneralTimestamp($startDate);



			$where .= 'addTime>=\''.$startTime.'\'';



		}



		if (!is_null($endDate) && $endDate !== '') {



			$where && $where .= ' AND ';



			$endTime = time::getGeneralTimestamp($endDate.' 23:59:59');



			$where .= 'addTime<=\''.$endTime.'\'';



		}else{



			$where && $where .= ' AND ';



			$endDate = date('Y-m-d');



			$endTime = time::getGeneralTimestamp($endDate.' 23:59:59');



			$where .= 'addTime<=\''.$endTime.'\'';



		}



		if ($total = db::dataCount('pay_payment', $where)){



			$list = db::select('pay_payment', '*', $where, 'addTime DESC', $pagesize, $page);



			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.($urlVar ? '&'.$urlVar : '')."&page={page}", $pageStyle);	



		}



	break;



	case 'orders'://交易记录



		$where = "uid='$member->m_id' AND types=0"; //使用商户API充值的数据



		$urlVar = '';



		$vars = array();



		$queryDay = cfg::getInt('pay', 'queryDay');//最多查询多少天的数据



		$queryDay || $queryDay = 1;//默认最少1天



		$queryTime = time::$todayEnd - (86400 * $queryDay);//最多查询多少天的数据



		



		$allTotal = db::dataCount('pay_payment', $where." AND addTime>=$queryTime");//总订单



		$payTotal = db::dataCount('pay_payment', $where." AND status=1 AND addTime>=$queryTime");//已付款总数量



		$notTotal = $allTotal - $payTotal;//没付款的数量



		$allMoney = round(db::one_one('pay_payment', 'SUM(money)', $where." AND addTime>=$queryTime"), 2);//总金额



		$payMoney = round(db::one_one('pay_payment', 'SUM(money)', $where." AND status=1 AND addTime>=$queryTime"), 2);//已付款总金额



		$notMoney = $allMoney - $payMoney;//没付款的总金额



		



		$kyes = array('status', 'type', 'out_trade_no', 'sn', 'startDate', 'endDate');



		foreach($kyes as $v){



			$val = $var->{'gp_'.$v};



			$vars[$v] = $val;



			!is_null($val) && $val !== '' && (($urlVar && $urlVar .= '&') || !$urlVar) && $urlVar .= $v.'='.urlencode($val);



		}



		extract($vars);



		if (!is_null($status) && ($status == 0 || $status == 1)){



			$where && $where .= ' AND ';



			$where .= "status='$status'";



		}else $status = -1;



		if (!is_null($type) && $type !== '' && pay::ename($type)){



			$where && $where .= ' AND ';



			$where .= "type='$type'";



		}



		if (!is_null($out_trade_no) && $out_trade_no !== ''){



			$where && $where .= ' AND ';



			$where .= "out_trade_no='$out_trade_no'";



		}



		if (!is_null($sn) && $sn !== ''){



			$where && $where .= ' AND ';



			$where .= "sn='$sn'";



		}



		if (!is_null($startDate) && $startDate !== '') {



			$where && $where .= ' AND ';



			$startTime = time::getGeneralTimestamp($startDate);



			if ($startTime + (86400 * $queryDay) < time::$todayEnd){//如果查询时间大于系统设置的返回 默认为最高



				$startTime = $queryTime;



				$startDate = date('Y-m-d', $startTime);



			}



			$where .= 'addTime>=\''.$startTime.'\'';



		}else{



			$where && $where .= ' AND ';



			$startDate = date('Y-m-d');



			$startTime = time::getGeneralTimestamp($startDate);



			$where .= 'addTime>=\''.$startTime.'\'';



		}



		if (!is_null($endDate) && $endDate !== '') {



			$where && $where .= ' AND ';



			$endTime = time::getGeneralTimestamp($endDate.' 23:59:59');



			$where .= 'addTime<=\''.$endTime.'\'';



		}else{



			$where && $where .= ' AND ';



			$endDate = date('Y-m-d');



			$endTime = time::getGeneralTimestamp($endDate.' 23:59:59');



			$where .= 'addTime<=\''.$endTime.'\'';



		}



		if ($total = db::dataCount('pay_payment', $where)){



			$list = db::select('pay_payment', '*', $where, 'addTime DESC', $pagesize, $page);



			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.($urlVar ? '&'.$urlVar : '')."&page={page}", $pageStyle);	



		}



	break;



	case 'count'://收入统计



		$where = "uid='$member->m_id' AND (types=0 OR types=3) AND status=1"; //使用商户API充值的数据



		$urlVar = '';



		$vars = array();



		$payMoney = round(db::one_one('pay_payment', 'SUM(money)', $where." AND status=1"), 2);//已付款总金额



		$memberMoney = round(db::one_one('pay_payment', 'SUM(money1)', $where." AND status=1"), 2);//商户收款的总金额



		



		$kyes = array('startDate', 'endDate');



		foreach($kyes as $v){



			$val = $var->{'gp_'.$v};



			$vars[$v] = $val;



			!is_null($val) && $val !== '' && (($urlVar && $urlVar .= '&') || !$urlVar) && $urlVar .= $v.'='.urlencode($val);



		}



		extract($vars);



		if (!is_null($startDate) && $startDate !== '') {



			$where && $where .= ' AND ';



			$startTime = time::getGeneralTimestamp($startDate);



			$where .= 'addTime>=\''.$startTime.'\'';



		}else{



			$where && $where .= ' AND ';



			$startDate = date('Y-m-d');



			$startTime = time::getGeneralTimestamp($startDate);



			$where .= 'addTime>=\''.$startTime.'\'';



		}



		if (!is_null($endDate) && $endDate !== '') {



			$where && $where .= ' AND ';



			$endTime = time::getGeneralTimestamp($endDate.' 23:59:59');



			$where .= 'addTime<=\''.$endTime.'\'';



		}else{



			$where && $where .= ' AND ';



			$endDate = date('Y-m-d');



			$endTime = time::getGeneralTimestamp($endDate.' 23:59:59');



			$where .= 'addTime<=\''.$endTime.'\'';



		}


		$allMoney   = round(db::one_one('pay_payment', 'SUM(money)', $where));//不扣费率的收入
		$actualMoney = round(db::one_one('pay_payment', 'SUM(money1)', $where));//扣费率的收入
		
		if ($total = db::dataCount('pay_payment', $where)){



			$list = db::select('pay_payment', '*', $where, 'addTime DESC', $pagesize, $page);



			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl."&page={page}", $pageStyle);	



		}



	break;



	case 'ordersca'://通道统计
		$where = "uid='$member->m_id' AND (types=0 OR types=3)"; //使用商户API充值的数据
		$where1 = "(status=1 OR status=0) AND uid='$member->m_id'"; //使用商户API充值的数据
		$urlVar = '';
		$vars = array();
		$kyes = array('startDate', 'endDate');
		foreach($kyes as $v){
			$val = $var->{'gp_'.$v};
			$vars[$v] = $val;
			!is_null($val) && $val !== '' && (($urlVar && $urlVar .= '&') || !$urlVar) && $urlVar .= $v.'='.urlencode($val);

		}
		extract($vars);
		if (!is_null($startDate) && $startDate !== '') {
			$startTime = time::getGeneralTimestamp($startDate);
			$where && $where .= ' AND ';
			$where .= 'addTime>=\''.$startTime.'\'';
			$where1 && $where1 .= ' AND ';
			$where1 .= 'addTime>=\''.$startTime.'\'';
		}else{
			$startDate = date('Y-m-d');
			$startTime = time::getGeneralTimestamp($startDate);
			$where && $where .= ' AND ';
			$where .= 'addTime>=\''.$startTime.'\'';
			$where1 && $where1 .= ' AND ';
			$where1 .= 'addTime>=\''.$startTime.'\'';
		}
		if (!is_null($endDate) && $endDate !== '') {
			$endTime = time::getGeneralTimestamp($endDate.' 23:59:59');
			$where && $where .= ' AND ';
			$where .= 'addTime<=\''.$endTime.'\'';
			$where1 && $where1 .= ' AND ';
			$where1 .= 'addTime<=\''.$endTime.'\'';
		}else{
			$endTime = time::getGeneralTimestamp($endDate.' 23:59:59');
			$endDate = date('Y-m-d');
			$where && $where .= ' AND ';
			$where .= 'addTime<=\''.$endTime.'\'';
			$where1 && $where1 .= ' AND ';
			$where1 .= 'addTime<=\''.$endTime.'\'';

		}
		$payArr= pay::$array;
		if ($payArr){



			foreach(pay::$array as $k => $v){//统计每个支付类型的金额



				/*已支付*/



				$payMoney = round(db::one_one('pay_payment', 'SUM(money)', $where." AND type='$k' AND status=1"), 2);



				$payTotal = (int)(db::dataCount('pay_payment', $where." AND type='$k' AND status=1"));



				/*未支付*/



				$notMoney = round(db::one_one('pay_payment', 'SUM(money)', $where." AND type='$k' AND status=0"), 2);



				$notTotal = (int)(db::dataCount('pay_payment', $where." AND type='$k' AND status=0"));



				/*商户得到的*/



				$memberMoney = round(db::one_one('pay_payment', 'SUM(money1)', $where." AND type='$k' AND status=1"), 2);



				



				$payArr[$k] = array('name' => $v, 'payMoney' => $payMoney, 'payTotal' => $payTotal, 'memberMoney' => $memberMoney, 'notTotal' => $notTotal, 'notMoney' => $notMoney, 'allTotal' => $payTotal + $notTotal, 'allMoney' => $payMoney + $notMoney);



			}



		}

		$cashMoney   = round(db::one_one('pay_cash', 'SUM(cashMoney)', $where1));//不扣费率的收入
		$actualMoney = round(db::one_one('pay_cash', 'SUM(money)', $where1));//已经提现的  实际到账的

		$payMoney = round(db::one_one('pay_payment', 'SUM(money)', $where." AND status=1"), 2);//已付款总金额
		$memberMoney = round(db::one_one('pay_payment', 'SUM(money1)', $where." AND status=1"), 2);//商户收款的总金额



	break;
	case 'rates':
		$vip = $member->m_vip;
		$one = array();
		if ($vip > 0) $one = db::one('pay_bl', '*', "id='$vip'");
		$one || $one = db::one('pay_bl', '*', "money=0");
		if (!$one) qscms::showMessage('请先购买接口套餐', qscms::getUrl('/vip'));
		if ($var->p2 == 'edit'){
			if (form::hash()){
				$datas = form::get3('type');
				$type = $datas['type'];
				$arr = array();
				if (pay::ename($type)){
					if (db::exists('pay_off', "uid='$member->m_id' AND type='$type' AND isSys=1")){
						$arr['msg'] = '该通道已被系统关闭，无法自主修改，请联系客服';
					}else{
						if (db::exists('pay_off', "uid='$member->m_id' AND type='$type'")){
							db::delete('pay_off', "uid='$member->m_id' AND type='$type'");
							$arr['status'] = true;
							$arr['st'] = false;
						}else {
							db::insert('pay_off', array('uid' => $member->m_id, 'type' => $type));
							$arr['status'] = true;
							$arr['st'] = true;
						}
					}
				}
				exit(string::json_encode($arr));
			}
		}
		if ($member->bl){
			foreach($one as $k => $v){
				if (!empty($member->bl[$k]) && $member->bl[$k] >= 0 && $member->bl[$k] < 1){
					$one[$k] = $member->bl[$k];
				}
			}
		}
	break;

	case 'api':



		if ($var->p2 == 'show'){



			$var->tplName = $op.'_'.$var->p2;	



		}



	break;



	case 'info'://修改信息



		if (form::hash()){



			$rs = $member->editInfo($_POST);



			if ($rs === true) {



				$arr['status'] = true;



				$arr['url'] = qscms::getUrl('/user/info');



			}



			else $arr['msg'] = $rs;



			exit(string::json_encode($arr));



		}



	break;



	case 'userpwd'://修改密码



		if (form::hash()){



			$rs = $member->setPwd($_POST);



			if ($rs === true) $arr['status'] = true;



			else $arr['msg'] = $rs;



			exit(string::json_encode($arr));



		}



	break;



	case 'consumption'://消费列表



		if ($total = db::dataCount('log_money', "uid=$member->m_id AND money<0")){



			$list = db::select('log_money', '*', "uid=$member->m_id AND money<0", 'time DESC', $pagesize, $page);



			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl."&page={page}", $pageStyle);	



		}



	break;



	case 'payment'://充值列表



		if ($total = db::dataCount('pay_payment', "uid=$member->m_id AND types=1")){



			$list = db::select('pay_payment', '*', "uid=$member->m_id AND types=1", 'addTime DESC', $pagesize, $page);



			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl."&page={page}", $pageStyle);	



		}



	break;



	case 'payments'://提现列表
		
		$where = "uid=$member->m_id";
		
		$kyes = array('startDate', 'endDate');



		foreach($kyes as $v){



			$val = $var->{'gp_'.$v};



			$vars[$v] = $val;



			!is_null($val) && $val !== '' && (($urlVar && $urlVar .= '&') || !$urlVar) && $urlVar .= $v.'='.urlencode($val);



		}



		extract($vars);



		if (!is_null($startDate) && $startDate !== '') {



			$where && $where .= ' AND ';



			$startTime = time::getGeneralTimestamp($startDate);



			$where .= 'addTime>=\''.$startTime.'\'';



		}



		if (!is_null($endDate) && $endDate !== '') {



			$where && $where .= ' AND ';



			$endTime = time::getGeneralTimestamp($endDate.' 23:59:59');



			$where .= 'addTime<=\''.$endTime.'\'';



		}else{



			$where && $where .= ' AND ';



			$endDate = date('Y-m-d');



			$endTime = time::getGeneralTimestamp($endDate.' 23:59:59');



			$where .= 'addTime<=\''.$endTime.'\'';



		}
		$allMoney = db::one_one('pay_cash', 'SUM(cashMoney)', $where);//提现总金额
		$actualMoney = db::one_one('pay_cash', 'SUM(money)', $where);//实际提现总金额
		if ($total = db::dataCount('pay_cash', $where)){



			$list = db::select('pay_cash', '*', $where, 'addTime DESC', $pagesize, $page);



			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl."&page={page}", $pageStyle);	



		}



	break;



	/*



	case 'transfer'://提现转让



		if (form::hash()){



			$rs = $member->transfer($_POST);



			if ($rs === true) $arr['status'] = true;



			else $arr['msg'] = $rs;



			exit(json_encode($arr));



		}



	break;



	*/



	case 'takecash'://提现



		if (!$member->isCash && !$member->isCash1) qscms::gotoUrl('/user/info');//没有填写信息 让用户去修改



		if (form::hash()){



			$rs = $member->cash($_POST);



			if ($rs === true) $arr['status'] = true;



			else $arr['msg'] = $rs;



			exit(json_encode($arr));



		}



	break;



	/*case 'takecashcode'://提现短信验证码 



		if (form::hash()){



			$datas['mobile'] = $member->m_mobile;



			if ($datas['mobile'] && form::checkMobilephone($datas['mobile'])){



				if (db::exists('member', "mobile='$datas[mobile]'")){



					$msg = '您的手机号：{mobile}，验证码：{vcode}。请不要把验证码泄露给其他人。如非本人操作，可不用理会！';



					$rs = member_base::message($datas['mobile'], $msg);



					if ($rs === true) $arr['status'] = true;



					elseif ($rs) $arr['msg'] = $rs;



					else $arr['msg'] = '验证码发送失败,请联系客服';



				}else $arr['msg'] = '没有该手机号';



			} else $arr['msg'] = '手机格式不正确';



		} else $arr['msg'] = '操作异常';



		exit(json_encode($arr));//使用发送短信的 string::json_encode  就使用不了了。 PHPclass冲突 改成PHP函数*/



	break;



	case 'reg':



		$p3 = $var->p2;



		$pid = '';



		if ($p3 != 'index'){



			$pid = base64_decode($p3);



		}



		if (form::hash()){

			if (form::checkVcode()){

				$rs = member_base::reg($_POST);

			}else $rs = '图像验证码错误';

			if ($rs === true) {



				$arr['url'] = qscms::getUrl('/user');



				$arr['status'] = true;



			}else $arr['msg'] = $rs;



			exit(string::json_encode($arr));



		}



	break;



	case 'regcode'://获取注册验证码



		if (form::hash()){



			$datas = form::get3('mobile');

			if (form::checkVcode()){

				if ($datas['mobile'] && form::checkMobilephone($datas['mobile'])){

	

					if (!db::exists('member', "mobile='$datas[mobile]'")){

	

						$msg = '您的手机号：{mobile}，注册验证码：{vcode}，请不要把验证码泄露给其他人。如非本人操作，可不用理会！';

	

						$rs = member_base::message($datas['mobile'], $msg);

	

						if ($rs === true) $arr['status'] = true;

	

						elseif ($rs) $arr['msg'] = $rs;

	

						else $arr['msg'] = '验证码发送失败,请联系客服';

	

					}else $arr['msg'] = '手机号码已被使用';

	

				} else $arr['msg'] = '手机格式不正确';

			}else $arr['msg'] = '图形验证码错误';

		} else $arr['msg'] = '操作异常';



		exit(string::json_encode($arr));



	break;



	case 'usermobile':



		if (form::hash()){



			$rs = $member->edit_mobile($_POST);



			if ($rs === true) {



				$arr['url'] = qscms::getUrl('/user/usermobile');



				$arr['status'] = true;



			}else $arr['msg'] = $rs;



			exit(string::json_encode($arr));



		}



	break;



	case 'mobilecode'://修改手机 旧手机短信验证码 



		if (form::hash()){



			$datas['mobile'] = $member->m_mobile;



			if ($datas['mobile'] && form::checkMobilephone($datas['mobile'])){



				if (db::exists('member', "mobile='$datas[mobile]'")){



					$msg = '您的手机号：{mobile}，验证码：{vcode}。请不要把验证码泄露给其他人。如非本人操作，可不用理会！';



					$rs = member_base::message($datas['mobile'], $msg);



					if ($rs === true) $arr['status'] = true;



					elseif ($rs) $arr['msg'] = $rs;



					else $arr['msg'] = '验证码发送失败,请联系客服';



				}else $arr['msg'] = '没有该手机号';



			} else $arr['msg'] = '手机格式不正确';



		} else $arr['msg'] = '操作异常';



		exit(string::json_encode($arr));



	break;



	case 'mobilecode1'://修改手机 新手机短信验证码 



		if (form::hash()){



			$datas = form::get3('mobile');



			if ($datas['mobile'] && form::checkMobilephone($datas['mobile'])){



				if (!db::exists('member', "mobile='$datas[mobile]'")){



					$msg = '您的手机号：{mobile}，注册验证码：{vcode}，请不要把验证码泄露给其他人。如非本人操作，可不用理会！';



					$rs = member_base::message($datas['mobile'], $msg);



					if ($rs === true) $arr['status'] = true;



					elseif ($rs) $arr['msg'] = $rs;



					else $arr['msg'] = '验证码发送失败,请联系客服';



				}else $arr['msg'] = '手机号码已被使用';



			} else $arr['msg'] = '手机格式不正确';



		} else $arr['msg'] = '操作异常';



		exit(string::json_encode($arr));



	break;



	case 'userrecord':



		if ($total = db::dataCount('login_log', "uid=$member->m_id")){



			$list = db::select('login_log', '*', "uid=$member->m_id", 'time DESC', $pagesize, $page);



			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl."&page={page}", $pageStyle);	



		}



	break;



	case 'userweixin':



		if (!$member->m_wxid){



			$imgUrl = $member->qrcode_url();



		}else{



			if (form::hash()){



				$rs = $member->wx_relieve($_POST);



				if ($rs === true) {



					$arr['url'] = qscms::getUrl('/user/userweixin');



					$arr['status'] = true;



				}else $arr['msg'] = $rs;



				exit(string::json_encode($arr));



			}



		}



	break;



	case 'weixincode':



		if (form::hash()){



			$datas['mobile'] = $member->m_mobile;



			if ($datas['mobile'] && form::checkMobilephone($datas['mobile'])){



				if (db::exists('member', "mobile='$datas[mobile]'")){



					$msg = '您的手机号：{mobile}，验证码：{vcode}。请不要把验证码泄露给其他人。如非本人操作，可不用理会！';



					$rs = member_base::message($datas['mobile'], $msg);



					if ($rs === true) $arr['status'] = true;



					elseif ($rs) $arr['msg'] = $rs;



					else $arr['msg'] = '验证码发送失败,请联系客服';



				}else $arr['msg'] = '没有该手机号';



			} else $arr['msg'] = '手机格式不正确';



		} else $arr['msg'] = '操作异常';



		exit(string::json_encode($arr));



	break;



	case 'cs':



		//$msg = '您的手机号：{mobile}，注册验证码：{vcode}，请不要把验证码泄露给其他人。如非本人操作，可不用理会！';



		//$rs = member_base::message('135411043471', $msg);



		//print_r($rs);exit;



	break;



	case 'forgot'://找回密码



	



		if (form::hash()){



			$rs = member_base::edit_password($_POST);



			if ($rs === true) {



				$arr['url'] = qscms::getUrl('/user');



				$arr['status'] = true;



			}else $arr['msg'] = $rs;



			exit(string::json_encode($arr));



		}



	break;



	case 'forgotcode'://获取找回密码验证码



		if (form::hash()){



			$datas = form::get3('mobile');



			if ($datas['mobile'] && form::checkMobilephone($datas['mobile'])){



				if (db::exists('member', "mobile='$datas[mobile]'")){



					$rs = member_base::message($datas['mobile'], false);



					if ($rs === true) $arr['status'] = true;



					elseif ($rs) $arr['msg'] = $rs;



					else $arr['msg'] = '验证码发送失败,请联系客服';



				}else $arr['msg'] = '没有该手机号';



			} else $arr['msg'] = '手机格式不正确';



		} else $arr['msg'] = '操作异常';



		exit(string::json_encode($arr));



	break;



	//退出登录



	case 'logout';



		member_base::logout();



		qscms::gotoUrl('/index.html');



	break;



}







?>