<?php
class upload{
	public $suffix;
	public $insertDB = true;
	private $uploadDir;
	function __construct(){
		$this->clear_cache();
		$this->uploadDir = d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/upload'));
		file::createFolder($this->uploadDir);
	}
	function __destruct(){}
	function toupload($obj_name='', $ftype='',$file_size=-1){
		$rs=array('count'=>0,'error'=>0,'info'=>array());
		if($obj_name){
			if ($ftype && $ftype == 'image') $this->suffix = "jpg|jpeg|png|gif|";
			if(!empty($obj_name) && !empty($_FILES[$obj_name])){
				$files = $_FILES[$obj_name];
				if(!empty($files['name'])){
					if(is_array($files['name'])){
						//数组
						foreach($files['name'] as $k=>$v){
							$next=true;
							if($ftype){
								if(!preg_match("/^$ftype/",$files['type'][$k]))$next=false;
							}
							if($next){
								$info['name']=$files['name'][$k];
								$info['type']=$files['type'][$k];
								$info['tmp_name']=$files['tmp_name'][$k];
								$info['error']=$files['error'][$k];
								$info['size']=$files['size'][$k];
								$msg=$this->upload($info,$k,$file_size);
								if($msg['status']===true){
									$rs['count']++;
									$rs['info'][$obj_name][]=$msg;
								} else {
									$rs['error']++;
									$rs['errors'][$obj_name][$k]=$msg['error'];
								}
							}
						}
						if($rs['count']>0)if(count($rs['info'][$obj_name])==1)$rs['info'][$obj_name]=$rs['info'][$obj_name][0];
					} else {
						$msg=$this->upload($files,0,$file_size);
						if($msg['status']===true){
							$rs['count']++;
							$rs['info'][$obj_name]=$msg;
						} else {
							$rs['error']++;
							$rs['errors'][$obj_name]=$msg['error'];
						}
					}
				}
			}
		} else {
			if($_FILES){
				foreach($_FILES as $obj_name=>$files){
					if($files['name']){
						if(is_array($files['name'])){
							//数组
							foreach($files['name'] as $k=>$v){
								$info['name']=$files['name'][$k];
								$info['type']=$files['type'][$k];
								$info['tmp_name']=$files['tmp_name'][$k];
								$info['error']=$files['error'][$k];
								$info['size']=$files['size'][$k];
								$msg=$this->upload($info,$k,$file_size);
								if($msg['status']===true){
									$rs['count']++;
								}
								$rs['info'][$obj_name][]=$msg;
							}
						} else {
							$msg=$this->upload($files,0,$file_size);
							if($msg['status']===true){
								$rs['count']++;
							}
							$rs['info'][$obj_name]=$msg;
						}
					}
				}
			}
		}
		return $rs;
	}
	function upload($info,$k=0,$file_size=-1){
		$timestamp = time::$timestamp;
		if($info['name']){
			$msg['errno']=$info['error'];
			switch($info['error']){
				case UPLOAD_ERR_OK:
					$save=tempnam($this->uploadDir, '');
					//list($msec,$sec)=explode(" ",microtime());
					//$tmp_name=date("YmdHis",$sec)."_{$msec}_$k";
					$save_info=pathinfo($save);
					$tmp_name=$save_info['basename'];
					if(preg_match("/(?>)(.*)\.(.+)/",$info['name'],$matches)){
						$name=$matches[1];
						$suffix=$matches[2];
					} else {
						$name=$files['name'];
						$suffix='';
					}
					if($this->suffix){
						if(!$suffix){
							$msg['status']=false;
							$msg['error']='后缀名为空';
							break;
						} else {
							if(strpos(strtolower($this->suffix),strtolower($suffix)."|")===false){
								$msg['status']=false;
								$msg['error']='非法后缀名';
								break;
							}
						}
					}
					$type=$info['type'];
					$size=$info['size'];
					if($file_size==-1 || $size<=$file_size){
						if(@move_uploaded_file($info['tmp_name'],$save)||@copy($info['tmp_name'],$save)){
							if ($this->insertDB) {
								$id = db::insert('cache_upload', array(
									'tmp_name' => $tmp_name,
									'name'     => $name,
									'suffix'   => $suffix,
									'type'     => $type,
									'size'     => $size,
									'dateline' => $timestamp
								), true);
								if(db::affectedRows()==1){
									$msg['status'] = true;
									$msg['db_id']  = $id;
								} else {
									$msg['status'] = false;
									$msg['error']  = '插入数据库失败';
									@unlink($save);
								}
							} else {
								$msg['status']   = true;
								$msg['tmp_name'] = $tmp_name;
								$msg['name']     = $name;
								$msg['suffix']   = $suffix;
								$msg['type']     = $type;
								$msg['size']     = $size;
								$msg['file']     = $save;
							}
							@unlink($info['tmp_name']);
						} else {
							$msg['status']=false;
							$msg['error']='移动文件失败';
						}
					} else {
						$msg['status']=false;
						$msg['error']='文件尺寸大于'.$file_size.'字节';
					}
				break;
				case UPLOAD_ERR_INI_SIZE:
					$msg['status']=false;
					$msg['error']='文件大小超过系统设置';
				break;
				case UPLOAD_ERR_FORM_SIZE:
					$msg['status']=false;
					$msg['error']='文件大小超过表单设置';
				break;
				case UPLOAD_ERR_PARTIAL:
					$msg['status']=false;
					$msg['error']='文件只有部分被上传';
				break;
				case UPLOAD_ERR_NO_FILE:
					$msg['status']=false;
					$msg['error']='没有文件上传';
				break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$msg['status']=false;
					$msg['error']='找不到临时文件夹';
				break;
				case UPLOAD_ERR_CANT_WRITE:
					$msg['status']=false;
					$msg['error']='写入文件失';
				break;
			}
		}
		return $msg;
	}
	function clear($id = false){
		$where = '';
		if($id)$where=" where id=$id";
		$query = db::query("select * from ".db::table('cache_upload').$where);
		while($line = db::fetch($query)){
			@unlink($this->uploadDir.$line['tmp_name']);
			db::query("delete from ".db::table('cache_upload')." where id=$line[id]", 'UNBUFFERED');
			$rs[]=$line;
		}
		return $rs;
	}
	function get($id){
		if ($item = db::one('cache_upload', '*', "id='$id'")) {
			$item['file'] = $this->uploadDir.$item['tmp_name'];
			return $item;
		}
		return false;
	}
	function move($id,$folder,$tmpname=false){
		if($line = db::fetchFirst("select * from ".db::table('cache_upload')." where id=$id")){
			$file = $this->uploadDir.$line['tmp_name'];
			//!file_exists(WEB_ROOT.$folder)&&mkdirs($folder);
			if(!preg_match("/^(?>).*\\/([^\\/]+)$/",$folder)){
				if($tmpname){
					list($msec,$sec)=explode(' ',microtime());
					$fname=$sec.sprintf('%03d',floor($msec*1000+0.5)).'.'.$line['suffix'];
					while(file_exists(WEB_ROOT.$folder.$fname)){
						list($msec,$sec)=explode(' ',microtime());
						$fname=$sec.sprintf('%03d',floor($msec*1000+0.5)).'.'.$line['suffix'];
					}
					@touch(WROOT.$folder.$fname);
					//$fname=$line['tmp_name'].'.'.$line['suffix'];
				} else $fname=$line['name'].'.'.$line['suffix'];
				$folder.=$fname;
			}
			file_exists($file)&&@copy($file,WEB_ROOT.$folder);
			@unlink($file);
			db::query("delete from ".db::table('cache_upload')." where id=$line[id]",'UNBUFFERED');
			$url = preg_replace("/^\.\\//","/",$folder);
			return array('source'=>$file,'des'=>$folder,'url'=>$url,'fullurl'=>qscms::getUrl($url, true), 'name'=>$fname);
		}
	}
	function move2($id, $folder){
		if($line = db::fetchFirst("select * from ".db::table('cache_upload')." where id='$id'")){
			$file = $this->uploadDir.$line['tmp_name'];
			$tmpFile = tempnam($folder, '');
			$pathInfo = pathinfo($tmpFile);
			$filename = $pathInfo['filename'];
			$basename = $filename.'.'.$line['suffix'];
			$saveFile = $pathInfo['dirname'].D.$basename;
			file_exists($file) && @copy($file, $saveFile);
			@unlink($file);
			@unlink($tmpFile);
			db::query("delete from ".db::table('cache_upload')." where id=$line[id]",'UNBUFFERED');
			return array('source' => $saveFile, 'basename' => $basename, 'filename' =>$filename, 'suffix' => $line['suffix']);
		}
	}
	public static function getSoft($name, $dir){
		$upload = new self();
		$upload->suffix='exe|rar|zip|msi|7z|';
		$rs = $upload->toupload($name);
		if ($rs['count'] == 1) {
			!file_exists($dir) && file::createFolder($dir);
			if ($rs = $upload->move2($rs['info'][$name]['db_id'], $dir)) {
				return $rs['basename'];
			}
		}
		return false;
	}
	public static function getFileFull($name, $suffix = ''){
		$upload = new self();
		$upload->suffix   = $suffix;
		$upload->insertDB = false;
		$rs = $upload->toupload($name);
		if ($rs['count'] == 1) {
			return $rs['info'][$name];
		}
		return false;
	}
	public static function getFile($name, $suffix = '') {
		if ($rs = self::getFileFull($name, $suffix)) return $rs['file'];
		return false;
	}
	public static function uploadName($name){
		return md5($name);
	}
	
