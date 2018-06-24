<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
$top_menu=array(
	'index'     => '基本短信设置',
	'index1'     => '阿里大于短信设置',
	'send'      => '发送短信',
	'list'      => '发送记录',
	//'mobile'    => '手机发送记录',
	//'vcode_log' => '验证码发送记录',
	//'pwd2_log'  => '操作码发送记录',
);
$top_menu_key = array_keys($top_menu);
($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
switch ($method) {
	case 'index':
		
	break;
	case 'send':
		$money = message::getMoney();
		if (!is_numeric($money)) {
			admin::show_message('不能发送短信，错误信息：'.language::get($money, 'message'));
		}
		$money = sprintf('%0.1f', $money);
		if (form::is_form_hash()) {
			extract($_POST);
			if (!empty($phones) && !empty($message)) {
				$rs = message::send($phones, $message);
				if (is_numeric($rs) && $rs >= 0) {
					admin::show_message('成功发送了'.$rs.'条', $baseUrl.'&method='.$method);
				} else {
					admin::show_message('发送失败，错误信息：'.language::get($rs, 'message'));
				}
			} else {
				$info = '参数错误';
			}
		}
	break;
	case 'list':
		admin::getList('vcode', '*', '', 'id desc');
		$list = $var->list;
		$multipage = $var->multipage;
	break;
	case 'mobile':
		admin::getList('log_sms', '*', '', 'timestamp desc');
		$list = $var->list;
		$multipage = $var->multipage;
	break;
	case 'vcode_log':
		admin::getList('log_vcode', '*', '', 'timestamp desc');
		$list = $var->list;
		$multipage = $var->multipage;
	break;
	case 'pwd2_log':
		admin::getList('log_safepwd', '*', '', 'timestamp desc');
		$list = $var->list;
		$multipage = $var->multipage;
	break;
}
?>