<?php

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');

class message{

	private static $ini = false, $logined = false, $is_true = false, $isStatus = false, $money = 0, $oneMsgLen = 70, $suffix = false, $status = false, $username = false, $password = false, $passwordRun = false, $account_url = false, $send_url = false, $price = false, $encoding = 'utf-8';

	public static function ini($username = '', $password = '', $price = 0){

		$statusStr = $username = $password  = $passwordRun = $account_url = $send_url = $price = $sys_msg_debug = '';

		

		

		$statusStr || $statusStr = cfg::get('sms', 'status');

		$username || $username = cfg::get('sms', 'username');

		$password || $password = cfg::get('sms', 'password');

		$passwordRun || $passwordRun = cfg::get('sms', 'passwordRun');

		$account_url || $account_url = cfg::get('sms', 'account_url');

		$send_url || $send_url = cfg::get('sms','send_url');

		$price    || $price    = cfg::get('sms', 'price_one');

		$sys_msg_debug = cfg::getBoolean('sms', 'is_imitate');

		//self::$oneMsgLen = cfg::getInt('sms', 'oneLen');

		//self::$suffix = cfg::get('sms', 'msg_suffix');

		//self::$oneMsgLen -= mb_strlen(self::$suffix);

		//self::$suffix = iconv(ENCODING, self::$encoding, self::$suffix);

		$is_true = false;//是否设置其中一条状态是发送成功的

		if ($statusStr){

			$statusArr = explode("\r\n", $statusStr);

			if ($statusArr){

				foreach($statusArr as $v){

					if (trim($v)){

						$sp = preg_split("/[\s]+/", trim($v));

						if (count($sp) == 3){

							if ($sp[2] == 'true'){

								$is_true = true;	

							}

							$status[$sp[0]] = array('msg' => $sp[1], 'status' => ($sp[2] == 'true' ? true : false));

							$isStatus = true;

						}else $isStatus = false;

					}else $isStatus = false;

					

				}	

			}

		}

		if ($username && $password && $price) {

			$username = urlencode(iconv(ENCODING, 'GBK', $username));

			$password = urlencode(iconv(ENCODING, 'GBK', $password));

			if ($sys_msg_debug) {

				$html = '9999.9';

			} else {

				$is_num = cfg::getBoolean('sms', 'is_num');

				$datas = array('username' => $username, 'password' => $password, 'passwordRun' => $passwordRun);

				$url = qscms::replaceVars($account_url, $datas);

				if ($html = winsock::get_html($url)) {

					//echo $html;exit;

					/*

					if ($html_ = string::getPregVal('/>(.+?)<\/string>/', $html)){

						$sp = explode('/', $html_);

						$sp = array_slice($sp, 0, 3);

						if (count($sp) == 3) {

							list($code, $uname, $count) = $sp;

							if ($code == '000') {

								$html = intval($count) * $price;

							}

						}

					}else{

						$sp = explode(',', $html);

						$html = $sp[1];

					}

					if ($is_num) $html = intval($html) * $price;

					*/

				}

			}

			if (is_numeric($html)) {

				self::$isStatus = $isStatus;//后台配置的返回状态代码是否正确

				self::$is_true = $is_true;//配置的状态码中是否有发送成功的

				self::$status = $status;//返回状态数组

				self::$passwordRun = $passwordRun;

				self::$account_url = $account_url;

				self::$send_url = $send_url;

				self::$username = $username;

				self::$password = $password;

				self::$price    = $price;

				self::$money    = (float)$html;

				self::$logined  = true;

			}

		}

		self::$ini = true;

	}

	public static function getMoney(){

		if (!self::$ini) self::ini();

		if (self::$logined) {

			if (!self::$isStatus) return 'sms中的status配置的状态码格式错误';

			if (!self::$is_true) return 'sms中的status配置中添加一条第三个参数为true的状态';

			return self::$money;

		}

		return 'not_login';

	}

	public static function getLen(){

		return self::$oneMsgLen;

	}

	public static function howMuch($message, $money){

		$message   = iconv(ENCODING, self::$encoding, $message);

		$message   = preg_replace('/\r\n|\n/', '', $message);

		$msgLength = mb_strlen($message, self::$encoding);

		if ($msgLength > 0) {

			$msgCount    = floor(($msgLength -1) / self::$oneMsgLen) + 1;

			return $msgCount * $money;

		}

		return 0;

	}

