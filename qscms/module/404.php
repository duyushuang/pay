<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
!qscms::defineTrue('INSTALL') && qscms::gotoUrl('/install/');
$var->setNotInVal('gp_op', array('index'));
$op = $var->gp_op;
$var->tplName = $op;
switch ($op) {
	case 'index':
		error::_404();
	break;
}
?>