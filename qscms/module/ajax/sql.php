<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
define('IN', 'AJAX');
define('AJAX', 'SQL');
include_once(m(ADMIN_FOLDER.'/module/ini'));
$rs = array('type' => 'sql ajax');
$action = $var->gp_action;
switch ($action) {
	case 'data':
		if (qscms::defineTrue('IN_ADMIN')) {
			if (form::is_form_hash(false)) {
				if (checkWrite('tools_database')) {
					$datas = form::get2('name', 'sql');
					if ($datas['name'] && $datas['sql']) {
						$datas['timestamp'] = time::$timestamp;
						$rs['id'] = db::insert('sql_log', $datas, true);
						$rs['status'] = true;
					} else {
						$rs['err'] = '参数错误！';
					}
				} else $rs['err'] = '对不起，您没有权限写入本项';
			} else $rs['err'] = '非法操作！';
		} else {
			$rs['err'] = '请先登陆';
		}
		echo string::json_encode($rs);
	break;
}
exit;
?>