<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
$tplRoot = !empty($tplRoot) ? $tplRoot : '';
require_once $tplRoot."WxPay.JsApiPay.php";
require_once $tplRoot."../lib/WxPay.Api.php";
require_once $tplRoot.'log.php';
	$mchPay = new WxPayWxMchPay();
	// 用户openid
	$mchPay->values('openid', $openid);//'ocQxexDsFoYi_6sBsmUTKBEyhnT8'
	// 商户订单号
	$mchPay->values('partner_trade_no', time().mt_rand(100,999));
	// 校验用户姓名选项
	$mchPay->values('check_name', 'NO_CHECK');
	// 企业付款金额  单位为分
	$mchPay->values('amount', $money * 100);
	// 企业付款描述信息
	empty($remark) && $remark = '提现';
	$mchPay->values('desc', $remark);
	// 调用接口的机器IP地址  自定义
	$mchPay->values('spbill_create_ip', '127.0.0.1'); # getClientIp()
	// 收款用户姓名
	// $mchPay->setParameter('re_user_name', 'Max wen');
	// 设备信息
	// $mchPay->setParameter('device_info', 'dev_server');
	
	$mchPay->createXml();
	//print_r($mchPay);exit;
	$data = $mchPay->get();
	/*
	if( !empty($data) ) {
		$data = simplexml_load_string($data, null, LIBXML_NOCDATA);
		if ($data->return_code == 'SUCCESS'){
			echo qscms::ipint().'<br />';
			echo 'OK';exit;	
		}
		print_r($data);exit;
		echo json_encode($data);
	}else{
		echo json_encode( array('return_code' => 'FAIL', 'return_msg' => 'transfers', 'return_ext' => array()) );
	}
	*/
?>