	public static function send($phones, $message, $returnMsg = false){

		if (!self::$ini) self::ini();

		if (self::$logined) {

			if (!self::$isStatus) return 'sms中的status配置的状态码格式错误或配置不能为空';

			if (!self::$is_true) return 'sms中的status配置中添加一条第三个参数为true的状态';

			$phones = preg_replace('/\D/', ',', $phones);

			$sp     = explode(',', $phones);

			$phones = array();

			foreach ($sp as $v) {

				if (form::checkMobilephone($v)) {

					$phones[] = $v;

				}

			}

			$phoneCount = count($phones);

			if ($phoneCount > 0) {

				$message   = preg_replace('/\r\n|\n/', '', $message);

				$message   = iconv(ENCODING, self::$encoding, $message);

				$msgLength = mb_strlen($message, self::$encoding);

				if ($msgLength > 0) {

					$msgList     = $rnMsg = array();

					$msgCount    = floor(($msgLength -1) / self::$oneMsgLen) + 1;

					$sendCount   = 0;

					$onePriceAll = self::$price * $msgCount;

					$priceAll    = $onePriceAll * $phoneCount;

					if ($priceAll < self::$money) {

						for ($i = 0; $i < $msgCount; $i++) {

							$msgList[] = mb_substr($message, $i * self::$oneMsgLen, self::$oneMsgLen, self::$encoding);

						}

						foreach ($phones as $phone) {

							foreach ($msgList as $msg) {

								$inTure = self::sendOne($phone, $msg);

								if ($inTure === true) {

									$sendCount++;

									if ($returnMsg) {

										$rnMsg[$phone][] = $msg;

									}

								}else{

									return $inTure;	

								}

							}

						}

						if ($returnMsg) {

							return array('complate' => $sendCount, 'list' => $rnMsg);

						}

						return $sendCount;

					}

					return '平台短信余额不足';

				}

				return '发送信息为空';

			}

			return '发送号码为空';

		}

		return '短信平台登录失败';

	}

	public static function sms_time(){

		static $s;

		if (isset($s)) return $s;

		$s = cfg::get('sms', 'sms_time');

		$s || $s = 1;//默认1分钟时间

		return $s * 60;

	}

	public static function sendOne($phone, $msg){

		//$msg .= self::$suffix;

		$timestamp = time::$timestamp;

		$sys_msg_debug = cfg::getBoolean('sms', 'is_imitate');

		if (!self::$ini) self::ini();

		if (self::$logined) {

			if (!self::$isStatus) return 'sms中的status配置的状态码格式错误';

			if (!self::$is_true) return 'sms中的status配置中添加一条第三个参数为true的状态';

			$msgId = db::insert('log_sms', array(

				'mobilephone' => $phone,

				'content'     => iconv(self::$encoding, ENCODING, $msg),

				'timestamp'   => $timestamp,

				'money'       => self::$price,

				'status'      => 0

			), true);

			if ($msgId) {

				$msg = rawurlencode($msg);

				$stateNum = '';

				if ($sys_msg_debug) {

					return true;

				} else {

					$datas = array(

						'username' => self::$username,

						'password' => self::$password,

						'passwordRun' => self::$passwordRun,

						'mobile'    => $phone,

						'content'	=> $msg,

					);

					$url = qscms::replaceVars(self::$send_url, $datas);

					//echo $url;exit;

					$html = winsock::get_html($url);

					if($html !== '') {

						if ($htmls = string::getPregVal('/>(.+?)<\/string>/', $html)){

							$htmls == '0808191630319344' && $html = '0';

							$html = $htmls;

							

						}

						$k = intval($html);

					}

				}

				if (self::$status){

					$rs = isset(self::$status[$k]) ? self::$status[$k] : '';

					if (!empty($rs['status'])) {

						self::$money -= self::$price;

						db::update('log_sms', array('status' => 1), "id='$msgId'");

						return true;

					} else {

						db::update('log_sms', array('remark' => !empty($rs['msg']) ? $rs['msg'] : '未知错误'), "id='$msgId'");

						$return = !empty($rs['msg']) ? $rs['msg'] : '发送失败，请稍后重试';

						return $return;

					}

				} return false;

			}

		}

		return false;

	}

	

		//$msg .= self::$suffix;

		

		







