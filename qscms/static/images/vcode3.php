<?php
define('USE_DB', false);
//define('CONTENT_TYPE', 'binary');
include(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'index.php');
qscms::ini();
qscms::nocache();
vcode3::show();
?>