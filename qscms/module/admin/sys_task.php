<?php

 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');

//die('禁用');
$top_menu=array(
	'list'  => '任务列表',
	'add'   => '添加任务',
	'edit'  => array('name' => '编辑任务', 'hide' => true)
);
if ($status = $var->getInt('gp_status')) {
	task::changeStatus($status);
	qscms::gotoUrl($referer, true);
}
if ($run = $var->getInt('gp_run')) {
	task::run($run);
	admin::show_message('执行完毕', $referer);
}
$top_menu_key = array_keys($top_menu);
($method && in_array($method, $top_menu_key)) || $method = $top_menu_key[0];
switch ($method) {
	case 'list':
		if (form::is_form_hash()) {
			extract(form::get3('del', 'ids', 'sort'));
			if (!empty($del)) {
				admin::show_message('删除了'.task::del($del).'个任务', $baseUrl.'&method=list');
			} else {
				task::setSort($ids, $sort);
				admin::show_message('设置排序成功', $baseUrl.'&method=list');
			}
		}
		$list = array();
		if ($total = task::total()) {
			$list = task::getList($pagesize, $page);
			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.'&method=list&page={page}', $pagestyle);
		}
	break;
	case 'add':
		if (form::is_form_hash()) {
			$rs = task::addToPost();
			if ($rs === true) {
				admin::show_message('添加成功', $baseUrl.'&method=list');
			} else admin::show_message('添加失败：'.$rs);
		}
	break;
	case 'edit':
		$id = $var->getInt('gp_id');
		if ($arr = task::get($id)) {
			if (form::is_form_hash()) {
				$rs = task::editToPost($id);
				if ($rs === true) {
					admin::show_message('编辑成功', $baseUrl.'&method=list');
				} else admin::show_message('编辑失败：'.$rs);
			}
			extract(qscms::filterArray($arr, array('name', 'type', 'time', 'timeType', 'filename', 'code')));
			if ($timeType == 1) {
				$time = time::getGeneralTimestamp($time);
			} elseif ($timeType == 0) {
				$__t = time::daytime($time);
				if ($__t['hour']) $time = $__t['hour'].':'.$__t['minute'].':'.$__t['second'];
				elseif ($__t['minute']) $time = $__t['minute'].':'.$__t['second'];
			} elseif ($timeType == 3) {
				$__t = time::daytime($time);
				$time = 'w:'.($__t['day'] + 1).' '.$__t['hour'].':'.$__t['minute'].':'.$__t['second'];
			} elseif ($timeType == 4) {
				$__t = time::daytime($time);
				$time = 'm:'.($__t['day'] + 1).' '.$__t['hour'].':'.$__t['minute'].':'.$__t['second'];
			}
			$update = true;
		} else admin::show_message('不存在该任务');
	break;
}
?>