<?php
!defined('IN_QS_PLUGIN') && IN_QS_PLUGIN !== true && exit('error');
$pid = $var->getInt('gp_pid');
$id = $var->getInt('gp_id');
$top_menu=array(
	'list'        => '信息列表',
	'addTitle'    => array('name' => '添加标题', 'url' => $baseUrl.'&method=addTitle&pid='.$pid, 'hide' => false),
	'editContent' => array('name' => '编辑/查看文章', 'hide' => true)
);
$top_menu_key = array_keys($top_menu);
($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
$tb = 'manual_help';
switch ($method) {
	case 'list':
		$pid  = isset($pid) ? db::exists($tb, array('id' => $pid)) ? intval($pid) : 0 : 0;
		if (form::is_form_hash()) {
			extract(form::get3('del'));
			if ($del) {
				treeDB::delete($tb, $del);
				admin::show_message('删除成功', $baseUrl.'&method='.$method.'&pid='.$pid);
			}
		}
		if ($total = treeDB::childsCount($tb, $pid)) {
			$list = treeDB::childs($tb, $pid, 'id,title,l,r,upTime', '', '', $pagesize, $page);
			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.'&method='.$method.'&pid='.$pid.'&page={page}', $pagestyle);
		}
	break;
	case 'addTitle':
		$pid = isset($pid) ? db::exists($tb, array('id' => $pid)) ? intval($pid) : 0 : 0;
		$pinfo = $pid > 0 ? db::one($tb, '*', "id='$pid'") : array();
		
		if (form::is_form_hash()) {
			$datas = form::get3('title');
			if ($datas['title']) {
				$datas += array(
					'addTime' => $timestamp,
					'upTime'  => $timestamp
				);
				if (treeDB::insert($tb, $datas, $pid) !== false) {
					admin::show_message('添加成功！', $baseUrl.'&method=list&pid='.$pid);
				} else {
					admin::show_message('添加失败，请重试！');
				}
			} else {
				admin::show_message('参数错误！');
			}
		}
	break;
	case 'editContent':
		if ($pinfo = db::one($tb, '*', "id='$id'")) {
			if (form::is_form_hash()) {
				$datas = form::get2('content');
				$datas += array('upTime' => $timestamp);
				if (db::update($tb, $datas, "id='$id'")) {
					admin::show_message('更新成功！', $baseUrl.'&method=list='.$pid);
				} else {
					admin::show_message('更新失败！');
				}
			}
			$content = $pinfo['content'];
			$update = true;
		} else admin::show_message('很抱歉，要编辑的文章不存在！');
	break;
}
include(template::load('admin_manage'));
?>