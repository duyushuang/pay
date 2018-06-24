<?php

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
$config = qscms::getConfig('global');
$old_sys_admin_folder = $config['sys_admin_folder'];
$oldPre = $config['db_table_pre'];
extract($config);
if(form::is_form_hash()){
	foreach(form::get('auth_key','sys_admin_folder','db_host','db_port','db_name','db_user','db_pwd','db_table_pre') as $k=>$v) {
		$config[$k]=$v;
	}
	if($config['db_table_pre'] != $oldPre){
		$query = db::query("SHOW TABLES LIKE '".str_replace('_', '\_', $oldPre)."%'");
		$p_len = strlen($oldPre);
		$pre2 = $config['db_table_pre'];
		while($line = db::fetch($query,MYSQL_NUM)){
			$table_name = $line[0];
			$sql = "ALTER TABLE `$table_name` RENAME `$pre2".substr($table_name, $p_len)."`";
			db::query($sql);
		}
	}
	$goto = '';
	$parent = false;
	/*if ($old_sys_admin_folder != $config['sys_admin_folder']) {
		if (@rename(qd('./module/'.$old_sys_admin_folder), qd('./module/'.$config['sys_admin_folder']))) {
			$goto = qscms::getUrl('/'.$config['sys_admin_folder'].'/');
			$parent = true;
		}
	}*/
	file::write(qd(qscms::getCfgPath('/system/cfgRoot')).'global.php', '<?php !defined(\'IN_QSCMS\')&&exit(\'ERROR\');$config='.string::formatArray($config).';?>');
	admin::show_message('系统设置修改成功！', $goto, $parent);
}
?>