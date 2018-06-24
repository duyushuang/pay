<?php
include_once(dirname(__FILE__).'/../../../index.php');
$config = array (	
		//应用ID,您的APPID。
		'app_id' => '2017040206530916',

		//商户私钥，您的原始格式RSA私钥
		'merchant_private_key' =>'-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQCpfnRKKeO0TqWVHlcatWiGfFZDLV3lCgXci8Tz/XYEHOC5jIQZ
P+TGk/8cFkZo+T2hJFfS1QrjiYyvdRpBd5Xzne5Flo1pySM34bz+WOvYCdDN+Xye
MRe3OlPjiWwss8P301XvqHzwFr8Dyet+tk19ndeNgybtUTNjSYlehldNTQIDAQAB
AoGAHs1SSOAP94aLZOwcnEf3dOlHq/GkrKkDo67q3gRj3B97X3z/zK7l3oiqxenu
ZclVv+Eg7Lm2vt8SaBh56wfWgiF0J3kf78O95xzx0Jx1uXJti46x0F5nFXHs+TyU
eMMMh25JDFML2tl9rWzAXXHvX5nNZOEi2+RhPQCVxztIS4ECQQDg7ltyQA76nBhd
Ombc1Yw2rWdfbaZ92OqhfC2SRceclPqBNgjBhgZ5u1pu8w3afg8JK+bM0bXhkwCa
cdOZO0KxAkEAwOfSIFwftCPByH+4lJVquwsGuyFuFmPQ6tBVrfIiYb3q/W0C4tVt
IYnPkD0MFfT4re0855qfL2BjVSRbfIgDXQJBALOy+e/u6YiiCH0C2Yb4PIq6Qmnk
6iyEjf7xfF7tzKl2BCQSjTA+6RF78qXNHHZAW7bSEhP0PsC2drbs1UYIKCECQQCl
YBgMFe44CM+Ai7454z50y6chli9Ckp/wLlrFnOdM0/w49tOak03Tisme5jcOO81V
jiTAiRYfD4sY2upzgEchAkBeHeXHxTpcA/FutOXlNE5X8xdC20ZPP3in3GeysKO5
Xfuy+thsr96KzwJ6hZSBj/T3y7KswA/cuKTPK3t79k8q
-----END RSA PRIVATE KEY-----',
		
		//'merchant_private_key' => './aop/rsa_private_key.pem',
		//异步通知地址
		'notify_url' => WEB_URL."/qscms/payment/alipay_qrcode/alipay/notify_url.php",
		
		//同步跳转
		'return_url' => WEB_URL."/qscms/payment/alipay_qrcode/alipay/return_url.php",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCpfnRKKeO0TqWVHlcatWiGfFZDLV3lCgXci8Tz/XYEHOC5jIQZP+TGk/8cFkZo+T2hJFfS1QrjiYyvdRpBd5Xzne5Flo1pySM34bz+WOvYCdDN+XyeMRe3OlPjiWwss8P301XvqHzwFr8Dyet+tk19ndeNgybtUTNjSYlehldNTQIDAQAB",
		//'alipay_public_key' => './aop/rsa_public_key.pem',
		
	
);