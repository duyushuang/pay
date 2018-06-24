<?php

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');

!qscms::defineTrue('INSTALL') && qscms::gotoUrl('/install/');

$op = $var->p1;

$out_trade_no = '';
switch($op){

	case 'index'://用户充值

		$rs = pay::recharge_index($_POST);

		if (is_array($rs)){

			qscms::gotoUrl("/pay/?out_trade_no=$rs[out_trade_no]&payment=$rs[payment]");

		}else exit($rs);

	break;

	case 'qrcode':
		$openid = $auth_code = '';
		if (IS_MODULE1 && (qscms::is_alipay() || qscms::is_weixin() || qscms::is_qqpay())){
			$uid = $var->p2;
			$m = new member_center($uid);
			//$openid = 'obwBVwLGXAtb66kndpC6FXgusu6Y';
			if (!$m->status || !$m->m_keys || !$m->m_isApi) qscms::showMessage('该商户未开通接口调用，请联系客服开通该服务。');
			/*千万别把异步处理丢到 授权下面 不然微信和支付 会跳转的*/
			if (form::hash()){
				$arr = array('status' => false, 'msg' => '', 'data' => array());
				$_POST['uid'] = $uid;
				$rs = qrcode::pay($_POST);
				if (is_array($rs)) {
					$arr['status'] = true;
					$arr['data'] = $rs;
				}
				else $arr['msg'] = $rs;
				exit(json_encode($arr));
			}
			
			
			
			if (qscms::is_alipay()){//获取支付宝 授权 然后通过授权code 获取 user_id
				$app_id = cfg::get('alipay', 'app_id');
				if (!$app_id) exit('Alipay information is not perfect');
			
				$weburl = cfg::get('web', 'weburl');
				if ($weburl){
					$webInfo = pathinfo($weburl);
					if ($webInfo['basename'] != $var->domains['host']){
						qscms::gotoUrl($weburl.'/recharge/qrocde/'.$uid, true);
					}
				}
				$url = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id='.$app_id.'&scope=auth_base&redirect_uri='.urlencode(qscms::getUrl('/recharge/qrcode/'.$uid)).'&state=state';
				if ($var->gp_auth_code){
					$auth_code = $var->gp_auth_code;
				}else{
					header("location:".$url);
				}
			}elseif (qscms::is_weixin()){//如果是微信 获取微信openid 这些信息
				$weburl = cfg::get('web', 'weburl');
				if ($weburl){
					$webInfo = pathinfo($weburl);
					if ($webInfo['basename'] != $var->domains['host']){
						qscms::gotoUrl($weburl.'/recharge/qrocde/'.$uid, true);
					}
				}
				$wx = new weixin();
				$openid = $wx->GetOpenid();
			}elseif (qscms::is_qqpay()){
			}
			$var->tplName = 'qrcode';

		}else {
			echo '是否手机访问'.IS_MODULE1.'<br />';
			echo $_SERVER['HTTP_USER_AGENT'];exit;
			qscms::showMessage('请使用支付宝，微信或QQ扫码', '/');
		}

	break;

	case 'user'://商户充值
	case 'users'://商户充值

		if (!$member) qscms::gotoUrl('/user/login');

		$out_trade_no = $var->gp_out_trade_no;
		if ($item = db::one('pay_payment', '*', "sn='$out_trade_no' AND status=0")){

			//if ($item['addTime'] + )

			$money = $item['money'];

		}

	break;

	case 'test'://测试

		$money = $var->getMoney('gp_money');

		if ($money < 0.01) $money = 0.01;

			db::autocommit();

			do {

				$sn = db::createId();

			} while(db::exists('pay_payment', array('sn' => $sn), '', true));

			if (db::insert('pay_payment', array(

				'sn' => $sn,

				'uid' => '',//默认一个

				'type' => 'wxpay',

				'types' => 2,//商户充值的

				'status' => 0,

				'money'  => $money,

				'addTime' => time()

			))){

				db::commit(true);

				$out_trade_no = $sn;

				//qscms::gotoUrl("/recharge/user?out_trade_no=$sn");

			}

			db::autocommit(false);

			if (!$out_trade_no) qscms::showMessage('操作失败');

	break;

}

if ($money < 0) qscms::showMessage('操作失败');

?>