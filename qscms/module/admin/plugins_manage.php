<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */

(!defined('IN_QSCMS') || IN_QSCMS !== true) && exit('error');
(!defined('IN_ADMIN') || IN_ADMIN !== true) && die('error');
$top_menu=array(
	'all'         => '全部插件',
	'installed'   => '已安装插件',
	'uninstalled' => '未安装插件'/*,
	'upload'      => '上传插件'*/
);
$top_menu_key = array_keys($top_menu);
( ($method = $var->gp_method) && in_array($method, $top_menu_key) ) || $method = $top_menu_key[0];
if ($install = $var->gp_install) {
	//安装
	checkWrite();
	if (plugins::install($install)) {
		admin::show_message('安装成功', $var->referer);
	} else {
		admin::show_message('安装失败', $var->referer);
	}
} elseif ($uninstall = $var->gp_uninstall) {
	//卸载
	checkWrite();
	if (plugins::uninstall($uninstall)) {
		admin::show_message('卸载成功', $var->referer);
	} else {
		admin::show_message('卸载失败', $var->referer);
	}
} elseif ($suspend = $var->gp_suspend) {
	//挂起 暂停
	checkWrite();
	if (plugins::suspend($suspend)) {
		admin::show_message('挂起成功', $var->referer);
	} else {
		admin::show_message('挂起失败', $var->referer);
	}
} elseif ($resume = $var->gp_resume) {
	//恢复挂起
	checkWrite();
	if (plugins::resume($resume)) {
		admin::show_message('恢复成功', $var->referer);
	} else {
		admin::show_message('恢复失败', $var->referer);
	}
}
switch ($method) {
	case 'all':
		if (form::is_form_hash()) {
			if ($del = $_POST['del']) {
				plugins::delPlugins($del);
				admin::show_message('删除完毕', $var->referer);
			}
		}
		$list = plugins::getPlugins();
	break;
	case 'installed':
		$list = plugins::getPlugins('installed');
	break;
	case 'uninstalled':
		$list = plugins::getPlugins('uninstalled');
	break;
}
?>