<?php





(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');

$top_menu=array(

	'paylist' => '用户充值明细',

	'cashlist' => '用户提现明细',

	'f_view' => '财务明细',

	//'f_add' => '添加财务项目',

	//'f_edit' => array('name' => '编辑财务项目', 'hide' => true)

);

$top_menu_key = array_keys($top_menu);

($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];

switch ($method) {

	case 'paylist':

	 	$wh = 'types=1';

		$keys = array('status', 'type', 'out_trade_no', 'sn', 'uid', 'minMoney', 'maxMoney', 'minCredit', 'maxCredit', 'addTime1', 'addTime2');

		$vars = $urlVar = '';

		foreach ($keys as $v) {

			$val = $var->{'gp_'.$v};

			$vars[$v] = $val;

			!is_null($val) && $val !== '' && (($urlVar && $urlVar .= '&') || !$urlVar) && $urlVar .= $v.'='.urlencode($val);

		}

		extract($vars);

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

		$allTotal = db::dataCount('pay_payment', $wh);//总订单

		$payTotal = db::dataCount('pay_payment', $wh." AND status=1");//已付款总数量

		$notTotal = $allTotal - $payTotal;//没付款的数量

		$allMoney = db::one_one('pay_payment', 'SUM(money)', $wh);//总金额

		$payMoney = db::one_one('pay_payment', 'SUM(money)', $wh." AND status=1");//已付款总金额

		$notMoney = $allMoney - $payMoney;//没付款的总金额

		$memberMoney = db::one_one('pay_payment', 'SUM(money1)', $wh." AND status=1");//商户收款的总金额

	break;

	case 'cashlist':

		/*

		if (form::hash()) {

			extract(form::get3('del'));

			if ($del) {

				admin::show_message('删除了'.db::del_ids('pay_cash', $del).'个财务项目', $baseUrl.'&method=cashlist&page='.$page);

			}

		}

		*/

		$sh = $var->getInt('gp_sh');

		$qx = $var->getInt('gp_qx');

		$qx1 = $var->getInt('gp_qx1');

		$wx = $var->getInt('gp_wx');//微信企业打款

		if ($sh){

			if (db::update('pay_cash', 'status=1', "id='$sh' AND status=0")) {

				admin::show_message('确认打款成功', $baseUrl.'&method=cashlist&page='.$page);

			}else admin::show_message('操作失败', $baseUrl.'&method=cashlist&page='.$page);

		}elseif($wx){

			$item = db::one('pay_cash', '*', "id='$wx' AND status=0 AND type=2");

			if (!$item) admin::show_message('操作失败', $baseUrl.'&method=cashlist&page='.$page);

			//print_r($item);exit;

			$openid = $item['wxid'];

			$money = $item['money'];

			$tplRoot = qd(self::getCfgPath('/system/tplRoot_payment').'wxpay/example/');

			include_once($tplRoot.'transfers.php');//企业付款

			if(!empty($data)) {//返回的发送结果

				$data = simplexml_load_string($data, null, LIBXML_NOCDATA);

				if ($data->return_code == 'SUCCESS' && $data->result_code == 'SUCCESS'){

					if (db::update('pay_cash', 'status=1', "id='$wx' AND status=0") !== false) {

						admin::show_message('确认打款成功', $baseUrl.'&method=cashlist&page='.$page);	

					}else{
						pay::log('提现ID：'.$wx.' 修改数据错误(但系统已经打款成功，请手动点击（已打款）确认该订单)');
						admin::show_message('修改数据错误(但系统已经打款成功，请手动点击（已打款）确认该订单)：错误提示('.db::error().')', $baseUrl.'&method=cashlist&page='.$page);

					}

				}else{
					//print_r($data);exit;
					admin::show_message('微信企业打款失败：如余额不足（<a target="_blank" href="https://pay.weixin.qq.com/index.php/core/sp_transfer">点击进入微信商户充值</a>）错误提示('.$data->err_code_des.')', $baseUrl.'&method=cashlist&page='.$page);

				}

			}else{

			

			}	

			

			

		}elseif ($qx){//返回现的金额

			$userInfo = db::one('pay_cash', 'uid,money,cashMoney', "id='$qx' AND status=0");

			if ($userInfo){

				if (db::update('pay_cash', 'status=2', "id='$qx' AND status=0")){

					$m = new member_center($userInfo['uid']);

					if ($m->addMoney($userInfo['cashMoney'], 0, '提现审核失败，返回提现金额', 11)){ //看返回哪一个 cashMoney 没算手续费  money 扣了手续费的

						admin::show_message('确认(返回提现金额)取消打款驳回成功');	

					}else admin::show_message('未能返回用户提现金额（请联系技术处理）');

				}else admin::show_message('操作失败', $baseUrl.'&method=cashlist&page='.$page);

			}

		}elseif ($qx1){//不返回提现金额

			$userInfo = db::one('pay_cash', 'uid,money', "id='$qx1' AND status=0");

			if ($userInfo){

				if (db::update('pay_cash', 'status=2,isT=1', "id='$qx1' AND status=0")){

					db::insert('log_money', array('uid' => $userInfo['uid'], 'type' => 11, 'money' => 0, 'remark' => '提现审核失败，不返回提现金额', 'time' => time::$timestamp));

					admin::show_message('确认(不返回提现金额)取消打款驳回成功', $baseUrl.'&method=cashlist&page='.$page);	

				}else admin::show_message('操作失败', $baseUrl.'&method=cashlist&page='.$page);

			}

		

			

		}

		

		$wh = '1=1';

		$keys = array('status', 'type','card', 'uid', 'minMoney', 'maxMoney', 'minCredit', 'maxCredit', 'addTime1', 'addTime2', 't');

		$vars = $urlVar = '';

		foreach ($keys as $v) {

			$val = $var->{'gp_'.$v};

			$vars[$v] = $val;

			!is_null($val) && $val !== '' && (($urlVar && $urlVar .= '&') || !$urlVar) && $urlVar .= $v.'='.urlencode($val);

		}

		extract($vars);

		if (!is_null($status) && $status > -1){

			if ($status == 3) $wh .= " AND status='2' AND isT='1'"; //取消 没返回商户金额的

			elseif ($status == 2) $wh .= " AND status='2' AND isT='0'";//取消 返回商户金额的

			else $wh .= " AND status='$status'";//提现成功的

		}else $status = -1;

		

		if (!is_null($t) && $t !== ''){

			$cashT = cfg::get('web', 'cashT');

			if ($t == $cashT){

				$idsArr = db::select('member', 'id', "cashT='$t' OR cashT=0");

				

			}else{

				$idsArr = db::select('member', 'id', "cashT='$t'");

			}

			$ids = '';

			if ($idsArr){

				foreach($idsArr as $v){

					$ids && $ids .= ',';

					$ids .= $v['id'];	

				}

			}

			if ($ids) {

				$wh .= " AND uid in($ids)";

			}

		}

		

		if (!is_null($type) && $type > -1){

			$wh .= " AND type='$type'";

		}else $type = -1;

		

		if (!is_null($card) && $card !== ''){

			$wh .= " AND card='$card'";

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

		if (!is_null($addTime1) && $addTime1  !== '') {

			$addTime1  = time::getGeneralTimestamp($addTime1 );

			$wh .= ' AND addTime>=\''.$addTime1 .'\'';

		}

		if (!is_null($addTime2) && $addTime2 !== '') {

			$addTime2 = time::getGeneralTimestamp($addTime2.' 23:59:59');

			$wh .= ' AND addTime<=\''.$addTime2.'\'';

		}

		if ($total = db::dataCount('pay_cash', $wh)){

			$list = db::select('pay_cash', '*', $wh, 'status,addTime DESC', $pagesize, $page);

			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.'&method='.$method.($urlVar ? '&'.$urlVar : '')."&page={page}", $pagestyle);	

		}

		$allMoney  = round(db::one_one('pay_cash', 'SUM(cashMoney)', $wh), 2);//提现金额

		$loadMoney = round(db::one_one('pay_cash', 'SUM(money)', $wh.' AND status=0'), 2);//等待提现金额

		$cashMoney = round(db::one_one('pay_cash', 'SUM(money)', $wh.' AND status=1'), 2);//已打款金额

		$backMoney = round(db::one_one('pay_cash', 'SUM(cashMoney)', $wh.' AND status=2 AND isT=0'), 2);//已返回退款金额

		$notMoney  = round(db::one_one('pay_cash', 'SUM(cashMoney)', $wh.' AND status=2 AND isT=1'), 2);//未返回金额

	break;

	case 'f_view':

		//$sysMoney  = round(db::one_one('system_log', 'SUM(money)'), 2);//api 费用统计

		$sysMoney  = round(db::one_one('pay_payment', 'SUM(money-money1)', 'types=0 AND status=1'), 2);//api 费用统计

		$qrcodeMoney  = round(db::one_one('pay_payment', 'SUM(money-money1)', 'types=3 AND status=1'), 2);//线下二维码交易费用统计

		$testMoney = round(db::one_one('pay_payment', 'SUM(money)', 'types=2 AND status=1'), 2);//前台测试费用统计

		$noMoney   = round(db::one_one('pay_cash', 'SUM(cashMoney)', 'status=2 AND isT=1'), 2);//提现没返回金额的统计

		$yesMoney  = round(db::one_one('pay_cash', 'SUM(cashMoney-money)', 'status=1'), 2);//提现成功提现费用统计

		$outMoney  = round(db::one_one('log_money', 'SUM(money)', 'type=0'), 2);//系统处理总费用

		$allMoney  =  $sysMoney + $testMoney +  $yesMoney;//系统总利润 没算 提现没返回金额的和 系统操作的

	break;

	/*

	case 'f_add':

		if (form::hash()) {

			$datas = form::get3('name', 'key', 'remark');

			if (db::exists('sys_finance', array('key' => $datas['key']))) admin::show_message('标记：'.$datas['key'].'已存在');

			if (db::insert('sys_finance', $datas)) {

				admin::show_message('添加成功', $baseUrl.'&method=f_list');

			} else admin::show_message('添加失败，请检查是否数据库错误');

		}

	break;

	case 'f_edit':

		$id = $var->getInt('gp_id');

		if ($item = db::one('sys_finance', '`name`,`key`,`remark`', "id='$id'")) {

			extract($item);

			if (form::hash()) {

				$datas = form::get3('name', 'key', 'remark');

				if (db::exists('sys_finance', "`key`='$datas[finance]' AND id<>'$id'")) admin::show_message('标记：'.$datas['key'].'已存在');

				if (db::update('sys_finance', $datas, "id='$id'")) {

					admin::show_message('编辑成功', $baseUrl.'&method=f_list');

				} else admin::show_message('添加失败，请检查是否数据库错误');

			}

		} else admin::show_message('该财务项目不存在');

	break;

	*/

}

?>