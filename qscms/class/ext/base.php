<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class ext_base{
	public function __call($name, $arguments){
		$f = false;
		if (substr($name, -4) == 'Form' || ($f = strrpos($name, 'Form_')) !== false) {
			if (form::hash()) {
				$formType = '';
				if ($f === false) {
					$callName = substr($name, 0, -4);
				} else {
					$callName = substr($name, 0, $f);
					$formType = substr($name, $f + 5);
				}
				if (!$formType || (!empty($_POST['formType']) && $_POST['formType'] == $formType)) {//判断表单类型
				
					if (method_exists($this, $callName)) {
						$datas = array();
						if (count($arguments) > 0) {
							$datas = call_user_func_array(array('form', 'get3'), $arguments);
						} else $datas = $_POST;
						return $this->$callName($datas);
						//call_user_func_array(array($this, $callName), array($datas));
					} else {
						throw new e_qscms('method not exists:'.__CLASS__.'->'.$callName);
					}
				}
			}
		}
		return false;
	}
	public static function formCall($name, $arguments = array()){
		$f = false;
		if (substr($name, -4) == 'Form' || ($f = strrpos($name, 'Form_')) !== false) {
			if (form::hash()) {
				$formType = '';
				if ($f === false) {
					$callName = substr($name, 0, -4);
				} else {
					$callName = substr($name, 0, $f);
					$formType = substr($name, $f + 5);
				}
				if (!$formType || (!empty($_POST['formType']) && $_POST['formType'] == $formType)) {//判断表单类型
					$selfName = get_called_class();
					if (method_exists($selfName, $callName)) {
						$datas = array();
						if (count($arguments) > 0) {
							$datas = call_user_func_array(array('form', 'get3'), $arguments);
						} else $datas = $_POST;
						//return $selfName::$callName($datas);
						return call_user_func_array(array($selfName, $callName), array($datas));
					} else {
						throw new e_qscms('method not exists:'.__CLASS__.'::'.$callName);
					}
				}
			}
		}
		return false;
	}
}
?>