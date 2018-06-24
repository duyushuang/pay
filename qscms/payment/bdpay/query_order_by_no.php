<?php date_default_timezone_set('PRC');

/**
 * 这个是调用bdpay_sdk里通过百度钱包订单号查询接口查询订单信息的DEMO
 *
 */
if (!defined("bdpay_sdk_ROOT"))
{
	define("bdpay_sdk_ROOT", dirname(__FILE__) . DIRECTORY_SEPARATOR);
}

require_once(bdpay_sdk_ROOT . 'bdpay_sdk.php');
require_once(bdpay_sdk_ROOT . 'bdpay_pay.cfg.php');

$bdpay_sdk = new bdpay_sdk();

$order_no = $_POST['order_no'];

/*
 * 字符编码转换，百度钱包默认的编码是GBK，商户网页的编码如果不是，请转码。涉及到中文的字段请参见接口文档
 * 步骤：
 * 1. URL转码
 * 2. 字符编码转码，转成GBK
 * 
 * $good_name = iconv("UTF-8", "GBK", urldecode($good_name));
 * $good_desc = iconv("UTF-8", "GBK", urldecode($good_desc));
 * 
 */

// 用于测试的商户请求支付接口的表单参数，具体的表单参数各项的定义和取值参见接口文档
		
$content = $bdpay_sdk->query_baifubao_pay_result_by_order_no($order_no);

if(false === $content){
	$bdpay_sdk->log('create the url for baifubao query interface failed');
}
else {
	$bdpay_sdk->log('create the url for baifubao query interface success');
	echo "查询成功\n";
	echo $content;
}

?>