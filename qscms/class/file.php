<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class file{
	public static function fopen($filename, $mode){
		if ($mode == 'a+') {
			if (!file_exists($filename)) touch($filename);
			$f = fopen($filename, 'r+');
			fseek($f, 0, SEEK_END);
			return $f;
		} else return fopen($filename, $mode);
	}
	public static function write($file,$data, $append = false){
		if ($fp = fopen($file,$append ? 'ab' : 'wb')) {
			flock($fp, LOCK_EX) ;
			fwrite($fp,$data);
			flock($fp, LOCK_UN); 
			fclose($fp);
			return true;
		}
		return false;
	}
	public static function read($file){
		$fp = fopen($file,"rb");	
		flock($fp, LOCK_SH) ;
		$data=@fread($fp,filesize($file));
		flock($fp, LOCK_UN); 
		fclose($fp);
		return $data;
	}
	public static function seek($handle, $offset, $marker = ''){
		
	}
	public static function back(){
		if(func_num_args()>0){
			foreach(func_get_args() as $v){
				if(file_exists($v)){
					@rename($v,$v.'.bak');
				}
			}
		}
	}
	public static function unback(){
		if(func_num_args()>0){
			foreach(func_get_args() as $v){
				if(file_exists($v.'.bak')){
					@rename($v.'.bak',$v);
				}
			}
		}
	}
	public static function download($file, $del = false){
		set_time_limit(0);
		if(file_exists($file)){
			qscms::ob_end_clean();
			$info = pathinfo($file);
			$size = filesize($file);
			header("Content-type: application/octet-stream");   
			header("Accept-Ranges: bytes");   
			header("Accept-Length: ".$size);
			header("Content-Length: ".$size);
			header("Content-Disposition:attachment;filename=".iconv(ENCODING,'gbk',str_replace(' ', '_', $info['basename'])));
			if($f=fopen($file,'rb')){
				while($r = fread($f, 1024)){
					echo $r;
				}
				fclose($f);
			} else echo 'error';
			$del && @unlink($file);
			return true;
		} else return false;
	}
	public static function copyFile($source, $dest){
		//return @copy(d($source), d($dest));
		return @copy($source, $dest);
	}
	public static function mkdir($d){
		//$d = d($d);
		if(!file_exists($d)){
			return @mkdir($d);
		}
		return false;
	}
	public static function createFile($d){
		//$d = d($d);
		if(!file_exists($d)){
			return @touch($d);
		}
		return false;
	}
	public static function moveFile($source,$dest){
		//$source = d($source);
		//$dest   = d($dest);
		//echo 123 .'<br />';
		//echo $source.'<br />'.d($dest);exit;
		if(@copy($source, d($dest))){
			return @unlink($source);
		} else {
			return false;
		}
	}
	public static function dirIsEmpty($dir){
		if ($o = @opendir($dir)) {
			$isEmpty = true;
			while ($r = readdir($o)) {
				if ($r != '.' && $r !== '..') {
					$isEmpty = false;
					break;
				}
			}
			closedir($o);
			return $isEmpty;
		}
		return true;
	}
	public static function unlink($file){
		if (@unlink($file)) {
			$info = pathinfo($file);
			if (self::dirIsEmpty($info['dirname'])) {
				@rmdir($info['dirname']);
			}
			return true;
		}
		return false;
	}
	public static function delFolder($s,$fullpath=1,$del_folder=true){
		if($fullpath)$folder = d($s);
		else $folder = $s;
		!in_array(substr($folder, -1), array('\\', '/')) && $folder .= D;
		!in_array(substr($s, -1), array('\\', '/')) && $s .= D;
		if (file_exists($folder)) {
			$files = scandir($folder);
			foreach($files as $v){
				if($v != '.' && $v != '..'){
					$this_file = $folder.$v;
					$type = filetype($this_file);
					if($type == 'dir'){
						self::delFolder($this_file, 0, $del_folder);
						if ($del_folder && file_exists($this_file)) {
							if (!@rmdir($this_file)) return false;
						}
					} elseif($type=='file') {
						if (!@unlink($this_file)) return false;
					}
				}
			}
		} else return true;
		return ($del_folder && @rmdir($folder)) || (!$del_folder);
	}
	public static function rmDir($dir){
		if (in_array(substr($dir, -1), array('/', '\\'))) $dir = substr($dir, 0, -1);
		if ($o = opendir($dir)) {
			while ($filename = readdir($o)) {
				if (!in_array($filename, array('.', '..'))) {
					$file = $dir.D.$filename;
					if (filetype($file) == 'dir') self::rmDir($file);
					else @unlink($file);
				}
			}
			closedir($o);
		}
		return @rmdir($dir);
	}
	public static function copyFolder($s, $d, $move = 0, $fullpath = 1){
		if($fullpath){
			$s = d($s);
			$d = d($d);
			if(!file_exists($d)){
				if(!@mkdir($d))return false;
			}
		}
		!in_array(substr($s, -1), array('\\', '/')) && $s .= D;
		if (!file_exists($s)) return false;
		file_exists($d) || self::createFolder($d);
		$files = scandir($s);
		foreach ($files as $v){
			if ($v != '.' && $v != '..') {
				$this_file = $s.$v;
				$d_file    = $d.$v;
				$type=filetype($this_file);
				if($type=='dir'){
					if(!file_exists($d_file)){
						if(!mkdir($d_file))return false;
					}
					self::copyFolder($this_file, $d_file.D, $move, 0);
					if ($move) @rmdir($this_file);
				} elseif($type == 'file'){
					if (!@copy($this_file, $d_file)) return false;
					if($move) @unlink($this_file);
				}
			}
		}
		if($fullpath){
			if($move)return @rmdir($s);
			else
			return true;
		}
		return true;
	}
	public static function moveFolder($s, $d){
		return self::copyFolder($s, $d, 1);
	}
	public static function listFolders($s){
		//$s = d($s);
		if (is_dir($s)) {
			if ($dh = opendir($s)) {
				while (($file = readdir($dh)) !== false) {
					filetype($s.$file)=='dir'&&!in_array($file,array('.','..'))&&($rn[]=$file);
				}
				closedir($dh);
			}
		}
		return $rn;
	}
	public static function listFileAll($dir){
		//$dir0 = d($dir);
		!in_array(substr($dir, -1), array('\\', '/')) && $dir .= D;
		$rn   = array();
		if(is_dir($dir)){
			if($dh = opendir($dir)){
				while(($file = readdir($dh))!==false){
					if(filetype($dir.$file) == 'dir'){
						if(!in_array($file, array('.','..'))) if($list = self::listFileAll($dir.$file))$rn = array_merge($rn, $list);
					} else $rn[]=$dir.$file;
				}
			}
		}
		return $rn;
	}
	public static function getFiles($dir, $ignore = ''){
		$rn  = array();
		//$dir = d($dir);
		strpos('/\\', substr($dir, -1)) === false && $dir.=D;
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if(filetype($dir.$file)=='file'){
						if($ignore){
							if(preg_match($ignore, $file))
							$rn[] = $file;
						} else {
							$rn[] = $file;
						}
					}
				}
				closedir($dh);
			}
		}
		return $rn;
	}
	public static function listFiles($s,$gl=''){
		//$s = d($s);
		$rn = array();
		if (is_dir($s)) {
			if ($dh = opendir($s)) {
				while (($file = readdir($dh)) !== false) {
					if(filetype($s.$file)=='file'){
						if($gl){
							if(preg_match($gl,$file))
							$rn[]=$file;
						} else {
							$rn[]=$file;
						}
					}
				}
				closedir($dh);
			}
		}
		return $rn;
	}
	function listFolderFile($s=''){
		//$s = d($s);
		$rs = scandir($s);
		foreach($rs as $k=>$v){
			if($v != '.' && $v != '..'){
				$this_file=$s.'/'.$v;
				unset($file_info);
				$file_info['filectime'] = filectime($this_file);
				$file_info['filemtime'] = filemtime($this_file);
				$file_info['is_write']  = is_writable($this_file);
				$file_info['is_read']   = is_readable($this_file);
				$file_info['size']      = filesize($this_file);
				$files[]=array('name' => $v, 'is_dir' => is_dir($this_file), 'info' => $file_info);
			}
		}
		if($files){
			$is_dir = qscms::arrid($files,'is_dir');
			$name   = qscms::arrid($files,'name');
			array_multisort($is_dir, SORT_DESC, SORT_NUMERIC, $name, SORT_ASC,SORT_STRING, $files);
		}
		return $files;
		//return array('folder'=>$folders,'files'=>$files);
	}
	public static function rename($oldName, $newName){
		if (@rename($oldName, $newName)) return true;
		if (@copy($oldName, $newName)) {
			@unlink($oldName);
			return true;
		}
		return false;
	}
	public static function createDateFolder($parentFolder, $fill = '/'){
		$t = time::$timestamp;
		$folders[]=date('Y',$t);
		$folders[]=date('m',$t);
		$folders[]=date('d',$t);
		if(!file_exists($parentFolder))self::createFolder($parentFolder);
		!in_array(substr($parentFolder, -1), array('\\', '/')) && $parentFolder .= D;
		$path = $parentFolder;
		foreach($folders as $folder){
			$path .= $folder.D;
			if (file_exists($path)) {
				if (!@mkdir($path)) {
					throw new e_qscms('创建目录失败：'.u($path).'<br />请检查上级目录是否可写');
				}
			}
		}
		return implode($fill,$folders);
	}
	public static function createFolders(){
		$list_count     = func_num_args();
		$complate_count = 0;
		if($list_count>0){
			$path_list=func_get_args();
			foreach($path_list as $path){
				if(self::createFolder($path))$complate_count++;
			}
		}
		return $complate_count;
	}
	public static function createFolder($path){
		if(!file_exists($path)){
			$path = strtr($path, '\\/', D.D);
			//$folder_list = preg_split('/\\/|\\\\/',$path);
			$folder_list = explode(D, $path);
			$path = '';
			foreach($folder_list as $k => $folder){
				if($folder != ''){
					$path && substr($path, -1) != D && $path .= D;
					$path .= $folder;
					if(!qscms::inWebRoot($path) && !@file_exists($path)){
						if(!@mkdir($path)) {
							throw new e_qscms('创建目录失败：'.u($path).'<br />请检查上级目录是否可写');
							return false;
						}
					}
				} else {
					if ($k == 0) {
						$path .= D;
					}
				}
			}
		}
		return true;
	}
	public static function createFolderToFile($file){
		if (!file_exists($file)) {
			return self::createFolder(dirname($file));
		}
		return true;
	}
	public static function getBaseName($file){
		if (($f = strrpos($file, D)) !== false) $str = substr($file, $f + 1);
		else $str = $file;
		return $str;
	}
	public static function getFileName($file){
		$str = self::getBaseName($file);
		if (($f = strrpos($str, '.')) !== false) $filename = substr($str, 0, $f);
		else $filename = $str;
		return $filename;
	}
	public static function getRandFile($dir, $ignore = false){
		static $cache;
		$dir = strtr($dir, '/\\', D.D);
		substr($dir, -1) != D && $dir .= D;
		$key = $dir.$ignore;
		if (isset($cache[$key])) $files = $cache[$key];
		else {
			if ($ignore) {
				$ignore = qscms::addcslashes($ignore, '\\.');
				$ignore = str_replace('*', '.*?', $ignore);
				$ignore = '/^'.$ignore.'$/s';
			}
			$files = self::getFiles($dir, $ignore);
			$cache[$key] = $files;
		}
		$count = count($files);
		if ($count > 0) return u($dir.$files[rand(0, $count - 1)]);
		return '';
	}
}
?>