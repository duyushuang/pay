<?php

 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
($cache_file_sys_css_lib=cache::get_data('sys_css_lib'))&&(include($cache_file_sys_css_lib));
!empty($cache_sys_css_lib) || $cache_sys_css_lib = array();
if(form::is_form_hash()){
	$_POST = qscms::stripslashes($_POST);
	$name_ = $_POST['name'];
	$url_  = $_POST['url'];
	$b_    = $_POST['b'];
	$count = count($name_);
	$configs=$arr=$css_lib=array();
	for($i=0;$i<$count;$i++){
		$name=$name_[$i];
		$url=$url_[$i];
		$b=$b_[$i];
		if($name!=='' && $url!=='') {
			$arr['name']=$name;
			$arr['url']=$url;
			$arr['b']=$b;
			$configs[]=$arr;
			$css_lib[$name]=$url;
		}
	}
	//print_r($css_lib);
	//print_r($configs);exit;
	if($css_lib){
		$file    = qd('./class/css.php');
		$phpcode = file::read($file);
		$phpcode = preg_replace('/(\/\*config start\*\/).*?(\/\*config end\*\/)/s','$1private static $lib_list='.string::formatArray($css_lib).';$2',$phpcode);
		file::write($file,$phpcode);
	}
	cache::write_data('sys_css_lib','<?php $cache_sys_css_lib='.string::formatArray($configs).';');
	//qscms::refresh();
	admin::showMessage('设置成功');
}
?>