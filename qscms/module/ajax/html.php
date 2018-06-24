<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
loadFunc('ajax');
if (!preg_match('/^[a-zA-Z0-9_]+$/', $var->v0)) exit('fuck you!');
if (template::exists($var->v0)) {
	include(template::load($var->v0));
	$html = qscms::ob_get_contents();
	qscms::ob_clean();
	$rs = ajax_return_true(array('html' => $html));
} else $rs = ajax_return_false();
echo string::json_encode($rs);
exit;
?>