<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class pay{
	/*
	支付类型
	*/
	/*支付类型*/
	public static $array = array(
		'wxpay'  => '微信', 
		'alipay' => '支付宝', 
		'qqpay'  => 'QQ钱包', 
		'bdpay'  => '百度网银', 
		
	);
	/*这些属于百度网银的*/
	public static $bdpay = array(
		101		 => '中国工商银行', 
		201		 => '中国招商银行', 
		301		 => '中国建设银行', 
		401		 => '中国农业银行', 
		501		 => '中信银行', 
		601		 => '浦东发展银行', 
		1902     => '中国邮政储蓄银行',
		1101	 => '交通银行', 
		1201     => '中国银行', 
		//11       => '银联', 
		//701		 => '中国光大银行', 
		//801		 => '平安银行', 
		//13       => '银联在线UPOP', 
		//1901     => '广发银行'     //百度关了
	);//这些按百度网银比例算
	public static function ename($name){
		if (!empty(self::$array[$name])){
			return self::$array[$name];
		}elseif (!empty(self::$bdpay[$name])){
			return self::$bdpay[$name];
		}
		return false;
	}
	/*
	public static function typeArr(){
		static $typeArr;
		if ($typeArr) return $typeArr;
		$typeArr = array('wxpay', 'alipay', 'qqpay', 'wypay', 'bdpay', '11', '101', '201', '301', '401', '501', '601', '701', '801', '1101', '1201', '13', '1901');
		return $typeArr;
	}
	*/
	public static function log($content){
		$file = d('./log/'.date('Y-m-d').'.txt');
		file::createFile($file);
		return file::write($file, $content."\r\n", true);
	}
	public static function recharge_index($datas){
		
		$datas = qscms::filterArray($datas, array('notify_url', 'return_url', 'out_trade_no', 'pid', 'site_name', 'subject', 'total_fee', 'type', 'sign', 'sign_type'), true);
		$formSign = $datas['sign'];
		$sign_type = $datas['sign_type'];
		$pid = $datas['pid'];
		$m = new member_center($pid);
		if (!$m->status || !$m->m_keys || !$m->m_isApi) return 'ERROR:PID failed';//status是否禁用 keys该用户没有 isApi是否开启使用api支付接口
		$sign = string::keymd5Sign($datas, $m->m_keys);
		if ($formSign === $sign){//签名正确
			
			//判断notify_url 回调地址是否是网址
			if ($datas['notify_url']){
				$datas['notify_url'] = trim($datas['notify_url']);
				if (!substr($datas['notify_url'], 0, 7) == 'http://' || !substr($datas['notify_url'], 0, 8) == 'https://'){
					return 'ERROR:notify_url：http:// OR https://';
				}
			}
			//if (!$datas['subject']) return 'ERROR:subject Can not be empty';
			
			if (!$datas['out_trade_no']) return 'ERROR:out_trade_no Can not be empty';
			//if (!preg_match('/^[A-Za-z0-9]+$/', $datas['out_trade_no'])) return 'ERROR:out_trade_no Can only be numbers or letters';
			if (strlen($datas['out_trade_no']) > 60 || strlen($datas['out_trade_no']) < 4) return 'ERROR:out_trade_no maximum length is 60 bytes and minimum length is 5 bytes';
			
			$referer = '';//来路
			(!empty($_SERVER['HTTP_REFERER']) && ($referer = $_SERVER['HTTP_REFERER']));
			
			$pathinfo = pathinfo($referer);
			if (empty($pathinfo['dirname'])) $pathinfo['dirname'] = '';
			if (cfg::get('pay', 'isReferer')){//开启判断来路的
				if (!$m->m_siteurl) return 'ERROR:Site address error';
				if ($pathinfo['dirname']     != $m->m_siteurl) return 'ERROR:Site address error';
				$notify_url = pathinfo($datas['notify_url']);
				if (stripos($datas['notify_url'], $m->m_siteurl) === false) return 'ERROR:notify_url address error';
				$return_url = pathinfo($datas['return_url']);
				if (stripos($datas['return_url'], $m->m_siteurl) === false) return 'ERROR:return_url address error';
			}
			if ($datas['total_fee'] < 0.01) return 'ERROR:The amount must not be less than 0.01';
			
			$type = $datas['type'];
			if (!empty(pay::$bdpay[$datas['type']])) $type = 'bdpay';// 银行全部属于百度网银的
			
			if (self::ename($datas['type'])){//有这个支付名称
				if (db::exists('pay_off', "uid='$datas[pid]' AND type='$datas[type]'")) return 'ERROR:This type is closed';
				
				if (db::exists('pay_payment', "out_trade_no='$datas[out_trade_no]'")) {
					if ($sn = db::one_one('pay_payment','sn', "out_trade_no='$datas[out_trade_no]' AND uid='$datas[pid]'")){
						return array('out_trade_no' => $sn, 'payment' => $datas['type']);
					}
					return 'ERROR:out_trade_no repeat';
				}
				
				
				
				db::autocommit();
				do {
					$sn = db::createId();
				} while(db::exists('pay_payment', array('sn' => $sn), '', true));
				if (db::insert('pay_payment', array(
					'sn'                => $sn,
					'uid'               => $datas['pid'],
					'out_trade_no'      => $datas['out_trade_no'],
					'referer_url'  	    => $pathinfo['dirname'],
					'notify_url' 		=> $datas['notify_url'],
					'return_url' 		=> $datas['return_url'],
					'site_name'			=> $datas['site_name'],
					'subject'			=> $datas['subject'],
					'type' 		    	=> $type,
					'types' 			=> 0,//用户充值的
					'status' 			=> 0,
					'money'  			=> $datas['total_fee'],
					'addTime' 			=> time()
				))){
					db::commit(true);
					return array('out_trade_no' => $sn, 'payment' => $datas['type']);
				}
				db::autocommit(false);
			}else return 'ERROR:type does not exist';
		}else return 'ERROR:Verification sign failed';
		
	}
	
}
?>