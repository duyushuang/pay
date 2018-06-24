<?php

 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
$serverInfo = PHP_OS.' / PHP v'.PHP_VERSION;
$serverInfo .= @ini_get('safe_mode') ? ' Safe Mode' : NULL;
$serverSoft = $_SERVER['SERVER_SOFTWARE'];
$dbVersion = db::resultFirst("SELECT VERSION()");
if(@ini_get('file_uploads')) {
	$fileUpload = ini_get('upload_max_filesize');
} else {
	$fileUpload = '当前服务器禁止上传';
}
$dbSize = 0;
$pre = qscms::getCfgPath('/global/db_table_pre');
$query = db::query("SHOW TABLE STATUS LIKE '$pre%'");
while($table = db::fetch($query)) {
	$dbSize += $table['Data_length'] + $table['Index_length'];
}
$dbSize = floor($dbSize / 1024 / 1024 * 100 + 0.5) / 100;
$dbSize .= ' M';


/*new*/
$stat = time::$todayStart;
$end = time::$todayEnd;
$stat1 = $stat - 86400;
$end1 = $end - 86400;


$wh = '1=1 ';
$allTotal = db::dataCount('pay_payment', $wh);//总订单
$payTotal = db::dataCount('pay_payment', $wh." AND status=1");//已付款总数量
$notTotal = $allTotal - $payTotal;//没付款的数量
$allMoney = round(db::one_one('pay_payment', 'SUM(money)', $wh), 2);//总金额
$payMoney = round(db::one_one('pay_payment', 'SUM(money)', $wh." AND status=1"), 2);//已付款总金额
$notMoney = $allMoney - $payMoney;//没付款的总金额
$memberMoney = round(db::one_one('pay_payment', 'SUM(money1)', $wh." AND status=1"), 2);//商户收款的总金额
?>