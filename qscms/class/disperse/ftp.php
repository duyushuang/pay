<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
set_time_limit(0);
class disperse_ftp{
	private $vars;
	public function __get($key){
		if (isset($this->vars[$key])) return $this->vars[$key];
		return '';
	}
	public function __set($key, $val){
		$this->vars[$key] = $val;
	}
	public function __construct($ip, $port, $username, $password, $path, $webUrl = '') {
		$this->vars   = array();
		$this->status = false;
		if ($conn_id = ftp_connect($ip, $port)) {
			if (ftp_login($conn_id, $username, $password)) {
				$this->conn_id = $conn_id;
				$this->chdir($path);
				$this->rootPath = substr($this->path, 0, -1);
				$this->path     = '/';
				$this->status  = true;
				$this->webUrl  = $webUrl;
			}
		}
	}
	public function __destruct(){
		if ($this->status) {
			ftp_close($this->conn_id);
		}
	}
	private function path($path = ''){
		$path || $path = $this->path;
		return $this->rootPath.$path;
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
			if ($__list = ftp_rawlist($this->conn_id, $this->path())) {
				$dirs  = array();
				$files0 = $files1 = array();
				foreach ($__list as $v) {
					if (preg_match('/^([-d][rwx-]{9})\s.+?(\d+)\s([a-zA-Z]+\s\d+\s\d{1,2}:\d{1,2})\s(.+)$/', $v, $matches)) {
						$mod  = $matches[1];
						$size = $matches[2];
						$time = $matches[3];
						$file = $matches[4];
						$t = substr($mod, 0, 1);
						if ($t == 'd') {
							if(!in_array($file, array('.', '..'))) $dirs[] = $file;
						} else {
							$suffix = '';
							if (($f = strrpos($file, '.')) !== false) $suffix = substr($file, $f + 1);
							$files0[] = $file;
							$files1[] = array(
								'file' => $file,
								'suffix' => $suffix
							);
						}
					}
				}
				array_multisort($dirs, SORT_ASC, SORT_STRING);
				array_multisort($files1, SORT_ASC, SORT_STRING, $files0);
				$list = array(
					'dirs' => $dirs,
					'files' => $files1
				);
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
	public function delFile($file, $delDir = false){
		if ($this->status) {
			$rs = ftp_delete($this->conn_id, $this->path($file));
			if ($rs && $delDir) {
				$f = strrpos($file, '/');
				$path = substr($file, 0, $f);
				if ($path != '') {
					$f = strrpos($path, '/');
					$path = substr($path, $f);
					$path .= '/';
					$path == '//' && $path = '/';
					if ($path != '/') {
						$arr = $this->getFDList($path);
						if (!$arr['dirs'] && !$arr['files']) {
							ftp_rmdir($this->conn_id, $this->path($path));
						}
					}
				}
			}
			return $rs;
		}
		return false;
	}
	public function delDir($path){
		if ($this->status) {
			substr($path, 0, 1) != '/' && $path = '/'.$path;
			substr($path, -1) != '/' && $path .= '/';
			$list = $this->getFDList($path);
			foreach ($list['files'] as $v) {
				if (!$this->delFile($path.$v['file'])) return false;
			}
			foreach ($list['dirs'] as $v) {
				if (!$this->delDir($path.$v.'/')) return false;
			}
			return ftp_rmdir($this->conn_id, $this->path($path));
		}
		return false;
	}
	public function fileExists($file){
		if ($this->status) {
			return ftp_mdtm($this->conn_id, $this->path($file)) == -1 ? false : true;
		}
		return false;
	}
	public function uploadFile($remoteFile, $localFile){
		if ($this->status) {
			if (file_exists($localFile)) {
				substr($remoteFile, 0, 1) != '/' && $remoteFile = '/'.$remoteFile;
				$pathInfo = pathinfo($remoteFile);
				$dir = $pathInfo['dirname'];
				if ($dir != '/') {
					$dir = substr($dir, 1);
					$path = '/';
					foreach (explode('/', $dir) as $v) {
						$path .= $v;
						if (!$this->fileExists($path)) {
							if (!ftp_mkdir($this->conn_id, $this->path($path))) return false;
						}
						$path .= '/';
					}
				}
				if ($f = fopen($localFile, 'rb')) {
					$ret = ftp_nb_fput($this->conn_id, $this->path($remoteFile), $f, FTP_BINARY);
					while ($ret == FTP_MOREDATA) {
					   $ret = ftp_nb_continue($this->conn_id);
					}
					if (is_resource($f)) fclose($f);
					if ($ret == FTP_FINISHED) return true;
				}
			}
		}
		return false;
	}
}
?>