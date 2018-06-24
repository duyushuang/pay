<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
function ajax_return($status = true, $msg = ''){
	$rn = array(
		'status' => $status
	);
	if ($msg) {
		if (is_array($msg)) {
			$rn += $msg;
		} else {
			$rn['msg'] = $msg;
		}
	} else {
		$rn['msg'] = '';
	}
	return $rn;
}
function ajax_return_true($msg = ''){
	return ajax_return(true, $msg);
}
function ajax_return_false($msg = ''){
	return ajax_return(false, $msg);
}
?>