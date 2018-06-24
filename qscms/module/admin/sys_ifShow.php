<?php
/**
 * @author    溺水的狗 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
$imgId = $var->getInt('gp_imgId');
$u = new upload();
if ($item = $u->get($imgId)) {
	if (form::isMatch('/^image\/(jpg|jpeg|png|gif)$/', $item['type'])) {
		header('Content-Type: '.$item['type']);
		echo file::read($item['file']);
	}
}
exit;
?>