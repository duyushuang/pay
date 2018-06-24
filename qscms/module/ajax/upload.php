<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
include_once(m(ADMIN_FOLDER.'/module/ini'));
loadFunc('ajax');
$rs = ajax_return_false();
set_time_limit(0);
if (qscms::defineTrue('IN_ADMIN')) {
	switch ($var->v0) {
		case 'image':
			$upName   = 'filedata';
			//$saveDir0 = qscms::getCfgPath('/system/imgRoot');
			//$saveDir1 = date('Y/m/d/', time::$timestamp);
			//$saveDir  = d($saveDir0.$saveDir1);
			$d = dfile::getObj('article');
			$uprs = $d->uploadImage($upName);
			if ($uprs) {
				$url = $d->getUrl('/'.$uprs['filename'].'.'.$uprs['suffix']);
				$rs = array();
				$rs['err'] = '';
				$rs['msg'] = array(
					'url'       => $url,
					'localfile' => substr($url, strrpos($url, '/'))
				);
			} else $rs = array('err' => '上传失败');
		break;
		case 'pic':
			if ($_POST) {
				if (album_uploadPic() === true) $rs = ajax_return_true();
				else $rs = ajax_return_false();
			} else $rs = ajax_return_false();
		break;
	}
} else $rs = ajax_return_false();
echo string::json_encode($rs);
exit;
?>