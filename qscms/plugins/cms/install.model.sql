drop table if exists `{pre}cms_{modelName}_cate`;
CREATE TABLE `{pre}cms_{modelName}_cate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(32) DEFAULT NULL,
  `ename` varchar(32) DEFAULT NULL,
  `addTime` int(10) unsigned NOT NULL,
  `editTime` int(10) unsigned NOT NULL DEFAULT '0',
  `total` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
insert into `{pre}cms_{modelName}_cate`(`name`,`ename`,`addTime`,`editTime`) values('默认分类','default','{timestamp}', '{timestamp}');
drop table if exists `{pre}cms_{modelName}`;
CREATE TABLE `{pre}cms_{modelName}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `cid` int(10) unsigned NOT NULL,
  `addTime` int(10) unsigned NOT NULL,
  `editTime` int(10) unsigned NOT NULL DEFAULT '0',
  `addIp` int(10) unsigned NOT NULL,
  `clicks` int(10) unsigned NOT NULL DEFAULT '0',
  `top` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8