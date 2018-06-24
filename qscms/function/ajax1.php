<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
function ajax_return($error = 0, $message = ''){
	$rn = array(
		'error' => $error
	);
	if ($message) {
		if (is_array($message)) {
			$rn += $message;
		} else {
			$rn['message'] = $message;
		}
	} else {
		$rn['message'] = '';
	}
	return $rn;
}
function ajax_return_true($message = ''){
	return ajax_return(0, $message);
}
function ajax_return_false($message = ''){
	return ajax_return(1, $message);
}
?>