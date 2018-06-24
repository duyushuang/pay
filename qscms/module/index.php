<?php

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');

!qscms::defineTrue('INSTALL') && qscms::gotoUrl('/install/');

$time = time();

if (!$member && !in_array($var->p1, array('index', 'cs'))) qscms::gotoUrl('/user/login.html');




switch($var->p1){

	case 'index':

	

		

		//$a = $member->fc('20170721160547838928', 2);

		//echo $a;exit; 

	break;

	

	case 'cs':
	
		/*
$msg = '您的手机号：{mobile}，验证码：{vcode}。请不要把验证码泄露给其他人。如非本人操作，可不用理会！';
		$rs = member_base::message('13541104347', $msg);
		echo $rs;exit;

		/*

		$list = db::select('pay_payment|system_log:sn=sn', 'id,uid,sn,money,money1,bl|allMoney,money log_money,bl bl1', "t0.money=t0.money1 AND t0.bl!=0");

		

		if ($list){

			foreach($list as $v){

				db::update('pay_payment', 'bl=0', "id='$v[id]'");	

				db::delete('system_log', 'bl=0', "sn='$v[sn]'");

			}

		}

		

		

		exit;

		*/

		/*测试多码合一 每个用户生成独一二维码 用于一码多付*/

		$dir = d(qscms::getCfgPath('/system/imgRoot').'phpqrcode/');

		$url = u(qscms::getCfgPath('/system/imgRoot').'phpqrcode/');

		$qrcodeDir = $dir.$member->m_id.'.png';

		$qrcodeUrl = $url.$member->m_id.'.png';

		if (!file_exists($qrcodeDir)){

			$tplRoot = qd(qscms::getCfgPath('/system/tplRoot_phpqrcode'));

			include_once($tplRoot.'phpqrcode.php');

			$url = WEB_URL.'/recharge/qrcode/'.$member->m_id;//

			$errorCorrectionLevel = 'L';  // 纠错级别：L、M、Q、H  

			$matrixPointSize = 10; // 点的大小：1到10  

			QRcode::png($url, $qrcodeDir, $errorCorrectionLevel, $matrixPointSize, 2);

		}

		

		

		

		//echo file_get_contents('https://www.jinmipay.com/gave');

		exit;

		echo preg_match('/^[A-Za-z0-9]+$/', '123123123123');exit;

		print_r($_SERVER);exit;

		echo isMobile() ? 1 : 2;exit;

		echo message::cash_sendOne(13541104347);exit;

		echo qscms::addslashes("\]kqpowqwek'\\/\\//';");exit;

		echo qscms::addslashes("\]kqpowqwek'\\/\\//';");exit;

		//print_r($var->domains);exit;

		//echo WEB_URL;exit;

		//print_r(member_base::get_fans_info('obwBVwLGXAtb66kndpC6FXgusu6Y'));exit;

		echo $member->qrcode_url();exit;

	/*

		echo time::$todayStart;exit;

		$rs = message::login_sendOne(13541104347);

		echo $rs;exit;

		*/

		//$ip = qscms::ipint();

		//echo qscms::intip();exit;

	

	/*

		$cs = array(

			'code' => 1,

			'msg'  => '',

			'data' => array('20160806151343349' => 'SUCCESS', '20160806151343349'  => 'FALSE')

		);

		echo string::json_encode($cs);

		print_r($var);exit;

		*/

	exit;

	break;

}

?>