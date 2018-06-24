<?php

//---------------------------------------------------------
//财付通即时到帐支付后台回调示例，商户按照此文档进行开发即可
//---------------------------------------------------------
@header('Content-Type: text/html; charset=UTF-8');
require ("qqpay.config.php");
require ("ResponseHandler.class.php");
require ("RequestHandler.class.php");
require ("client/ClientResponseHandler.class.php");
require ("client/TenpayHttpClient.class.php");
/* 创建支付应答对象 */
$resHandler = new ResponseHandler();
$resHandler->setKey($tenpay_config['key']);
//判断签名
if($resHandler->isTenpaySign()) {
	//判断签名及结果（即时到帐）
	if($resHandler->getParameter("pay_info") == 'success' && $resHandler->getParameter("pay_result") == "0") {
		//取结果参数做业务处理
		$out_trade_no = $resHandler->getParameter("sp_billno");
		//财付通订单号
		$transaction_id = $resHandler->getParameter("transaction_id");
		//金额,以分为单位
		$total_fee = $resHandler->getParameter("total_fee");
		//币种
		$fee_type = $resHandler->getParameter("fee_type");
			
		//------------------------------
		//处理业务开始
		//------------------------------
		$rs = member_center::confirmPay($out_trade_no, $transaction_id);
		//------------------------------
		//处理业务完毕
		//------------------------------
		echo "success";
	} else {
		echo "fail";
	}

} else {
	echo "fail";
}

?>