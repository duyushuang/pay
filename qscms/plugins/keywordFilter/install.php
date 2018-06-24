<?php
//如果安装成功返回true反之false
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
include_once($dir.D.'common.php');
pluginReplaceArgs($files, $args);
b_nav::add2('表单数据过滤', $pluginName, 'plugins');
return true;
?>