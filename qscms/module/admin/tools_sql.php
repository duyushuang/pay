<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
$top_menu=array(
	'index'     => '已创建SQL',
	'create'  => '创建SQL'
);
$top_menu_key = array_keys($top_menu);
($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
($edit  = $var->getInt('gp_edit')) && $method='create';
if($del = $var->getInt('gp_del')){
	db::del_id('sql',$del);
	qscms::gotoUrl($baseUrl,true);
}
($create = $var->getInt('gp_create')) && $method='create2';
switch($method){
	case 'index':
		$list = db::select('sql', '*', '', 'id');
	break;
	case 'create':
		if(form::is_form_hash()){
			if(!empty($_POST['sql']) && !empty($_POST['name']) && ($sql = $_POST['sql']) && ($name = $_POST['name'])){
				$args = array();
				if(preg_match_all("/\{([a-zA-Z0-9_]+)\}/", $sql, $matches, PREG_SET_ORDER)){
					foreach($matches as $v){
						if($v[1] != 'pre'){
							if(empty($_POST[$v[1]])) admin::show_message("参数填写不完整");
							$args[$v[1]] = $_POST[$v[1]];
						}
					}
				}
				($args && $args = qscms::addslashes(serialize($args))) || $args = '';
				if(!empty($_POST['is_edit']) && $_POST['is_edit']) db::update('sql', array('name' => $name, 'sql' => $sql, 'args' => $args),"id='$edit'");
				else db::insert('sql', array('name' => $name, 'sql' => $sql, 'args' => $args));
				admin::show_message((!empty($_POST['is_edit']) && $_POST['is_edit'] ? '编辑' : '添加').'成功', $baseUrl);
			} else admin::show_message('参数填写不完整');
		}
		$item = array(
			'name' => '',
			'sql'  => ''
		);
		$args = array();
		$edit && ($item = db::one('sql', '*', "id='$edit'")) && $item['args'] && $args = unserialize($item['args']);
		$item || $edit = 0;
	break;
	case 'create2':
		$item = db::one('sql', 'name,args', "id='$create'");
		if($item['args']) $args = unserialize($item['args']);
		else $args = array();
		if(form::is_form_hash()){
			if($args){
				foreach($args as $k=>$v){
					if(empty($_POST[$k])) admin::show_message("参数填写不完整");
				}
			}
			$sql = db::one_one('sql', '`sql`', "id='$create'");
			$sql = str_replace('{pre}', PRE, $sql);
			$args && $sql = preg_replace('/\{([a-zA-Z0-9_]+)\}/e','$_POST[\'$1\']', $sql);
			$sp = qscms::trimExplode(';', $sql);
			foreach ($sp as $v){
				if ($v) {
					db::queryUnbuffered($v);
				}
			}
			admin::show_message("执行：$sql 完毕！",$baseUrl);
		}
	break;
}
?>