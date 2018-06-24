<?php

 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
$top_menu = array(
	'itemList' => '项目列表',
	'itemAdd'  => '添加项目',
	'itemEdit' => array('name' => '编辑项目', 'hide' => true),
	'datas'    => '数据管理'
);
$top_menu_key = array_keys($top_menu);
($method && in_array($method, $top_menu_key)) || $method = $top_menu_key[0];
switch ($method) {
	case 'itemList':
		if (form::is_form_hash()) {
			extract(form::get3('ids', 'del', 'sort'));
			if ($del) {
				admin::show_message('删除了'.tkd::delete($del).'个项目', $baseUrl.'&method=itemList');
			} else {
				if (tkd::setSort($ids, $sort)) {
					admin::show_message('设置排序成功', $baseUrl.'&method=itemList');
				} else {
					admin::show_message('设置排序失败');
				}
			}
		}
		$list = array();
		if ($total = tkd::total()) {
			$list = tkd::getList($pagesize, $page);
			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.'&method='.$method.'&page={page}', $pagestyle);
		}
	break;
	case 'itemAdd':
		if (form::is_form_hash()) {
			if (tkd::addItem($_POST)) {
				admin::show_message('添加成功', $baseUrl.'&method=itemList');
			} else {
				admin::show_message('添加失败');
			}
		}
	break;
	case 'itemEdit':
		if ($item = tkd::get($var->getInt('gp_id'))) {
			if (form::is_form_hash()) {
				if (tkd::editItem($_POST, $var->getInt('gp_id'))) {
					admin::show_message('编辑成功', $baseUrl.'&method=itemList');
				} else {
					admin::show_message('编辑失败');
				}
			}
			extract(qscms::filterArray($item, array('name', 'marker', 'whereList')));
			$whereList && $whereList = unserialize($whereList);
			$update = true;
		} else admin::show_message('该项目不存在');
	break;
	case 'datas':
		if (form::is_form_hash()) {
			if (tkd::setData($_POST)) {
				admin::show_message('设置成功');
			} else {
				admin::show_message('设置失败');
			}
		}
		$list = tkd::getList(0, 0, 'id,name,marker,title,title1,keywords,keywords1,description,description1');
	break;
}
?>