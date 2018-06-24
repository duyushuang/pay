<?php
!defined('IN_QS_PLUGIN') && IN_QS_PLUGIN !== true && exit('error');
include_once($dir.D.'common.php');
//@unlink($manageFile);
pluginClearFiles($files);
b_nav::del2($pluginName, 'plugins');
$query = db::query("select * from {$pre}cms_model");
while ($line = db::fetch($query)) {
	$parentMenuEname = b_nav::getEname($line['parentMenuId']);
	$menuEname       = b_nav::getEname($line['menuId']);
	b_nav::del2($menuEname, $parentMenuEname);
	db::querys("
	drop table if exists `{$pre}cms_{$line[ename]}_cate`;
	drop table if exists `{$pre}cms_{$line[ename]}`
	");
	@unlink(qd('./module/'.ADMIN_FOLDER.'/'.$parentMenuEname.'_'.$menuEname.'.php'));
	@unlink(d(qscms::getCfgPath('/system/tplRoot').'admin/'.$parentMenuEname.'_'.$menuEname.'.htm'));
}
db::querys("
DROP TABLE if exists `{$pre}cms_model`;
DROP TABLE if exists {$pre}cms_model_fields;
DROP TABLE if exists {$pre}cms_model_index");
return true;
?>