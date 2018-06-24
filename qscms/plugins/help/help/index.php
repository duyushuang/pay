<?php
include(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'qscms'.DIRECTORY_SEPARATOR.'index.php');
qscms::run(false);
$var = qscms::v('_G');
template::initialize(d('./help/templates/'), d('./help/cache/'));
$as = array('main', 'html', 'article');
$action = $var->gp_action;
(isset($action) && in_array($action, $as)) || $action = $as[0];
switch ($action) {
	case 'main':
		$tpl = 'main';
	break;
	case 'html':
		$name = $var->gp_name;
		if ($name && template::exists($name)) {
			$tpl = $name;
		} else error::_404();
	break;
	case 'article':
		$tpl = 'article';
		$id = $var->getInt('gp_id');
		if (($id = intval($id)) && $item = db::one('manual_help', '*', "id='$id'")) {
			
		} else error::_404();
	break;
	default:
		error::_404();
	break;
}
include(template::load($tpl));
?>