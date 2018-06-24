<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
!qscms::defineTrue('INSTALL') && qscms::gotoUrl('/install/');
switch($var->p1){
	case 'order':
		$pid = $var->gp_pid;
		$key = $var->gp_key;
		$out_trade_no = $var->gp_out_trade_no;
		$arr = array('code' => 0, 'msg' => '');
		if ($pid && $key && $out_trade_no){
			if (db::exists('member',"id='$pid' AND `keys`='$key' AND status=0 AND isApi=1")){
				if ($item = db::one('pay_payment', '*', "out_trade_no='$out_trade_no' AND uid='$pid'")){
					$arr['trade_no'] = $item['sn'];
					$arr['out_trade_no'] = $item['out_trade_no'];
					$arr['type'] = $item['type'];
					$arr['addtime'] = $item['addTime'];
					$arr['paytime'] = $item['payTime'];
					$arr['subject'] = $item['subject'];
					$arr['money'] = $item['money'];
					$arr['status'] = $item['status'];
					$arr['code'] = 1;
				}else $arr['msg'] = '没有找到该订单';
			}else $arr['msg'] = 'PID或KEY错误';
		}else $arr['msg'] = '参数不正确';
		exit(string::json_encode($arr));
		
		
		/*
		api/order?pid={商户ID}&key={商户密钥}&out_trade_no={商户订单号}
	返回状态码 	code 	Int 	1 	1为成功，其它值为失败
	返回信息 	msg 	String 	查询订单号成功！ 	
	支付订单号 	trade_no 	String 	2016080622555342651 	支付订单号
	商户订单号 	out_trade_no 	String 	20160806151343349 	商户系统内部的订单号
	支付方式 	type 	String 	alipay 	alipay:支付宝1,wxpay:微信支付
	商户ID 	pid 	Int 	1001 	发起支付的商户ID
	创建订单时间 	addtime 	String 	2016-08-06 22:55:52 	
	完成交易时间 	endtime 	String 	2016-08-06 22:55:52 	
	商品名称 	name 	String 	VIP会员 	
	商品金额 	money 	String 	1.00 	
	支付状态 	status 	Int 	0 	1为支付成功，0为未支付
		*/
	break;	
}
?>