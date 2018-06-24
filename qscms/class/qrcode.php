<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class qrcode{
	public static function pay($datas){
		
		
		
		$datas = qscms::filterArray($datas, array('uid', 'money', 'title', 'openid', 'auth_code'), true);
		if ($datas['money'] < 0.01) return '充值金额必须大于或等于0.01元。';
		$type = '';
		if (qscms::is_weixin()) $type = 'wxpay';
		elseif (qscms::is_alipay()) $type = 'alipay';
		elseif (qscms::is_qqpay()) $type = 'qqpay';
		
		
		$subject = '-';
		$datas['title'] && $subject = $datas['title'];
		db::autocommit();
		do {
			$sn = db::createId();
		} while(db::exists('pay_payment', array('sn' => $sn), '', true));
		if (db::insert('pay_payment', array(
			'sn'                => $sn,
			'uid'               => $datas['uid'],
			'subject'			=> $subject,
			'type' 		    	=> $type,
			'types' 			=> 3,//线下充值的
			'status' 			=> 0,
			'money'  			=> $datas['money'],
			'addTime' 			=> time()
		))){
			db::commit(true);
			$out_trade_no = $sn;
		}else{
			db::autocommit(false);
			return '订单生成失败';
		}
		
		
		
		//$out_trade_no = date("YmdHis", time()).mt_rand(10,99);
		
		if ($type == 'wxpay'){//微信支付的
			if (!$datas['openid']) return '微信支付操作失败';
		
			$tplRoot = qd(qscms::getCfgPath('/system/tplRoot_payment').'wxpay/example/');
			require_once $tplRoot."WxPay.JsApiPay.php";
			require_once $tplRoot."../lib/WxPay.Api.php";
			require_once $tplRoot.'log.php';
			$openId = $datas['openid'];
			$tools = new JsApiPay();
			$input = new WxPayUnifiedOrder();
			$input->SetBody($subject);
			$input->SetAttach($subject);
			$input->SetOut_trade_no($out_trade_no);
			$input->SetTotal_fee($datas['money'] * 100);
			$input->SetTime_start(date("YmdHis"));
			$input->SetTime_expire(date("YmdHis", time() + 600));
			$input->SetGoods_tag($subject);
			$input->SetNotify_url(WEB_URL."/qscms/payment/wxpay/example/notify.php");
			$input->SetTrade_type("JSAPI");
			$input->SetOpenid($openId);
			$order = WxPayApi::unifiedOrder($input);
			if (!empty($order['err_code_des']) || $order['return_msg'] != 'OK'){
				$msg = $order['err_code_des'] ? $order['err_code_des'] : $order['return_msg'];
				return $msg;
			}else{
				$json = $tools->GetJsApiParameters($order);
				return string::json_decode($json);
			}
		}elseif ($type == 'alipay'){
			$tplRoot = qd(qscms::getCfgPath('/system/tplRoot_payment').'alipay_qrcode/alipay/aop/');
			include_once($tplRoot.'AopClient.php');
			include_once($tplRoot.'request/AlipayTradeCreateRequest.php');
			include_once($tplRoot.'request/AlipaySystemOauthTokenRequest.php');
			$app_id = cfg::get('alipay', 'app_id');
			$aop = new AopClient ();
			$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
			$aop->appId = $app_id;
			$aop->rsaPrivateKeyFilePath =  $tplRoot.'rsa_private_key.pem';
			$aop->alipayPublicKey = $tplRoot.'rsa_public_key.pem';
			$aop->apiVersion = '1.0';
			$aop->signType = 'RSA';
			$aop->postCharset='UTF-8';
			$aop->format='json';
			$request = new AlipaySystemOauthTokenRequest ();
			$request->setGrantType("authorization_code");
			$request->setCode($datas['auth_code']);
			$result = $aop->execute ( $request); 
			$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
			$obj = $result->$responseNode;
			$buyer_id = $obj->user_id;
			if (!$buyer_id) return '支付宝授权失败';
			$request = new AlipayTradeCreateRequest ();
			$request->setBizContent(json_encode(array('out_trade_no' => $out_trade_no, 'total_amount' => $datas['money'], 'subject' => $subject, 'buyer_id' => $buyer_id, 'body' => $subject)));
			$request->setNotifyUrl(WEB_URL."/qscms/payment/alipay_qrcode/alipay/notify_url.php");
			$request->setReturnUrl(WEB_URL."/qscms/payment/alipay_qrcode/alipay/return_url.php");
			$result = $aop->execute ( $request); 
			$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
			$tradeNO = $result->$responseNode->trade_no;
			if ($tradeNO){
				return array('tradeNO' => $tradeNO);
			}else return '支付宝参数错误';
		
		}elseif ($type == 'qqpay'){
			$tplRoot = qd(qscms::getCfgPath('/system/tplRoot_payment').'qqpay/');
			require_once($tplRoot."qqpay.config.php");
			require_once($tplRoot."RequestHandler.class.php");
			/* 创建支付请求对象 */
			$reqHandler = new RequestHandler();
			$reqHandler->init();
			$reqHandler->setKey($tenpay_config['key']);
			$reqHandler->setGateUrl("https://myun.tenpay.com/cgi-bin/wappayv2.0/wappay_init.cgi");
			
			//----------------------------------------
			//设置支付参数 
			//----------------------------------------
			$reqHandler->setParameter("ver", "2.0"); //版本号，ver默认值是1.0
			$reqHandler->setParameter("charset", "1"); //1 UTF-8, 2 GB2312
			$reqHandler->setParameter("bank_type", "0"); //银行类型
			$reqHandler->setParameter("desc", $subject); //商品描述，32个字符以内
			$reqHandler->setParameter("pay_channel", "1"); //描述支付渠道
			$reqHandler->setParameter("bargainor_id", trim($tenpay_config['mch']));
			$reqHandler->setParameter("sp_billno", $out_trade_no);
			
			$reqHandler->setParameter("total_fee", $datas['money'] * 100);  //总金额
			$reqHandler->setParameter("fee_type", "1");               //币种
			$reqHandler->setParameter("notify_url", WEB_URL."/qscms/payment/qqpay/notify_url.php");//请求的URL
			//print_r($reqHandler);exit;
			$reqUrl = $reqHandler->getRequestURL();
			//echo $reqUrl;exit;
			$data = winsock::open($reqUrl);
			$data || $data = file_get_contents($reqUrl);
			if(preg_match("!<token_id>(.*?)</token_id>!",$data,$match)){
				$code_url = 'https://myun.tenpay.com/mqq/pay/qrcode.html?_wv=1027&_bid=2183&t='.$match[1];
				return array('prepayId' => $match[1]);
			}else{
				preg_match("!<err_info>(.*?)</err_info>!",$data,$match);
				return !empty($match[1]) ? $match[1] : '未知错误';
			}
			
		}else return '未知错误';
	}
}
	
	