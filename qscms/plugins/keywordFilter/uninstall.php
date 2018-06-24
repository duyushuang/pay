<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
include_once($dir.D.'common.php');
//@unlink($manageFile);
pluginClearFiles($files);
b_nav::del2($pluginName, 'plugins');

return true;
?>