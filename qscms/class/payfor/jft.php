<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class payfor_jft{
	private $id, $key, $url;
	public $status, $name;
	public function __construct(){
		//$payfor_config = cache::get_array('payfor_config', true);
		if ($info = payfor_topup::getPayforInfo('jft')) {
			$this->name   = $info['name'];
			$this->id     = $info['arg_userid'];
			$this->key    = $info['arg_key'];
			$this->status = $info['status'];
		} else {
			$this->status = false;
			$this->name   = '未知充值方式';
		}
		$this->url  = 'http://do.jftpay.net/chargebank.aspx';//'http://tech.yeepay.com:8080/robot/debug.action'
	}
	public function payfor($id, $money, $datas){ 
		$web_name = cfg::get('sys', 'webName');
		if ($this->status) {
			$args = array(
				'parter' => $this->id,
				'type'   => $datas['type'],
				'value'  => sprintf('%0.2f', $money),
				'orderid' => $id,
				'callbackurl' => qscms::getUrl('/payfor/jft/callback'),
			);
			$str = $this->combineArgs($args).$this->key;
			$args['sign'] = md5($str);
			$args = array(
				'url'   => $this->url,
				'type'  => 'get',
				'encoding' => 'GBK',
				'datas' => $args
			);
			return $args;
		}
		return false;
	}
	private function iconv($arr, $AToB = true){
		if (is_array($arr)) {
			foreach ($arr as $k => $v) {
				$arr[$k] = $this->iconv($v, $AToB);
			}
			return $arr;
		} else return $AToB ? iconv(ENCODING, 'GBK', $arr) : iconv('GBK', ENCODING, $arr);
	}
	private function combineArgs($arr){
		$str = '';
		foreach ($arr as $k => $v) {
			$str && $str .= '&';
			$str .= $k.'='.$v;
		}
		return $str;
	}
	public function checkReturnA(){
		if ($this->status) {
			$var = qscms::v('_G');
			$arr = qscms::filterArray($_GET, array('orderid', 'opstate', 'ovalue'));
			$str = $this->combineArgs($arr).$this->key;
			$sign = md5($str);
			if ($sign == $var->gp_sign) {
				return true;
			}
		}
		return false;
	}
	public function checkReturnB(){
		if ($this->checkReturnA() !== false) {
			$var = qscms::v('_G');
			return $var->gp_orderid;
		}
		return false;
	}
}
?>