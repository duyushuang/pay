<?php


(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
$top_menu=array(
	'index' => '网站配置',
);
$adminUser = $var->admin['username'];
$ip = qscms::ipint();
$time = time();
$top_menu_key = array_keys($top_menu);
($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
switch ($method) {
	case 'index':
	break;
}
?>