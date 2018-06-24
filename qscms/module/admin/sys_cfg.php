<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
if ( ($view = $var->getInt('gp_view')) ) {
	if ($cate = cfg::getCate($view)) {
		$tab = 'cfg';
		$cateListUrl = $baseUrl.'&method=cateList';
		$baseUrl.='&view='.$view;
	}
}
isset($tab) || $tab = 'cate';
switch ($tab) {
	case 'cate':
		$top_menu=array(
			'cateList' => '配置分类列表',
			'addCate'   => '添加配置分类',
			'editCate'  => array('name' => '编辑配置分类', 'isHide' => true)
		);
		if($edit = $var->getInt('gp_edit'))$method='edit';
		$top_menu_key = array_keys($top_menu);
		($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
		switch($method){
			case 'cateList':
				if (form::is_form_hash()) {
					extract(form::get3('del'));
					if ($del) {
						cfg::delCate($del);
						admin::show_message('删除完毕', $baseUrl.'&method=cateList');
					}
				}
				if ($total = cfg::getCateTotal()) {
					$list = cfg::getCates($page, $pagesize);
					$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.'&page={page}', $pagestyle);
				}
			break;
			case 'addCate':
				$name = $remark = '';
				if (form::is_form_hash()) {
					extract(form::get3('name', 'remark'));
					$rs = cfg::addCate($name, $remark);
					if (is_numeric($rs) && $rs > 0) {
						admin::show_message('添加成功', $baseUrl.'&method=index');
					} else admin::show_message($rs);
				}
			break;
			case 'editCate':
				$id = $var->getInt('gp_id');
				if (form::is_form_hash()) {
					extract(form::get3('name', 'remark'));
					$rs = cfg::editCate($name, $remark, $id);
					if ($rs === true) {
						admin::show_message('修改成功', $baseUrl.'&method=index');
					} else admin::show_message($rs);
				}
				if ($item = cfg::getCate($id)) {
					$update = true;
					extract(qscms::filterArray($item, array('name', 'remark')));
				} else admin::show_message('很抱歉，不存在该分类');
			break;
		}
	break;
	case 'cfg':
		if (($cfgId = $var->getInt('gp_ifUpload'))) {
			if ($cfg = cfg::getCfg($cfgId)) {
				$rs = cfg::formCall('uploadImgForm', array(array('cfgId', 'int')));
				$name = $cfg['name'];
				include(template::load('ifUpload'));
				exit;
			}
		}
		$top_menu=array(
			'back' => array(
				'name' => '返回配置列表',
				'url'  => $cateListUrl,
				'hide' => false
			),
			'cfgList'     => '配置列表',
			'cfgListInfo' => '配置信息',
			'addCfg'      => '添加配置',
			'editCfg'     => array('name' => '编辑配置', 'isHide' => true)
		);
		if($edit=$var->getInt('gp_edit'))$method='edit';
		$top_menu_key = array_keys($top_menu);
		($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[1];
		switch($method){
			case 'cfgList':
				if (form::is_form_hash()) {
					extract(form::get3('ids', 'del', 'sort'));
					if ($del) {
						$delCount = cfg::delCfg($del);
						admin::show_message('成功删除'.$delCount.'条配置信息', $baseUrl.'&method=cfgList');
					} elseif ($sort) {
						cfg::setSort($ids, $sort);
						admin::show_message('设置排序完毕', $baseUrl.'&method=cfgList');
					}
				}
				$list = array();
				if ($total = cfg::getCfgTotal($cate['id'])) {
					$list = cfg::getCfgs($cate['id'], $page, $pagesize);
					$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.'&method=cfgList&page={page}', $pagestyle);
				}
			break;
			case 'cfgListInfo':
				if (form::is_form_hash()) {
					if (cfg::setCfg($cate['id'], $_POST)) {
						admin::show_message('设置成功', $baseUrl.'&method=cfgListInfo');
					} else admin::show_message('设置失败！');
				}
				$list = cfg::getCfgs($cate['id'], 0, 0);
			break;
			case 'addCfg':
				$name = $type = $attach = $run = $remark = '';
				if (form::is_form_hash()) {
					extract(form::get2('name', 'type', 'attach', 'run', 'remark'));
					$rs = cfg::addCfg($cate['id'], $name, $type, $attach, $remark, $run);
					if (is_numeric($rs) && $rs > 0) {
						admin::show_message('添加成功', $baseUrl.'&method=index');
					} else admin::show_message($rs);
				}
			break;
			case 'editCfg':
				$id = $var->getInt('gp_id');
				$name = $type = $attach = $run = $remark = '';
				if (form::is_form_hash()) {
					extract(form::get2('name', 'type', 'attach', 'run', 'remark'));
					$rs = cfg::editCfg($id, $name, $type, $attach, $remark, $run);
					if ($rs === true) {
						admin::show_message('修改成功', $baseUrl.'&method=index');
					} else admin::show_message($rs);
				}
				if ($item = cfg::getCfg($id)) {
					$update = true;
					extract(qscms::filterArray($item, array('name', 'type', 'attach', 'run', 'remark')));
				} else admin::show_message('很抱歉，不存在该分类');
			break;
		}
	break;
}
?>