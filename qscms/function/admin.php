<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
function checkRead($key = ''){
	$var = qscms::v('_G');
	$key || $key = $var->action.'_'.$var->operation;
	if ( qscms::defineTrue('IN_ADMIN') ) {
		if ( qscms::defineTrue('IN_FOUNDER') ) {
			return true;
		} else {
			$admin = $var->admin;
			if (!empty($admin['authority'][$key]) && $admin['authority'][$key] & 1) return true;
			else {
				if (!defined('IN') || !in_array(IN, array('AJAX'))) {
					admin::show_message('对不起，您没有权限查看该页数据！');
				}
				return false;
			}
		}
	}
}
function checkWrite($key = ''){
	//global $admin, $action, $operation;
	$var = qscms::v('_G');
	$key || $key = $var->action.'_'.$var->operation;
	if (IN_ADMIN === true) {
		if (IN_FOUNDER === true) {
			return true;
		} else {
			//$key = $action.'_'.$operation;
			$admin = $var->admin;
			if (!empty($admin['authority'][$key]) && $admin['authority'][$key] & 2) return true;
			else {
				if (!defined('IN') || !in_array(IN, array('AJAX'))) {
					admin::show_message('对不起，您没有权限修改该页数据！');
				}
				return false;
			}
		}
	}
}
function call_del_club_ico(&$line){
	$file = qscms::getImgDir('club', $line['ico']);
	@unlink($file);
	$line = false;
}
?>