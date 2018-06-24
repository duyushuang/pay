<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class data_taobaoUser extends data_taobaoBase{
	public static function getApiUser($nick){
		$args = array(
			'method' => 'taobao.user.get',
			'fields' => 'uid,nick,sex,buyer_credit,seller_credit,type,promoted_type,consumer_protection,has_shop',
			'nick'   => $nick
		);
		//消保
		//promoted_type 实名认证标志 有无实名认证。可选值:authentication(实名认证),not authentication(没有认证)
		//http://rate.taobao.com/user-rate-13ed87d3175ac9f8979fd27a33c47eee.htm 信用页面
		if(($rs = parent::get($args)) && $rs['user']){
			$rs = $rs['user'];
			if (!$rs['promoted_type']) {
				$html = winsock::get_html('http://rate.taobao.com/user-rate-'.$rs['uid'].'.htm');
				if (strpos($html, '支付宝实名认证') !== false) $rs['promoted_type'] = 'authentication';
				else $rs['promoted_type'] = 'not authentication';
				if (preg_match('/<li class="join-status">(.+?)<\/li>/is', $html, $matches)) {
					if (strpos($matches[1], '未加入') !== false) $rs['consumer_protection'] = false;
					else $rs['consumer_protection'] = true;
				} else $rs['consumer_protection'] = false;
			}
			return $rs;
		} else {
			return false;
		}
	}
	public static function userExists($nick){
		return self::getUserToUrl($nick);
		$args = array(
			'method' => 'taobao.user.get',
			'fields' => 'nick',
			'nick'   => $nick
		);
		if(($rs = parent::get($args)) && !empty($rs['user'])){
			return true;
		} else {
			return false;
		}
	}
	public static function getUser($nick){
		return self::getApiUser($nick);
	}
	/*public static function getUserToUrl($nick){
		$gbNick = iconv(ENCODING, 'gbk', $nick);
		$url = 'http://tradecardseller.wangwang.taobao.com/tradecard/popupNameCard.htm?uid=cntaobao'.urlencode($gbNick).'&type=1&jumpFrom=aliwwserver';
		if ($html = winsock::get_html($url)) {echo $html;exit;
				if (preg_match('/<a href="(http:\/\/rate\.taobao\.com\/user-rate-\d+\.htm)" target="_blank">/', $html, $matches)) {
				$url = $matches[1];
				if ($html = winsock::get_html($url)) {
					if (preg_match('/<ul class="sep">(.+?)<\/ul>/s', $html, $matches)) {
						$str = $matches[1];
						$rs = array(
							'buyer_credit'        => array('score' => 0),
							'seller_credit'       => array('score' => 0),
							'promoted_type'       => 'not authentication',
							'consumer_protection' => false
						);
						if (preg_match('/卖家信用：\s*(\d+)/', $str, $matches)) {
							$rs['seller_credit']['score'] = intval($matches[1]);
						}
						if (preg_match('/买家信用：\s*(\d+)/', $str, $matches)) {
							$rs['buyer_credit']['score'] = intval($matches[1]);
						}
						if (strpos($html, '支付宝实名认证') !== false) $rs['promoted_type'] = 'authentication';
						else $rs['promoted_type'] = 'not authentication';
						if (preg_match('/<li class="join-status">(.+?)<\/li>/is', $html, $matches)) {
							if (strpos($matches[1], '未加入') !== false) $rs['consumer_protection'] = false;
							else $rs['consumer_protection'] = true;
						} else $rs['consumer_protection'] = false;
						return $rs;
					}
				}
			}
		}
		return false;
	}*/
	public static function getUserToUrl($nick){
		//$gbNick = iconv(ENCODING, 'gbk', $nick);
		if ($html = winsock::post_html('http://www.mzyz.com/action.asp?s=cha', 'UserName='.rawurlencode($nick))) {
			if (preg_match('/(http:\/\/rate\.taobao\.com\/user-rate-\d+\.htm)/', $html, $matches)) {
				$url = $matches[1];
				if ($html = winsock::get_html($url)) {
					if (preg_match('/<ul class="sep.*?">(.+?)<\/ul>/s', $html, $matches)) {
						$str = $matches[1];
						$str = preg_replace('/<.*?>/s', '', $str);
						$rs = array(
							'buyer_credit'        => array('score' => 0),
							'seller_credit'       => array('score' => 0),
							'promoted_type'       => 'not authentication',
							'consumer_protection' => false
						);
						if (preg_match('/卖家信用：\s*(\d+)/', $str, $matches)) {
							$rs['seller_credit']['score'] = intval($matches[1]);
						}
						if (preg_match('/买家信用：\s*(\d+)/', $str, $matches)) {
							$rs['buyer_credit']['score'] = intval($matches[1]);
						}
						if (strpos($html, '支付宝实名认证') !== false) $rs['promoted_type'] = 'authentication';
						else $rs['promoted_type'] = 'not authentication';
						if (preg_match('/<li class="join-status">(.+?)<\/li>/is', $html, $matches)) {
							if (strpos($matches[1], '未加入') !== false) $rs['consumer_protection'] = false;
							else $rs['consumer_protection'] = true;
						} else $rs['consumer_protection'] = false;
						return $rs;
					}
				}
			}
		}
		return false;
	}
}
?>