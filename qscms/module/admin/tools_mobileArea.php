<?php


(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
$top_menu=array(
	'cardA' => '选项卡A',
	'cardB' => '选项卡B'
);
$top_menu_key = array_keys($top_menu);
($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
$datas = array();
foreach (db::select('mobile_area', '*', '', 'pid,id') as $v) {
	if ($v['pid']) $datas[$v['pid']]['sub'][] = array('name' => $v['name'], 'code' => $v['code']);
	else $datas[$v['id']] = array('name' => $v['name'], 'sub' => array());
}
$datas = array_values($datas);
echo file::write(s('js/marea.js'), 'var areaList = '.string::json_encode($datas)) ? 'OK' : 'ERROR';
exit;
?>