<?php
	class qsclass {
		var $config;

		function __construct($partner = '', $key = ''){
			$config['sign_type']     = strtoupper('MD5');
			//字符编码格式 目前支持 gbk 或 utf-8
			$config['input_charset'] = strtolower('utf-8');
			//商户ID
			$config['partner']       = '1000';
			//商户KEY
			$config['key']           = '4QYPZRFkU09W96ihZJ0EWKVo4f6nzCrl1HMaZIMv';
			//支付API地址 
			$config['apiurl']        = file_get_contents('https://www.fengbaopay.com/apiurl');
			if ($partner) $config['partner'] = $partner;
			if ($key) $config['key'] = $key;
			/*--------------------------商户配置------------------------------*/
			$this->config = $config;
			$this->gateway_new = $this->config['apiurl'].'recharge?';
		}
		function qsclass() {
			$this->__construct();
		}
		
		function buildRequestForm($para_temp, $method = 'POST') {
			//待请求参数数组
			$para = $this->buildRequestPara($para_temp);
			
			$sHtml = "<form id='qsSubmit' name='alipaysubmit' action='".$this->gateway_new."input_charset=".trim(strtolower($this->config['input_charset']))."' method='".$method."'>";
			while (list ($key, $val) = each ($para)) {
				$sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
			}
			//submit按钮控件请不要含有name属性
			$sHtml = $sHtml."</form>";
			
			$sHtml = $sHtml."<script>document.forms['qsSubmit'].submit();</script>";
			
			return $sHtml;
		}
		function verifyNotify(){
			$data = $_POST;
			$data || $data = $_GET;
			if (!empty($data) && !empty($data['sign'])){
				$prestr = $this->paraFilter($data);
				$prestr = $this->argSort($prestr);
				$prestr = $this->createLinkstring($prestr);
				if ($this->md5Sign($prestr, $this->config['key']) == $data['sign']){
					return $data;	
				}else{
					logResult($this->md5Sign($prestr, $this->config['key']).'----'.$data['sign']);
					return false;	
				}
			}else return false;
			
		}
		function buildRequestPara($para_temp) {
			$para_temp['pid'] = trim($this->config['partner']);
			//除去待签名参数数组中的空值和签名参数
			$para_filter = $this->paraFilter($para_temp);
	
			//对待签名参数数组排序
			$para_sort = $this->argSort($para_filter);
	
			//生成签名结果
			$mysign = $this->buildRequestMysign($para_sort);
			
			//签名结果与签名方式加入请求提交参数组中
			$para_sort['sign'] = $mysign;
			$para_sort['sign_type'] = strtoupper(trim($this->config['sign_type']));
			
			return $para_sort;
		}
		function paraFilter($para) {
			$para_filter = array();
			while (list ($key, $val) = each ($para)) {
				if ($key == "sign" || $key == "sign_type" || $val == "") continue;
				else $para_filter[$key] = $para[$key];
			}
			return $para_filter;
		}
		function argSort($para) {
			ksort($para);
			reset($para);
			return $para;
		}
		function buildRequestMysign($para_sort) {
			//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
			$prestr = $this->createLinkstring($para_sort);
			
			$mysign = $this->md5Sign($prestr, $this->config['key']);
	
			return $mysign;
		}
		function createLinkstring($para) {
			$arg  = "";
			while (list ($key, $val) = each ($para)) {
				$arg.=$key."=".$val."&";
			}
			//去掉最后一个&字符
			$arg = substr($arg,0,count($arg)-2);
			
			//如果存在转义字符，那么去掉转义
			if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
			//print_r($arg);exit;
			return $arg;
		}
		function md5Sign($prestr, $key) {
			$prestr = $prestr . $key;
			return md5($prestr);
		}
		
		/**
		 * 验证签名
		 * @param $prestr 需要签名的字符串
		 * @param $sign 签名结果
		 * @param $key 私钥
		 * return 签名结果
		 */
		function md5Verify($prestr, $sign, $key) {
			$prestr = $prestr . $key;
			$mysgin = md5($prestr);
		
			if($mysgin == $sign) {
				return true;
			}
			else {
				return false;
			}
		}
		/**
		 * 写日志，方便测试（看网站需求，也可以改成把记录存入数据库）
		 * 注意：服务器需要开通fopen配置
		 * @param $word 要写入日志里的文本内容 默认值：空值
		 */
		function logResult($word='') {
			$fp = fopen("logs.txt","a");
			flock($fp, LOCK_EX) ;
			fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
			flock($fp, LOCK_UN);
			fclose($fp);
		}
	}
?>