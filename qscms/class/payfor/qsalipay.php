<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class payfor_qsalipay{
	private $vars, $agent, $key, $seller;
	public function __construct(){
		$this->vars = array();
		$this->status = false;
		$this->agent = '2088001232075031';
		$this->key   = 'b5tzcx89nbii7b0iinawazma7anhs62y';
		if ($info = db::one('payfor_interface', '*', "ename='qsalipay'")) {
			$this->char_set      = ENCODING;
			$this->return_url    = qscms::getUrl('/payfor/qsalipay/return/');
			$this->notify_url    = qscms::getUrl('/payfor/qsalipay/callback/');
			$this->transport     = 'http';
			$this->seller        = $info['username'];
			$this->status        = $info['status'];
		} else {
			$this->status = false;
			$this->name   = '未知充值方式';
		}
		$this->url = 'https://mapi.alipay.com/gateway.do?_input_charset='.strtolower($this->char_set);
		//$this->url = 'https://www.alipay.com/cooperate/gateway.do';
	}
	public function __get($key){
		if (isset($this->vars[$key])) return $this->vars[$key];
		return '';
	}
	public function __set($key, $val){
		$this->vars[$key] = $val;
	}
	public function payfor($id, $money){
		$web_name = cfg::get('sys', 'webName');
		if ($this->status) {
			$this->show_url = WEB_URL.'/';
			$datas = $this->payfor2($id, $web_name.'在线充值', $web_name.'在线充值', $money);
			//print_r($datas);exit;
			$args = array(
				'url'   => $this->url,
				'type'  => 'post',
				'datas' => $datas
			);
			return $args;
		}
		return false;
	}
	private function payfor2($oid, $subject, $content, $money){
		/*$money = $price;
			$__royalty = array();
			$__percent = 0.97;
			$__royalty_parameters = '';
			$_royalty = '';//分润EMAIL
			if ($_royalty) {
				foreach (explode(',', $_royalty) as $v) {
					if ($v = trim($v)) {
						$__royalty[] = $v;
					}
				}
			}
			if ($__royalty) {
				$__count = count($__royalty);
				$__p = $__percent / $__count;
				$__p = floor($__p * 100) / 100;
				$__moneyAll = floor($money * $__percent);
				$__moneyP = 0;
				foreach ($__royalty as $__k => $__v) {
					$__isLast = $__k + 1 == $__count;
					$__money = $__isLast ? $__moneyAll - $__moneyP: floor($__moneyAll * $__p);
					$__k > 0 && $__royalty_parameters .= '|';
					$__royalty_parameters .= $__v.'^'.$__money.'^alipay Fee';
					$__moneyP += $__money;
				}
			}
			$__royalty_parameters = 'info@lqwl.com^'.(floor($money * 0.01 * 100) / 100).'^alipay Fee'.($__royalty_parameters ? '|'.$__royalty_parameters : '');*/
		$parameter = array(
			'agent'              => $this->agent,
			"service"			 => 'create_direct_pay_by_user',
			"payment_type"		 => "1",
			"partner"			 => $this->agent,
			"_input_charset"	 => $this->char_set,
			"seller_email"		 => $this->seller,
			"return_url"		 => $this->return_url,
			"notify_url"		 => $this->notify_url,
			"out_trade_no"		 => $oid,
			"subject"			 => $subject,
			"total_fee"			 => $money,
			"royalty_type"		 => '',
			"royalty_parameters" => ''
		);
		
		$parameter   = $this->para_filter($parameter);
		$sort_array  = $this->arg_sort($parameter);
		$mysign      = $this->build_mysign($sort_array, $this->key, 'MD5');
		//custom
		$sort_array['sign'] = $mysign;
		$sort_array['sign_type'] = 'MD5';
		//print_r($sort_array);exit;
		return $sort_array;
		
		
		ksort($parameter);
		reset($parameter);
		$param = '';
		$sign  = '';

		foreach ($parameter as $key => $val)
		{
			$param .= "$key=" .urlencode($val). "&";
			$sign  .= "$key=$val&";
		}

		$param = substr($param, 0, -1);
		$sign  = substr($sign, 0, -1). $this->key;
		return 'https://www.alipay.com/cooperate/gateway.do?'.$param. '&sign='.md5($sign).'&sign_type=MD5';
	}
	private function para_filter($parameter) {
		$para = array();
		while (list ($key, $val) = each ($parameter)) {
			if($key == "sign" || $key == "sign_type" || $val == "")continue;
			else	$para[$key] = $parameter[$key];
		}
		return $para;
	}
	private function arg_sort($array) {
		ksort($array);
		reset($array);
		return $array;
	}
	private function build_mysign($sort_array, $security_code, $sign_type = "MD5") {
		$prestr = $this->create_linkstring($sort_array);     	//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = $prestr.$security_code;				//把拼接后的字符串再与安全校验码直接连接起来
		$mysgin = $this->sign($prestr,$sign_type);			    //把最终的字符串加密，获得签名结果
		return $mysgin;
	}
	private function create_linkstring($array) {
		$arg  = "";
		while (list ($key, $val) = each ($array)) {
			$arg.=$key."=".$val."&";
		}
		$arg = substr($arg,0,count($arg)-2);		     //去掉最后一个&字符
		return $arg;
	}
	private function create_linkstring_urlencode($array) {
		$arg  = "";
		while (list ($key, $val) = each ($array)) {
			if ($key != "service" && $key != "_input_charset")
				$arg.=$key."=".urlencode($val)."&";
			else $arg.=$key."=".$val."&";
		}
		$arg = substr($arg,0,count($arg)-2);		     //去掉最后一个&字符
		return $arg;
	}
	private function sign($prestr,$sign_type) {
		$sign='';
		if($sign_type == 'MD5') {
			$sign = md5($prestr);
		}elseif($sign_type =='DSA') {
			//DSA 签名方法待后续开发
			die("DSA 签名方法待后续开发，请先使用MD5签名方式");
		}else {
			die("支付宝暂不支持".$sign_type."类型的签名方式");
		}
		return $sign;
	}
	private function getSign($datas){
		unset($datas['sign'], $datas['sign_type'], $datas['ms']);
		$parameter   = $this->para_filter($datas);
		$sort_array  = $this->arg_sort($datas);
		$mysign      = $this->build_mysign($sort_array, $this->key, 'MD5');
		return $mysign;
	}
	private function get_verify($url,$time_out = "60") {
        $urlarr     = parse_url($url);
        $errno      = "";
        $errstr     = "";
        $transports = "";
        if($urlarr["scheme"] == "https") {
            $transports = "ssl://";
            $urlarr["port"] = "443";
        } else {
            $transports = "tcp://";
            $urlarr["port"] = "80";
        }
        $fp=@fsockopen($transports . $urlarr['host'],$urlarr['port'],$errno,$errstr,$time_out);
        if(!$fp) {
            die("ERROR: $errno - $errstr<br />\n");
        } else {
            fputs($fp, "POST ".$urlarr["path"]." HTTP/1.1\r\n");
            fputs($fp, "Host: ".$urlarr["host"]."\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: ".strlen($urlarr["query"])."\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $urlarr["query"] . "\r\n\r\n");
            while(!feof($fp)) {
                $info[]=@fgets($fp, 1024);
            }
            fclose($fp);
            $info = implode(",",$info);
            return $info;
        }
    }
	public function notify_verify($notify_id) {
		if ($this->transport == "https") {
            $gateway = "https://www.alipay.com/cooperate/gateway.do?";
        } else {
            $gateway = "http://notify.alipay.com/trade/notify_query.do?";
        }
        if($this->transport == "https") {
            $veryfy_url = $gateway. "service=notify_verify" ."&partner=" .$this->agent. "&notify_id=".$notify_id;
        } else {
            $veryfy_url = $gateway. "partner=".$this->agent."&notify_id=".$notify_id;
        }//echo $veryfy_url;exit;
        $veryfy_result = $this->get_verify($veryfy_url);
		return $veryfy_result;
    }
	private function checkReturn($datas){
		if (empty($datas)) {
			return false;
		} else {
			$mysign = $this->getSign($datas);
			$responseTxt = 'true';
			if (!empty($datas["notify_id"])) {
				$responseTxt = $this->notify_verify($datas["notify_id"]);
			}
			if (preg_match("/true$/i", $responseTxt) && $mysign == $datas["sign"]) {
				return $datas['out_trade_no'];
			} else {
				return false;
			}
		}
	}
	public function checkReturnA(){
		return $this->checkReturn($_GET);
	}
	public function checkReturnB(){
		return $this->checkReturn($_POST);
	}
}
?>