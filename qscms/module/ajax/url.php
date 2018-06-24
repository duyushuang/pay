<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
loadFunc('ajax');
loadFunc('url');
set_time_limit(0);
$rs = ajax_return_false('未知错误');
switch ($var->gp_op) {
	case 'short':
		$url = trim($var->gp_url);
		$alias = trim($var->gp_alias);
		$surl = '';
		$addRs = url_addOne($url, $alias, $surl);
		if ($addRs === true) {
			$rs = ajax_return_true(array('surl' => $surl));
		} else $rs = ajax_return_false($addRs);
	break;
	case 'long':
		$url = trim($var->gp_url);
		$longUrl = '';
		$gRs = url_getLongUrl($url, $longUrl);
		if ($gRs === true) {
			$rs = ajax_return_true(array('url' => $longUrl));
		} else $rs = ajax_return_false($gRs);
	break;
}
echo string::json_encode($rs);
exit;
?>