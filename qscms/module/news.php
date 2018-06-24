<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
!qscms::defineTrue('INSTALL') && qscms::gotoUrl('/install/');
$id = (int)$var->p1;
$item = array();
if ($id){
	$item = db::one('cms_news', '*', "id='$id'");
	db::update('cms_news', 'clicks=clicks+1', "id='$id'");
}
$item || qscms::showMessage('没有找到你访问的页面！');

?>