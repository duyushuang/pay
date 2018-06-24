<?php
include_once(dirname(__FILE__).'/../../index.php');
class sp_conf{
	// 商户在百度钱包的商户ID
	
	public static $MCHID,$KEY;
	const SP_NO = '1002666363';
	// 密钥文件路径，该文件中保存了商户的百度钱包合作密钥，该文件需要放在一个安全的地方，切勿让外人知晓或者外网访问
	const SP_KEY_FILE = 'sp.key';
	// 商户订单支付成功
	const SP_PAY_RESULT_SUCCESS = 1;
	// 商户订单等待支付
	const SP_PAY_RESULT_WAITING = 2;
	// 日志文件
	const LOG_FILE = 'sdk.log';
	
	// 百度钱包PC端即时到账支付接口URL（需要用户登录百度钱包）
	const BFB_PAY_DIRECT_LOGIN_URL = "https://www.baifubao.com/api/0/pay/0/direct/0";
	
	// 百度钱包PC端网银支付前置接口（从商家页面直接跳到银行的支付页面，不需要用户登录百度钱包）
	const BFB_PAY_DIRECT_NO_LOGIN_URL = "https://www.baifubao.com/api/0/pay/0/direct";
	// 百度钱包订单号查询支付结果接口URL
	const BFB_QUERY_ORDER_URL = "https://www.baifubao.com/api/0/query/0/pay_result_by_order_no";
	// 百度钱包订单号查询重试次数
	const BFB_QUERY_RETRY_TIME = 3;
	// 百度钱包支付成功
	const BFB_PAY_RESULT_SUCCESS = 1;
	// 百度钱包支付通知成功后的回执
	const BFB_NOTIFY_META = "<meta name=\"VIP_BFB_PAYMENT\" content=\"BAIFUBAO\">";
	
	// 签名校验算法
	const SIGN_METHOD_MD5 = 1;
	const SIGN_METHOD_SHA1 = 2;
	// 百度钱包即时到账接口服务ID
	const BFB_PAY_INTERFACE_SERVICE_ID = 1;
	// 百度钱包查询接口服务ID
	const BFB_QUERY_INTERFACE_SERVICE_ID = 11;
	// 百度钱包接口版本
	const BFB_INTERFACE_VERSION = 2;
	// 百度钱包接口字符编码
	const BFB_INTERFACE_ENCODING = 1;
	// 百度钱包接口返回格式：XML
	const BFB_INTERFACE_OUTPUT_FORMAT = 1;
	// 百度钱包接口货币单位：人民币
	const BFB_INTERFACE_CURRENTCY = 1;
}
sp_conf::$MCHID = cfg::get('bdpay', 'pid');
sp_conf::$KEY = cfg::get('bdpay', 'key');
?>