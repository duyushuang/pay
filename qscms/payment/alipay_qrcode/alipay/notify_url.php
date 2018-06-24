<?php
/*
之前的全部不要了 支付宝回调回来 去支付宝查询一次 来判断
 */

include_once(dirname(__FILE__).'/../../../index.php');

$out_trade_no = $_POST['out_trade_no'];
$trade_no     = $_POST['trade_no'];
$app_id = cfg::get('alipay', 'app_id');
/*--------------------------------------------------------------------------------------------------*/
$tplRoot = qd(qscms::getCfgPath('/system/tplRoot_payment').'alipay_qrcode/alipay/aop/');
include_once($tplRoot.'AopClient.php');
include_once($tplRoot.'request/AlipayTradeQueryRequest.php');
$aop = new AopClient ();
$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
$aop->appId = $app_id;
$aop->rsaPrivateKeyFilePath =  $tplRoot.'rsa_private_key.pem';
$aop->alipayPublicKey = $tplRoot.'rsa_public_key.pem';
$aop->apiVersion = '1.0';
$aop->signType = 'RSA';
$aop->postCharset='UTF-8';
$aop->format='json';
$request = new AlipayTradeQueryRequest ();
$request->setBizContent("{" .
"\"out_trade_no\":\"".$out_trade_no."\"," .
"\"trade_no\":\"".$trade_no."\"" .
"  }");
$result = $aop->execute ( $request); 
$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
$resultCode = $result->$responseNode->code;
if ($result->$responseNode->trade_status == 'TRADE_SUCCESS'){
	$rs = member_center::confirmPay($out_trade_no, $trade_no);
	if (!$rs){
		logResult("ERROR::member_center::confirmPay($out_trade_no, $trade_no)\r\n");	
		echo 'fail';
	}else{
		echo 'success';
	}
}else{
	echo 'fail';
}
exit;

//success

//fail

?>