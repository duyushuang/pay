<?php





(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');

$top_menu=array(

	'list' => 'api交易记录',

	'list1' => '线下二维码交易记录',

	'list2' => '平台测数据',

	'ordersca' => 'api通道统计',

);

$top_menu_key = array_keys($top_menu);

($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];

switch ($method) {

	case 'list':

	case 'list1':

	case 'list2':

		if ($method == 'list') $wh = 'types=0';

		elseif ($method == 'list1') $wh = 'types=3';

		elseif ($method == 'list2') $wh = 'types=2';

		$keys = array('status', 'type', 'out_trade_no', 'sn', 'uid', 'minMoney', 'maxMoney', 'minCredit', 'maxCredit', 'addTime1', 'addTime2', 'payTime1', 'payTime2', 'referer_url', 'notify_url', 'return_url');

		$vars = $urlVar = '';

		foreach ($keys as $v) {

			$val = $var->{'gp_'.$v};

			$vars[$v] = $val;

			!is_null($val) && $val !== '' && (($urlVar && $urlVar .= '&') || !$urlVar) && $urlVar .= $v.'='.urlencode($val);

		}

		extract($vars);



		if (!is_null($referer_url) && $referer_url !== ''){

			$wh .= " AND referer_url like '%$referer_url%'";

		}
		if (!is_null($notify_url) && $notify_url !== ''){

			$wh .= " AND notify_url like '%$notify_url%'";

		}

		if (!is_null($return_url) && $return_url !== ''){

			$wh .= " AND return_url like '%$return_url%'";

		}


		if (!is_null($status) && ($status == 0 || $status == 1)){

			$wh .= " AND status='$status'";

		}else $status = -1;

		if (!is_null($type) && $type !== ''){

			$wh .= " AND type='$type'";

		}

		if (!is_null($sn)  && $sn  !== ''){

			$wh .= " AND sn='$sn'";

		}

		if (!is_null($out_trade_no)  && $out_trade_no!== ''){

			$wh .= " AND out_trade_no='$out_trade_no'";

		}

		if (!is_null($uid) && $uid !== ''){

			$wh .= " AND uid='$uid'";

		}

		if (!is_null($minMoney) && $minMoney  !== ''){

			$wh .= " AND money >= $minMoney";

		}

		if (!is_null($maxMoney)  && $maxMoney  !== ''){

			$wh .= " AND money <= $maxMoney";

		}

		if (!is_null($minCredit)  && $minCredit  !== ''){

			$wh .= " AND credit >= $minCredit";

		}

		if (!is_null($maxCredit)  && $maxCredit  !== ''){

			$wh .= " AND credit <= $maxCredit";

		}


		if (!is_null($payTime1) && $payTime1  !== '') {

			$payTime1  = time::getGeneralTimestamp($payTime1 );

			$wh .= ' AND payTime>=\''.$payTime1 .'\'';

		}

		if (!is_null($payTime2) && $payTime2 !== '') {

			$payTime2 = time::getGeneralTimestamp($payTime2.' 23:59:59');

			$wh .= ' AND payTime<=\''.$payTime2.'\'';

		}
		
		if (!is_null($addTime1) && $addTime1  !== '') {

			$addTime1  = time::getGeneralTimestamp($addTime1 );

			$wh .= ' AND addTime>=\''.$addTime1 .'\'';

		}

		if (!is_null($addTime2) && $addTime2 !== '') {

			$addTime2 = time::getGeneralTimestamp($addTime2.' 23:59:59');

			$wh .= ' AND addTime<=\''.$addTime2.'\'';

		}

		$list = array();
		if ($total = db::dataCount('pay_payment', $wh)){

			$list = db::select('pay_payment', '*', $wh, 'id DESC', $pagesize, $page);

			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.'&method='.$method.($urlVar ? '&'.$urlVar : '').'&page={page}', $pagestyle);

		}

		

	break;

	case 'ordersca':

		$wh = "types=0"; //使用商户API充值的数据

		$urlVar = '';

		$vars = array();

		$payMoney = db::one_one('pay_payment', 'SUM(money)', $wh." AND status=1");//已付款总金额

		$memberMoney = db::one_one('pay_payment', 'SUM(money1)', $wh." AND status=1");//商户收款的总金额

		

		$kyes = array('uid', 'addTime1', 'addTime2');

		foreach($kyes as $v){

			$val = $var->{'gp_'.$v};

			$vars[$v] = $val;

			!is_null($val) && $val !== '' && (($urlVar && $urlVar .= '&') || !$urlVar) && $urlVar .= $v.'='.urlencode($val);

		}

		extract($vars);

		if (!is_null($uid) && $uid !== ''){

			$wh .= ' AND uid=\''.$uid.'\'';	

		}

		if (!is_null($addTime1) && $addTime1 !== '') {

			$startTime = time::getGeneralTimestamp($addTime1);

			$wh .= ' AND addTime>=\''.$startTime.'\'';

		}

		if (!is_null($addTime2) && $addTime2 !== '') {

			$endTime = time::getGeneralTimestamp($addTime2.' 23:59:59');

			$wh .= ' AND addTime<=\''.$endTime.'\'';

		}

		$list = array();

		if ($payMoney){

			foreach(pay::$array as $k => $v){//统计每个支付类型的金额

				/*已支付*/

				$payMoney = round(db::one_one('pay_payment', 'SUM(money)', $wh." AND type='$k' AND status=1"), 2);

				$payTotal = (int)(db::dataCount('pay_payment', $wh." AND type='$k' AND status=1"));

				/*未支付*/

				$notMoney = round(db::one_one('pay_payment', 'SUM(money)', $wh." AND type='$k' AND status=0"), 2);

				$notTotal = (int)(db::dataCount('pay_payment', $wh." AND type='$k' AND status=0"));

				/*商户得到的*/

				$memberMoney = round(db::one_one('pay_payment', 'SUM(money1)', $wh." AND type='$k' AND status=1"), 2);

				$list[$k] = array('name' => $v, 'payMoney' => $payMoney, 'payTotal' => $payTotal, 'memberMoney' => $memberMoney, 'notTotal' => $notTotal, 'notMoney' => $notMoney, 'allTotal' => $payTotal + $notTotal, 'allMoney' => $payMoney + $notMoney);

			}

		}

		//$payMoney = db::one_one('pay_payment', 'SUM(money)', $wh." AND status=1");//已付款总金额

		//$memberMoney = db::one_one('pay_payment', 'SUM(money1)', $wh." AND status=1");//商户收款的总金额

	break;

}

$allTotal = db::dataCount('pay_payment', $wh);//总订单

$payTotal = db::dataCount('pay_payment', $wh." AND status=1");//已付款总数量

$notTotal = $allTotal - $payTotal;//没付款的数量

$allMoney = round(db::one_one('pay_payment', 'SUM(money)', $wh), 2);//总金额

$payMoney = round(db::one_one('pay_payment', 'SUM(money)', $wh." AND status=1"), 2);//已付款总金额

$notMoney = $allMoney - $payMoney;//没付款的总金额

$memberMoney = round(db::one_one('pay_payment', 'SUM(money1)', $wh." AND status=1"), 2);//商户收款的总金额

?>