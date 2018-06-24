<?php

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');

!qscms::defineTrue('INSTALL') && qscms::gotoUrl('/install/');

//if (!$member) qscms::gotoUrl('/user/login.html');

$payment = $var->gp_payment;

$out_trade_no = $var->gp_out_trade_no;

$item = db::one('pay_payment', '*', "sn='$out_trade_no'");

if ($item){

	$site_name = $item['site_name'];

	if (!$site_name) $site_name = '';

	if ($item['status'] == 1){

		qscms::showMessage('该订单已支付');
		//qscms::showMessage('该订单已支付', $item['return_url'] ? $item['return_url'] : qscms::getUr('/'));	

	}

	if ($item['addTime'] + (15 * 60) < time()) {

		qscms::showMessage('订单超时');

	}

	if ($item['status'] != 0){

		qscms::showMessage('订单失效');

	}

}else qscms::showMessage('没有该订单');

$return_url = WEB_URL;
if (!empty($item['return_url'])) $return_url = $item['return_url'];

$out_trade_no = $item['sn'];

switch($item['types']){

	case 0://商户接口调用的

		$subject = $site_name.'-'.$item['subject'];

		//$subject = $webName.'订单：'.$item['sn'];

		$total_fee = $item['money'];

		//$total_fee = 0.01;

	break;

	case 1://商户充值的

		$subject ='订单号：'.$item['sn'];

		$total_fee = $item['money'];

	break;

	case 2://测试数据

		$subject = '测试数据：'.$item['sn'];

		$total_fee = $item['money'];

	break;

}

switch($payment){

	case 'wxpay'://微信

		db::update('pay_payment', "type='$payment'", "sn='$item[sn]'");	

		$tplRoot = qd(qscms::getCfgPath('/system/tplRoot_payment').'wxpay/example/');

		if (qscms::is_weixin() && IS_MODULE1) {

			/*

			//需要微信内部支付的话 就用主域名 泛域名支付不了 原因是 微信设置的回调地址不一致 获取不了openid

			*/

			$weburl = cfg::get('web', 'weburl');

			if ($weburl){

				$webInfo = pathinfo($weburl);

				if ($webInfo['basename'] != $var->domains['host']){

					qscms::gotoUrl($weburl.'pay/?payment='.$payment.'&out_trade_no='.$out_trade_no, true);

				}

			}

			include_once($tplRoot.'jsapi.php');	

		}elseif (qscms::isApp()){

			include_once($tplRoot.'native.php');

		}elseif (IS_MODULE1) {
			 include_once($tplRoot.'native.php');
			//include_once($tplRoot.'h5.json.php');

		}else include_once($tplRoot.'native.php');

	break;

	case 'alipay'://支付宝

		db::update('pay_payment', "type='$payment'", "sn='$item[sn]'");

		$tplRoot = qd(qscms::getCfgPath('/system/tplRoot_payment').'alipay'.(IS_MP ? '_m' : '').'/');

		include_once($tplRoot.'alipayapi.php');

	break;

	case 'qqpay'://QQ钱包

		db::update('pay_payment', "type='$payment'", "sn='$item[sn]'");	

		$tplRoot = qd(qscms::getCfgPath('/system/tplRoot_payment').'qqpay/');

		/*

			include_once($tplRoot.'qqpay_m1.php');//PC QQ登录支付

		*/

		include_once($tplRoot.'qqpay'.(IS_MP ? '_m' : '').'.php');

		

	break;

	/*

	case 'bdpay'://百度钱包

		db::update('pay_payment', "type='$payment'", "sn='$item[sn]'");	

		$tplRoot = qd(self::getCfgPath('/system/tplRoot_payment').'bdpay/');

		include_once($tplRoot.'pay_unlogin.php');

	break;

	*/

	default://百度网银

		if (pay::ename($payment)){

			db::update('pay_payment', "type='$payment'", "sn='$item[sn]'");	

			$tplRoot = qd(qscms::getCfgPath('/system/tplRoot_payment').'bdpay/');

			include_once($tplRoot.'pay_unlogin.php');

		}else{

			qscms::showMessage('没有该支付类型');

		}

	/*

	case 11: //银联

	case 101://中国工商银行

	case 201://中国招商银行

	case 301://中国建设银行

	case 401://中国农业银行

	case 501://中信银行

	case 601://浦东发展银行

	case 701://中国光大银行

	case 801://平安银行

	case 1101://交通银行

	case 1201://中国银行

	case 13://银联在线UPOP

	case 1901://广发银行

	*/

	//百度钱包的 网银系列

		

	break;

}

exit;

?>