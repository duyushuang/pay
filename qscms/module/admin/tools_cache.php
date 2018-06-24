<?php

 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
set_time_limit(0);
function __areaCache($aid) {
	$list = area::get_cities($aid);
	foreach ($list as $k => $v) {
		if ($v['sub_num'] > 0) __areaCache($k);
	}
}
if(form::is_form_hash()){
	if($type = $var->gp_type){
		checkWrite();
		foreach($type as $v){
			switch($v){
				case 'data':
					
					//file::delFolder(d('./cache/html/tpl'), 0, false);
					cache::upCache();//更新系统总缓存
					cfg::updateCfgAll();//更新配置缓存
					plugins::upCache();//更新插件缓存
					b_nav::upCache();//更新后台导航缓存
					task::upCache();//更新系统任务缓存数据
					tkd::updateCache();//更新TKD缓存
					cacheData::deleteCache();//删除模版缓存数据
					echo 'complate';
				break;
				case 'cache':
					cache::upCache();//更新系统总缓存
					echo 'cache complate';
				break;
				case 'cfg':
					cfg::updateCfgAll();//更新配置缓存
					echo 'cfg complate';
				break;
				case 'plugin':
					plugins::upCache();//更新插件缓存
					echo 'plugin complate';
				break;
				case 'nav':
					b_nav::upCache();//更新后台导航缓存
					echo 'nav complate';
				break;
				case 'task':
					task::upCache();//更新系统任务缓存数据
					echo 'task complate';
				break;
				case 'tkd':
					tkd::updateCache();//更新TKD缓存
					echo 'tkd complate';
				break;
				case 'tplCache':
					cacheData::deleteCache();//删除模版缓存数据
					echo 'tplCache complate';
				break;
				case 'tpl':
					file::delFolder(d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/tpl')), 0, false);
					file::delFolder(d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/multipage')), 0, false);
					echo 'tpl complate';
				break;
				case 'ptpl':
					file::delFolder(d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/program')), 0, false);
					echo 'ptpl complate';
				break;
				case 'thumb':
					file::delFolder(d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/thumb')), 0, false);
					echo 'thumb complate';
				break;
				case 'area':
					file::delFolder(d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/data').'area/'), 0, false);
					__areaCache(0);
					$dir = d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/data').'text/');
					if ($f = @opendir($dir)) {
						while ($r = readdir($f)) {
							if (substr($r, 0, 11) == 'json_cities') {
								@unlink($dir.$r);
							}
						}
						@closedir($f);
					}
					echo 'area complate';
				break;
				case 'memcache':
					memory::flush();
					echo 'memory complate';
				break;
			}
		}
		exit;
	}
}
?>