<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
$id = $var->gp_id;
if($id){
	$item = db::one('byself_msg','*',"id='$id'");
	if (!$item) error::_404();
	/*
	if (db::exists('byself_msg', "id='$id'")){
		qscms::gotoUrl('/vxin_art?id='.$id, true);
	}else error::_404();
	*/
}
?>