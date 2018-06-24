<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
set_time_limit(0);
class disperse_web{
	private $vars;
	public function __get($key){
		if (isset($this->vars[$key])) return $this->vars[$key];
		return '';
	}
	public function __set($key, $val){
		$this->vars[$key] = $val;
	}
	public function __construct($url, $key, $varName, $path, $webUrl = '') {
		$this->vars     = array();
		$this->status   = true;
		$this->url      = $url;
		$this->key      = $key;
		$this->varName  = $varName;
		$this->chdir($path);
		$this->rootPath = substr($this->path, 0, -1);
		$this->path     = '/';
		$this->webUrl   = $webUrl;
	}
	private function path(){
		return $this->rootPath.$this->path;
	}
	private function getApiUrl($action){
		if ($this->status){
			return $this->url.'?key='.urlencode($this->key).'&path='.urlencode($this->rootPath).'&action='.urlencode($action);
		}
		return '';
	}
	public function chdir($path){
		$path == '' || substr($path, 0, 1) != '/' && $path = '/'.$path;
		substr($path, -1) != '/' && $path .= '/';
		$this->path = $path;
	}
	public function getFDList($path = ''){
		$path && $this->chdir($path);
		$list = array('dirs' => array(), 'files' => array());
		if ($this->status) {
			$url = $this->getApiUrl('fdlist');
			if ($url) {
				$url .= '&file='.urlencode($this->path);
				$data = winsock::get_html($url);
				if ($data) {
					$data = @unserialize($data);
					if (isset($data['dirs']) && is_array($data['dirs']) && isset($data['files']) && is_array($data['files'])) {
						$dirs = $data['dirs'];
						$files0 = $files1 = array();
						foreach ($data['files'] as $v) {
							$suffix = '';
							if (($f = strrpos($v, '.')) !== false) {
								$suffix = substr($v, $f + 1);
							}
							$files0[] = $v;
							$files1[] = array(
								'file'   => $v,
								'suffix' => $suffix
							);
						}
						array_multisort($dirs, SORT_ASC, SORT_STRING);
						array_multisort($files0, SORT_ASC, SORT_STRING, $files1);
						$list = array(
							'dirs' => $dirs,
							'files' => $files1
						);
					}
				}
			}
		}
		return $list;
	}
	public function getFList(){
		$list = $this->getFDList();
		return $list['files'];
	}
	public function getDList(){
		$list = $this->getFDList();
		return $list['dirs'];
	}
	private function getFilename($file){
		$len = strlen($this->path);
		if (substr($file, 0, $len) == $this->path) return substr($file, $len);
		return '';
	}
	public function delFile($file, $delDir = false){
		if ($this->status) {
			if ($url = $this->getApiUrl('del')) {
				$op = $delDir ? 'fileAndDir' : 'file';
				$url .= '&op='.$op.'&file='.urlencode($file);
				if (winsock::get_html($url) == 'success') {
					return true;
				}
			}
		}
		return false;
	}
	public function delDir($path){
		if ($this->status) {
			if ($url = $this->getApiUrl('del')) {
				$url .= '&op=dir&file='.urlencode($path);
				if (winsock::get_html($url) == 'success') {
					return true;
				}
			}
		}
		return false;
	}
	public function fileExists($file){
		if ($this->status) {
			if ($url = $this->getApiUrl('exists')) {
				$url .= '&file='.urlencode($file);
				if (winsock::get_html($url) == 'success') {
					return true;
				}
			}
		}
		return false;
	}
	public function uploadFile($remoteFile, $localFile){
		if ($this->status) {
			if (file_exists($localFile)) {
				if ($url = $this->getApiUrl('upload')) {
					$url .= '&file='.urlencode($remoteFile).'&varName='.urlencode($this->varName);
					if (winsock::uploadFile($url, $this->varName, $localFile) == 'success') {
						return true;
					}
				}
			}
		}
		return false;
	}
}
?>