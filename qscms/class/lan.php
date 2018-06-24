<?php
class lan{
	function __construct($file = 'index', $folder = 'default', $lan = 'cn'){
		$file = qd('./language/'.$folder.'/'.$file.'.php');
		if (file_exists($file)) {
			@include($file);
			$this->lanArr = $lang;
		} else $this->lanArr = $lang;
		$this->lan = $lan;
	}
	function set($lan){
		$this->lan = $lan;
	}
	function __invoke($name){
		return $this->get($name);
	}
	function get($name){
		if (isset($this->lanArr[$this->lan][$name])) {
			return $this->lanArr[$this->lan][$name];
		} else return $name;
	}
}
?>