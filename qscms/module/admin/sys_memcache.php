<?php


(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
$top_menu = array(
	'__tmp' => array('name' => cfg::getBoolean('sys', 'memcache') ? '关闭Memcache' : '开启Memcache', 'url' => qscms::getUrl('/'.$var->gp_m.'/?action=sys&operation=cfg&view=2&method=cfgListInfo'), 'hide' => false),
	'list'  => '服务器列表',
	'add'   => '添加服务器',
	'edit'  => array('name' => '编辑服务器', 'hide' => true)
);
if (memory::$isMemcache) {
	$top_menu['listInfo'] = '缓存服务器信息列表';
}
$top_menu_key = array_keys($top_menu);
($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[1];
switch ($method) {
	case 'list':
		if (form::hash()) {
			extract(form::get3('del', 'ids', 'sort'));
			if ($del) {
				admin::show_message('删除了'.mem::delServer($del).'个Memcache服务器', $baseUrl.'&method=list');
			}
			if ($sort) {
				mem::setSort($ids, $sort);
				admin::show_message('设置排序成功', $baseUrl.'&method=list');
			}
		}
		$list = mem::getServerAll(false);
	break;
	case 'add':
		$ip = '127.0.0.1';
		$port = '11211';
		$weight = '1';
		$rs = mem::addServerToForm();
		if ($rs !== false) {
			if ($rs === true) admin::show_message('添加成功', $baseUrl.'&method=list');
			else admin::show_message($rs);
		}
	break;
	case 'edit':
		$id = $var->getInt('gp_id');
		($item = mem::getServer($id)) || admin::show_message('该服务器不存在');
		$ip     = $item['ip'];
		$port   = $item['port'];
		$weight = $item['weight'];
		$rs = mem::editServerToForm($id);
		if ($rs !== false) {
			if ($rs === true) admin::show_message('修改成功', $baseUrl.'&method=list');
			else admin::show_message($rs);
		}
	break;
	case 'listInfo':
		!memory::$isMemcache && admin::show_message('还没启用Memcache');
		$infoNames = array(
			'pid'                   => '进程PID',
			'uptime'	            => '服务器运行时间',
			'time'                  => '服务器当前时间',
			'version'               => 'Memcache版本',
			'pointer_size'          => '操作系统',
			'curr_items'            => '当前存储数据量',
			'total_items'           => '服务器启动以后数据储存总量',
			'bytes'                 => '当前数据占用内存',
			'curr_connections'      => '当前连接数',
			'total_connections'     => '服务器后曾经打开过的连接数',
			'connection_structures' => '服务器分配的连接构造数',
			'cmd_get'               => '获取数据总次数',
			'cmd_set'               => '写入数据总次数',
			'bytes_read'            => '总读取字节数',
			'bytes_written'         => '总发送字节数',
			'limit_maxbytes'        => '可用内存大小',
			'threads'               => '当前线程数'
		);
		$infoKeys = array_keys($infoNames);
		$infoValues = array_values($infoNames);
		$listInfo = memory::$mem->getExtendedStats();
	break;
}
?>