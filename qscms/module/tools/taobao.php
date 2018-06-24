<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
template::addPath($var->gp_ac, $var->gp_ac);
$var->setNotInVal('gp_op', array('mouseChange', '950', 'fullScreen'));

$var->tplName = $var->gp_op;
?>