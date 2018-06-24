<?php
class member_shop extends ext_base{
	static function sysConvert($uid, $sid, $money, $bank_id){
		if ($money > 0){
			$id = self::insertShop1($uid, $money, $bank_id);
			if (is_numeric($id)){
				return self::matchToId($sid, $id, $money, true);
			}else return $id;//返回的不是id了　返回的错误信息
		}
		 return false;
	}
	
	public static function loginSys($uid){
		return $uid;
		$line = db::one('member', "id,salt,password,lastLoginIp", "id='$uid'");
		if ($line){
			scms::setcookie('memberAuth', $line['id'].'|'.$line['password'], 1200);	
		}
	}
	static function matchToId($fsid, $tsid, $money, $isSys = false, $expireTime = 48){//通过商品匹配
		if ($money <= 0) return '匹配额必须大于0';
		echo "$fsid|$tsid|$money\r\n";
		$time = time();
		db::autocommit();//开启不自动提交数据
		$fshop = db::one('shop', '*', "id='$fsid' AND type='0' AND status='101' AND restartsys=0 AND yuMoney>='$money'", false, true);
		$tshop = db::one('shop', '*', "id='$tsid' AND type='1' AND status='201' AND restartsys=0 AND yuMoney>='$money'", false, true);
		if ($fshop && $tshop) {
			if ($fshop['status'] == 300) return '捐助订单已删';
			//echo "买入：$fsid 总额：$fshop[allMoney] 余额：$fshop[yuMoney]\r\n卖出：$tsid 总额：$tshop[allMoney] 余额：$tshop[yuMoney]\r\n匹配金额：$money\r\n\r\n";
			$fshop['yuMoney'] = $fshop['allMoney'] - db::one_one('shop_order', 'SUM(money)', "fsid='$fsid' AND status<>13");//获取买入真实余额
			$tshop['yuMoney'] = $tshop['allMoney'] - db::one_one('shop_order', 'SUM(money)', "tsid='$tsid' AND status<>13");//获取卖出真实余额
			if ($fshop['yuMoney'] < $money || $tshop['yuMoney'] < $money) return "余额不足1";
			if (!$isSys){
				if ($tshop['yuMoney1'] < $money) return "余额不足2";
			}
			$f = array('yuMoney' => $fshop['yuMoney'], 'buyTotal' => $fshop['buyTotal'] + 1);
			$t = array('yuMoney' => $tshop['yuMoney'], 'sellTotal' => $tshop['sellTotal'] + 1);
			$f['yuMoney'] -= $money;
			$t['yuMoney'] -= $money;
			$f['yuMoney'] <= 0 && $f['status'] = 102;
			$t['yuMoney'] <= 0 && $t['status'] = 202;
			//echo "$f[yuMoney]|$t[yuMoney]\r\n\r\n";
			$arr = array(
				'fsid' => $fshop['id'],
				'fuid' => $fshop['uid'],
				'tsid' => $tshop['id'],
				'tuid' => $tshop['uid'],
				'money' => $money,
				'addTime' => $time,
				'endTime' => $time + (3600 * $expireTime),
				'status'  => 10,
				'debug'   => 5
			);
			if (db::insert('shop_order', $arr)) {
				
				//db::update('shop', $f, "id='$fsid'");
				//db::update('shop', $t, "id='$tsid'");
				$error = false;
				$error || $error = !db::update('shop', "yuMoney=yuMoney-$money,status=IF(yuMoney<=0,102,101),buyTotal=$fshop[buyTotal]", "id='$fsid'");
				$error || $error = !db::update('shop', "yuMoney=yuMoney-$money,yuMoney1=yuMoney1-$money,status=IF(yuMoney<=0,202,201),sellTotal=$tshop[sellTotal]", "id='$tsid'");
				if (!$error) {
					$fmobile = db::one_one('member', 'mobile', "id='$fshop[uid]'");
					$tmobile = db::one_one('member', 'mobile', "id='$tshop[uid]'");
					$fmobile && message::addSendVcodeInfo($fmobile, '已匹配');
					$tmobile && message::addSendVcodeInfo($tmobile, '已匹配');
					db::commit(true);
					return true;
				} else {
					db::rollback(true);
					return '更新失败';
				}
			} else {
				db::rollback(true);
				return '插入失败';
			}
		}
		db::autocommit(false);//关闭不自动提交数据
		return '商品不存在，或条件不成立';
	}
	static function reword($sid){//通过该买入奖励上级
		if ($shop = db::one('shop', "*", "id='$sid' AND type='0' AND isReword='0'")) {
			$reword = cache::get_array('reword');
			$user = db::one('member', '*', "id='$shop[uid]'");
			$rank = intval($user['rank']);//
			$baseMoney = qscms::formatMoney($shop['allMoney']);//买入总额 用于分配奖励基数
			$rewordSSClose = cfg::getBoolean('rewordSS', 'close');
			if ($rank > 0) {//有上级才分配奖励
				$userLevel = count($reword[0]['list']);//普通会员获取层级
				$userGetRank = $rank - $userLevel;
				$userOverflow = $reword[0]['overflow'] ? true : false;//普通会员是否无限级
				
				$managerLevel = count($reword[1]['list']);//经理获取层数
				$managerGetRank = $rank - $managerLevel;//经理获取层数
				$managerOverflow = $reword[1]['overflow'] ? true :false;//经理是否无限级
				
				$witherLevel = count($reword[2]['list']);//递减数量
				$witherOverflow = $reword[2]['overflow'] ? true :false;//是否无限递减
				
				$rewordList = array();
				$rewordGetDay = cfg::getInt('web', 'rewordGetDay');//非顶级会员提现限制天数
				$p = 0;//烧伤度
				$isSetP = false;//是否设置烧伤度
				foreach (treeDB::parents('member', $shop['uid'], 'id,rank,rewordWither,is_own', "isManager='0'".($userOverflow ? '' : " AND rank>=$userGetRank")) as $v) {
					$atParentIndex  = $rank - $v['rank'] - 1;
					$re = false;
					if (isset($reword[0]['list'][$atParentIndex])) $re = $reword[0]['list'][$atParentIndex];
					elseif ($reword[0]['overflow']) $re = $reword[0]['overflow'];
					if ($re) {
						if (!$rewordSSClose) {
							if (!$isSetP) {
								if ($v['rewordWither'] > 0) {
									$index = $v['rewordWither'] - 1;
									if (isset($reword[2]['list'][$index])) $p = $reword[2]['list'][$index];
									elseif ($reword[2]['overflow']) $p = $reword[2]['overflow'];
								}
								$isSetP = true;
							}
						}
						if ($v['is_own'] == 0){//不是白名单的才烧伤 SB江
							$p && $re *= $p;
						}
						$rewordList[] = array(
							'uid' => $v['id'],
							'rank' => $v['rank'],
							'frank' => $atParentIndex + 1,//和上级相隔多少代
							'reword' => $re,
							'rewordType' => 1,//领导奖
							'isManager' => false
						);
					}
				}
				$p = 0;//烧伤度
				$isSetP = false;//是否设置烧伤度
				foreach (treeDB::parents('member', $shop['uid'], 'id,rank,rewordWither,is_own', "isManager='1'".($managerOverflow ? '' : " AND rank>=$managerGetRank")) as $v) {
					$re = false;
					$atParentIndex  = $rank - $v['rank'] - 1;
					if (isset($reword[1]['list'][$atParentIndex])) $re = $reword[1]['list'][$atParentIndex];
					elseif ($reword[1]['overflow']) $re = $reword[1]['overflow'];
					if ($re) {
						if (!$rewordSSClose) {
							if (!$isSetP) {
								if ($v['rewordWither'] > 0) {
									$index = $v['rewordWither'] - 1;
									if (isset($reword[2]['list'][$index])) $p = $reword[2]['list'][$index];
									elseif ($reword[2]['overflow']) $p = $reword[2]['overflow'];
								}
								$isSetP = true;
							}
						}
						if ($v['is_own'] == 0){//不是白名单的才烧伤 SB江
							$p && $re *= $p;
						}
						
						$rewordList[] = array(
							'uid' => $v['id'],
							'rank' => $v['rank'],
							'frank' => $atParentIndex + 1,//和上级相隔多少代
							'reword' => $re,
							'rewordType' => 2,//经理奖
							'isManager' => true
						);
					}
				}
				
				$managerMaxRewordMonth = cfg::getMoney('web', 'managerMaxRewordMonth');//每个月经理将最多多少
				
				if ($recommend = cfg::getMoney('web', 'recommend')) {//如果有推荐奖
					if ($tuid = db::one_one('member', 'tuid', "id='$shop[uid]'")) {
						$re = db::one('member', '*', "id='$tuid'");
						if (!$rewordSSClose) {
							if ($re['rewordWither'] > 0) {//奖金烧伤
								$index = $re['rewordWither'] - 1;
								$p = 0;//烧伤度
								if (isset($reword[2]['list'][$index])) $p = $reword[2]['list'][$index];
								elseif ($reword[2]['overflow']) $p = $reword[2]['overflow'];
								if ($re['is_own'] == 0){//不是白名单的才烧伤 SB江
									$p && $recommend *= $p;
								}
							}
						}
						$rewordList[] = array(
							'uid' => $tuid,
							'rank' => 0,
							'frank' => 1,
							'reword' => $recommend,
							'rewordType' => 0,//推荐奖
							'isManager' => false
						);
					}
				}
				
				
				$bei = cfg::getInt('m', 'bei');
				$low = cfg::getInt('m', 'low');
				$buyDay = cfg::getInt('m', 'buyDay');
				$sellDay = cfg::getInt('m', 'sellDay');
				$lixiDay = cfg::getInt('m', 'lixiDay');
				$lixiPercent = qscms::percentDecimal(cfg::get('m', 'lixiPercent'));
				$rewordAll = 0;//总共奖励多少
				$var = qscms::v('_G');
				$start = $var->tsmStart;
				$end   = $var->tsmEnd;
				foreach ($rewordList as $v) {//
					$v['money'] = qscms::formatMoney($baseMoney * $v['reword']);//奖励金额
					$arr = array(
						'isSys'        => 1,
						'rewordType'   => $v['rewordType'],//奖励类型
						'uid'          => $v['uid'],
						'allMoney'     => 0,
						'yuMoney'      => 0,
						'lixi'         => $v['money'],
						'lixiYu'       => $v['money'],
						'dayLixi'      => $lixiDay,
						'dayStart'     => $buyDay,
						'addTime'      => time(),
						'startTime'    => time(),//利息解冻时间
						'lixiStatus'   => 1,
						'status'       => 102,//不用冻结
						'remark'       => '提成奖励',
						'fuid'         => $shop['uid'],
						'fsid'         => $shop['id'],
						'fpercent'     => $v['reword'],
						'frank'        => $v['frank'],
						'isLock'       => 1//锁定
					);
					if ($rewordGetDay > 0 && $v['frank'] > 1) {//如果限制了提现天数，并且不是顶级会员
						$arr['lixiStatus'] = 0;
						$arr['lixiTime']   = $arr['startTime'] + $rewordGetDay * 86400;//利息解冻时间
					}
					
					$next = true;
					if ($managerMaxRewordMonth > 0 && $v['isManager']) {//限制经理奖
						$all = db::one_one('shop', 'SUM(lixi)', "isSys='1' AND uid='$v[uid]' AND addTime>='$start' AND addTime<='$end'");
						if ($all >= $managerMaxRewordMonth) $next = false;
					}
					
					if ($next) {
						if (db::insert('shop', $arr)) {
							$rewordAll += $v['money'];
						}
					}
				}
				if ($rewordAll > 0) {
					db::update('shop', "isReword='1',rewordMoney='$rewordAll'", "id='$shop[id]'");
				}
			} else {//没有上级的
				db::update('shop', "isReword='1',rewordMoney='0'", "id='$shop[id]'");
			}
		}
	}
	static function rewordConfirm($id){
		db::update('shop', 'isLock=0', "fsid='$id'");
	}
	static function lixiUnFreeze($id = false){
		$__time = time();
		if (!$id) {
			db::update('shop', 'status=101', "status=100 AND startTime<=$__time");
			db::update('shop', 'lixiStatus=1', "type='0' AND lixiStatus=0 AND lixiTime<=$__time AND fsid=0");
		}
		/**
		 * 合并隔代
		 */
		//foreach (db::select('shop', '*', ($id ? "id='$id' AND " : '')."type='0' AND lixiStatus=0 AND lixiTime<=$__time AND isLock=0 AND fsid>0 AND (rewordType=0 OR frank>1)") as $v) {
		foreach (db::select('shop', '*', ($id ? "id='$id' AND " : '')."type='0' AND lixiStatus=0 AND lixiTime<=$__time AND isLock=0 AND fsid>0 AND frank>1", false, 50) as $v) {
			$pid = db::one_one('shop', 'id', "uid='$v[uid]' AND rewordType=3 AND cid=0");
			if (!$pid) {
				$pid = db::insert('shop', array(
					'isSys'        => 1,
					'rewordType'   => 3,//奖励类型 派生
					'uid'          => $v['uid'],
					'allMoney'     => 0,
					'yuMoney'      => 0,
					'lixi'         => 0,
					'lixiYu'       => 0,
					'dayLixi'      => 0,
					'dayStart'     => 0,
					'addTime'      => time(),
					'startTime'    => time(),//利息解冻时间
					'lixiStatus'   => 1,//已解冻
					'status'       => 102,//不用冻结
					'remark'       => '派生奖励',
					'fuid'         => 0,
					'fsid'         => 0,
					'fpercent'     => 0,
					'frank'        => 0,
					'isLock'       => 0//锁定
				), true);
			}
			if ($pid) {
				db::update('shop', "lixi=lixi+$v[lixiYu],lixiYu=lixiYu+$v[lixiYu],status=102", "id='$pid'");
			}
			db::update('shop', "lixiStatus=1,status=103,hid=$pid", "id='$v[id]'");
		}
	}
	static function rewordUnFreeze($id){//隔代奖励解冻
		$time = time();
		db::update('shop', "lixiTime=$time,isLock=0", "id='$id'");
		self::lixiUnFreeze($id);
	}
	static function rewordDelete($id){
		db::delete('shop', "fsid='$id'");
	}
	
