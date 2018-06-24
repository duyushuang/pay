<?php
!defined('IN_QS_PLUGIN') && IN_QS_PLUGIN !== true && exit('error');
include_once($dir.D.'common.php');
//@unlink($manageFile);
if (!file::delFolder($destinationDir, 0)) pluginMessage('删除文件失败，请重试！');
pluginClearFiles($files);
b_nav::del2($pluginName, 'plugins');
db::querys("DROP TABLE `{$pre}manual_help`");
return true;
?>