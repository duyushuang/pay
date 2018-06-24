<?php
//if($_SERVER['HTTP_REFERER'])
include dirname(__FILE__).DIRECTORY_SEPARATOR.'../../index.php';
qscms::run(false);
$img = new securimage();
$img->show();
?>