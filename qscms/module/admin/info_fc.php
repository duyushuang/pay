<?php


(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
$top_menu=array(
	'index' => '分成设置',
	'list' => '分成列表',
	'qx' => array('name' => '撤销恢复', 'hide' => true)
);
$adminUser = $var->admin['username'];
$ip = qscms::ipint();
$time = time();
$top_menu_key = array_keys($top_menu);
($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
switch ($method) {
	case 'index':
		if (form::hash()){
			$datas = form::get3('operation', 'm', 'c');
			if($datas['operation'] == 'fc'){
				if (count($datas['m']) == count($datas['c'])){
					db::delete('fc');
					foreach ($datas['m'] as $k => $v){
						if (isset($v) && isset($datas['c'][$k])){
							db::insert('fc', array(
								'm' => $v,
								'c' => $datas['c'][$k],
							));
						}
					}
					admin::showMessage('操作成功', $baseUrl);
				}else admin::showMessage('操作失败', $baseUrl);
			}
		}
		$fc = db::select('fc', '*', '', 'id');
		//$list = db::
	break;
	case 'list';
		$wh = '1=1';
		$sn = $uidType = $status = $minMoney = $maxMoney = '';
		$keys = array('sn', 'out_trade_no', 'uid', 'minMoney', 'maxMoney', 'minCredit', 'maxCredit','addTime1', 'addTime2');
		$urlVar = '';
		$vars = array();
		foreach ($keys as $v) {
			$val = $var->{'gp_'.$v};
			$vars[$v] = $val;
			!is_null($val) && $val !== '' && (($urlVar && $urlVar .= '&') || !$urlVar) && $urlVar .= $v.'='.urlencode($val);
		}
		extract($vars);
		if ($sn){
			$wh .= " AND sn='$sn'";
		}
		if ($out_trade_no){
			$wh .= " AND out_trade_no='$out_trade_no'";
		}
		if ($uid){
			$wh .= " AND (uid='$uid')"; // OR fuid='$uid'
		}
		if ($minMoney){
			$wh .= " AND money >= $minMoney";
		}
		if ($maxMoney){
			$wh .= " AND money <= $maxMoney";
		}
		if ($minCredit){
			$wh .= " AND credit >= $minCredit";
		}
		if ($maxCredit){
			$wh .= " AND credit <= $maxCredit";
		}
		if (!is_null($addTime1) && $addTime1 !== '') {
				$wh && $wh .= ' AND ';
				$time1 = time::getGeneralTimestamp($addTime1);
				$wh .= 'time>=\''.$time1.'\'';
			}
		if (!is_null($addTime2) && $addTime2 !== '') {
				$wh && $wh .= ' AND ';
				$time2 = time::getGeneralTimestamp($addTime2.' 23:59:59');
				$wh .= 'time<=\''.$time2.'\'';
		}
		$list = array();
		if ($total = db::dataCount('fc_log', $wh)){
			$list = db::select('fc_log', '*', $wh, 'id DESC', $pagesize, $page);
			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.'&method='.$method.($urlVar ? '&'.$urlVar : '').'&type=so&page={page}', $pagestyle);
		}
		
	break;
	/*
	case 'qx':
		$id = $var->getInt('gp_id');
		if ($id){
			if ($item = db::one('fc_log', '*', "id='$id'")){
				if ($item['status'] == 0){//撤销 就是把金额积分 撤销掉
					db::update('log_money', 'status=1', "id='$item[id]'");
					db::update('member', "money=money-$item[money],credit=credit-$item[credit]", "id='$item[uid]'");
					db::insert('admin_log', "user='$adminUser',info='撤销用户{$item[uid]}日志操作：金额日志ID：{$item[id]}',ip='$ip',addTime='$time'");
				}else{//恢复 就是把金额积分 恢复回去
					db::update('log_money', 'status=0', "id='$item[id]'");
					db::update('member', "money=money+$item[money],credit=credit+$item[credit]", "id='$item[uid]'");
					db::insert('admin_log', "user='$adminUser',info='恢复用户{$item[uid]}日志操作：金额日志ID：{$item[id]}',ip='$ip',addTime='$time'");
				}
				admin::showMessage('操作成功');
			}
		}
	break;
	*/
}
?>