<?php
/* *
 * 配置文件
 */
include_once(dirname(__FILE__).'/../../index.php');
//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//商户号
$tenpay_config['mch']	= cfg::get('qqpay', 'MCH');

//安全检验码
$tenpay_config['key']	= cfg::get('qqpay', 'KEY');

?>