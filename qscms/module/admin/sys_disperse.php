<?php

 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
$top_menu=array(
	'list'  => '储存器列表',
	'add'   => '添加储存器',
	'edit'  => array('name' => '编辑储存器', 'hide' => true),
	'fms'   => array('name' => '文件管理', 'hide' => true)
);
$top_menu_key = array_keys($top_menu);
($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
switch ($method) {
	case 'list':
		if (form::hash()) {
			extract(form::get3('del', 'ids', 'sort'));
			if (!empty($del)) {
				admin::show_message('删除了'.disperse_obj::del($ids).'个储存器', $baseUrl.'&method=list');
			}
			disperse_obj::setSort($ids, $sort);
			admin::show_message('设置排序成功', $baseUrl.'&method=list');
		}
		$list = array();
		if ($total = disperse_obj::total()) {
			$list = disperse_obj::getList($pagesize, $page);
		}
	break;
	case 'add':
		if (form::is_form_hash()) {
			$rs = disperse_obj::add($_POST);
			if ($rs === true) {
				admin::show_message('添加成功', $baseUrl.'&method=list');
			} else {
				admin::show_message('添加失败：'.$rs);
			}
		}
	break;
	case 'edit':
		$id = $var->getInt('gp_id');
		if ($datas = disperse_obj::get($id)) {
			if (form::is_form_hash()) {
				$rs = disperse_obj::edit($_POST, $id);
				if ($rs === true) {
					admin::show_message('修改成功', $referer);
				} else {
					admin::show_message('修改失败');
				}
			}
		} else admin::show_message('该储存器不存在');
	break;
	case 'fms':
		//echo winsock::open('http://www.my.com/upload/qscmsFMS.php', 'postData=123&abc=456');exit;
		//echo winsock::uploadFile('http://www.my.com/upload/qscmsFMS.php?key=qscms_fms_key&path=/test/&file=/a/b/c/1.jpg', 'qscmsUploadData', d('./a.jpg'));
		//exit;
		$id = $var->getInt('gp_id');
		if ($datas = disperse_obj::get($id)) {
			//$c = disperse_obj::getObj($id);
			//print_r($c->getFDList());
			//exit;
			//echo $c->uploadFile('/a/b/c/1.jpg', d('./a.jpg'))?1:2;
			//exit;
		} else admin::show_message('该储存器不存在');
	break;
}
?>