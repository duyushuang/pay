<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
!qscms::defineTrue('INSTALL') && qscms::gotoUrl('/install/');
member_agent::loginCookie();
$member = $var->agent;
$pagestyle = qscms::getCfgPath('/system/newPageStyle');
$noLoginArr = array('login');//登录不允许访问
if (!$member && !in_array($var->p1, $noLoginArr)){
	qscms::gotoUrl('/member/login.html');
}
if ($member && in_array($var->p1, $noLoginArr)){
	qscms::gotoUrl('/member/index.html');
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
		$todayStart = time::$todayStart;
		$lastStart  = $todayStart - 86400;
		$todayEnd = time::$todayEnd;
		$a = db::one_one('fc_log', 'SUM(money)', "uid='$member->m_id'");
		$allMoney = round(db::one_one('fc_log', 'SUM(money)', "uid='$member->m_id'"), 2);
		$allTotal = db::dataCount('fc_log', "uid='$member->m_id'");
		$dayMoney = round(db::one_one('fc_log', 'SUM(money)', "uid='$member->m_id' AND addTime>$todayStart"), 2);
		$lastMoney = round(db::one_one('fc_log', 'SUM(money)', "uid='$member->m_id' AND addTime>$lastStart AND addTime<$todayStart"), 2);//昨天
		
	break;
	case 'list'://我的代理 我的下级列表
		$list = array();
		$wh = $multipage = '';
		$rank = member_center::fcCount();
		
		$keys = array('user', 'regTime1', 'regTime2');
		$vars = array();
		$urlVar = '';
		foreach ($keys as $v) {
			$val = $var->{'gp_'.$v};
			$vars[$v] = $val;
			!is_null($val) && $val !== '' && (($urlVar && $urlVar .= '&') || !$urlVar) && $urlVar .= $v.'='.urlencode($val);
		}
		extract($vars);
		if (!is_null($user) && $user !== '') {
			if (form::checkMobilephone($user)) $wh .= " AND money='$user'";
			elseif (is_numeric($user)) $wh = " AND id='$user'";
		}
		if (!is_null($regTime1) && $regTime1 !== '') {
			$regTime1 = time::getGeneralTimestamp($regTime1);
			$wh .= ' AND regTime>=\''.$regTime1.'\'';
		}
		if (!is_null($regTime2) && $regTime2 !== '') {
			$regTime2 = time::getGeneralTimestamp($regTime2.' 23:59:59');
			$wh .= ' AND regTime<=\''.$regTime2.'\'';
		}
		if ($total = treeDB::childsCount_all('member', $member->m_id, 'rank<='.($member->m_rank + $rank).$wh)){
			$list = treeDB::childs_all('member', $member->m_id, 'id,name,mobile,money,isApi,regTime,rank', 'rank<='.($member->m_rank + $rank).$wh);
			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.($urlVar ? '&'.$urlVar : '').'&page={page}', $pagestyle);
		}
	break;
	case 'count':
		if ($uid = $var->getInt('gp_user')){
			$rs = treeDB::exists_parent('member', $member->m_id, $uid);	
			if (!$rs) qscms::gotoUrl('/member/list');
			$where = "uid='$uid' AND (types=0 OR types=3) AND status=1"; //使用商户API充值的数据
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

			
			
		}
		else{
			qscms::gotoUrl('/member/list');	
		}
	case 'order'://提成详情
		$list = array();
		$wh = $multipage = '';
		
		$keys = array('out_trade_no', 'uid', 'addTime1', 'addTime2');
		$vars = array();
		$urlVar = '';
		foreach ($keys as $v) {
			$val = $var->{'gp_'.$v};
			$vars[$v] = $val;
			!is_null($val) && $val !== '' && (($urlVar && $urlVar .= '&') || !$urlVar) && $urlVar .= $v.'='.urlencode($val);
		}
		extract($vars);
		
		
		if (!is_null($out_trade_no) && $out_trade_no !== '') {
			$wh .= " AND out_trade_no='$out_trade_no'";
		}
		if (!is_null($uid) && $uid !== '') {
			$wh .= " AND fuid='$uid'";
		}
		if (!is_null($addTime1) && $addTime1 !== '') {
			$time1 = time::getGeneralTimestamp($addTime1);
			$wh .= ' AND addTime>=\''.$time1.'\'';
		}
		if (!is_null($addTime2) && $addTime2 !== '') {
			$time2 = time::getGeneralTimestamp($addTime2.' 23:59:59');
			$wh .= ' AND addTime<=\''.$time2.'\'';
		}
		if ($total = db::dataCount('fc_log', "uid='$member->m_id'". $wh)){
			$list = db::select('fc_log', '*', "uid='$member->m_id'".$wh, 'id DESC', $pagesize, $page);
			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.($urlVar ? '&'.$urlVar : '').'&page={page}', $pagestyle);
		}
	break;
	case 'login':
		if(form::hash()){
			$rs = member_agent::login($_POST);
			if ($rs === true){
				$arr['status'] = true;
				$arr['msg']    = '登录成功';
				$arr['url']    = qscms::getUrl('/member');
			}else $arr['msg'] = $rs;
			exit(string::json_encode($arr));
		}
	break;
	case 'logout':
		qscms::unsetcookie('agentAuth');
		qscms::gotoUrl('/member/login');
	break;
}