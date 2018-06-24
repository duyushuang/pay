<?php
!defined('IN_QS_PLUGIN') && IN_QS_PLUGIN !== true && exit('error');
include_once(PLUGIN_ROOT.'common.func.php');
$args = array('pluginType' => $pluginType, 'pluginName' => $pluginName);
$manageFile = m(ADMIN_FOLDER.D.'plugins_'.$pluginName);
$files = array(
	array(
		's' => $dir.D.'plugin.php',
		'd' => $dir.D.'plugin.inc.php'
	),
	array(
		's' => $dir.D.'libs'.D.'manageInterface.php',
		'd' => $manageFile
	)
);
$p_tables = array('manual_help');
$destinationDir = d('./help/');
$sourceDir      = $dir.D.'help'.D;
$pre = PRE;
?>