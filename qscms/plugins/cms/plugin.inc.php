<?php
class plugin_cms{
	private $root, $cacheDir;
	public function __construct(){
		$this->root = dirname(__FILE__).D;
		$this->cacheDir = $this->root.'cache'.D;
	}
	public function run(){
		//include(template::load('test'));
	}
	public function menu($args){
		$menuName = 'menu_'.$args[0];
		include(template::load($menuName));
	}
	public function manage(){
		$var = qscms::v('_G');
		$page          = $var->gp_page;
		$pagesize      = $var->gp_pagesize;
		$adminUrl      = $var->adminUrl;
		$custom_menu_exists = $var->custom_menu_exists;
		$action        = $var->action;
		$operation     = $var->operation;
		$method        = $var->method;
		$baseUrl       = $var->baseUrl;
		$pagestyle     = $var->pagestyle;
		$menu_name     = $var->menu_name;
		$menu_sub_name = $var->menu_sub_name;
		$timestamp     = time::$timestamp;
		$pre           = PRE;
		$pluginRoot = $this->root;
		include($pluginRoot.'libs'.D.'manage.php');
	}
	private function writeArray($name, $arr){
		file_exists($this->cacheDir) || common::create_folder($this->cacheDir);
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
	private function getModels(){
		return $this->getArray('models');
	}
	private function saveModel($id){
		if ($model = db::one('cms_model', '*', "id='$id'")) {
			$modelName  = $model['ename'];
			$modelAlias = $model['name'];
			if (!($menu = b_nav::getMenu($model['menuId']))) return false;
			if (!($parentMenu = b_nav::getMenu($menu['pid']))) return false;
			$filename = $parentMenu['ename'].'_'.$menu['ename'];
			$ln = chr(10);
			if (!($table1 = db::showCreateTable('cms_'.$modelName.'_cate'))) return false;
			if (!($table2 = db::showCreateTable('cms_'.$modelName))) return false;
			$table = 'drop table if exists `'.PRE.'cms_'.$modelName.'_cate`;';
			$table .= $ln;
			$table .= $table1.';';
			$table .= $ln;
			$table .= 'insert into `'.PRE.'cms_'.$modelName.'_cate`(`name`,`ename`,`addTime`,`editTime`) values(\'默认分类\',\'default\',\'{timestamp}\', \'{timestamp}\');';
			$table .= $ln;
			$table .= 'drop table if exists `'.PRE.'cms_'.$modelName.'`;';
			$table .= $ln;
			$table .= $table2.';';
			$table = preg_replace('/ AUTO_INCREMENT=\d+/', '', $table);
			$table = str_replace(PRE, '{pre}', $table);
			$table = str_replace($modelName, '{modelName}', $table);
			$libFile = d('./'.ADMIN_FOLDER.'/'.$filename.'.php');
			$tplFile = d('./templates/default/founder/'.$filename.'.htm');
			if (!file_exists($libFile) || !file_exists($tplFile)) return false;
			$code = file::read($libFile);
			$code = str_replace($modelName , '{modelName}' , $code);
			$code = str_replace($modelAlias, '{modelAlias}', $code);
			file::write($this->root.'libs'.D.'cms_'.$modelName.'.php', $code);
			$code = file::read($tplFile);
			$code = str_replace($modelName, '{modelName}', $code);
			$code = str_replace($modelAlias, '{modelAlias}', $code);
			file::write($this->root.'templates'.D.'cms_'.$modelName.'.htm', $code);
			file::write($this->root.'install.model.'.$modelName.'.sql', $table);
			$models = $this->getModels();
			$models[$modelName] = $model['name'];
			$this->writeArray('models', $models);
			return true;
		}
		return false;
	}
	public function getFieldValues($args){
		list($modelName) = $args;
		$values = array();
		if ($fields = db::select('cms_model_fields|cms_model:id=mid', 'fieldName,htmlListValue|', 't1.ename=\''.$modelName.'\'')) {
			foreach ($fields as $v) {
				$choose = string::parseChoose($v['htmlListValue']);
				if ($choose) {
					foreach ($choose as $v1) {
						$values[$v['fieldName']][$v1['value']] = $v1['key'];
					}
				}
			}
		}
		return $values;
	}
}
?>