	public static function sendOne1($phone, $code){//阿里通信

		$timestamp = time::$timestamp;

		$msg = '';

		$msgId = db::insert('log_sms', array(

			'mobilephone' => $phone,

			'content'     => iconv(self::$encoding, ENCODING, $msg),

			'timestamp'   => $timestamp,

			'money'       => self::$price,

			'status'      => 0

		), true);

		
		
		//include(d('./alisend/TopSdk.php'));
		include(d('./dysms_php/aliyun-dysms-php-sdk/api_demo/SmsDemo.php'));
		$resp = SmsDemo::sendSms($phone, $code);
	
		$resp = qscms::get_object_vars_final($resp);
		if ($resp['Code'] != 'OK'){

			$return = !empty($resp['Message']) ? $resp['Message'] : '未知错误';

			db::update('log_sms', array('remark' => $return), "id='$msgId'");

			return $return;

		}else{

			db::update('log_sms', array('status' => 1), "id='$msgId'");

			return true;		

		}

		return false;

	}
	
	
	public static function login_sendOne($phone){//阿里通信

		$timestamp = time::$timestamp;
		$msg = '';
		
		include(d('./dysms_php/aliyun-dysms-php-sdk/api_demo/SmsDemo.php'));
		$resp = SmsDemo::sendSmslogin($phone);

		$resp = qscms::get_object_vars_final($resp);

		if ($resp['Code'] != 'OK'){

			$return = !empty($resp['Message']) ? $resp['Message'] : '未知错误';

			return $return;

		}else{

			return true;		

		}

		return false;

	}

	

	public static function cash_sendOne($phone){//阿里通信

		$timestamp = time::$timestamp;

		$msg = '';

		

		include(d('./dysms_php/aliyun-dysms-php-sdk/api_demo/SmsDemo.php'));
		$resp = SmsDemo::sendSmscash($phone);

		$resp = qscms::get_object_vars_final($resp);

		if ($resp['Code'] != 'OK'){

			$return = !empty($resp['Message']) ? $resp['Message'] : '未知错误';

			return $return;

		}else{

			return true;		

		}

		return false;

	}
	
	
	/*下面是阿里大于 上面是阿里通信*/
	
	public static function sendOne2($phone, $code){//阿里大于

		$timestamp = time::$timestamp;

		$msg = '';

		$msgId = db::insert('log_sms', array(

			'mobilephone' => $phone,

			'content'     => iconv(self::$encoding, ENCODING, $msg),

			'timestamp'   => $timestamp,

			'money'       => self::$price,

			'status'      => 0

		), true);

		
		
		include(d('./alisend/TopSdk.php'));

		

		$param = cfg::get('alisend', 'param');//验证参数 

		$signName = cfg::get('alisend', 'signName');//使用的那个模板

		$templateCode = cfg::get('alisend', 'templateCode');

		$c = new TopClient;

		$c ->appkey = cfg::get('alisend', 'appkey');//'23709438' ;

		$c ->secretKey = cfg::get('alisend', 'secretKey');//'e88f8fa58cc1cdcd2aa350422be43d8a' ;

		$req = new AlibabaAliqinFcSmsNumSendRequest;

		$req ->setExtend( "" );

		$req ->setSmsType( "normal" );

		$req ->setSmsFreeSignName($signName);//金米科技

		$req ->setSmsParam( "{".$param.":'$code'}" ); 

		$req ->setRecNum( "$phone");

		$req ->setSmsTemplateCode($templateCode);

		//$req ->setSmsTemplateCode( "$tempId" );

		$resp = $c ->execute( $req );

		$resp = qscms::get_object_vars_final($resp);
		//print_r($resp);exit;
		if (!empty($resp['sub_msg']) || !empty($resp['code'])){

			$return = !empty($resp['sub_msg']) ? $resp['sub_msg'] : '未知错误';

			db::update('log_sms', array('remark' => $return), "id='$msgId'");

			return $return;

		}else{

			db::update('log_sms', array('status' => 1), "id='$msgId'");

			return true;		

		}

		return false;

	}