	public static function tempname($dir, $suffix = 'tmp'){
		if ($suffix == 'tmp') return tempnam($dir, '');
		!in_array(substr($dir, -1), array('/', '\\')) && $dir .= D;
		list($msec, $sec) = explode(' ', microtime());
		$fname = $sec . sprintf('%03d', floor($msec * 1000 + 0.5)) . '.' . $suffix;
		while(file_exists($dir . $fname)){
			list($msec, $sec) = explode(' ', microtime());
			$fname = $sec . sprintf('%03d', floor($msec * 1000 + 0.5)) . '.' . $suffix;
		}
		return $dir . $fname;
	}
	public static function uploadFile($name, $dir, $suffix = ''){
		!file_exists($dir) && file::createFolder($dir);
		if (file_exists($dir)) {
			$upload = new self();
			$upload->suffix   = $suffix;
			$upload->insertDB = false;
			
			$rs = $upload->toupload($name);
			if ($rs['count'] == 1) {
				$var = qscms::v('_G');
				$hash = $var->sys_hash;
				$filename = self::uploadName($hash.urlencode($rs['info'][$name]['name']));
				$file = $dir.$filename.'.'.$rs['info'][$name]['suffix'];
				if (file_exists($file)) {
					$pathinfo = pathinfo($file);
					return $pathinfo['basename'];	
				}
				//$file = self::tempname($dir, $rs['info'][$name]['suffix']);
				if (@copy($rs['info'][$name]['file'], $file)) {
					$pathinfo = pathinfo($file);
					@unlink($rs['info'][$name]['file']);
					//@unlink($file);
					//return array('basename' => $filename.'.'.$rs['info'][$name]['suffix'], 'filename' => $filename, 'suffix' => $rs['info'][$name]['suffix']);
					//return $rs['info'];
					return $pathinfo['basename'];
				}
				@unlink($rs['info'][$name]['file']);
			}
		}
		return false;
	}
	public static function uploadFile1($name, $dir, $suffix = ''){
		!file_exists($dir) && file::createFolder($dir);
		if (file_exists($dir)) {
			$upload = new self();
			$upload->suffix   = $suffix;
			$upload->insertDB = false;
			
			$rs = $upload->toupload($name);
			if ($rs['count'] == 1) {
				$var = qscms::v('_G');
				$hash = $var->sys_hash;
				$filename = self::uploadName($hash.urlencode($rs['info'][$name]['name']));
				$file = $dir.$filename.'.'.$rs['info'][$name]['suffix'];
				if (file_exists($file)) return false;
				//$file = self::tempname($dir, $rs['info'][$name]['suffix']);
				if (@copy($rs['info'][$name]['file'], $file)) {
					$pathinfo = pathinfo($file);
					@unlink($rs['info'][$name]['file']);
					//@unlink($file);
					return array('basename' => $filename.'.'.$rs['info'][$name]['suffix'], 'filename' => $filename, 'suffix' => $rs['info'][$name]['suffix']);
					//return $rs['info'];
					return $pathinfo['basename'];
				}
				@unlink($rs['info'][$name]['file']);
			}
		}
		return false;
	}
	public static function uploadImage($name, $dir, $returnArr = false, $saveName = '', $saveSuffix = ''){
		!file_exists($dir) && file::createFolder($dir);
		if (file_exists($dir)) {
			$upload = new self();
			$upload->suffix   = 'jpg|jpeg|png|gif|';
			$upload->insertDB = false;
			$rs = $upload->toupload($name);
			if ($rs['count'] == 1) {
				$sourceFile = $rs['info'][$name]['file'];
				$checkInfo = getimagesize($sourceFile);
				if ($checkInfo !== false) {
					if (in_array($checkInfo['mime'], array('image/jpeg', 'image/gif', 'image/png'))) {
						if (!$saveName) {
							$file = self::tempname($dir, $rs['info'][$name]['suffix']);
						} else $file = $dir.$saveName.'.'.$saveSuffix;
						if (@copy($sourceFile, $file)) {
							$pathinfo = pathinfo($file);
							@unlink($rs['info'][$name]['file']);
							if ($returnArr) {
								return array(
									'name'     => $rs['info'][$name]['name'],
									'size'     => $rs['info'][$name]['size'],
									'suffix'   => $rs['info'][$name]['suffix'],
									'basename' => $pathinfo['basename'],
									'filename' => $pathinfo['filename']
								);
							}
							return $pathinfo['basename'];
						}
						@unlink($rs['info'][$name]['file']);
					}
				}
			}
		}
		return false;
	}
	function write($id){
		if($line = db::fetchFirst("select * from ".db::table('cache_upload')." where id=$id")){
			$file = $this->uploadDir.$line['tmp_name'];
			if(file_exists($file)){
				echo file_get_contents($file);
			}
		}
	}
	function cache_count(){
		return db::data_count('cache_upload');
	}
	function clear_cache(){
		$query = db::query('SELECT id,tmp_name FROM `'.db::table('cache_upload').'` WHERE '.time::$timestamp.'-dateline>\'300\'');
		while($line = db::fetch($query)){
			@unlink($this->uploadDir.$line['tmp_name']);
			db::query("delete from ".db::table('cache_upload')." where id=$line[id]",'UNBUFFERED');
		}
	}
}
?>