	static function rewordShop_order($id, $p){//诚信奖金
		if ($item = db::one('shop_order', '*', "id='$id' AND status='20'")) {
			$money = qscms::formatMoney($item['money']);
			$reword = floor($money * $p * 100) / 100;//奖励的数量
			if ($reword > 0) {
				$user = db::one('member', '*', "id='$item[uid]'");
				$lixiDay = cfg::getInt('web', 'shopRewordLixiTime');//诚信解冻天数
				$arr = array(
					'isSys'        => 1,
					'uid'          => $item['fuid'],
					'allMoney'     => 0,
					'yuMoney'      => 0,
					'lixi'         => $reword,
					'lixiYu'       => $reword,
					'dayLixi'      => 0,
					'dayStart'     => 0,
					'addTime'      => time(),
					'startTime'    => time(),//利息解冻时间
					'lixiStatus'   => 1,
					'status'       => 102,//不用冻结
					'remark'       => '诚信奖金'
				);
				if ($lixiDay){
					$time = time();
					$arr['lixiStatus'] = 0;
					$arr['lixiTime']   = $time + ($lixiDay * 86400);//利息解冻时间
				}
				db::insert('shop', $arr);
			}
		}
	}
	static function rewordShop($id, $p, $lixiTime = false){
		$time = time();
		if ($item = db::one('shop', '*', "id='$id' AND status='102' AND complateTime>0")) {
			$money = qscms::formatMoney($item['allMoney']);
			$reword = floor($money * $p * 100) / 100;//奖励的数量
			if ($reword > 0) {
				$user = db::one('member', '*', "id='$item[uid]'");
				$rewordGetDay = cfg::getInt('web', 'rewordGetDay');//非顶级会员提现限制天数
				$arr = array(
					'isSys'        => 1,
					'uid'          => $item['uid'],
					'allMoney'     => 0,
					'yuMoney'      => 0,
					'lixi'         => $reword,
					'lixiYu'       => $reword,
					'dayLixi'      => 0,
					'dayStart'     => 0,
					'addTime'      => time(),
					'startTime'    => time(),//利息解冻时间
					'lixiStatus'   => 1,
					'status'       => 102,//不用冻结
					'remark'       => '商品提成奖励'
				);
				if ($lixiTime > $time){
					$arr['lixiStatus'] = 0;
					$arr['lixiTime']   = $lixiTime;//利息解冻时间
				}
				if ($lixiTime === false){//如果 是false才执行这个 如果带入了参数小于当前时间直接解冻
					if ($rewordGetDay > 0 && $user['rank'] > 0) {//如果限制了提现天数，并且不是顶级会员
						$arr['lixiStatus'] = 0;
						$arr['lixiTime']   = $arr['startTime'] + $rewordGetDay * 86400;//利息解冻时间
					}
				}
				db::insert('shop', $arr);
			}
		}
	}
	static function insertShop1($uid, $money, $bank_id){//这种是 直接等待匹配的
		if (!db::exists('account', "uid='$uid' AND id='$bank_id'")) return '银行信息错误';
		$arr = array(
			'is_sys'	   => 1,
			'type'		   => 1,
			'uid'          => $uid,
			'bank'		   => $bank_id,
			'allMoney'     => $money,
			'yuMoney'      => $money,
			'lixi'         => 0,
			'lixiYu'       => 0,
			'dayLixi'      => 0,
			'dayStart'     => 0,
			'addTime'      => time(),
			'startTime'    => time(),//利息解冻时间
			'lixiStatus'   => 1,
			'status'       => 201,//不用冻结
			'remark'       => '',
		);
		$id = db::insert('shop', $arr, true);
		return $id;
	}
	static function insertShop($uid, $money, $remark = '幸福信', $type = 4){//这种是 用户自己提现的
		if ($money > 0){//至少幸福信多出的比例要大于10快才可以嘛 - -
			db::insert('add_money_url', array('url' => NOW_URL, 'time' => time(), 'ip' => qscms::v('_G')->intip));
			$arr = array(
				'is_sys'	   => 1,
				'isSys'        => 1,
				'rewordType'   => $type,//4是幸福信
				'uid'          => $uid,
				'allMoney'     => 0,
				'yuMoney'      => 0,
				'lixi'         => $money,
				'lixiYu'       => $money,
				'dayLixi'      => 0,
				'dayStart'     => 0,
				'addTime'      => time(),
				'startTime'    => time(),//利息解冻时间
				'lixiStatus'   => 1,
				'status'       => 102,//不用冻结
				'remark'       => $remark,
			);
			$id = db::insert('shop', $arr, true);
			return true;
		}else return '金额不能为空';
		
	}
	static function soMatch1($datas){
		static $moreThan, $firstMoney, $firstSpace, $firstCount, $bei, $ignoreIdsArr = array();
		
		$datas = form::get4($datas, array('id', 'minMoney', 'maxCount', 'same', 'blood', 'randSpace'));
		(!isset($datas['randSpace']) || !$datas['randSpace']) && $datas['randSpace'] = false;
		$randSpace = $datas['randSpace'];
		unset($datas['randSpace']);
		qscms::setType($datas, 'int');
		
		!isset($bei) && $bei = cfg::getMoney('m', 'bei');//最少购买 10
		!isset($moreThan) && $moreThan = cfg::getMoney('match', 'moreThan');
		!isset($firstMoney) && $firstMoney = cfg::getMoney('match', 'firstMoney');//优先排单金额
		!isset($firstSpace) && $firstSpace = cfg::getMoneySpace('match', 'firstSpace');//优先排单随机百分比区间
		!isset($firstCount) && $firstCount = cfg::getInt('match', 'firstCount');//优先排单条数
		$datas['maxCount'] && $firstCount = $datas['maxCount'];	
		if ($item = db::one('shop', '*', "id='$datas[id]'")) {
			if ($item['type'] == 1 && $item['status'] == 201 && $item['yuMoney1'] >= $bei) {
				if ($datas['minMoney'] <= $item['yuMoney']) {
					$uid = $item['uid'];//卖出用户ID
					$yuMoney = floor($item['yuMoney1'] / $bei) * $bei;//整数
					$useMoney = 0;//
					//$datas['same'] = 1;
					//$datas['blood'] = 1;
					$firstMatch = $yuMoney < $moreThan;//单条匹配
					
					
					$bloodWhere = false;
					$bloodSql = false;
					$bloodType = false;
					$list = array();
					$getUids = array();//获取过的用户ID
					if (in_array($datas['blood'], array(0, 1))) {//直属关系
						//$user = db::one('member', '*', "id='$item[uid]'");
						if ($datas['blood'] == 0) {//不能是上下级
							//$bloodWhere = "NOT(l<$user[l] AND r>$user[r] OR l>$user[l] AND r<$user[r])";
							$bloodType = 'out';
						} elseif ($datas['blood'] == 1) {//必须是上下级
							//$bloodWhere = "l<$user[l] AND r>$user[r] OR l>$user[l] AND r<$user[r]";
							$bloodType = 'in';
						}
						$bloodSql = "SELECT pid uid FROM ".db::table('member_tree')." WHERE cid='$item[uid]' UNION SELECT cid uid FROM ".db::table('member_tree')." t0,(SELECT rank + 1 rank FROM ".db::table('member')." WHERE id='$item[uid]') t1 WHERE pid='$item[uid]' AND t0.rank=t1.rank";
						$bloodSql = "SELECT uid FROM ($bloodSql) t";
					}
					//echo $bloodSql, "\r\n";//return false;
					$bloodWhere = "t1.status='0'".($bloodWhere ? ' AND '.$bloodWhere : '');//判断用户必须为没禁用
					$bloodWhere1 = "status='0'".($bloodWhere ? ' AND '.$bloodWhere : '');//判断用户必须为没禁用
					//$sql0 = db::sqlSelect('member', 'id', $bloodWhere1);
					$money = $datas['minMoney'] > 0 ? $datas['minMoney'] : $item['yuMoney'];
					
					/**
					 * 获取拒绝用户ID
					 */
					if (isset($ignoreIdsArr[$item['uid']])) $ignoreIds = $ignoreIdsArr[$item['uid']];
					else {
						$sql1 = db::sqlSelect('shop_order', 'fuid', "status='13' AND tuid='$item[uid]'");//获取该单用户的拒绝用户列表
						$ignoreIds = db::fetchArrayFirstAll($sql1);//拒绝的用户IDS
						$ignoreIds[] = $item['uid'];
					}
					$ignoreIds = '\''.implode('\',\'', $ignoreIds).'\'';
					//echo time::timerEnd(), "\r\n";
					/**
					 * 百分之十匹配
					 */
					$use10 = cfg::getBoolean('match', 'autoMatch_10');
					if ($use10) {
						$yuMoney10 = $yuMoney * 10;
						if ($bloodSql) {
							$sql = db::sqlSelect("shop|($bloodSql):uid=uid|member:id=t0.uid", 'id,uid,allMoney,yuMoney||email', "t0.status='101' AND t0.restartsys=0  AND t0.uid NOT IN($ignoreIds) AND t0.yuMoney<='$yuMoney10' AND t0.yuMoney=t0.allMoney AND ".($bloodType == 'in' ? "t1.uid IS NOT NULL" : "t1.uid IS NULL")." AND t2.status='0'", 't0.id', 20);
						} else {
							$sql = db::sqlSelect('shop|member:id=uid', 'id,uid,allMoney,yuMoney|email', "t0.status='101' AND t0.restartsys=0 AND t0.uid NOT IN($ignoreIds) AND t0.allMoney=t0.yuMoney AND t0.yuMoney<='$yuMoney10'  AND t1.status='0'", 'id', 20);
						}
						//echo $sql, "\r\n";
						foreach (db::fetchAll($sql) as $v) {//echo "10%\r\n";
							if ($datas['same'] && in_array($v['uid'], $getUids)) continue;
							if ($v['yuMoney'] <= 100) $use = $v['yuMoney'];
							else {
								$use = floor($v['yuMoney'] / 10);
							}
							if ($useMoney + $use >= $yuMoney) {
								$use = $yuMoney - $useMoney;
								$use = floor($use / $bei) * $bei;
								if (!$use) break;
								$break = true;
							}
							$list[] = array(
								'id' => $v['id'],
								'email'    => $v['email'],
								'allMoney' => $v['yuMoney'],
								'useMoney' => $use,
								'yuMoney'  => $v['yuMoney'] - $use
							);
							$useMoney += $use;
							$getUids[] = $v['uid'];
							if ($break) break;
						}
					}
					
					/**
					 * 优先排单
					 */
					if (!$list && $firstMoney > 0 && $firstSpace) {
						/**
						 * 改进 20160505
						 */
						if ($bloodSql) {
							$sql = db::sqlSelect("shop|($bloodSql):uid=uid|member:id=t0.uid", 'id,uid,allMoney,yuMoney||email', "t0.status='101' AND t0.restartsys=0 AND t0.uid NOT IN($ignoreIds) AND t0.allMoney>='$firstMoney' AND t0.yuMoney>='$bei' AND ".($bloodType == 'in' ? "t1.uid IS NOT NULL" : "t1.uid IS NULL")." AND t2.status='0'", 'id', $firstCount);
						} else {
							$sql = db::sqlSelect('shop|member:id=uid', 'id,uid,allMoney,yuMoney|email', "t0.status='101' AND t0.restartsys=0 AND t0.uid NOT IN($ignoreIds) AND t0.allMoney>='$firstMoney' AND t0.yuMoney>='$bei' AND t1.status='0'", 'id', $firstCount);
						}
						
						foreach (db::fetchAll($sql) as $v) {
							if ($datas['same'] && in_array($v['uid'], $getUids)) continue;
							if ($v['yuMoney'] / $v['allMoney'] <= $firstSpace[1]) $use = $v['yuMoney'];
							else {
								$start = 100;
								$len = $start / $firstSpace[0];//
								$end = $firstSpace[1] * $len;
								$p = rand($start, $end) / $len;//随机百分比
								$use = floor($v['allMoney'] * $p / $bei) * $bei;
							}
							if ($useMoney + $use >= $yuMoney) {
								$use = $yuMoney - $useMoney;
								if (!$use) break;
								$break = true;
							}
							$list[] = array(
								'id' => $v['id'],
								'email'    => $v['email'],
								'allMoney' => $v['yuMoney'],
								'useMoney' => $use,
								'yuMoney'  => $v['yuMoney'] - $use
							);
							$useMoney += $use;
							$getUids[] = $v['uid'];
							if ($break) break;
						}
					}
					
					if (!$list) {
						if ($firstMatch) {//如果允许单条匹配
							//echo '单条匹配', "\r\n";
						
							/**
							 * 改进 20160505
							 */
							if ($bloodSql) {
								$sql = db::sqlSelect("shop|($bloodSql):uid=uid|member:id=t0.uid", 'id,uid,allMoney,yuMoney||email', "t0.status='101' AND t0.restartsys=0 AND t0.uid NOT IN($ignoreIds) AND t0.yuMoney>='$yuMoney' AND ".($bloodType == 'in' ? "t1.uid IS NOT NULL" : "t1.uid IS NULL")." AND t2.status='0'", 'id', 1);
							} else {
								$sql = db::sqlSelect('shop|member:id=uid', 'id,uid,allMoney,yuMoney|email', "t0.status='101' AND t0.restartsys=0 AND t0.uid NOT IN($ignoreIds) AND t0.yuMoney>='$yuMoney' AND t1.status='0'", 'id', 1);
							}
							
							foreach (db::fetchAll($sql) as $v) {
								$list[] = array(
									'id' => $v['id'],
									'email'    => $v['email'],
									'allMoney' => $v['yuMoney'],
									'useMoney' => $yuMoney,
									'yuMoney'  => $v['yuMoney'] - $yuMoney
								);
							}
						}
					}
					if (!$list) {
						
						/**
						 * 改进 20160505
						 */
						if ($bloodSql) {
							$sql = db::sqlSelect("shop|($bloodSql):uid=uid|member:id=t0.uid", 'id,uid,allMoney,yuMoney||email', "t0.status='101' AND t0.restartsys=0 AND t0.uid NOT IN($ignoreIds) AND".($datas['minMoney'] > 0 ? " t0.yuMoney>='$datas[minMoney]' AND " : " t0.yuMoney>'$bei' AND ")." t0.yuMoney<'$yuMoney' AND ".($bloodType == 'in' ? "t1.uid IS NOT NULL" : "t1.uid IS NULL")." AND t2.status='0'", 'id', $getCount);
						} else {
							$sql = db::sqlSelect('shop|member:id=uid', 'id,uid,allMoney,yuMoney|email', "t0.status='101' AND t0.restartsys=0 AND t0.uid NOT IN($ignoreIds) AND".($datas['minMoney'] > 0 ? " t0.yuMoney>='$datas[minMoney]' AND " : " t0.yuMoney>'$bei' AND ")." t0.yuMoney<'$yuMoney' AND t1.status='0'", 'id', $getCount);
						}
						
						$useMoney = 0;
						foreach (db::fetchAll($sql) as $v) {
							if ($datas['same'] && in_array($v['uid'], $getUids)) continue;
							$use = $v['yuMoney'];
							$break = false;
							if ($useMoney + $use >= $yuMoney) {
								$use = $yuMoney - $useMoney;
								if (!$use) break;
								$break = true;
							}
							$list[] = array(
								'id' => $v['id'],
								'email'    => $v['email'],
								'allMoney' => $v['yuMoney'],
								'useMoney' => $use,
								'yuMoney'  => 0
							);
							$useMoney += $use;
							$getUids[] = $v['uid'];
							if ($break) break;
						}
					}
					if ($list) return $list;
					return '没有可匹配数据';
				}
				return '最小匹配金额大于剩余可匹配金额';
			}
			return '条件不符';
		}
		return '商品不存在';
	}
	static function soMatch($datas){
		static $moreThan, $firstMoney, $firstSpace, $firstCount, $bei, $ignoreIdsArr = array();
		
		$datas = form::get4($datas, array('id', 'minMoney', 'maxCount', 'same', 'blood', 'randSpace'));
		(!isset($datas['randSpace']) || !$datas['randSpace']) && $datas['randSpace'] = false;
		$randSpace = $datas['randSpace'];
		unset($datas['randSpace']);
		qscms::setType($datas, 'int');
		
		!isset($bei) && $bei = cfg::getMoney('m', 'bei');//最少购买 10
		!isset($moreThan) && $moreThan = cfg::getMoney('match', 'moreThan');
		!isset($firstMoney) && $firstMoney = cfg::getMoney('match', 'firstMoney');//优先排单金额
		!isset($firstSpace) && $firstSpace = cfg::getMoneySpace('match', 'firstSpace');//优先排单随机百分比区间
		!isset($firstCount) && $firstCount = cfg::getInt('match', 'firstCount');//优先排单条数
		$datas['maxCount'] && $firstCount = $datas['maxCount'];	
		if ($item = db::one('shop', '*', "id='$datas[id]'")) {
			if ($item['type'] == 1 && $item['status'] == 201 && $item['yuMoney1'] >= $bei) {
				if ($datas['minMoney'] <= $item['yuMoney']) {
					$uid = $item['uid'];//卖出用户ID
					$yuMoney = floor($item['yuMoney1'] / $bei) * $bei;//整数
					$useMoney = 0;//
					//$datas['same'] = 1;
					//$datas['blood'] = 1;
					$firstMatch = $yuMoney < $moreThan;//单条匹配
					
					
					$bloodWhere = false;
					$bloodSql = false;
					$bloodType = false;
					$list = array();
					$getUids = array();//获取过的用户ID
					if (in_array($datas['blood'], array(0, 1))) {//直属关系
						//$user = db::one('member', '*', "id='$item[uid]'");
						if ($datas['blood'] == 0) {//不能是上下级
							//$bloodWhere = "NOT(l<$user[l] AND r>$user[r] OR l>$user[l] AND r<$user[r])";
							$bloodType = 'out';
						} elseif ($datas['blood'] == 1) {//必须是上下级
							//$bloodWhere = "l<$user[l] AND r>$user[r] OR l>$user[l] AND r<$user[r]";
							$bloodType = 'in';
						}
						$bloodSql = "SELECT pid uid FROM ".db::table('member_tree')." WHERE cid='$item[uid]' UNION SELECT cid uid FROM ".db::table('member_tree')." t0,(SELECT rank + 1 rank FROM ".db::table('member')." WHERE id='$item[uid]') t1 WHERE pid='$item[uid]' AND t0.rank=t1.rank";
						$bloodSql = "SELECT uid FROM ($bloodSql) t";
					}
					//echo $bloodSql, "\r\n";//return false;
					$bloodWhere = "t1.status='0'".($bloodWhere ? ' AND '.$bloodWhere : '');//判断用户必须为没禁用
					$bloodWhere1 = "status='0'".($bloodWhere ? ' AND '.$bloodWhere : '');//判断用户必须为没禁用
					//$sql0 = db::sqlSelect('member', 'id', $bloodWhere1);
					$money = $datas['minMoney'] > 0 ? $datas['minMoney'] : $item['yuMoney'];
					
					/**
					 * 获取拒绝用户ID
					 */
					if (isset($ignoreIdsArr[$item['uid']])) $ignoreIds = $ignoreIdsArr[$item['uid']];
					else {
						$sql1 = db::sqlSelect('shop_order', 'fuid', "status='13' AND tuid='$item[uid]'");//获取该单用户的拒绝用户列表
						$ignoreIds = db::fetchArrayFirstAll($sql1);//拒绝的用户IDS
						$ignoreIds[] = $item['uid'];
					}
					$ignoreIds = '\''.implode('\',\'', $ignoreIds).'\'';
					//echo time::timerEnd(), "\r\n";
					/**
					 * 百分之十匹配
					 */
					$use10 = cfg::getBoolean('match', 'autoMatch_10');
					if ($use10) {
						$yuMoney10 = $yuMoney * 10;
						if ($bloodSql) {
							$sql = db::sqlSelect("shop|($bloodSql):uid=uid|member:id=t0.uid", 'id,uid,allMoney,yuMoney||email', "t0.status='101' AND t0.restartsys=0 AND t0.uid NOT IN($ignoreIds) AND t0.allMoney>=1000 AND t0.yuMoney<='$yuMoney10' AND t0.yuMoney=t0.allMoney AND ".($bloodType == 'in' ? "t1.uid IS NOT NULL" : "t1.uid IS NULL")." AND t2.status='0'", 't0.id', 20);
						} else {
							$sql = db::sqlSelect('shop|member:id=uid', 'id,uid,allMoney,yuMoney|email', "t0.status='101' AND t0.restartsys=0 AND t0.uid NOT IN($ignoreIds) AND t0.allMoney=t0.yuMoney AND t0.yuMoney<='$yuMoney10' AND t1.status='0'", 'id', 20);
						}
						//echo $sql, "\r\n";
						foreach (db::fetchAll($sql) as $v) {//echo "10%\r\n";
							if ($datas['same'] && in_array($v['uid'], $getUids)) continue;
							if ($v['yuMoney'] <= 100) $use = $v['yuMoney'];
							else {
								$use = floor($v['yuMoney'] / 10);
							}
							if ($useMoney + $use >= $yuMoney) {
								$use = $yuMoney - $useMoney;
								$use = floor($use / $bei) * $bei;
								if (!$use) break;
								$break = true;
							}
							$list[] = array(
								'id' => $v['id'],
								'email'    => $v['email'],
								'allMoney' => $v['yuMoney'],
								'useMoney' => $use,
								'yuMoney'  => $v['yuMoney'] - $use,
								'time'     => 24
							);
							$useMoney += $use;
							$getUids[] = $v['uid'];
							if ($break) break;
						}
					}
					
					/**
					 * 优先排单
					 */
					if (!$list && $firstMoney > 0 && $firstSpace) {
						/**
						 * 改进 20160505
						 */
						if ($bloodSql) {
							$sql = db::sqlSelect("shop|($bloodSql):uid=uid|member:id=t0.uid", 'id,uid,allMoney,yuMoney||email', "t0.status='101' AND t0.restartsys=0 AND t0.uid NOT IN($ignoreIds) AND t0.allMoney>='$firstMoney' AND t0.yuMoney>='$bei' AND ".($bloodType == 'in' ? "t1.uid IS NOT NULL" : "t1.uid IS NULL")." AND t2.status='0'", 'id', $firstCount);
						} else {
							$sql = db::sqlSelect('shop|member:id=uid', 'id,uid,allMoney,yuMoney|email', "t0.status='101' AND t0.restartsys=0 AND t0.uid NOT IN($ignoreIds) AND t0.allMoney>='$firstMoney' AND t0.yuMoney>='$bei' AND t1.status='0'", 'id', $firstCount);
						}
						
						foreach (db::fetchAll($sql) as $v) {
							if ($datas['same'] && in_array($v['uid'], $getUids)) continue;
							if ($v['yuMoney'] / $v['allMoney'] <= $firstSpace[1]) $use = $v['yuMoney'];
							else {
								$start = 100;
								$len = $start / $firstSpace[0];//
								$end = $firstSpace[1] * $len;
								$p = rand($start, $end) / $len;//随机百分比
								$use = floor($v['allMoney'] * $p / $bei) * $bei;
							}
							if ($useMoney + $use >= $yuMoney) {
								$use = $yuMoney - $useMoney;
								if (!$use) break;
								$break = true;
							}
							$list[] = array(
								'id' => $v['id'],
								'email'    => $v['email'],
								'allMoney' => $v['yuMoney'],
								'useMoney' => $use,
								'yuMoney'  => $v['yuMoney'] - $use
							);
							$useMoney += $use;
							$getUids[] = $v['uid'];
							if ($break) break;
						}
					}
					
					if (!$list) {
						if ($firstMatch) {//如果允许单条匹配
						
							/**
							 * 改进 20160505
							 */
							if ($bloodSql) {
								$sql = db::sqlSelect("shop|($bloodSql):uid=uid|member:id=t0.uid", 'id,uid,allMoney,yuMoney||email', "t0.status='101' AND t0.restartsys=0 AND t0.uid NOT IN($ignoreIds) AND t0.yuMoney>='$yuMoney' AND ".($bloodType == 'in' ? "t1.uid IS NOT NULL" : "t1.uid IS NULL")." AND t2.status='0'", 'id', 1);
							} else {
								$sql = db::sqlSelect('shop|member:id=uid', 'id,uid,allMoney,yuMoney|email', "t0.status='101' AND t0.restartsys=0 AND t0.uid NOT IN($ignoreIds) AND t0.yuMoney>='$yuMoney' AND t1.status='0'", 'id', 1);
							}
							
							foreach (db::fetchAll($sql) as $v) {
								$list[] = array(
									'id' => $v['id'],
									'email'    => $v['email'],
									'allMoney' => $v['yuMoney'],
									'useMoney' => $yuMoney,
									'yuMoney'  => $v['yuMoney'] - $yuMoney
								);
							}
						}
					}
					if (!$list) {
						
						/**
						 * 改进 20160505
						 */
						if ($bloodSql) {
							$sql = db::sqlSelect("shop|($bloodSql):uid=uid|member:id=t0.uid", 'id,uid,allMoney,yuMoney||email', "t0.status='101' AND t0.restartsys=0 AND t0.uid NOT IN($ignoreIds) AND".($datas['minMoney'] > 0 ? " t0.yuMoney>='$datas[minMoney]' AND " : " t0.yuMoney>'0' AND ")." t0.yuMoney<'$yuMoney' AND ".($bloodType == 'in' ? "t1.uid IS NOT NULL" : "t1.uid IS NULL")." AND t2.status='0'", 'id', $getCount);
						} else {
							$sql = db::sqlSelect('shop|member:id=uid', 'id,uid,allMoney,yuMoney|email', "t0.status='101' AND t0.restartsys=0 AND t0.uid NOT IN($ignoreIds) AND".($datas['minMoney'] > 0 ? " t0.yuMoney>='$datas[minMoney]' AND " : " t0.yuMoney>'0' AND ")." t0.yuMoney<'$yuMoney' AND t1.status='0'", 'id', $getCount);
						}
						
						$useMoney = 0;
						foreach (db::fetchAll($sql) as $v) {
							if ($datas['same'] && in_array($v['uid'], $getUids)) continue;
							$use = $v['yuMoney'];
							$break = false;
							if ($useMoney + $use >= $yuMoney) {
								$use = $yuMoney - $useMoney;
								if (!$use) break;
								$break = true;
							}
							$list[] = array(
								'id' => $v['id'],
								'email'    => $v['email'],
								'allMoney' => $v['yuMoney'],
								'useMoney' => $use,
								'yuMoney'  => 0
							);
							$useMoney += $use;
							$getUids[] = $v['uid'];
							if ($break) break;
						}
					}
					if ($list) return $list;
					return '没有可匹配数据';
				}
				return '最小匹配金额大于剩余可匹配金额';
			}
			return '条件不符';
		}
		return '商品不存在';
	}
	static function soMatch_admin($datas){
		static $moreThan, $firstMoney, $firstSpace, $firstCount, $bei, $ignoreIdsArr = array();
		$datas = form::get4($datas, array('id', 'minMoney', 'maxCount', 'same', 'blood', 'randSpace'));
		(!isset($datas['randSpace']) || !$datas['randSpace']) && $datas['randSpace'] = false;
		$randSpace = $datas['randSpace'];
		unset($datas['randSpace']);
		qscms::setType($datas, 'int');
		
		!isset($bei) && $bei = cfg::getMoney('m', 'bei');//最少购买 10
		!isset($moreThan) && $moreThan = cfg::getMoney('match', 'moreThan');
		!isset($firstMoney) && $firstMoney = cfg::getMoney('match', 'firstMoney');//优先排单金额
		!isset($firstSpace) && $firstSpace = cfg::getMoneySpace('match', 'firstSpace');//优先排单随机百分比区间
		!isset($firstCount) && $firstCount = cfg::getInt('match', 'firstCount');//优先排单条数
		$datas['maxCount'] && $firstCount = $datas['maxCount'];	
		if ($item = db::one('shop', '*', "id='$datas[id]'")) {
			if ($item['type'] == 1 && $item['status'] == 201 && $item['yuMoney'] >= $bei) {
				if ($datas['minMoney'] <= $item['yuMoney']) {
					$uid = $item['uid'];//卖出用户ID
					$yuMoney = floor($item['yuMoney'] / $bei) * $bei;//整数
					$useMoney = 0;//
					//$datas['same'] = 1;
					//$datas['blood'] = 1;
					$firstMatch = $yuMoney < $moreThan;//单条匹配
					
					
					$bloodWhere = false;
					$bloodSql = false;
					$bloodType = false;
					$list = array();
					$getUids = array();//获取过的用户ID
					if (in_array($datas['blood'], array(0, 1))) {//直属关系
						//$user = db::one('member', '*', "id='$item[uid]'");
						if ($datas['blood'] == 0) {//不能是上下级
							//$bloodWhere = "NOT(l<$user[l] AND r>$user[r] OR l>$user[l] AND r<$user[r])";
							$bloodType = 'out';
						} elseif ($datas['blood'] == 1) {//必须是上下级
							//$bloodWhere = "l<$user[l] AND r>$user[r] OR l>$user[l] AND r<$user[r]";
							$bloodType = 'in';
						}
						$bloodSql = "SELECT pid uid FROM ".db::table('member_tree')." WHERE cid='$item[uid]' UNION SELECT cid uid FROM ".db::table('member_tree')." t0,(SELECT rank + 1 rank FROM ".db::table('member')." WHERE id='$item[uid]') t1 WHERE pid='$item[uid]' AND t0.rank=t1.rank";
						$bloodSql = "SELECT uid FROM ($bloodSql) t";
					}
					//echo $bloodSql, "\r\n";//return false;
					$bloodWhere = "t1.status='0'".($bloodWhere ? ' AND '.$bloodWhere : '');//判断用户必须为没禁用
					$bloodWhere1 = "status='0'".($bloodWhere ? ' AND '.$bloodWhere : '');//判断用户必须为没禁用
					//$sql0 = db::sqlSelect('member', 'id', $bloodWhere1);
					$money = $datas['minMoney'] > 0 ? $datas['minMoney'] : $item['yuMoney'];
					
					/**
					 * 获取拒绝用户ID
					 */
					if (isset($ignoreIdsArr[$item['uid']])) $ignoreIds = $ignoreIdsArr[$item['uid']];
					else {
						$sql1 = db::sqlSelect('shop_order', 'fuid', "status='13' AND tuid='$item[uid]'");//获取该单用户的拒绝用户列表
						$ignoreIds = db::fetchArrayFirstAll($sql1);//拒绝的用户IDS
						$ignoreIds[] = $item['uid'];
					}
					$ignoreIds = '\''.implode('\',\'', $ignoreIds).'\'';
					//echo time::timerEnd(), "\r\n";
					/**
					 * 百分之十匹配
					 */
					 /*
					 * 后台操作搜索匹配订单的时候的就要这个
					 */
					 /*
					if ($datas['minMoney'] == 0){
						$use10 = cfg::getBoolean('match', 'autoMatch_10');
						if ($use10) {
						$yuMoney10 = $yuMoney * 10;
						if ($bloodSql) {
							$sql = db::sqlSelect("shop|($bloodSql):uid=uid|member:id=t0.uid", 'id,uid,allMoney,yuMoney||email', "t0.status='101' AND t0.uid NOT IN($ignoreIds) AND t0.yuMoney<='$yuMoney10' ".($datas['minMoney'] ? " AND yuMoney>='$datas[minMoney]'" : '')."  AND t0.yuMoney=t0.allMoney AND ".($bloodType == 'in' ? "t1.uid IS NOT NULL" : "t1.uid IS NULL")." AND t2.status='0'", 't0.id', 20);
						} else {
							$sql = db::sqlSelect('shop|member:id=uid', 'id,uid,allMoney,yuMoney|email', "t0.status='101' AND t0.uid NOT IN($ignoreIds) AND t0.allMoney=t0.yuMoney AND t0.yuMoney<='$yuMoney10' ".($datas['minMoney'] ? " AND yuMoney>='$datas[minMoney]'" : '')." AND t1.status='0'", 'id', 20);
						}
						//echo $sql, "\r\n";
						foreach (db::fetchAll($sql) as $v) {//echo "10%\r\n";
							if ($datas['same'] && in_array($v['uid'], $getUids)) continue;
							if ($v['yuMoney'] <= 100) $use = $v['yuMoney'];
							else {
								$use = floor($v['yuMoney'] / 10);
							}
							if ($useMoney + $use >= $yuMoney) {
								$use = $yuMoney - $useMoney;
								$use = floor($use / $bei) * $bei;
								if (!$use) break;
								$break = true;
							}
							$list[] = array(
								'id' => $v['id'],
								'email'    => $v['email'],
								'allMoney' => $v['yuMoney'],
								'useMoney' => $use,
								'yuMoney'  => $v['yuMoney'] - $use
							);
							$useMoney += $use;
							$getUids[] = $v['uid'];
							if ($break) break;
						}
					}
					}
					*/
					/**
					 * 优先排单
					 */
					 
					if (!$list && ($firstMoney > 0 || $datas['minMoney'] > 0) && $firstSpace) {
						/**
						 * 改进 20160505
						 */
						if ($bloodSql) {
							$sql = db::sqlSelect("shop|($bloodSql):uid=uid|member:id=t0.uid", 'id,uid,allMoney,yuMoney||email', "t0.status='101' AND t0.uid NOT IN($ignoreIds) AND t0.allMoney>='$firstMoney' AND t0.yuMoney>='$bei' ".($datas['minMoney'] ? " AND yuMoney>='$datas[minMoney]'" : '')." AND ".($bloodType == 'in' ? "t1.uid IS NOT NULL" : "t1.uid IS NULL")." AND t2.status='0'", 'id', $firstCount);
						} else {
							$sql = db::sqlSelect('shop|member:id=uid', 'id,uid,allMoney,yuMoney|email', "t0.status='101' AND t0.uid NOT IN($ignoreIds) AND t0.allMoney>='$firstMoney' AND t0.yuMoney>='$bei' ".($datas['minMoney'] ? " AND yuMoney>='$datas[minMoney]'" : '')." AND t1.status='0'", 'id', $firstCount);
						}
						foreach (db::fetchAll($sql) as $v) {
							if ($datas['same'] && in_array($v['uid'], $getUids)) continue;
							if ($v['yuMoney'] / $v['allMoney'] <= $firstSpace[1]) $use = $v['yuMoney'];
							else {
								$start = 100;
								$len = $start / $firstSpace[0];//
								$end = $firstSpace[1] * $len;
								$p = rand($start, $end) / $len;//随机百分比
								$use = floor($v['allMoney'] * $p / $bei) * $bei;
							}
							if ($useMoney + $use >= $yuMoney) {
								$use = $yuMoney - $useMoney;
								if (!$use) break;
								$break = true;
							}
							$list[] = array(
								'id' => $v['id'],
								'email'    => $v['email'],
								'allMoney' => $v['yuMoney'],
								'useMoney' => $use,
								'yuMoney'  => $v['yuMoney'] - $use
							);
							$useMoney += $use;
							$getUids[] = $v['uid'];
							if ($break) break;
						}
					}
					
					if (!$list) {
						if ($firstMatch) {//如果允许单条匹配
						
							/**
							 * 改进 20160505
							 */
							if ($bloodSql) {
								$sql = db::sqlSelect("shop|($bloodSql):uid=uid|member:id=t0.uid", 'id,uid,allMoney,yuMoney||email', "t0.status='101' AND t0.uid NOT IN($ignoreIds) AND t0.yuMoney>='$yuMoney' ".($datas['minMoney'] ? " AND yuMoney>='$datas[minMoney]'" : '')." AND ".($bloodType == 'in' ? "t1.uid IS NOT NULL" : "t1.uid IS NULL")." AND t2.status='0'", 'id', 1);
							} else {
								$sql = db::sqlSelect('shop|member:id=uid', 'id,uid,allMoney,yuMoney|email', "t0.status='101' AND t0.uid NOT IN($ignoreIds) AND t0.yuMoney>='$yuMoney' ".($datas['minMoney'] ? " AND yuMoney>='$datas[minMoney]'" : '')." AND t1.status='0'", 'id', 1);
							}
						
							foreach (db::fetchAll($sql) as $v) {
								$list[] = array(
									'id' => $v['id'],
									'email'    => $v['email'],
									'allMoney' => $v['yuMoney'],
									'useMoney' => $yuMoney,
									'yuMoney'  => $v['yuMoney'] - $yuMoney
								);
							}
						}
					}
					if (!$list) {
						
						/**
						 * 改进 20160505
						 */
						if ($bloodSql) {
							$sql = db::sqlSelect("shop|($bloodSql):uid=uid|member:id=t0.uid", 'id,uid,allMoney,yuMoney||email', "t0.status='101' AND t0.uid NOT IN($ignoreIds) AND".($datas['minMoney'] > 0 ? " t0.yuMoney>='$datas[minMoney]' AND " : " t0.yuMoney>'0' AND ")." t0.yuMoney<'$yuMoney' ".($datas['minMoney'] ? " AND yuMoney>='$datas[minMoney]'" : '')." AND ".($bloodType == 'in' ? "t1.uid IS NOT NULL" : "t1.uid IS NULL")." AND t2.status='0'", 'id', $getCount);
						} else {
							$sql = db::sqlSelect('shop|member:id=uid', 'id,uid,allMoney,yuMoney|email', "t0.status='101' AND t0.uid NOT IN($ignoreIds) AND".($datas['minMoney'] > 0 ? " t0.yuMoney>='$datas[minMoney]' AND " : " t0.yuMoney>'0' AND ")." t0.yuMoney<'$yuMoney' ".($datas['minMoney'] ? " AND yuMoney>='$datas[minMoney]'" : '')." AND t1.status='0'", 'id', $getCount);
						}
						
						$useMoney = 0;
						foreach (db::fetchAll($sql) as $v) {
							if ($datas['same'] && in_array($v['uid'], $getUids)) continue;
							$use = $v['yuMoney'];
							$break = false;
							if ($useMoney + $use >= $yuMoney) {
								$use = $yuMoney - $useMoney;
								if (!$use) break;
								$break = true;
							}
							$list[] = array(
								'id' => $v['id'],
								'email'    => $v['email'],
								'allMoney' => $v['yuMoney'],
								'useMoney' => $use,
								'yuMoney'  => 0
							);
							$useMoney += $use;
							$getUids[] = $v['uid'];
							if ($break) break;
						}
					}
					if ($list) return $list;
					return '没有可匹配数据';
				}
				return '最小匹配金额大于剩余可匹配金额';
			}
			return '条件不符';
		}
		return '商品不存在';
	}
	
