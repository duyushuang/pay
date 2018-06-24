<?php
	header("Content-type: text/html; charset=utf-8");
	require_once("qsclass.php");
	
	
	/*--------------------------以下是商户配置---------------------------*/
	//异步反馈接口页面路径
	$notify_url = "https://www.fengbaopay.com/api/notify_url.php";
	
	//页面跳转页面路径
	$return_url = "https://www.fengbaopay.com/api/return_url.php";
	//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
	//notify_url 和 return_url 每个地址不能超过600字符
	
	//商户网站订单号
	$out_trade_no = $_GET['out_trade_no'];
	//支付方式
	$type = $_GET['type'];
	//商品名称
	$subject = $_GET['subject'];
	//付款金额
	$total_fee = $_GET['total_fee'];
	//站点名称
	$site_name = $_GET['site_name'];
	$parameter = array(
		"type" => $type,
		"notify_url"	=> $notify_url,
		"return_url"	=> $return_url,
		"out_trade_no"	=> $out_trade_no,
		"subject"	=> $subject,
		"total_fee"	=> $total_fee,
		"site_name"	=> $site_name
	);
	$qsclass = new qsclass();
	$html = $qsclass->buildRequestForm($parameter);
	echo $html;
?>