<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */
/* 

 */
(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
$top_menu=array(
	'pay'     => '通用配置',
	'bl'      => '商户套餐',
	'addbl'   => '添加套餐',
	'editbl'  => array('name' => '添加套餐', 'hide' => true),
	'wxpay'   => '微信配置',
	'alipay'  => '支付宝配置',
	'qqpay'   => 'QQ钱包配置',
	'bdpay'   => '百度网银配置',
	
);
$top_menu_key = array_keys($top_menu);
($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
switch ($method) {
	case 'pay':
	break;
	case 'bl':
		if (form::hash()) {//删除模块
			extract(form::get3('del', 'sort', 'ids'));
			if ($del) {
				admin::show_message('请联系技术执行该操作');
				//admin::show_message('删除了'.db::del_ids('pay_bl', $del).'个商户套餐', $baseUrl.'&method=list');
			} elseif ($sort) {
				$count = form::arrayEqual($ids, $sort);
				if ($count) {
					for ($i = 0; $i < $count; $i++) {
						$id = $ids[$i];
						$st = $sort[$i];
						db::update('pay_bl', "`sort`='$st'", "id='$id'");
					}
					admin::show_message('更新排序成功', $baseUrl.'&method=list');
				}
			}
		}
		$list = db::select('pay_bl', '*', '', 'sort');
	break;
	case 'addbl':
	case 'editbl':
		$id = $var->gp_id;
		if ($id){
			extract(db::one('pay_bl', '*', "id='$id'"));	
			if (!$id) admin::show_message('没有找到该商户套餐');	
		}
		if (form::hash()){
			$datas = form::get3('name', 'wxpay', 'alipay', 'qqpay', 'bdpay', 'money', 'isOff');
			if ($id){
				db::update('pay_bl', $datas, "id='$id'");
				admin::show_message('修改成功');	
			}else{
				db::insert('pay_bl', $datas);
				admin::show_message('添加成功');	
			}
		}
	break;
	case 'wxpay':
	break;
	case 'alipay':
	break;
	case 'qqpay':
	break;
	case 'bdpay':
	break;
}
?>