	static function match($datas){
		$datas = form::get4($datas, array('tid', 'data'));
		if (is_array($datas['data'])) {
			$count = 0;
			foreach ($datas['data'] as $fid => $money) {
				$rs = self::matchToId($fid, $datas['tid'], $money);
				if ($rs === true) {
					$count++;
				}
			}
			if ($count > 0) return $count;
			return '匹配失败，请检查状态';
		}
		return '请提交正确数据';
	}
	static function match_admin($datas){
		$datas = form::get4($datas, array('tid', 'data'));
		if (is_array($datas['data'])) {
			$count = 0;
			foreach ($datas['data'] as $fid => $money) {
				$rs = self::matchToId($fid, $datas['tid'], $money, true);
				if ($rs === true) {
					$count++;
				}
			}
			if ($count > 0) return $count;
			return '匹配失败，请检查状态';
		}
		return '请提交正确数据';
	}
	static function autoMatch($ignore = false){
		//if (!$ignore) return false;
		//return false;
		$lockFile = d('./cache/match.lock');
		if (file_exists($lockFile)) {
			if (time() - filemtime($lockFile) > 3000) @unlink($lockFile);
			else return false;
		}
		$bei = cfg::getMoney('m', 'bei');
		if (cfg::getBoolean('match', 'auto')) {
			touch($lockFile);
			$minMoney = cfg::getMoney('match', 'autoMinMoney');//低于这个阀值才匹配
			$same     = cfg::getInt('match', 'autoSame');//是否允许重复用户
			$blood    = cfg::getInt('match', 'autoBlood');//上下级限制
			$count    = cfg::getInt('match', 'autoCount');//每次处理数量
			$soMinMoney = cfg::getInt('match', 'soMinMoney');//搜索捐助最小金额
			$orders = array(1 => false, 2 => 't0.yuMoney1', 3 => 't0.yuMoney1 DESC');
			$order = $orders[cfg::getInt('match', 'getDesc')];
			/*$firstMoney = cfg::getMoney('match', 'firstMoney');//优先排单金额
			$firstSpace = cfg::getMoneySpace('match', 'firstSpace');//优先排单随机百分比区间
			$firstCount = cfg::getInt('match', 'firstCount');//优先排单条数
			if ($firstMoney > 0 && $firstSpace !== false && $firstCount > 0) {
				foreach (db::select('shop', 'id', "type='1' AND status='201' AND allMoney>='$firstMoney' AND yuMoney>='$bei' AND yuMoney<='$minMoney'", 'id', $firstCount) as $v) {
					$rs = self::soMatch(array('id' => $v['id'], 'minMoney' => 0, 'maxCount' => 0, 'same' => $same, 'blood' => $blood, 'randSpace' => $firstSpace));
					if (is_array($rs)) {
						foreach ($rs as $v1) {
							self::matchToId($v1['id'], $v['id'], $v1['useMoney']);
						}
					}
				}
			}*/
			//$sql = db::sqlSelect('shop', 'id,uid', "type='1' AND status='201' AND yuMoney>='$bei' AND yuMoney<='$minMoney' AND isError='0'", 'id');
			//$sql = db::sqlSelect("($sql)|member:id=uid", 'id|', "t1.status='0'", false, $count);
			
			$sql = db::sqlSelect('shop|member:id=uid', 'id|', "t0.type='1' AND t0.status='201' AND t0.yuMoney1>='$bei' AND t0.yuMoney1<='$minMoney' AND t0.isError<>'1' AND t1.status='0' AND t1.isZD='0'", $order, $count);
			$arr = db::fetchAll($sql);
			$existsArr = array();
			foreach ($arr as $v) {
				$rs = self::soMatch(array('id' => $v['id'], 'minMoney' => $soMinMoney, 'maxCount' => 0, 'same' => $same, 'blood' => $blood));
				if (is_array($rs)) {
					foreach ($rs as $v1) {
						$key = $v1['id'].'_'.$v['id'].'_'.$v1['useMoney'];
						if (!in_array($key, $existsArr)) {
							echo self::matchToId($v1['id'], $v['id'], $v1['useMoney'], false, isset($v1['time']) ? $v1['time'] : 48);
							$existsArr[] = $key;
						}
					}
				} else echo $rs."\r\n";
			}
			@unlink($lockFile);
		}
	}
	static function autoMatch1($getCount = false){
		//if (!$ignore) return false;
		//return false;
		$bei = cfg::getMoney('m', 'bei');
		if (cfg::getBoolean('match', 'auto')) {
			touch($lockFile);
			$minMoney = cfg::getMoney('match', 'autoMinMoney');//低于这个阀值才匹配
			$same     = cfg::getInt('match', 'autoSame');//是否允许重复用户
			$blood    = cfg::getInt('match', 'autoBlood');//上下级限制
			$count    = cfg::getInt('match', 'autoCount');//每次处理数量
			$getCount || $getCount = $count;
			$soMinMoney = cfg::getInt('match', 'soMinMoney');//搜索捐助最小金额
			$orders = array(1 => false, 2 => 't0.yuMoney1', 3 => 't0.yuMoney1 DESC');
			$order = $orders[cfg::getInt('match', 'getDesc')];
			/*$firstMoney = cfg::getMoney('match', 'firstMoney');//优先排单金额
			$firstSpace = cfg::getMoneySpace('match', 'firstSpace');//优先排单随机百分比区间
			$firstCount = cfg::getInt('match', 'firstCount');//优先排单条数
			if ($firstMoney > 0 && $firstSpace !== false && $firstCount > 0) {
				foreach (db::select('shop', 'id', "type='1' AND status='201' AND allMoney>='$firstMoney' AND yuMoney>='$bei' AND yuMoney<='$minMoney'", 'id', $firstCount) as $v) {
					$rs = self::soMatch(array('id' => $v['id'], 'minMoney' => 0, 'maxCount' => 0, 'same' => $same, 'blood' => $blood, 'randSpace' => $firstSpace));
					if (is_array($rs)) {
						foreach ($rs as $v1) {
							self::matchToId($v1['id'], $v['id'], $v1['useMoney']);
						}
					}
				}
			}*/
			//$sql = db::sqlSelect('shop', 'id,uid', "type='1' AND status='201' AND yuMoney>='$bei' AND yuMoney<='$minMoney' AND isError='0'", 'id');
			//$sql = db::sqlSelect("($sql)|member:id=uid", 'id|', "t1.status='0'", false, $count);
			
			$sql = db::sqlSelect('shop|member:id=uid', 'id|', "t0.type='1' AND t0.status='201' AND t0.yuMoney1>='$bei' AND t0.yuMoney1<='$minMoney' AND t0.isError<>'1' AND t1.status='0'", $order, $getCount);
			$arr = db::fetchAll($sql);
			foreach ($arr as $v) {
				print_r($v);
				$rs = self::soMatch(array('id' => $v['id'], 'minMoney' => $soMinMoney, 'maxCount' => 0, 'same' => $same, 'blood' => $blood));
				print_r($rs);
			}
		}
	}
	static function autoMatchTest(){
		time::timerStart();
		$total = db::dataCount('shop');
		$pagesize = 100;
		$page = floor(($total - 1) / $pagesize) + 1;
		$page = 10;
		for ($i = 1; $i <= $page; $i++) {
			$list = db::select('shop', 'id,uid,allMoney,yuMoney', false, false, $pagesize, $i);
			break;
		}
		echo time::timerEnd();
	}
}
?>