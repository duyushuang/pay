<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class data{
	public $postData = false, $getData = false, $ipint, $intip, $domains, $_G = array();
	public static function initialize(){
		if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST){
			if(!MAGIC_QUOTES_GPC){
				$_POST = qscms::addslashes($_POST);
			}
			self::$postData = true;
		} else self::$postData = false;
		if($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET){
			if(!MAGIC_QUOTES_GPC){
				$_GET = qscms::addslashes($_GET);
			}
			self::$getData = true;
		}
		$_COOKIE = qscms::parseCookie($_COOKIE);
		self::$ipint   = qscms::ipint();
		self::$intip   = qscms::intip();
		self::$domains = qscms::domain_parse();
		self::regGP();
	}
	private static function regGP(){
		if (self::$getData) {
			foreach ($_GET as $k => $v) {
				self::$_G['gp_'.$k] = $v;
			}
		}
		if (self::$postData) {
			foreach ($_POST as $k => $v) {
				self::$_G['gp_'.$k] = $v;
			}
		}
	}
}
?>