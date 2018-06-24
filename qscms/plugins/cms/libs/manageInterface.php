<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
plugins::call('{pluginType}', '{pluginName}', 'manage');
exit;
?>