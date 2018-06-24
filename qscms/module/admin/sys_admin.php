<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
$top_menu=array(
	'index' => '管理员列表',
	'add'   => '添加管理员',
	'edit'  => array('name' => '编辑管理员', 'hide' => true)
);
if($edit = $var->getInt('gp_edit'))$method='edit';
if ($view = $var->getInt('gp_view'))$method = 'view';
$top_menu_key = array_keys($top_menu);
($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
$tbName = 'admins';
switch($method) {
	case 'index':
		admin::getList($tbName);
		$list = $var->list;
		$multipage = $var->multipage;
	break;
	case 'add':
		if (form::is_form_hash()) {
			$datas = form::get3('username', 'password', 'keys');
			$datas && extract($datas);
			if ($username && $password && $keys) {
				if ($username == $config['sys_user'] || db::exists($tbName, array('username' => $username))) {
					admin::show_message('对不起，该帐号已经存在了');
				}
				$keys2 = array();
				foreach ($keys as $k => $v) {
					$val = 0;
					foreach ($v as $v2) {
						$v2 = (int)$v2;
						$val |= 0xFFFFFFFF & 1 << $v2 - 1;
					}
					$keys2[$k] = $val;
				}
				
				$salt = qscms::salt();
				$password = qscms::saltPwd($salt, $password);
				if ($aid = db::insert($tbName, array(
					'username'           => $username,
					'salt'               => $salt,
					'password'           => $password,
					'regTimestamp'       => $timestamp,
					'lastLoginTimestamp' => 0,
					'loginTimes'         => 0
				), true)) {
					$values = '';
					foreach ($keys2 as $k => $v) {
						$values && $values .= ',';
						$values .= '(\''.$aid.'\', \''.$k.'\', \''.$v.'\')';
					}
					db::query("insert into {$pre}admin_authority values$values");
					admin::show_message('添加成功', $base_url.'&method=index');
				} else {
					admin::show_message('添加失败！！');
				}
			} else {
				admin::show_message('参数错误！！');
			}
		}
		$username = '';
	break;
	case 'edit':
		if (form::is_form_hash()) {
			$config = qscms::getConfig('global');
			$aid = $edit;
			$datas = form::get2('username', 'password', 'keys');
			$datas && $datas = qscms::filterHtml($datas);
			$datas && extract($datas);
			if ($username && $keys) {
				if ($username == $config['sys_user'] || db::exists($tbName, "username='$username' and id<>'$edit'")) {
					admin::show_message('对不起，该帐号已经存在了');
				}
				$keys2 = array();
				foreach ($keys as $k => $v) {
					$val = 0;
					foreach ($v as $v2) {
						$v2 = (int)$v2;
						$val |= 0xFFFFFFFF & 1 << $v2 - 1;
					}
					$keys2[$k] = $val;
				}
				$args = array(
					'username' => $username
				);
				if ($password) {
					$salt     = db::one_one($tbName, 'salt', "id='$aid'");
					$password = qscms::saltPwd($salt, $password);
					$args['password'] = $password;
				}
				if (db::update($tbName, $args, "id='$aid'")) {
					db::del_key('admin_authority', 'aid', $aid);
					$values = '';
					foreach ($keys2 as $k => $v) {
						$values && $values .= ',';
						$values .= '(\''.$aid.'\', \''.$k.'\', \''.$v.'\')';
					}
					db::query("insert into ".db::table('admin_authority')." values$values");
					admin::show_message('修改成功', $baseUrl.'&method=index');
				} else {
					admin::show_message('修改失败！！');
				}
			} else {
				admin::show_message('参数错误！！');
			}
		}
		$keys = array();
		if ($username = db::one_one($tbName, 'username', "id='$edit'")) {
			if ($line = db::get_list('admin_authority', '`key`,value', "aid='$edit'", '', 0)) {
				foreach ($line as $v) {
					$val = (int)$v['value'];
					for ($i = 0; $i < 32; $i++) {
						if ($val & 1 << $i) $keys[$v['key']][$i] = $i + 1;
					}
				}
			}
		}
	break;
}
?>