	public static function login_sendOne2($phone){//阿里大于

		$timestamp = time::$timestamp;

		$msg = '';

		include(d('./alisend/TopSdk.php'));

		

		$param = 'lgtime';//cfg::get('alisend', 'param');//验证参数 

		$signName = cfg::get('alisend', 'signName');//使用的那个模板

		

		$c = new TopClient;

		$c ->appkey = cfg::get('alisend', 'appkey');//'23709438' ;

		$c ->secretKey = cfg::get('alisend', 'secretKey');//'e88f8fa58cc1cdcd2aa350422be43d8a' ;

		$req = new AlibabaAliqinFcSmsNumSendRequest;

		$req ->setExtend( "" );

		$req ->setSmsType( "normal" );

		$req ->setSmsFreeSignName($signName);//金米科技

		//lgtime

		$time = date('d日H时i分');

		$req ->setSmsParam( "{".$param.":'$time'}" ); 

		$req ->setRecNum( "$phone");

		$req ->setSmsTemplateCode( "SMS_82145040");

		//$req ->setSmsTemplateCode( "$tempId" );

		$resp = $c ->execute( $req );

		$resp = qscms::get_object_vars_final($resp);

		if (!empty($resp['sub_msg']) || !empty($resp['code'])){

			$return = !empty($resp['sub_msg']) ? $resp['sub_msg'] : '未知错误';

			return $return;

		}else{

			return true;		

		}

		return false;

	}

	

	public static function cash_sendOne2($phone){//阿里大于

		$timestamp = time::$timestamp;

		$msg = '';

		include(d('./alisend/TopSdk.php'));

		

		$signName = cfg::get('alisend', 'signName');//使用的那个模板

		

		$c = new TopClient;

		$c ->appkey = cfg::get('alisend', 'appkey');//'23709438' ;

		$c ->secretKey = cfg::get('alisend', 'secretKey');//'e88f8fa58cc1cdcd2aa350422be43d8a' ;

		$req = new AlibabaAliqinFcSmsNumSendRequest;

		$req ->setExtend( "" );

		$req ->setSmsType( "normal" );

		$req ->setSmsFreeSignName($signName);//金米科技

		//lgtime

		$name = qscms::v('_G')->webName;

		$time = date('d日H时i分');

		$arrs = array(

			'sysname' => $name,

			'txtime' => $time,

		);

		//"{sysname".":'$name'}". ', txtime'.":'$time'}"

		$req ->setSmsParam(json_encode($arrs)); 

		$req ->setRecNum( "$phone");

		$req ->setSmsTemplateCode( "SMS_135034651");

		//$req ->setSmsTemplateCode( "$tempId" );

		$resp = $c ->execute( $req );

		$resp = qscms::get_object_vars_final($resp);

		if (!empty($resp['sub_msg']) || !empty($resp['code'])){

			$return = !empty($resp['sub_msg']) ? $resp['sub_msg'] : '未知错误';

			return $return;

		}else{

			return true;		

		}

		return false;

	}
	/*新版本的阿里云 云信通*/
	/*
	public static function cash_sendOne($phone){
		$timestamp = time::$timestamp;
		$msg = '';
		$msgId = db::insert('log_sms', array(
			'mobilephone' => $phone,
			'content'     => iconv(self::$encoding, ENCODING, $msg),
			'timestamp'   => $timestamp,
			'money'       => self::$price,
			'status'      => 0

		), true);
		include(d('./alisend1/api_demo/SmsDemo.php'));
		$demo = new SmsDemo(
			"LTAIJtUoiQYmVeLK",
			"ewkMjYCURHQnb3f5VRk9oi7jODyDAc"
		);
		$time = date('d日H时i分');
		$response = $demo->sendSms(
			"乐呗计费", // 短信签名
			"SMS_105315061", // 短信模板编号
			$phone, // 短信接收者
			Array(  // 短信模板中字段的值
				"name" => '',
				"txtime"=>$time,
			),
			"123"
		);
		if ($response->Message == 'OK'){
			db::update('log_sms', array('status' => 1), "id='$msgId'");
			return true;	
		}else{
			$return = !empty($response->Message) ? $response->Message : '未知错误';
			db::update('log_sms', array('remark' => $return), "id='$msgId'");
			return $return;
		}
		return false;
	}
	
	public static function login_sendOne($phone){
		$timestamp = time::$timestamp;
		$msg = '';
		$msgId = db::insert('log_sms', array(
			'mobilephone' => $phone,
			'content'     => iconv(self::$encoding, ENCODING, $msg),
			'timestamp'   => $timestamp,
			'money'       => self::$price,
			'status'      => 0

		), true);
		include(d('./alisend1/api_demo/SmsDemo.php'));
		$demo = new SmsDemo(
			"LTAIJtUoiQYmVeLK",
			"ewkMjYCURHQnb3f5VRk9oi7jODyDAc"
		);
		$time = date('d日H时i分');
		$response = $demo->sendSms(
			"乐呗计费", // 短信签名
			"SMS_105395046", // 短信模板编号
			$phone, // 短信接收者
			Array(  // 短信模板中字段的值
				"lgtime"=>$time,
			),
			"123"
		);
		if ($response->Message == 'OK'){
			db::update('log_sms', array('status' => 1), "id='$msgId'");
			return true;	
		}else{
			$return = !empty($response->Message) ? $response->Message : '未知错误';
			db::update('log_sms', array('remark' => $return), "id='$msgId'");
			return $return;
		}
		return false;	
	}
	*/
	/*******************************************************************************************/
	public static function sendVcode1($mobile, $vcode){

		$url = 'http://sms.106jiekou.com/utf8/sms.aspx?account=nonenone&password=123456vip&mobile='.$mobile.'&content='.rawurlencode('您的验证码是：'.$vcode.'。如需帮助请联系客服。');

		$rs = winsock::get_html($url);

		if ($rs != 100){

			return '短信发送失败，请稍后在尝试';

		}

		return true;

	}

