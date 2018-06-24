<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
qscms::nocache();
$add = $var->getVal('gp_add');
$del = $var->getVal('gp_del');
$method = $add? 'add' : ($del? 'del' : $method);
switch($method){
	case 'add':
		checkWrite();
		if(($args=url::uri($add)) && !empty($args['action']) && !empty($args['operation']) && ($menu = $menus[$args['action']]) && $menu['sub'][$args['operation']]){
			if(!db::exists('admin_custom_menu',array('action'=>$args['action'],'operation'=>$args['operation']))) {
				$menu_args = array('action'=>$args['action'],'operation'=>$args['operation']);
				$menu_args = qscms::addslashes($menu_args);
				$menu_id   = intval(db::insert('admin_custom_menu', $menu_args, true));
				echo '{status:true}';
			} else echo '{status:false,msg:"exists"}';
		}
		//if($menu=$menus[$args['action']]) {
			//(!$args['operation'] || !($title=$menu['sub'][$args['operation']])) && $title=$menu['name'];
			//$db->query("insert into ");
			//$menu_args=array('title'=>$title,'url'=>$admin_url.'?action='.$args['action'].($args['operation']?'&operation='.$args['operation']:''));
			
		//}
	break;
	case 'del':
		checkWrite();
		$del=(int)$del;
		db::del_id('admin_custom_menu',$del);
		echo '{status:true}';
	break;
	case 'get':
		foreach (db::select('admin_custom_menu', '*') as $line) {
			echo '<em id="custombar_'.$line['id'].'"><a onclick="mainFrame('.$line['id'].', this.href);doane(event)" href="'.$adminUrl.'?action='.$line['action'].'&operation='.$line['operation'].'" hidefocus="true">'.(!empty($menus[$line['action']]['sub'][$line['operation']]) ? $menus[$line['action']]['sub'][$line['operation']] : '').'</a><span onclick="del_admin_menu('.$line['id'].')" title="删除">&nbsp;&nbsp;</span></em>';
		}
	break;
}
exit;
?>