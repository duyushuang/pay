<?php
class plugin_keywordFilter{
	private $root, $cacheDir;
	public function __construct(){
		$this->root = dirname(__FILE__).D;
		$this->cacheDir = $this->root.'cache'.D;
	}
	private function check($pattern, $data){
		if (is_array($data)) {
			return $this->check($pattern, $data);
		} else {
			if (@preg_match($pattern, $data)) {
				return true;
			} else return false;
		}
	}
	public function run(){
		$var = qscms::v('_G');
		if ($var->postData) {
			$urlPath = substr(NOW_URL, strlen(WEB_URL));///rewrite.php?rewrite=
			substr($urlPath, 0, 21) == '/rewrite.php?rewrite=' && $urlPath = substr($urlPath, 21);
			$args = $this->getArray('cacheDatas');
			$check = false;
			if ($args['wheres'] && $args['keys']) {
				$keys = $args['keys'];
				if ($args['urlencode']) {
					$keys = str_replace('%7c', '\%7c', $keys);
					$keys = urldecode($keys);
				}
				$pattern = '/'.$keys.'/is';
				foreach ($args['wheres'] as $v)  {
					if (@preg_match($v['p'], $urlPath)) {
						$fields = $v['fields'];
						foreach ($_POST as $k => $v2) {
							if (!$fields || in_array($k, $fields)) {
								if ($this->check($pattern, $v2)) {
									if ($v['ajax']) {
										echo string::json_encode(array('status' => false, 'msg' => $args['tip'], 'is_login' => 1));
									} else {
										qscms::charSet();
										qscms::showMessage($args['tip']);
									}
									exit;
								}
							}
						}
					}
				}
			}
		}
	}
	public function menu($args){
		$menuName = 'menu_'.$args[0];
		include(template::load($menuName));
	}
	public function manage(){
		$var = qscms::v('_G');
		extract($var->getVals('action', 'operation', 'method', 'baseUrl', 'baseUrl0', 'page', 'pagesize', 'pagestyle', 'menu_name', 'menu_sub_name'));
		$pluginRoot = $this->root;
		include($pluginRoot.'libs'.D.'manage.php');
	}
	private function writeArray($name, $arr){
		file_exists($this->cacheDir) || file::createFolder($this->cacheDir);
		$file = $this->cacheDir.$name.'.php';
		file::write($file,'<?php exit;?>'.serialize($arr));
	}
	private function getArray($name){
		$rn   = array();
		$file = $this->cacheDir.$name.'.php';
		if(file_exists($file)){
			$rn = @unserialize(substr(file::read($file),13));
			!is_array($rn) && $rn=array();
		}
		return $rn;
	}
}
?>