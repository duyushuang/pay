<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
$adminId   = 0;
admin::loginCookie();
$admin     = qscms::v('_G')->getVal('admin');
define('IN_ADMIN', !empty($admin) ? true : false);
//print_r($admin);exit;
define('IN_FOUNDER', !empty($admin) && $admin['type'] == 'founder' ? true : false);

loadFunc('admin');
?>