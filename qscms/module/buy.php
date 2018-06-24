<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
!qscms::defineTrue('INSTALL') && qscms::gotoUrl('/install/');
if (!$member) qscms::gotoUrl('/users/login');
$id = (int)$var->p1;
if ($item = db::one('pay_bl', '*', "id='$id' AND isOff=1")){
	if ($item['money']<0){
		
	}else qscms::message('操作失败');	
}else qscms::message('操作失败');	
echo $id;exit; 
?>