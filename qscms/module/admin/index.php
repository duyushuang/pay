<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
include_once(MODULE_ROOT.'module'.D.'ini.php');
$tplDir = $var->v0;//$var->gp_tplDir;
$tplDir == 'index' && $tplDir = '';
$tplDir0 = $tplDir;
$tplDir = 'admin';
$tplRoot = qd(self::getCfgPath('/system/tplRoot'));
$cacheRoot = d(self::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/tpl'));
$tplRoot .= $tplDir.D;
$cacheRoot .= $tplDir.D;
template::initialize($tplRoot, $cacheRoot);
$var->gp_m = $var->p0;
if(IN_ADMIN === true){
	$pagestyle = qscms::getCfgPath('/system/newPageStyle');
	$var->pagestyle = $pagestyle;
	//$adminUrl = NOW_URL_PATH;
	$adminUrl = qscms::getUrl('/'.$var->gp_m.'/');
	$var->adminUrl = $adminUrl;
	$var->adminTplDir = $tplDir;
	$action = $var->getVal('gp_action');
	$operation = $var->getVal('gp_operation');
	if (!$action && !$operation) {
		$action = 'sys';
		$operation = 'index';
	}
	if ($action == 'sys' && $operation == 'index') $var->adminIndex = true;
	else $var->adminIndex = false;
	$method = $var->getVal('gp_method');
	$var->action    = $action;
	$var->operation = $operation;
	$var->method    = $method;
	if($action == 'logout'){
		admin::logout();
		qscms::gotoUrl($adminUrl, true);
	}
	$defTab = 'sys';
	$defSub = 'index';
	$menus  = array(
		'sys' => array(
			'name' => '系统设置', 
			'ico' => 'icon-settings',
			'sub'  => array(
				'index'      => array('name' => '管理中心首页', 'ico' => 'icon-home'),
				'nav'        => array('name' => '后台导航设置', 'ico' => 'icon-link'),
				'setting'    => array('name' => '系统设置', 'ico' => 'icon-wrench'),
				'founder'    => array('name' => '修改创始人', 'ico' => 'icon-pencil'),
				'admin'      => array('name' => '管理员管理', 'ico' => 'icon-users'),
				'cfg'        => array('name' => '配置管理', 'ico' => 'icon-puzzle'),
				'tpl'        => array('name' => '模板解析标记', 'ico' => 'fa fa-code'),
				'js'         => array('name' => 'JS文件库', 'ico' => 'fa fa-files-o'),
				'css'        => array('name' => 'CSS文件库', 'ico' => 'icon-docs'),
				'menu'       => array('name' => '菜单', 'hide' => true),
				'tkd'        => array('name' => '标题关键词描述', 'ico' => 'icon-note'),
				'disperse'   => array('name' => '分布式储存', 'ico' => 'icon-puzzle'),
				'task'       => array('name' => '系统任务', 'ico' => 'fa fa-tasks'),
				'memcache'   => 'Memcache',
				'ifUp'       => array('name' => '仿异步图片上传', 'hide' => true),
				'ifShow'       => array('name' => '仿异步图片显示', 'hide' => true)
			)
		)
	);
	$menus2 = b_nav::getCacheMenus();
	$menus += $menus2;
	if (!empty($var->admin['authority'])){
		foreach($menus as $k => $v){
			foreach($v['sub'] as $k1 => $v1){
				if (empty($var->admin['authority'][$k.'_'.$k1])){
					unset($menus[$k]['sub'][$k1]);
				}
			}
		}
		foreach($menus as $k => $v){
			if (empty($v['sub'])){
				unset($menus[$k]);
			}
		}
	}
	$var->menus = $menus;
	$var->menuAjax = false;//菜单异步
	$var->ajax = $var->menuAjax && $var->getBoolean('gp_ajax');
	unset($menus2);
	//$baseUrl = $adminUrl.($action || $operation?'?'.($action?'action='.$action:'').($operation?'&operation='.$operation:''):'');
	$baseUrl0 = qscms::getUrl('/'.$var->gp_m.'/'.($tplDir != 'admin' ? 'index/'.$tplDir.'/' : '').'?');
	$baseUrl = qscms::getUrl('/'.$var->gp_m.'/'.($tplDir != 'admin' ? 'index/'.$tplDir.'/' : '').($action || $operation?'?'.($action?'action='.$action:'').($operation?'&operation='.$operation:''):($tplDir0 ? '?tplDir='.$tplDir0 : '')));
	$var->baseUrl = $baseUrl;
	$var->baseUrl0 = $baseUrl0;
	$menuIcoDef = 'icon-th-large';
	$subMenuIcoDef = 'icon-puzzle-piece';
	if($action){
		$menu_name = $menus[$action]['name'];
		if (!empty($menus[$action]['ico'])) $menu_ico = $menus[$action]['ico'];
		else $menu_ico = $menuIcoDef;
		$var->menu_name = $menu_name;
		if($operation){
			$menu_sub_name = !empty($menus[$action]['sub'][$operation]) ? $menus[$action]['sub'][$operation]: '';
			if (is_array($menu_sub_name)) {
				if (!empty($menu_sub_name['ico'])) $menu_sub_ico = $menu_sub_name['ico'];
				else $menu_sub_ico = $subMenuIcoDef;
				$menu_sub_name = $menu_sub_name['name'];
			} else {
				$menu_sub_ico = $subMenuIcoDef;
			}
			$var->menu_sub_name = $menu_sub_name;
			$custom_menu_exists = db::exists('admin_custom_menu',array('action'=>$action,'operation'=>$operation));
			$var->custom_menu_exists = $custom_menu_exists;
			$m = $var->gp_m.'/'.$action.'_'.$operation;
			if(moduleExists($m)) {
				checkRead();
				include(m($m));
			} elseif(moduleExists($var->gp_m.'/'.$action)) {
				include(m($var->gp_m.'/'.$action));
			} else {
				throw new e_qscms('action:'.$action.'/'.$operation.' not exists');
				exit;
			}
			if(template::exists($action.'_'.$operation))include(template::load($action.'_'.$operation));
			elseif(template::exists($operation))include(template::load($operation));
			else include(template::load($action));
		} else {
			if(file_exists(SCRIPT_ROOT.'./'.$action.'.php'))include(SCRIPT_ROOT.'./'.$action.'.php');
			else {
				echo 'action:'.$action.' not exists';
				exit;
			}
			include(template::load($action));
		}
		
	} else {
		include(template::load('index'));
	}
	exit;
} else {
	//登录
	if(($error = admin::login())===true){
		qscms::refresh();
	}
	qscms::nocache();
	include(template::load('login'));
	exit;
}
?>