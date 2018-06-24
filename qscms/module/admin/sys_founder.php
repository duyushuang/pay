<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
$config = qscms::getConfig('global');
if(form::is_form_hash()) {
	extract(form::get3('sys_user','sys_pwd','sys_new_pwd'));
	!$sys_user && admin::show_message('创始人帐号不能为空');
	!$sys_pwd && admin::show_message('请输入创始人密码');
	if($sys_user != $config['sys_user'] || $sys_new_pwd != '') {
		if (qscms::saltPwdCheck($config['sys_salt'], $sys_pwd, $config['sys_pwd'])) {
			$config['sys_user']=$sys_user;
			if ($sys_new_pwd) {
				$salt = qscms::salt();
				$sys_pwd = qscms::saltPwd($salt, $sys_new_pwd);
				$config['sys_pwd'] = $sys_pwd;
				$config['sys_salt'] = $salt;
			}
			file::write(qd(qscms::getCfgPath('/system/cfgRoot')).'global.php','<?php !defined(\'IN_QSCMS\')&&exit(\'ERROR\');$config='.string::formatArray($config).';?>');
			qscms::unsetcookie('backAdmin');
			admin::show_message('修改成功！请重新登陆',$adminUrl,true);
		} else admin::show_message('创始人密码错误！');
	} else {
		admin::show_message('未做修改');
	}
}
$sys_user = $config['sys_user'];
?>