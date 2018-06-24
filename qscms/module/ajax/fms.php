<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
include_once(m(ADMIN_FOLDER.'/module/ini'));
loadFunc('ajax');
$rs = ajax_return_false();
set_time_limit(0);
if (qscms::defineTrue('IN_ADMIN')) {
	switch ($var->v0) {
		case 'delete':
			$id = $var->getInt('gp_id');
			if ($id && $var->gp_type && $var->gp_file) {
				$obj = disperse_obj::getObj($id);
				if ($obj) {
					$s = false;
					switch ($var->gp_type) {
						case 'dir':
							$s = $obj->delDir($var->gp_file);
						break;
						case 'file':
							$s = $obj->delFile($var->gp_file);
						break;
						default:
						break;
					}
					if ($s) $rs = ajax_return_true();
					else $rs = ajax_return_false('删除失败');
				}
			}
		break;
	}
} else $rs = ajax_return_false();
echo string::json_encode($rs);
exit;
?>