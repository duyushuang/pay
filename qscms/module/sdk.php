<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
!qscms::defineTrue('INSTALL') && qscms::gotoUrl('/install/');
$op = $var->p1;
template::addPath($var->p0, $var->p0);
$var->tplName = $op;
switch($op){
	case 'index'://充值测试
		if (form::hash()){
			$datas = form::get3('out_trade_no', 'subject', 'total_fee', 'type');
			
			//echo '/qspay.php?type='.$datas['type'].'&subject='.$datas['subject'].'&out_trade_no='.$datas['out_trade_no'].'&total_fee='.$datas['total_fee'];exit;
			qscms::gotoUrl('/api/qspay.php?type='.$datas['type'].'&subject='.$datas['subject'].'&out_trade_no='.$datas['out_trade_no'].'&total_fee='.$datas['total_fee']);//使用api接口
			/*
			if ($datas['total_fee'] < 0.01) qscms::showMessage('金额不能低于0.01元', $baseUrl);
			
			if (!$datas['subject']) qscms::showMessage('描述不能为空', $baseUrl);
			if (!is_numeric($datas['out_trade_no'])) qscms::showMessage('订单号只能是纯数字', $baseUrl);
			if (strlen($datas['out_trade_no']) > 60 || strlen($datas['out_trade_no']) < 9) qscms::showMessage('订单号限制长度10-60位', $baseUrl);
			if (!pay::ename($datas['type'])) qscms::showMessage('没有该支付方式', $baseUrl);
			
			db::autocommit();
			do {
				$sn = db::createId();
			} while(db::exists('pay_payment', array('sn' => $sn), '', true));
			if (db::insert('pay_payment', array(
				'sn' => $sn,
				'uid' => '',//默认一个
				'type' => $datas['type'],
				'types' => 2,//测试的
				'status' => 0,
				'money'  => $datas['total_fee'],
				'addTime' => time()
			))){
				db::commit(true);
				$out_trade_no = $sn;
				qscms::gotoUrl("/pay/?out_trade_no=$sn&payment=".$datas['type']);
			}
			db::autocommit(false);
			if (!$out_trade_no) qscms::message('未知错误', $baseUrl);
			*/
		}
	break;
}
?>