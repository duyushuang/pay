<?php
include(dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'index.php');
loadFunc('global');
$thisRoot = dirname(__FILE__);
$path = u($thisRoot);
$path = substr($path, strlen($weburl2));
template::initialize('./'.$path.'/tpl/', './'.$path.'/cache/');
if (!empty($type)) {
	$menus = array(
		'pic'   => '图片插入',
		'album' => '专辑插入',
		'group' => '小组插入'
	);
	$tps = array_keys($menus);
	(!empty($type) && in_array($type, $tps)) || $type = $tps[0];
	switch ($type) {
		case 'pic':
			$list = array();
			if (form::is_form_hash()) {
				extract(form::get3('ids'));
				$list = getPicsPath($ids);
			}
		break;
		case 'album':
			$list = array();
			if (form::is_form_hash()) {
				extract(form::get3('ids'));
				$list = getAlbumsPath($ids);
			}
		break;
	}
} else exit;
include(template::load('insert'));
?>