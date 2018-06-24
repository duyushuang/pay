<?php





(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');

$top_menu=array(

	'list' => '用户搜索',

	'add1' => '添加用户',

	'change'      => '调整用户关系',

	'addMoney' => '用户加钱',

	'info' => '资金日志',
	
	'message' => '发送站内息',

	'changeBl' => array('name' => '异步提交商户费率', 'hide' => true),
	
	'memberApi' => array('name' => '获取用户独立通道', 'hide' => true),
	
	'changeApi' => array('name' => '异步提交用户独立通道', 'hide' => true),

	'list4' => array('name' => '关系列表', 'hide' => true),

	'more' => array('name' => '详细信息', 'hide' => true),

	'gotoUser' => array('name' => '前往用户中心', 'hide' => true)

);

$adminUser = $var->admin['username'];
$ip = qscms::ipint();

$time = time();

$top_menu_key = array_keys($top_menu);

($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];

switch ($method) {

	case 'gotoUser':

		$m = new member_center($var->gp_id);

		qscms::setcookie('memberAuth', $m->m_id.'|'.$m->m_password);

		qscms::gotoUrl('/');

	break;
	case 'message':
		$uid = $var->getInt('gp_uid');
		$uid || $uid = '';
		if (form::hash()){
			$datas = form::get3('isF', 'uid', 'title', 'message');
			if (!$datas['message'] || !$datas['title']) admin::show_message('请填写发送标题及内容', $baseUrl.'&method=message');
			if ($datas['uid']){
				if (db::exists('member', "id='$datas[uid]'")){
					db::insert('member_message', array(
						'uid' => $datas['uid'],
						'title' => $datas['title'],
						'message' => $datas['message'],
						'addTime' => time()
					));
					admin::show_message('发送至'.$datas['uid'].'用户成功', $baseUrl.'&method=message');
				}else admin::show_message('没有找到该用户ID', $baseUrl.'&method=message');
			}else{
				if ($datas['isF'] == 1){
					$list = db::select('member', 'id');
					if ($list){
						foreach($list as $v){
							db::insert('member_message', array(
								'uid' => $v['id'],
								'title' => $datas['title'],
								'message' => $datas['message'],
								'addTime' => time()
							));		
						}
						admin::show_message('发送至所有用户成功', $baseUrl.'&method=message');	
					}else admin::show_message('当前还没有用户', $baseUrl.'&method=message');
				}else admin::show_message('请选择发送所有用户或填写发送独立用户', $baseUrl.'&method=message');
			}
		}
	break;
	case 'info':

		$wh = '1=1';

		$keys = array('type', 'uid', 'sn', 'minMoney', 'maxMoney', 'minCredit', 'maxCredit','addTime1', 'addTime2');

		$urlVar = '';

		$vars = array();

		foreach ($keys as $v) {

			$val = $var->{'gp_'.$v};

			$vars[$v] = $val;

			!is_null($val) && $val !== '' && (($urlVar && $urlVar .= '&') || !$urlVar) && $urlVar .= $v.'='.urlencode($val);

		}

		extract($vars);

		if (!is_null($type) && $type > -1){

			$wh .= " AND (type='$type')";

		}else $type = -1;

		

		if ($uid){

			$wh .= " AND uid='$uid'";

		}
		
		if ($sn){

			$wh .= " AND sn='$sn'";

		}
		if ($minMoney){

			$wh .= " AND money >= $minMoney";

		}

		if ($maxMoney){

			$wh .= " AND money <= $maxMoney";

		}

		if ($minCredit){

			$wh .= " AND credit >= $minCredit";

		}

		if ($maxCredit){

			$wh .= " AND credit <= $maxCredit";

		}

		if (!is_null($addTime1) && $addTime1 !== '') {

			$addTime1 = time::getGeneralTimestamp($addTime1);

			$wh .= ' AND time>=\''.$addTime1.'\'';

		}

		if (!is_null($addTime2) && $addTime2 !== '') {

			$addTime2 = time::getGeneralTimestamp($addTime2.' 23:59:59');

			$wh .= ' AND time<=\''.$addTime2.'\'';

		}

		$list = array();
		
		$allMoney = db::one_one('log_money', 'SUM(money)', $wh);//总金额
		if ($total = db::dataCount('log_money', $wh)){

			$list = db::select('log_money', '*', $wh, 'id DESC', $pagesize, $page);

			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.'&method='.$method.($urlVar ? '&'.$urlVar : '').'&page={page}', $pagestyle);

		}

	break;

	/*

	case 'zd':

		if (form::hash()){

			$datas = form::get3('user');

			if ($id = db::one_one('member', 'id', form::checkEmail($datas['user']) ? "email='$datas[user]'" : (form::checkMobilephone($datas['user']) ? "mobile='$datas[user]'" : "id='$datas[user]'"))){

				db::update('member', "isZD='1'", "id='$id'");

				db::query("UPDATE qs_member_tree t0 INNER JOIN qs_member t1 ON t0.cid=t1.id SET t1.isZD=1 WHERE t0.pid=$id");

				admin::show_message('阻断成功', $baseUrl.'&method=zd');

			}else{

				admin::show_message('请填写阻断用户', $baseUrl.'&method=zd');	

			}

		}

	break;

	*/

	case 'changeBl':

		//serialize

		$uid = $var->gp_uid;

    	$arr = array('status' => true, 'msg' => '');

    	if ($uid && pay::$array){

			$blArr = array();

			$isSet = true;

			foreach(pay::$array as $k => $v){

				$blArr[$k] = $var->{'gp_'.$k};

				if ($blArr[$k] < 0) $blArr[$k] = -1;

				if ($blArr[$k] > 1) {

					$arr['status'] = false;

					$arr['msg']    = '费率不能超过1(100%)';

					break;

				}

				if ($blArr[$k] != -1) $isSet = false;

			}

			

			if ($arr['status'] && $blArr){

				if ($isSet) db::update('member', "bl=''", "id='$uid'");//全部都是-1就是用默认的

				else {

					$bl = serialize($blArr);

					db::update('member', "bl='$bl'", "id='$uid'");//单独设置费率

				}

			}

		}else {

			$arr['status'] = false;

			$arr['msg']    = '系统接口混乱或者用户ID不存在';

		}

		exit(string::json_encode($arr));

		

	break;
	
	case 'memberApi':
		$uid = $var->gp_uid;
		$arr = array('status' => true, 'html' => array(), 'msg' => '');
		$list = array();
		if (pay::$array){
			$html = '';
			foreach(pay::$array as $k => $v){
				
				$isSys = db::exists('pay_off', "uid='$uid' AND type='$k' AND isSys=1");
				$html .= 
				'<div class="radio-list" style="margin-left:25px; margin-top:5px;">
        			<span class="btn btn-xs blue ajaxify" data-type="show" data-toggle="tooltip" title="" data-original-title="'.$v.'" style="width:100px;">'.$v.'</span>
          			<label class="radio-inline"><span><input value="0"  name="'.$k.'_" class="radio" '.($isSys ? '': 'checked="checked"').'  type="radio"></span>开通</label>
          			<label class="radio-inline"><span><input value="1" name="'.$k.'_" class="radio" '.($isSys ? 'checked="checked"': '').' type="radio"></span>关闭</label>
        		</div>';
			}
			$arr['html'] = $html;
		}else{
			$arr['status'] = false;
			$arr['msg'] = '系统接口混乱或者用户ID不存在';	
		}
		exit(string::json_encode($arr));
	break;
	case 'changeApi':
		$uid = $var->gp_uid;

		$arr = array('status' => true, 'msg' => '');
		//echo $uid;
		//print_r(pay::$array);
		if ($uid && pay::$array){

			$blArr = array();

			$isSet = true;

			foreach(pay::$array as $k => $v){

				$apiArr[$k] = $var->{'gp_'.$k};
				
				if ($apiArr[$k] == 1) {//加入限制
					if (!db::exists('pay_off', "uid='$uid' AND type='$k' AND isSys=1")){
						db::delete('pay_off', "uid='$uid' AND type='$k'");
						db::insert('pay_off', array('uid' => $uid, 'type' => $k, 'isSys' => 1));
					}
				}else{//取消限制
					db::delete('pay_off', "uid='$uid' AND type='$k'");
				}
			}
		}else {
			$arr['status'] = false;
			$arr['msg'] = '系统接口混乱或者用户ID不存在';
		}
		exit(string::json_encode($arr));
	break;
	case 'list':

		if (form::hash()) {

			extract(form::get3('del', 'disable', 'enable'));

			/*

			if ($del) {

				admin::show_message('删除了'.db::del_ids('member', $del).'个用户', $baseUrl.'&method=list');

			} else

			*/

			if ($disable) {

				db::update('member', 'status=1', "id ".sql::getInStr($disable));

				admin::show_message('禁用了'.db::changeRows().'个用户', $baseUrl.'&method=list');

			} elseif ($enable) {

				db::update('member', 'status=0', "id ".sql::getInStr($enable));

				admin::show_message('启用了'.db::changeRows().'个用户', $baseUrl.'&method=list');

			}

		}

		if ($var->gp_type == 'set') {

			if ($var->gp_status) {

				$user = $var->admin['username'];

				$ip = qscms::ipint();

				$time = time();

				db::update('member', "status=1-status", "id='{$var->gp_status}'");

				$is_status = db::one_one('member', 'status', "id='{$var->gp_status}'");

				db::insert('admin_log', "user='$adminUser',info='修改用户{$var->gp_status}状态为：".($is_status ? '禁用': '正常')."',ip='$ip',addTime='$time'");

				admin::show_message('操作成功');

			}elseif ($var->gp_isApi) {

				$user = $var->admin['username'];

				$ip = qscms::ipint();

				$time = time();

				db::update('member', "isApi=1-isApi", "id='{$var->gp_isApi}'");

				$is_isApi = db::one_one('member', 'isApi', "id='{$var->gp_isApi}'");

				db::insert('admin_log', "user='$adminUser',info='修改用户{$var->gp_isApi}状态为：".($is_isApi ? '正常': '关闭')."',ip='$ip',addTime='$time'");

				admin::show_message('操作成功');

			}elseif ($var->gp_agent){

				$user = $var->admin['username'];

				$ip = qscms::ipint();

				$time = time();

				db::update('member', "agent=1-agent", "id='{$var->gp_agent}'");

				$is_agent = db::one_one('member', 'agent', "id='{$var->gp_agent}'");

				db::insert('admin_log', "user='$adminUser',info='修改用户{$var->gp_agent}状态为：".($is_agent ? '正常': '关闭')."',ip='$ip',addTime='$time'");

				admin::show_message('操作成功');

			}

			/*

			if ($var->gp_isDebug) {





				if ($var->admin['username'] != 'admin'){

					admin::show_message('对不起，您没有权限修改该页数据！');

				}

				db::update('member', "isDebug=1-isDebug", "id='{$var->gp_isDebug}'");

				admin::show_message('操作成功');

			}

			*/

			

		}

		$isList = $var->gp_type == 'so';

		if ($isList) {

			$keys = array('agent', 'isApi', 'bl', 'vip', 'email', 'name', 'wxname', 'wxid', 'uid', 'status', 'mobile', 'regTime1', 'regTime2', 'minMoney', 'maxMoney', 'bank_alipay', 'bank_name');

			$vars = array();

			$urlVar = '';

			foreach ($keys as $v) {

				$val = $var->{'gp_'.$v};

				$vars['so_'.$v] = $val;

				!is_null($val) && $val !== '' && (($urlVar && $urlVar .= '&') || !$urlVar) && $urlVar .= $v.'='.urlencode($val);

			}

			if ($var->postData) {

				qscms::gotoUrl($baseUrl.'&method='.$method.'&type=so&'.$urlVar, true);

			}

			extract($vars);

			$where = '';

			if (!is_null($so_agent) && $so_agent !== '') {

				$where && $where .= ' AND ';

				$where .= 'agent=\''.intval($so_agent).'\'';

			}
			if ($so_minMoney){
				
				$where && $where .= ' AND ';
				$where .= "money >= $so_minMoney";
	
			}
	
			if ($so_maxMoney){
				$where && $where .= ' AND ';
				$where .= "money <= $so_maxMoney";
	
			}

			if (!is_null($so_isApi) && $so_isApi !== '') {

				$where && $where .= ' AND ';

				$where .= 'isApi=\''.intval($so_isApi).'\'';

			}

			if (!is_null($so_vip) && $so_vip !== '') {

				$where && $where .= ' AND ';

				if ($so_vip == 0) $where .= "vip=0";

				else $where .= "vip>0";

			}

			if (!is_null($so_bl) && $so_bl !== '') {

				$where && $where .= ' AND ';

				if ($so_bl == 0) $where .= "bl=''";

				else $where .= "bl!=''";

			}

			if (!is_null($so_uid) && $so_uid !== '') {

				$where && $where .= ' AND ';

				$where .= 'id=\''.intval($so_uid).'\'';

			}

			

			if (!is_null($so_wxid) && $so_wxid !== '') {

				$where && $where .= ' AND ';

				$where .= 'wxid=\''.$so_wxid.'\'';

			}

			if (!is_null($so_name) && $so_name !== '') {

				$where && $where .= ' AND ';

				if (strpos($so_name, '*') !== false) {

					$where .= 'name LIKE \''.str_replace('*', '%', $so_name).'\'';

				} else {

					$where .= 'name LIKE \'%'.$so_name.'%\'';

				}

			}

			

			if (!is_null($so_wxname) && $so_wxname !== '') {

				$where && $where .= ' AND ';

				if (strpos($so_wxname, '*') !== false) {

					$where .= 'wxname LIKE \''.str_replace('*', '%', $so_wxname).'\'';

				} else {

					$where .= 'wxname LIKE \'%'.$so_wxname.'%\'';

				}

			}

			if (!is_null($so_email) && $so_email !== '') {

				$where && $where .= ' AND ';

				if (strpos($so_email, '*') !== false) {

					$where .= 'email LIKE \''.str_replace('*', '%', $so_email).'\'';

				} else {

					$where .= 'email LIKE \'%'.$so_email.'%\'';

				}

			}

			

			if (!is_null($so_bank_name) && $so_bank_name !== '') {

				$where && $where .= ' AND ';

				if (strpos($so_bank_name, '*') !== false) {

					$where .= 'holder_firstname LIKE \''.str_replace('*', '%', $so_bank_name).'\'';

				} else {

					$where .= 'holder_firstname LIKE \'%'.$so_bank_name.'%\'';

				}

				$l = db::select('account', 'uid', $where);

				if ($l){

					foreach($l as $v){

						$ids && $ids .= ',';	

						$ids .= $v['uid'];

					}

					$where && $where .= ' AND ';

					$where .= "id in ($ids)";

				}

			}

			if (!is_null($so_bank_alipay) && $so_bank_alipay !== '') {

				$where && $where .= ' AND ';

				if (strpos($so_bank_alipay, '*') !== false) {

					$where .= 'alipay LIKE \''.str_replace('*', '%', $so_bank_alipay).'\'';

				} else {

					$where .= 'alipay LIKE \'%'.$so_bank_alipay.'%\'';

				}

				$l = db::select('account', 'uid', "alipay like '%$so_bank_alipay%'");

				if ($l){

					foreach($l as $v){

						$ids && $ids .= ',';	

						$ids .= $v['uid'];

					}

					$where && $where .= ' AND ';

					$where .= "id in ($ids)";

				}

				

			}

			if (!is_null($so_bank_number) && $so_bank_number !== '') {

				$where && $where .= ' AND ';

				if (strpos($so_bank_number, '*') !== false) {

					$where .= 'st_number LIKE \''.str_replace('*', '%', $so_bank_number).'\'';

				} else {

					$where .= 'st_number LIKE \'%'.$so_bank_number.'%\'';

				}

				$l = db::select('account', 'uid', $where);

				if ($l){

					foreach($l as $v){

						$ids && $ids .= ',';	

						$ids .= $v['uid'];

					}

					$where && $where .= ' AND ';

					$where .= "id in ($ids)";

				}

				

			}

			if (isset($so_mobile) && $so_mobile !== '') {

				$where && $where .= ' AND ';

				if (strpos($so_mobile, '*') !== false) {

					$where .= 'mobile LIKE \''.str_replace('*', '%', $so_mobile).'\'';

				} else {

					$where .= 'mobile LIKE \'%'.$so_mobile.'%\'';

				}

			}

			if (!is_null($so_regTime1) && $so_regTime1 !== '') {

				$where && $where .= ' AND ';

				$so_regTime1 = time::getGeneralTimestamp($so_regTime1);

				$where .= 'regTime>=\''.$so_regTime1.'\'';

			}

			if (!is_null($so_regTime2) && $so_regTime2 !== '') {

				$where && $where .= ' AND ';

				$so_regTime2 = time::getGeneralTimestamp($so_regTime2.' 23:59:59');

				$where .= 'regTime<=\''.$so_regTime2.'\'';

			}

			if (!is_null($so_status) && $so_status !== '') {

				$where && $where .= ' AND ';

				$where .= 'status=\''.intval($so_status).'\'';

			}

			$where1 = $where;

			$where = '';

			

			if ($where1 && $where) {

				$sql1 = db::sqlSelect('member', 'id', $where1);

				$where = preg_replace('/(`?\w+`?(?:=|<=|>=|<|>))/', 't1.$1', $where);

				$sql1 = db::sqlSelect("($sql1)|member_count:uid=id", 'id|', $where);

				if ($total = db::resultFirst('SELECT COUNT(*) FROM ('.$sql1.') t0')) {

					$sql1 .= ' ORDER BY id DESC LIMIT '.(($page - 1) * $pagesize).','.$pagesize;

					$list = db::select("($sql1)|member:id=id|member_count:uid=id", "|*|*", '', 't0.regTime DESC');

					

				}

			

			} elseif ($where1) {

				if ($total = db::dataCount('member', $where1)) {

					$sql = db::sqlSelect('member', '*', $where1, 'id DESC', $pagesize, $page);

					$list = db::select("($sql)|member_count:uid=id", "*|*", '', 't0.regTime DESC');

					

				}

			} elseif ($where) {

				if ($total = db::dataCount('member_count', $where)) {

					$sql = db::sqlSelect('member_count', '*', $where, 'uid', $pagesize, $page);

					$list = db::select("($sql)|member:id=uid", "*|*", '', 't0.id DESC');

				}

			} else {

				if ($total = db::dataCount('member')) {

					$sql = db::sqlSelect('member', '*', '', 'id DESC', $pagesize, $page);

					$list = db::select("($sql)|member_count:uid=id", "*|*", '', 't0.regTime DESC');

				}

			}

			$total && $multipage = multipage::parse($total, $pagesize, $page, $baseUrl.'&method='.$method.($urlVar ? '&'.$urlVar : '').'&type=so&page={page}', $pagestyle);

		}

	break;

	

	case 'list4':

		$id = $var->gp_id;

		$type = $var->gp_type;

		$sort = $var->gp_sort;

		if ($item = db::one('member', '*', "id='$id'")){

			//$c_item = db::one('member_count', '*', "uid='$id'");

			//$item = array_merge($item, $c_item);

			if ($type == 1){//上级

				if ($total = treeDB::parentCount_all('member', $id)){

					$list = treeDB::parents('member', $id, '*', '', $sort ?  '' : 'rank', $pagesize, $page);

				}

			}elseif ($type == 2){//下级

				if ($total = treeDB::childsCount_all('member', $id)){

					$list = treeDB::childs_all('member', $id, '*', '', $sort ?  '' : 'rank', $pagesize, $page);

				}

			}

			$total && $multipage = multipage::parse($total, $pagesize, $page, $baseUrl.'&method='.$method.'&id='.$id.'&type='.$type.'&sort='.$sort.'&page={page}', $pagestyle);

		}else admin::showMessage('没有找到该用户');

	break;

	case 'addMoney':

		if (form::hash()){

			$datas = form::get3('user', array('money', 'money'), array('credit', 'int'), 'remark');

			if ($datas['user'] && ($datas['money'] || $datas['credit']) && $datas['remark']){

				$m = new member_center($datas['user']);

				if ($m && $m->status){

					$rs = $m->addMoney($datas['money'], $datas['credit'], $datas['remark']);

					if ($rs === true){

						db::insert('admin_log', "user='$adminUser',info='增减用户{$m->m_id}金额{$datas[money]},积分{$datas[credit]}',ip='$ip',addTime='$time'");

						admin::showMessage('操作成功');

					}else admin::showMessage('操作失败');

				}else admin::showMessage('没找到用户或可能该用户已被禁用');

			}else admin::showMessage('操作失败，金额或积分，或');

		}

	break;

	case 'more':

		if (($id = $var->getInt('gp_id')) && ($member = db::one('member', '*', "id='$id'"))) {

			$memberCount = db::one('member_count', '*', "uid='$id'");

			$memberCountInfo = array();

			if (form::hash()){

				$datas = form::get3('name', 'card', 'mobile', 'alipay', 'cashNum', 'back_card', 'back_name', 'back_add', 'sitename', 'siteurl', 'qq', 'password', 'cashT');

				if ($datas['name'] != $member['name']){

					db::update('member', "name='$datas[name]'", "id='$member[id]'");

					db::insert('admin_log', "user='$adminUser',info='修改用户{$id}昵称，改前昵称{$member[name]}',ip='$ip',addTime='$time'");

				}
				if ($datas['cashNum'] != $member['cashNum']){

					db::update('member', "cashNum='$datas[cashNum]'", "id='$member[id]'");

					db::insert('admin_log', "user='$adminUser',info='修改用户{$id}独立提现次数，改前昵称{$member[cashNum]}',ip='$ip',addTime='$time'");

				}
				if ($datas['cashT'] != $member['cashT']){

					db::update('member', "cashT='$datas[cashT]'", "id='$member[id]'");

					db::insert('admin_log', "user='$adminUser',info='修改用户{$id}T+?，改前{$member[cashT]}',ip='$ip',addTime='$time'");

				}

				if ($datas['card'] != $member['card']){

					if (!db::exists('member', "card='$datas[card]' AND id!='$member[id]'")){

						db::update('member', "card='$datas[card]'", "id='$member[id]'");

						db::insert('admin_log', "user='$adminUser',info='修改用户{$id}身份证，改前身份证{$member[card]}',ip='$ip',addTime='$time'");

					} else admin::show_message('身份证已被使用');

				}

				if ($datas['mobile'] != $member['mobile']){

					if (!db::exists('member', "mobile='$datas[mobile]' AND id!='$member[id]'")){

						db::update('member', "mobile='$datas[mobile]'", "id='$member[id]'");

						db::insert('admin_log', "user='$adminUser',info='修改用户{$id}手机号，改前手机号{$member[mobile]}',ip='$ip',addTime='$time'");

					} else admin::show_message('该手机号已被使用');

				}

				if ($datas['alipay'] != $member['alipay']){

					db::update('member', "alipay='$datas[alipay]'", "id='$member[id]'");

					db::insert('admin_log', "user='$adminUser',info='修改用户{$id}支付宝，改前支付宝{$member[alipay]}',ip='$ip',addTime='$time'");

				}

				if ($datas['back_card'] != $member['back_card']){

					db::update('member', "back_card='$datas[back_card]'", "id='$member[id]'");

					db::insert('admin_log', "user='$adminUser',info='修改用户{$id}银行卡号，改前银行卡号{$member[back_card]}',ip='$ip',addTime='$time'");

				}

				if ($datas['back_name'] != $member['back_name']){

					db::update('member', "back_name='$datas[back_name]'", "id='$member[id]'");

					db::insert('admin_log', "user='$adminUser',info='修改用户{$id}收款银行，改前银行{$member[back_name]}',ip='$ip',addTime='$time'");

				}

				if ($datas['back_add'] != $member['back_add']){

					db::update('member', "back_add='$datas[back_add]'", "id='$member[id]'");

					db::insert('admin_log', "user='$adminUser',info='修改用户{$id}开户地址，改前开户地址{$member[back_add]}',ip='$ip',addTime='$time'");

				}

				if ($datas['qq'] != $member['qq']){

					db::update('member', "qq='$datas[qq]'", "id='$member[id]'");

					db::insert('admin_log', "user='$adminUser',info='修改用户{$id}开户地址，改前开户地址{$member[back_add]}',ip='$ip',addTime='$time'");

				}

				if ($datas['sitename'] != $member['sitename']){

					db::update('member', "sitename='$datas[sitename]'", "id='$member[id]'");

					db::insert('admin_log', "user='$adminUser',info='修改用户{$id}网站名称，改前网站名称{$member[sitename]}',ip='$ip',addTime='$time'");

				}

				if ($datas['siteurl'] != $member['siteurl']){

					db::update('member', "siteurl='$datas[siteurl]'", "id='$member[id]'");

					db::insert('admin_log', "user='$adminUser',info='修改用户{$id}网站地址，改前网站地址{$member[siteurl]}',ip='$ip',addTime='$time'");

				}

				if ($datas['password']){

					if (strlen($datas['password']) < 6 || strlen($datas['password']) > 16) admin::show_message('密码长度请设置在6-16位');

					$salt = $member['salt'];

					$password = qscms::saltPwd($salt, $datas['password']);

					db::update('member', "password='$password'", "id='$member[id]'");

					db::insert('admin_log', "user='$adminUser',info='修改用户{$id}密码，改前加密密码{$member[password]}',ip='$ip',addTime='$time'");

				}

				

				/*

				if ($datas['safePassword']){

					$salt = $member['salt'];

					$safePassword = qscms::saltPwd($salt, $datas['safePassword']);

					db::update('member', "safePassword='$safePassword'", "id='$member[id]'");

				}

				*/

				admin::show_message('修改成功', $baseUrl."&method=$method&id=$id");

			}

		} else admin::show_message('该用户不存在');

	break;

	

	case 'change':

		if (form::hash()) {

			$datas = form::get3(array('type', 'int'), 'val1', 'val2');

			$user1 = db::one('member', 'id,l,r,rank', "id='$datas[val1]'");

			$user2 = db::one('member', 'id,l,r,rank', "id='$datas[val2]'");

			if ($datas['type'] == 3) {

				if (!$datas['val2']) admin::showMessage('至少要输入用户B的ID');

				if (!$user2) admin::showMessage('者用户B不存在');

				if ($user1) {

					$uid1 = $user1['id'];

					$uid2 = $user2['id'];

				} else {

					$uid1 = 0;

					$uid2 = $user2['id'];

				}

			} else {

				if (!$datas['val1'] || !$datas['val2']) admin::showMessage('请输入用户A和用户B的ID');

				if (!$user1 || !$user2) admin::showMessage('用户A或者用户B不存在');

				$uid1 = $user1['id'];

				$uid2 = $user2['id'];

				unset($user1['id'], $user2['id']);

			}

			treeDB::update('member', $uid2, $uid1);

			admin::showMessage('修改成功');

		}

	break;

	case 'add1':

		//admin::showMessage('暂停使用');

		$isManager = 0;

		if (form::hash()) {

			$datas = form::get3('puid', 'username', 'email', 'mobile');

			$datas = qscms::trim($datas);

			if ($datas['mobile']) {

				if ($datas['email'] && !form::checkEmail($datas['email'])) admin::showMessage('邮箱格式错误');

				if (!form::checkMobilephone($datas['mobile'])) admin::showMessage('手机号格式错误');

				if ($datas['email'] && db::exists('member', "email='$datas[email]'")) admin::showMessage('用户邮箱已存在');

				if (db::exists('member', "mobile='$datas[mobile]'")) admin::showMessage('用户手机号已存在');

				$puid = 0;

				if ($datas['puid']) {

					//if (!form::checkEmail($datas['pemail'])) admin::showMessage('上级邮箱格式错误');

					$puid = db::one_one('member', 'id', "id='$datas[puid]'");

					if (!$puid) admin::showMessage('所填上级不存在');

				}

				$salt = qscms::salt();

				$user = array(

					'mobile'       => $datas['mobile'],

					'email'        => $datas['email'],

					'salt'         => $salt,

					'password'     => qscms::saltPwd($salt, $datas['email']),

					'regTime'      => time()

				);

				if ($id = treeDB::insert('member', $user, $puid, true)) {

					db::insert('admin_log', "user='$adminUser',info='添加用户{$id}',ip='$ip',addTime='$time'");

					admin::showMessage('添加成功');

				}

				else admin::showMessage('添加失败，请重试');

			} else admin::showMessage('最少要填手机号');

		}

	break;

}

?>