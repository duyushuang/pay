<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class message{
	private static $ini = false, $logined = false, $money = 0, $oneMsgLen = 70, $suffix, $username, $password, $price ,$encoding = 'utf-8';
	public static function ini($username = '', $password = '', $price = 0){
		$username || $username = cfg::get('sms', 'username');
		$password || $password = cfg::get('sms', 'password');
		$price    || $price    = cfg::get('sms', 'price_one');
		$sys_msg_debug = cfg::getBoolean('sms', 'is_imitate');
		self::$oneMsgLen = cfg::getInt('sms', 'oneLen');
		self::$suffix = cfg::get('sms', 'msg_suffix');
		self::$oneMsgLen -= mb_strlen(self::$suffix);
		self::$suffix = iconv(ENCODING, self::$encoding, self::$suffix);
		if ($username && $password && $price) {
			$username = urlencode(iconv(ENCODING, 'GBK', $username));
			$password = urlencode(iconv(ENCODING, 'GBK', $password));
			if ($sys_msg_debug) {
				$html = '9999.9';
			} else {
				$url = 'http://api.weimi.cc/2/account/balance.html?uid='.$username.'&pas='.$password.'&type=json';
				if ($html = winsock::get_html($url)) {
					$rs = string::json_decode($html);
					if (!isset($rs['code']) || $rs['code'] >= 0) {
						$html = $rs['sms-left'] * $price;
					}
				}
			}
			if (is_numeric($html)) {
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
								if (self::sendOne($phone, $msg)) {
									$sendCount++;
									if ($returnMsg) {
										$rnMsg[$phone][] = $msg;
									}
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
	public static function sendOne($phone, $msg){
		$msg .= self::$suffix;
		$timestamp = time::$timestamp;
		$sys_msg_debug = cfg::getBoolean('sms', 'is_imitate');
		if (!self::$ini) self::ini();
		if (self::$logined) {
			$msgId = db::insert('log_sms', array(
				'mobilephone' => $phone,
				'content'     => iconv(self::$encoding, ENCODING, $msg),
				'timestamp'   => $timestamp,
				'money'       => self::$price,
				'status'      => 0
			), true);
			if ($msgId) {
				$msg = urlencode($msg);
				$stateNum = '';
				if ($sys_msg_debug) {
					$rs = array('code' => 0, 'msg' => '发送成功');
				} else {
					if ($html = winsock::post_html('http://api.weimi.cc/2/sms/send.html', 'uid='.self::$username.'&pas='.self::$password.'&mob='.$phone.'&con='.$msg.'&type=json')) {
						$rs = string::json_decode($html);
					} else $rs = array('code' => -100, 'msg' => '服务器无相应');
				}
				//if(preg_match("/^\s*(\d{3}|-\d{2})\\/Send:(\d{1,100})\\/Consumption:([\d\.]+)\\/Tmoney:([\d\.]+)\\/sid:(\d+)?$/is", $html, $rs)){
					/*$status = array(
						0   => array('status' => true, 'msg' => '短信提交成功'),
						-1  => array('status' => false, 'msg' => '参数不正确'),
						-2  => array('status' => false, 'msg' => '非法账号'),
						-3  => array('status' => false, 'msg' => 'IP鉴权失败'),
						-4  => array('status' => false, 'msg' => '账号余额不足'),
						-5  => array('status' => false, 'msg' => '下发失败'),
						-6  => array('status' => false, 'msg' => '短信内容含有非法关键字'),
						-7  => array('status' => false, 'msg' => '同一个号码、同一段短信内容，在同一小时内重复下发'),
						-8  => array('status' => false, 'msg' => '拓展特服号码不正确'),
						-9  => array('status' => false, 'msg' => '非法子账号'),
						-10 => array('status' => false, 'msg' => '定时计划时间不正确'),
						-11 => array('status' => false, 'msg' => 'CID不正确'),
						-13 => array('status' => false, 'msg' => '一次性提交手机号码过多'),
						-16 => array('status' => false, 'msg' => '接口调用错误次数太多')
					);*/
				if ($rs['code'] === 0) {
					self::$money -= self::$price;
					db::update('log_sms', array('status' => 1), "id='$msgId'");
					return true;
				} else {
					db::update('log_sms', array('remark' => $rs['msg']), "id='$msgId'");
					return false;
				}
			}
		}
		return false;
	}
	
	public static function sendVcode($phone){
		if ($time= db::one_one('log_sms_vcode', 'time', "mobile='$phone'", 'id DESC')) {
			$time = intval($time);
			$spaceTime = cfg::getInt('sms', 'time_vcode');
			if (time() - $time < $spaceTime) return '请在'.($time + $spaceTime - time()).'秒后在发送';
		}
		$var = qscms::v('_G');
		$vcode = string::getRandStr(6, 1);//qscms::salt();
		$msg = cfg::get('sms', 'vcodeStr');
		$datas = array('webName' => $var->webName, 'vcode' => $vcode);
		$msg = qscms::replaceVars($msg, $datas);
		$rs = self::send($phone, $msg);
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