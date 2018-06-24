<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
!qscms::defineTrue('INSTALL') && qscms::gotoUrl('/install/');
?>