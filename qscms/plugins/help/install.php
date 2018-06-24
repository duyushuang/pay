<?php
//如果安装成功返回true反之false
!defined('IN_QS_PLUGIN') && IN_QS_PLUGIN !== true && exit('error');
include_once($dir.D.'common.php');
if (b_nav::exists($pluginName, 'plugins')) {
	pluginMessage('很抱歉，系统检查到，plugins菜单下面已经存在了'.$pluginName);
}
foreach ($p_tables as $v) {
	db::tableExists($v) && pluginMessage('很抱歉，数据库表：“'.db::table($v).'”已经存在了！');
}

if (file_exists($destinationDir)) {
	pluginMessage('很抱歉，路径：'.$destinationDir.'已经存在！');
}
if (!file::copyFolder($sourceDir, $destinationDir, 0, 0)) pluginMessage('安装失败，复制文件失败，请检查时候可写！');
pluginReplaceArgs($files, $args);
b_nav::add2('帮助手册', $pluginName, 'plugins');
db::querys("CREATE TABLE `{$pre}manual_help` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(80) DEFAULT NULL,
  `content` text,
  `addTime` int(10) unsigned NOT NULL,
  `upTime` int(10) unsigned NOT NULL,
  `l` int(10) unsigned NOT NULL,
  `r` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `i1` (`l`,`r`,`id`),
  KEY `i2` (`r`,`l`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
return true;
?>