<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
$args = array('pluginType' => $pluginType, 'pluginName' => $pluginName);
$manageFile = m(ADMIN_FOLDER.D.'plugins_'.$pluginName);
function pluginReplaceArgs($files, $args){
	if (is_array($files) && is_array($args)) {
		foreach ($files as $v) {
			$s = $v['s'];
			$d = $v['d'];
			if (file_exists($s)) {
				$code = file::read($s);
				$code = qscms::replaceVars($code, $args);//preg_replace('/\{(\w+)\}/e', '$args[\'$1\']', $code);
				file::write($d, $code);
			}
		}
		return true;
	}
	return false;
}
function pluginClearFiles($files){
	if (is_array($files)) {
		foreach ($files as $v) {
			@unlink($v['d']);
		}
	}
}
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
?>