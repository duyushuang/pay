<?php
class error{
	public static function _404($msg = '', $url = ''){
		template::initialize(qd(qscms::getCfgPath('/system/tplRoot'.(IS_MODULE ? '_m' : ''))), d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/tpl'.(IS_MODULE ? '_m' : ''))));
		qscms::ob_clean();
		extract(qscms::v('_G')->getVals('webName'));
		header("HTTP/1.1 404 Not Found");
		include(template::load('error/404', true));
		//throw new e_qscms('404 Not Found');
		exit;
	}
	public static function bbsMsg($message){
		template::initialize(qd(qscms::getCfgPath('/system/tplRoot').cfg::get('sys', 'tplFolder').'bbs/'), d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/tpl').cfg::get('sys', 'tplFolder').'bbs/'));//设置BBS模板缓存目录
		qscms::ob_clean();
		$var = qscms::v('_G');
		extract($var->getVals('webName'));
		$cssList = array(
			css::getUrl('login', 'bbs')
		);
		include(template::load('show_message'));
		exit;
	}
	public static function forumNotFound(){
		self::bbsMsg('对不起，您访问的版块不存在');
	}
}
?>