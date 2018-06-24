<?php


(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
$top_menu=array(
	'upgrade' => '程序升级',
	'getHex'  => '文件转换二进制',
	'getHexXml' => '转换为二进制XML'
);
$top_menu_key = array_keys($top_menu);
($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
loadFunc('upgrade');
$__menuName = $top_menu[$method];
switch ($method) {
	case 'upgrade':
		if (form::hash()) {
			$rs = upgrade('file');
			if ($rs === true) {
				admin::show_message('升级成功');
			} else admin::show_message('升级失败：'.$rs);
		}
	break;
	case 'getHex':
		$data = '';
		if (form::hash()) {
			$data = getFileHex('file');
		}
	break;
	case 'getHexXml':
		$data = '';
		if (form::hash()) {
			if ($saveAs = $var->getNoHtml('gp_saveAs')) {
				$data = getFileHexXml('file', $saveAs);
			} else admin::show_message('请输入保存路径');
		}
	break;
}
?>