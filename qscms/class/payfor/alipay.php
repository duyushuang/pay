<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class payfor_alipay{
	public $status, $name;
	private $url;
	public function __construct(){
		if ($info = db::one('payfor_interface', '*', "ename='alipay'")) {
			$this->name   = $info['name'];
			$this->status = $info['status'];
		} else {
			$this->status = false;
			$this->name   = '未知充值方式';
		}
		$this->url    = qscms::getUrl('/member/topup/');
	}
	public function payfor($id, $money, $datas){
		if ($this->status) {
			db::update('topup', array(
				'remark1' => $datas['alipayAcc'],
				'remark2' => $datas['alipayId']
			), "id='$id'");
			$args = array(
				'url'   => $this->url,
				'type'  => 'get',
				'datas' => array(
					'tab' => 'alipay'
				)
			);
			qscms::setMsg('支付宝转账充值申请已经提交成功，请登陆支付宝网站进行转账，转账后联系'.cfg::get('sys', 'webName').'充值专员为您入账');
			return $args;
		}
		return false;
	}
}
?>