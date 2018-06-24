<?php
define('IS_SHELL', true);
if (isset($argv) && is_array($argv) && count($argv) == 2) {
	list($path, $url) = $argv;
	$_GET['path'] = $url;
	include(dirname($path).DIRECTORY_SEPARATOR.'index.php');
	
}
?>