<?php

//---------------------------------------------------------
//财付通即时到帐支付页面回调示例，商户按照此文档进行开发即可
//---------------------------------------------------------
require ("qq.config.php");
require ("ResponseHandler.class.php");
require ("RequestHandler.class.php");
require ("client/ClientResponseHandler.class.php");
require ("client/TenpayHttpClient.class.php");

@header('Content-Type: text/html; charset=UTF-8');

/* 创建支付应答对象 */
$resHandler = new ResponseHandler();
$resHandler->setKey($tenpay_config['key']);

//判断签名
if($resHandler->isTenpaySign()) {
	
	//通知id
	$notify_id = $resHandler->getParameter("notify_id");
	
	//通过通知ID查询，确保通知来至财付通
	//创建查询请求
	$queryReq = new RequestHandler();
	$queryReq->init();
	$queryReq->setKey($tenpay_config['key']);
	$queryReq->setGateUrl("https://gw.tenpay.com/gateway/verifynotifyid.xml");
	$queryReq->setParameter("partner", $tenpay_config['mch']);
	$queryReq->setParameter("notify_id", $notify_id);
	
	//通信对象
	$httpClient = new TenpayHttpClient();
	$httpClient->setTimeOut(5);
	//设置请求内容
	$httpClient->setReqContent($queryReq->getRequestURL());
	
	//后台调用
	if($httpClient->call()) {
		//设置结果参数
		$queryRes = new ClientResponseHandler();
		$queryRes->setContent($httpClient->getResContent());
		$queryRes->setKey($tenpay_config['key']);
		
		//判断签名及结果
		//只有签名正确,retcode为0，trade_state为0才是支付成功
		if($queryRes->isTenpaySign() && $queryRes->getParameter("retcode") == "0" && $queryRes->getParameter("trade_state") == "0" && $queryRes->getParameter("trade_mode") == "1" ) {
			//取结果参数做业务处理
			$out_trade_no = $queryRes->getParameter("out_trade_no");
			//财付通订单号
			$transaction_id = $queryRes->getParameter("transaction_id");
			//金额,以分为单位
			$total_fee = $queryRes->getParameter("total_fee");
			//如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
			$discount = $queryRes->getParameter("discount");
			
			//------------------------------
			//处理业务开始
			//------------------------------
			$item = db::one('pay_payment', '*', "sn='$out_trade_no' AND status=1");
			if ($item){
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
				}
			}

		} else {
			//当做不成功处理
			exit("财付通即时到帐支付失败!");
		}
	}else {
		exit("财付通即时到帐支付失败!".$httpClient->getErrInfo());
		//后台调用通信失败,写日志，方便定位问题，这些信息注意保密，最好不要打印给用户
		//('订单通知查询失败:' . $httpClient->getResponseCode() .',' . $httpClient->getErrInfo().$resHandler->getDebugInfo(),4,'shop');
	} 
} else {
	exit("认证签名失败!".$resHandler->getDebugInfo());
}

?>