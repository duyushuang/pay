<?php
/**
 * @author    刘江 <373718549@qq.com>
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */
define('USE_DB', false);
include(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'index.php');
qscms::ini();
qscms::nocache();
vcode2::show();
?>