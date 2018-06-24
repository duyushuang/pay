<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class shop_order extends ext_base{
	public function __construct($id){
		$var = qscms::v('_G');
		$member = $var->member;
		$uid    = $member->uid;
		if ($datas = db::one('shop_order', '*', "id='$id' AND (suid='$uid' OR buid='$uid')")) {
			foreach ($datas as $k => $v) {
				$this->{'o_'.$k} = $v;
			}
			$this->cate = db::one('shop_cate', '*', "id='$this->o_cid'");
			$this->isSeller = $uid == $this->o_suid;
			$this->o_money = qscms::formatMoney($this->o_money);
			$this->seller = new member_center($this->o_suid);
			$this->buyer = new member_center($this->o_buid);
			switch ($this->o_trade_type) {
				case 0:
					$money = $this->o_money - qscms::formatMoney(db::one_one('log_freeze_money', 'money', "iid='$this->o_sid'"));
				break;
				case 1:
					$sxf = $this->o_money * cfg::getFloat('web', 'sxf1');
					switch ($this->o_who) {
						case 'seller':
							$money = $this->o_money;
						break;
						case 'buyer':
							$money = $this->o_money + qscms::formatMoney($sxf);
						break;
						case 'both':
							$money = $this->o_money + qscms::formatMoney($sxf / 2);
						break;
					}
				break;
				case 2:
					$sxf = $this->o_money * cfg::getFloat('web', 'sxf2');
					switch ($this->o_who) {
						case 'seller':
							$money = $this->o_money;
						break;
						case 'buyer':
							$money = $this->o_money + qscms::formatMoney($sxf);
						break;
						case 'both':
							$money = $this->o_money + qscms::formatMoney($sxf / 2);
						break;
					}
				break;
			}
			$this->needMoney = $money;
			$this->isLoad = true;
		} else $this->isLoad = false;
	}
	public function payfor(){//$status = array('未付款', '冻结保证金', '取消交易', '已付款', '已发货', '完成');
		if (!$this->isSeller) {
			if ($this->o_status == 0 || $this->o_status == 1) {
				if ($this->o_status == 0) {//担保、中介
					$rs = $this->buyer->addMoney(-$this->needMoney, 2, '购买'.$this->cate['name'].$this->o_title);
					if ($rs === true) {
						db::update('shop_order', array('status' => 3), "id='$this->o_id'");
						return '付款成功，请等待发货';
					}
					return $rs;
				} else {//一口价
					$rs = $this->buyer->addMoneyAndFreeze($this->o_sid, -$this->needMoney, 2, '购买'.$this->cate['name'].$this->o_title);
					if ($rs === true) {
						db::update('shop_order', array('status' => 3), "id='$this->o_id'");
						return '付款成功，请等待发货';
					}
					return $rs;
				}
			}
			return '该订单状态不能付款';
		}
		return '卖家不能付款';
	}
	public function cancel(){
		if (!$this->isSeller) {
			if ($this->o_status == 0 || $this->o_status == 1) {
				db::update('shop_order', array('status' => 2), "id='$this->o_id'");
				return '取消订单成功';
			}
			return '该订单状态不能取消';
		}
		return '卖家不能取消订单';
	}
	public function send(){
		if ($this->isSeller) {
			if ($this->o_status == 3) {
				db::update('shop_order', array('status' => 4), "id='$this->o_id'");
				return '发货成功';
			}
			return '该订单状态不能发货';
		}
		return '买家不能发货';
	}
	public function confirm(){
		if (!$this->isSeller) {
			if ($this->o_status == 4) {
				$sysMoney = $money = 0;
				switch ($this->o_trade_type) {
					case 0:
						$money = $this->o_money;
						$sxf = $this->seller->level->levelCfg['add']['shop']['ykj']['sxf'];//该等级费率
						$sysMoney = qscms::formatMoney($money * $sxf);//手续费
						$money -= $sysMoney;
					break;
					case 1://担保
						$sxf = $this->o_money * cfg::getFloat('web', 'sxf1');
						switch ($this->o_who) {
							case 'seller':
								$sysMoney = qscms::formatMoney($sxf);
								$money = $this->o_money - $sysMoney;
							break;
							case 'buyer':
								$money = $this->o_money;
							break;
							case 'both':
								$sysMoney = qscms::formatMoney($sxf / 2);
								$money = $this->o_money - $sysMoney;
							break;
						}
						$sysMoney = $sxf;
					break;
					case 2://中介
						$sxf = $this->o_money * cfg::getFloat('web', 'sxf2');
						switch ($this->o_who) {
							case 'seller':
								$sysMoney = qscms::formatMoney($sxf);
								$money = $this->o_money - $sysMoney;
							break;
							case 'buyer':
								$money = $this->o_money;
							break;
							case 'both':
								$sysMoney = qscms::formatMoney($sxf / 2);
								$money = $this->o_money - $sysMoney;
							break;
						}
						$sysMoney = $sxf;
					break;
				}
				db::update('shop_order', array('status' => 5), "id='$this->o_id'");
				$this->seller->addMoney($money, 2, '售出'.$this->cate['name'].$this->o_title);
				$sysMoney > 0 && background::addMoney(2, $sysMoney, '售出'.$this->cate['name'].$this->o_title);
				$this->seller->addCredit1('shop/ykj', 6, '售出'.$this->cate['name'].$this->o_title, intval($this->o_money));//增加卖家积分
				$this->buyer->addCredit1('shop/ykj', 6, '购买'.$this->cate['name'].$this->o_title, intval($this->o_money));//增加买家积分
				return '确认收货成功';
			}
			return '该订单状态不能确认收货';
		}
		return '卖家不能确认收货';
	}
	public static function createList($datas){
		$var = qscms::v('_G');
		$member = $var->member;
		if (!$member) return array('needLogin' => true);
		$datas = form::get4($datas, array(array('trade_type', 'int'), array('cid', 'int'), 'goods_name', array('goods_price', 'money'), 'account', 'role', 'who', 'period', 'remark'));
		$s = new shopModule($datas['cid']);$a = 'i';
		if (!$s->isLoad) return '不存在该分类'.gettype($datas['cid']).$datas['cid'];
		if (!in_array($datas['trade_type'], array(1, 2))) return '交易类型错误';
		$m = new member_center($datas['account']);
		if (!$m->status) return '您输入的对方帐号不存在';
		if ($m->uid == $member->uid) return '不能和自己创建交易';
		if (!in_array($datas['role'], array('buyer', 'seller'))) return '买卖类型错误';
		if (!in_array($datas['who'], array('buyer', 'seller', 'both'))) return '请勿非法操作';
		if (!in_array($datas['period'], cfg::getListValues('web', 'orderDays'))) return '请勿非法操作';
		$count = form::arrayEqual($datas['goods_name'], $datas['goods_price']);
		$reg = $s->c_titleRegStr;
		$titleName = $s->c_titleName;
		if ($datas['role'] == 'buyer') {//买家
			$buid = $member->uid;
			$suid = $m->uid;
		} else {//卖家
			$buid = $m->uid;
			$suid = $member->uid;
		}
		if ($count !== false) {
			$list = array();
			for ($i = 0; $i < $count; $i++) {
				$title = trim($datas['goods_name'][$i]);
				$money = $datas['goods_price'][$i];
				if (!$title) return '第'.($i + 1).'个'.$titleName.'不能为空';
				if ($reg && !form::isMatch($reg, $title)) return '第'.($i + 1).'个'.$titleName.'格式错误';
				if ($money <= 0) return '第'.($i + 1).'个'.$titleName.'价格不能为0';
				$arr = array(
					'sid'        => 0, 
					'bid'        => 0,
					'suid'       => $suid, 
					'buid'       => $buid,
					'cid'        => $datas['cid'],
					'title'      => $title,
					'des'        => $datas['des'],
					'trade_type' => $datas['trade_type'],//一口价
					'who'        => $datas['who'],
					'expire'     => time::$timestamp + $datas['period'] * 86400,
					'money'      => $money,
					'status'     => 0,
					'time'       => time::$timestamp
				);
				$list[] = $arr;
			}
			$right = array();
			$error = array();
			foreach ($list as $k => $v) {
				if (db::insert('shop_order', $v)) {
					$right[] = $k + 1;
				} else $error[] = $k + 1;
			}
			if (!$error) {
				return true;
			} else return '第'.implode(',', $error).'条数据创建失败';
		}
		return '数量不正确';
	}
}
?>