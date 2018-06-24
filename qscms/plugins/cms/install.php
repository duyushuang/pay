<?php
//如果安装成功返回true反之false
!defined('IN_QS_PLUGIN') && IN_QS_PLUGIN !== true && exit('error');
include_once($dir.D.'common.php');
pluginReplaceArgs($files, $args);
b_nav::add2('多功能文章模型', $pluginName, 'plugins');
$pre = PRE;
db::querys("
CREATE TABLE `{$pre}cms_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` int unsigned NOT NULL DEFAULT '0',
  `name` varchar(32),
  `ename` varchar(32),
  `menuId` int unsigned not null,
  `menuName` varchar(32),
  `parentMenuId` int unsigned not null,
  `parentMenuName` varchar(32),
  `addTime` int(10) unsigned NOT NULL,
  `lastEditTime` int unsigned not null default 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `{$pre}cms_model_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(10) unsigned NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `name` varchar(32) DEFAULT NULL,
  `fieldName` varchar(128) DEFAULT NULL,
  `fieldType` varchar(128) DEFAULT NULL,
  `htmlName` varchar(32) DEFAULT NULL,
  `htmlType` varchar(32) DEFAULT NULL,
  `htmlWidth` varchar(6) DEFAULT NULL,
  `htmlHeight` varchar(6) DEFAULT NULL,
  `imageWidth` varchar(6) DEFAULT NULL,
  `imageHeight` varchar(6) DEFAULT NULL,
  `htmlListValue` text,
  `htmlDefaultValue` varchar(32) DEFAULT NULL,
  `htmlIsReg` tinyint unsigned NOT NULL DEFAULT '0',
  `htmlRegStr` varchar(255) DEFAULT NULL,
  `tip` varchar(255) DEFAULT NULL,
  `bListShow` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `{$pre}cms_model_index` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(10) unsigned NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `name` varchar(32) DEFAULT NULL,
  `indexName` varchar(128) DEFAULT NULL,
  `indexFields` varchar(128) DEFAULT NULL,
  `indexType` tinyint UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
");
return true;
?>