	public static function addSendVcodeInfo($mobile, $vcode){

		$msg = '您的订单：'.$vcode.'。请登录网站查看。';	

		if (db::insert('mobile_message', array(

			'mobile' => $mobile,

			'content' => $msg,

			'addTime' => time()

		))){

			return true;

		}else return false;

	}

	public static function sendVcodeInfo_all($num = 50){

		$list = db::select('mobile_message', '*', '', '', $num);

		if ($list){

			foreach($list as $v){

				$rs = self::sendVcodeInfo1($v['mobile'], $v['content']);

				if ($rs === true) {

					echo '发送成功<br />';

				}

				else echo '发送失败<br />';

				db::del_id('mobile_message', $v['id']);

			}	

		}

	}

	public static function sendVcodeInfo1($mobile, $msg){

		$url = 'http://sms.106jiekou.com/utf8/sms.aspx?account=nonenone&password=123456vip&mobile='.$mobile.'&content='.rawurlencode($msg);

		$rs = winsock::get_html($url);

		if ($rs != 100){

			return '短信发送失败，请稍后在尝试';

		}

		return true;

	}

	

	public static function sendVcodeInfo($mobile, $vcode){

		$url = 'http://sms.106jiekou.com/utf8/sms.aspx?account=nonenone&password=123456vip&mobile='.$mobile.'&content='.rawurlencode('您的订单：'.$vcode.'。请登录网站查看。');

		$rs = winsock::get_html($url);

		if ($rs != 100){

			return '短信发送失败，请稍后在尝试';

		}

		return true;

	}

	public static function sendVcode($phone){

		if ($time= db::one_one('log_sms_vcode', 'time', "mobile='$phone'", 'id DESC')) {

			$time = intval($time);

			$spaceTime = cfg::getInt('sms', 'time_vcode');

			if (time() - $time < $spaceTime) return '请在'.($time + $spaceTime - time()).'秒后在发送';

		}

		$var = qscms::v('_G');

		

		

		$vcode = string::getRandStr(6, 1);//qscms::salt();

		//$msg = cfg::get('sms', 'vcodeStr');

		//$datas = array('webName' => $var->webName, 'vcode' => $vcode);

		//$msg = qscms::replaceVars($msg, $datas);

		//$rs = self::send($phone, $msg);

		$url = 'http://sms.106jiekou.com/utf8/sms.aspx?account=nonenone&password=123456vip&mobile='.$phone.'&content='.rawurlencode('您的验证码是：'.$vcode.'。如需帮助请联系客服。');

		$rs = winsock::get_html($url);

		if ($rs != 100){

			return '短信发送失败，请稍后在尝试';

		}

		if (is_numeric($rs) && $rs > 0) {//发送成功

			$datas = array(

				'mobile' => $phone,

				'vcode'  => $vcode,

				'time'   => time()

			);

			if (db::insert('log_sms_vcode', $datas)) {

				return true;

			}

			return '发送失败，请重试！';

		} else return $rs;

	}

}

?>