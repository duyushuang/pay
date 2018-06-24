<?php
template::addPath("vxeditor_tabs","vxeditor_tabs");

$tab = $var->p1;
$tabs = array("tab1",'tab2','tab3','tab4','tab6','tab7','tab8','tab9','tab10','tab11');
if(!in_array($tab,$tabs)) exit('error');
$var->tplName =$tab;
?>