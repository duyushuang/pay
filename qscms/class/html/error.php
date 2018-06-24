<?php
/**

 */
class html_error extends html_message{
	public function __construct($message){
		$title = SOFTWARE_NAME.'运行遇到错误！';
		parent::__construct($message, $title);
	}
}
?>