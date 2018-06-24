<?php
/**
 * 这个商户的returl_url页面实现的模板
 * 该页面的业务逻辑是：
 * 1. 当商户收到百度钱包支付成功的通知后，调用sdk中预处理操作确定该订单支付成功
 * 2. 确认支付成功后，商户自己的业务逻辑，比如记账之类的。
 * 注意，sdk中的query_order_state()方法，必须商户自己实现，
 * 否则会由于收到多次百度钱包的支付结果通知，导致商户自己出现资金的不一致。
 */
require_once 'bdpay_sdk.php';

$bdpay_sdk = new bdpay_sdk();

$bdpay_sdk->log(sprintf('get the notify from baifubao, the request is [%s]', print_r($_GET, true)));
if (false === $bdpay_sdk->check_bfb_pay_result_notify()) {
	$bdpay_sdk->log('get the notify from baifubao, but the check work failed');
	return;
}
$bdpay_sdk->log('get the notify from baifubao and the check work success');


/*
 * 此处是商户收到百度钱包支付结果通知后需要做的自己的具体业务逻辑，比如记账之类的。 只有当商户收到百度钱包支付 结果通知后，
 * 所有的预处理工作都返回正常后，才执行该部分
 */

// 向百度钱包发起回执
//$bdpay_sdk->notify_bfb();
$out_trade_no = !empty($_GET['order_no']) ? $_GET['order_no'] : '';
if ($out_trade_no && $item = db::one('pay_payment', '*', "sn='$out_trade_no' AND status=1")){
	$arr['status'] = true;
	$m = new member_center($item['uid']);
	if ($item['return_url']){
		$datas = array(
			'pid'          => $item['uid'],//商户ID
			'out_trade_no' => $item['out_trade_no'],//用户提交的订单号
			'trade_no'     => $sn,//返回系统生成的交易成功订单号 不是微信支付宝生成的订单号
			'money'  	   => $item['money'],
			'type'         => $userInfo['type'],
			'trade_status' => 'SUCCESS',
			'sign_type'    => 'MD5'
		);
		$datas['sign'] = string::keymd5Sign($datas, $m->m_keys);
		qscms::gotoUrl($item['return_url'].'?'.string::createLinkstring($datas), true);
	}else $arr['url'] = qscms::gotoUrl('/user/');
}

?>