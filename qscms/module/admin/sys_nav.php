<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
$view = $var->gp_view;
$tab = $var->getVal('gp_tab');
if ($view) {
	$tab = 'sub';
	$cateName = b_nav::getMenuName($view);
}
$tab || $tab = 'index';
switch ($tab){
	case 'index':
		$top_menu=array(
			'index' => '菜单列表',
			'add'   => '添加菜单',
			'edit'  => array('name' => '编辑分类', 'hide' => true)
		);
		if($edit = $var->getInt('gp_edit'))$method = 'edit';
		$top_menu_key = array_keys($top_menu);
		($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
		switch($method){
			case 'index':
				if (form::is_form_hash()) {
					extract($_POST);
					if ($del) {
						b_nav::del($del);
						admin::show_message('删除完毕', $baseUrl);
					}
					b_nav::setSort($ids, $sort);
					admin::show_message('设置排序完成', $baseUrl);
				}
				$list = b_nav::getMenus();
			break;
			case 'add':
				$f_name = $f_ename = '';
				if (form::is_form_hash()) {
					$f_name  = $var->getVal('gp_f_name');
					$f_ename = $var->getVal('gp_f_ename');
					if ($f_name && $f_ename) {
						if (b_nav::add($f_name, $f_ename, '', 0)) {
							admin::show_message('添加成功', $baseUrl);
						} else {echo db::error();exit;
							admin::show_message('添加失败，请重试！');
						}
					} else {
						$info = '参数错误';
					}
				}
			break;
			case 'edit':
				$f_name = $f_ename = '';
				if (form::is_form_hash()) {
					extract($_POST);
					if (b_nav::edit($f_name, $f_ename, '', $edit)) {
						admin::show_message('修改成功', $baseUrl);
					} else {
						admin::show_message('修改失败');
					}
				}
				if ($menu = b_nav::getMenu($edit)) {
					$f_name  = $menu['name'];
					$f_ename = $menu['ename'];
				}
			break;
		}
	break;
	case 'sub':
		$baseUrl .= '&view='.$view;
		$top_menu=array(
			'index' => $cateName.'菜单列表',
			'add'   => '添加'.$cateName.'菜单',
			'edit'  => array('name' => '编辑'.$cateName.'客服', 'isHide' => true)
		);
		$edit = $var->getInt('gp_edit');
		if($edit) $method = 'edit';
		$top_menu_key = array_keys($top_menu);
		($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
		switch($method){
			case 'index':
				if (form::is_form_hash()) {
					extract($_POST);
					if ($del) {
						b_nav::del($del);
						admin::show_message('删除完毕', $baseUrl);
					}
					b_nav::setSort($ids, $sort);
					admin::show_message('设置排序完成', $baseUrl);
				}
				$list = b_nav::getSubMenus($view);
			break;
			case 'add':
				$f_create = NULL;
				$f_name = $f_ename = $f_curl = '';
				if (form::is_form_hash()) {
					if ($datas = form::get2('f_name', 'f_ename', 'f_curl', 'f_create')) {
						extract($datas);
					}
					if ($f_name && ($f_ename || $f_curl)) {
						if (b_nav::add($f_name, $f_ename, $f_curl, $view)) {
							if ($f_create) {
								if ($pEName = b_nav::getEname($view)) {
									/**
									 * 创建初始文件
									 */
									$fileKey = $pEName.'_'.$f_ename;
									$phpFile = m(ADMIN_FOLDER.'/'.$fileKey);
									$tplFile = qd(self::getCfgPath('/system/tplRoot').ADMIN_FOLDER.'/'.$fileKey.'.htm');
									$phpCode = '<?php


(!defined(\'IN_ADMIN\') || IN_ADMIN!==true) && die(\'error\');
$top_menu=array(
	\'cardA\' => \'选项卡A\',
	\'cardB\' => \'选项卡B\'
);
$top_menu_key = array_keys($top_menu);
($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
switch ($method) {
	case \'cardA\':
	break;
	case \'cardB\';
	break;
}
?>';
									$tplCode = '{sub h}{sub f}';
									file::write($phpFile, $phpCode);
									file::write($tplFile, $tplCode);
								}
							}
							admin::show_message('添加成功', $baseUrl);
						} else {
							admin::show_message('添加失败，请重试！');
						}
					} else {
						$info = '参数错误';
					}
				}
				$f_create = 0;
			break;
			case 'edit':
				$update = true;
				if (form::is_form_hash()) {
					if ($datas = form::get2('f_name', 'f_ename', 'f_curl')) {
						extract($datas);
					}
					if ($f_name && ($f_ename || $f_curl)) {
						if (b_nav::edit($f_name, $f_ename, $f_curl, $edit)) {
							admin::show_message('修改成功', $baseUrl);
						} else {
							admin::show_message('修改失败');
						}
					} else {
						$info = '参数错误';
					}
				}
				if ($menu = b_nav::getMenu($edit)) {
					$f_name  = $menu['name'];
					$f_ename = $menu['ename'];
					$f_curl  = $menu['curl'];
				}
			break;
		}
	break;
}
?>