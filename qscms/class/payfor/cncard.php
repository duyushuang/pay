<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class payfor_cncard{
	private $id, $key, $url;
	public $status, $name;
	public function __construct(){
		$payfor_config = cache::get_array('payfor_config', true);
		if (isset($payfor_config) && isset($payfor_config['chinabank_status']) && $payfor_config['chinabank_status']==1) {
			$this->id      = $payfor_config['chinabank_id'];
			$this->key     = $payfor_config['chinabank_key'];
			$this->status = true;
		} else {
			$this->status = false;
		}
		$this->url  = 'https://www.cncard.net/purchase/getorder.asp';
		$this->name = '֧';
	}
	public function payfor($id, $money){
		global $weburl, $sys_debug, $timestamp;
		if ($this->status) {
			//if ($sys_debug) $money = 0.01;
			
			$c_mid		= $this->id;						//̻ţ̻ɹ󼴿ɻã̻ɹʼлȡñ
			$c_order	= $id;					    //̻վնŹɵĶţظ
			$c_name		= "";						//̻еջ
			$c_address	= "";				//̻еջ˵ַ
			$c_tel		= "";				//̻еջ˵绰
			$c_post		= "";						//̻еջʱ
			$c_email	= "";		//̻еջEmail
			$c_orderamount = $money;					//̻ܽ
			$c_ymd		= date('Ymd', $timestamp);					//̻ĲڣʽΪ"yyyymmdd"20050102
			$c_moneytype= "0";							//֧֣0Ϊ
			$c_retflag	= "1";							//̻֧ɹǷҪָ̻ļ0÷ 1Ҫ
			$c_paygate	= "";							//̻վѡøֵֵɲμ֧@ӿֲᡷ¼һ֧@ѡдΪֵ
			$c_returl	= $weburl.'/payfor/cncard/GetPayNotify.php';//c_retflagΪ1ʱõַ̻֧֪ͨҳ棬ύļ(ӦļGetPayNotify.php)
			$c_memo1	= "ABCDE";						//̻Ҫ֧֪ͨת̻һ
			$c_memo2	= "12345";						//̻Ҫ֧֪ͨת̻
			$c_pass		= $this->key;						//֧Կ¼̨̻ʻϢ-Ϣ-ȫϢе֧Կ
			$notifytype	= "1";							//0֪ͨͨʽ/1֪ͨʽֵΪ֪ͨͨʽ
			$c_language	= "0";							//˹ʿ֧ʱʹøֵ֧ʱҳֵ֣Ϊ0ҳʾΪ/1ҳʾΪӢ
		
			$srcStr = $c_mid . $c_order . $c_orderamount . $c_ymd . $c_moneytype . $c_retflag . $c_returl . $c_paygate . $c_memo1 . $c_memo2 . $notifytype . $c_language . $c_pass;	//˵ָ֧ʽ(c_paygate)ֵʱҪûѡ֧ʽȻٸûѡĽMD5ܣҲ˵ʱҳӦòΪҳ棬Ϊɡ
			
		//--ԶϢMD5
			//̻ԶϢMD5ǩַ
			$c_signstr	= md5($srcStr);
			
			$args = array(
				'url'   => $this->url,
				'type'  => 'post',
				'datas' => array(
					'c_mid'         => $c_mid,
					'c_order'       => $c_order,
					'c_name'        => $c_name,
					'c_address'     => $c_address,
					'c_tel'         => $c_tel,
					'c_post'        => $c_post,
					'c_email'       => $c_email,
					'c_orderamount' => $c_orderamount,
					'c_ymd'         => $c_ymd,
					'c_moneytype'   => $c_moneytype,
					'c_retflag'     => $c_retflag,
					'c_paygate'     => $c_paygate,
					'c_returl'      => $c_returl,
					'c_memo1'       => $c_memo1,
					'c_memo2'       => $c_memo2,
					'notifytype'    => $notifytype,
					'c_language'    => $c_language,
					'c_signstr'     => $c_signstr
				)
			);
			return $args;
		}
		return false;
	}
	public function checkReturnA(){
		$c_mid			= $_REQUEST['c_mid'];			//̻ţ̻ɹ󼴿ɻã̻ɹʼлȡñ
		$c_order		= $_REQUEST['c_order'];			//̻ṩĶ
		$c_orderamount	= $_REQUEST['c_orderamount'];	//̻ṩĶܽԪΪλСλ磺13.05
		$c_ymd			= $_REQUEST['c_ymd'];			//̻ĶڣʽΪ"yyyymmdd"20050102
		$c_transnum		= $_REQUEST['c_transnum'];		//֧ṩĸñʶĽˮţպѯ˶ʹã
		$c_succmark		= $_REQUEST['c_succmark'];		//׳ɹ־Y-ɹ N-ʧ			
		$c_moneytype	= $_REQUEST['c_moneytype'];		//֧֣0Ϊ
		$c_cause		= $_REQUEST['c_cause'];			//֧ʧܣֵʧԭ		
		$c_memo1		= $_REQUEST['c_memo1'];			//̻ṩҪ֧֪ͨת̻һ
		$c_memo2		= $_REQUEST['c_memo2'];			//̻ṩҪ֧֪ͨת̻
		$c_signstr		= $_REQUEST['c_signstr'];		//֧ضϢMD5ַܺ
	
		//--УϢ---
		if($c_mid=="" || $c_order=="" || $c_orderamount=="" || $c_ymd=="" || $c_moneytype=="" || $c_transnum=="" || $c_succmark=="" || $c_signstr==""){
			//echo "֧Ϣ!";
			return false;
		}

	//--õ֪ͨϢƴַΪ׼MD5ܵԴҪעǣƴʱȺ˳ܸı
		//̻֧Կ¼̨̻(https://www.cncard.net/admin/)ڹҳҵֵ
		$c_pass = $this->key;
		
		$srcStr = $c_mid . $c_order . $c_orderamount . $c_ymd . $c_transnum . $c_succmark . $c_moneytype . $c_memo1 . $c_memo2 . $c_pass;

	//--֧֪ͨϢMD5
		$r_signstr	= md5($srcStr);

	//--У̻վ֪ͨϢMD5ܵĽ֧ṩMD5ܽǷһ
		if($r_signstr!=$c_signstr){
			//echo "ǩ֤ʧ";
			return false;
		}

	//--У̻
		$MerchantID = $this->id;	//̻Լı
		if($MerchantID!=$c_mid){
			//echo "ύ̻";
			return false;
		}

	//--У鷵ص֧ĸʽǷȷ
		if($c_succmark!="Y" && $c_succmark!="N"){
			//echo "ύ";
			return false;
		}

	//--ݷص֧̻ԼķȲ
		if($c_succmark="Y"){
			//̻Լ򣬽зϵв
		}

	//--֧֪ͨ
		//<result>̶ֵΪ1ʾ̻ѳɹյص֧ɹ֪ͨ
		//<reURL> ̻ʾûҳURL(ӦļGetPayHandle.php)
		//echo "<result>1</result><reURL>http://www.yoursitename.com/urlpath/GetPayHandle.php</reURL>";
		return $c_order;
	}
	public static function checkReturnB(){
		return $this->checkReturnA();
	}
}
?>