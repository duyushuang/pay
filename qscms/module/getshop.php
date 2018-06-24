<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
!qscms::defineTrue('INSTALL') && qscms::gotoUrl('/install/');
$payment = $var->gp_payment;
$out_trade_no = $var->gp_out_trade_no;
$arr = array('status' => false, 'url' => '', 'msg' => '操作失败');
if ($out_trade_no && $item = db::one('pay_payment', '*', "sn='$out_trade_no' AND status=1")){
	$arr['status'] = true;
	$m = new member_center($item['uid']);
	if ($item['return_url']){
		$datas = array(
			'pid'          => $item['uid'],//商户ID
			'out_trade_no' => $item['out_trade_no'],//用户提交的订单号
			'trade_no'     => $out_trade_no,//返回系统生成的交易成功订单号 不是微信支付宝生成的订单号
			'subject'      => $item['subject'],
			'money'  	   => $item['money'],
			'type'         => $item['type'],
			'trade_status' => 'SUCCESS',
			'sign_type'    => 'MD5'
		);
		$datas['sign'] = string::keymd5Sign($datas, $m->m_keys);
		$arr['url']    = qscms::getUrl('/jump?url='.rawurlencode($item['return_url'].'?'.string::createLinkstring($datas)));
	}else $arr['url']  = qscms::getUrl('/jump');
	$arr['msg'] = '充值成功';
}
exit(string::json_encode($arr));
?>