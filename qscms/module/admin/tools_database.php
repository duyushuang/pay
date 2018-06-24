<?php
(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
$top_menu=array(
	'sql'     => 'SQL工具',
	'import'  => '导入备份',
	'export'  => '备份数据',
	'exportTrigger' => '备份触发器',
	'import2' => array('name'   => '导入本地备份', 'hide' => true),
	'exportRun' => array('name' => '开始备份',     'hide' => true)
);
$top_menu_key = array_keys($top_menu);
($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
$method || $method='sql';
$backupDir = d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/data').'sql/backup/');
set_time_limit(0);
if($download = $var->gp_download){
	$file = $backupDir.$download;
	file::download($file);
	exit;
}
loadFunc('database');
function importFile($sqlFile, $del = false, $offsetLine = 0){
	/*$ln = chr(10);
	$count = 0;
	if($f = fopen($sqlFile, 'rb')){
		$sql = '';
		while (!feof($f)) {
			$str = fgets($f);
			if (substr($str, -2)==';'.$ln) {
				$sql .= substr($str, 0, -2);
				$count++;
				$count > $offsetLine && db::query($sql);
				$sql = '';
			} else {
				$sql .= $str;
			}
		}
		fclose($f);
		$del && @unlink($sqlFile);
		return true;
	}
	return false;*/
	if ($f = db_openFile($sqlFile)) {
		$count = db_getCount($f);
		$readLen = ftell($f);
		fseek($f, 0, SEEK_END);
		$fsize = ftell($f);
		fseek($f, $readLen);
		$c = 0;
		while ($readLen < $fsize) {
			$size = intval(trim(fread($f, 10)));
			$readLen += $size + 10;
			$sql = fread($f, $size);
			$c++;
			$c > $offsetLine && db::query($sql);
		}
		fclose($f);
		$del && @unlink($sqlFile);
		return true;
	}
}
switch($method){
	case 'sql':
		if(($sql = $var->gp_sql) && db::$connected === true){
			checkWrite();
			db::setVar('query_halt', false);
			$sql = trim(stripslashes($sql));
			$sql_list = array();
			$sp = string::str_split($sql);
			$len = count($sp);
			$delimiter = ';';
			$delimiterLen = mb_strlen($delimiter);
			$delimiterArr = string::str_split($delimiter);
			$str = '';
			$strLen = 0;
			$inDelimiter = false;
			$flagI = 0;
			$flagArr = array();
			for ($i = 0; $i < $len; $i++) {
				$isEnd = $i + 1 == $len;
				$s = $sp[$i];
				if ($str || !in_array($s, array("\r", "\n", "\t"))) {
					if ($inDelimiter) {
						if (in_array($s, array("\r", "\n", ' ', "\t")) || $isEnd) {//echo 'delimiter:'.$str, "\r\n";
							$delimiter = $str;
							$delimiterLen = mb_strlen($delimiter);
							$delimiterArr = string::str_split($delimiter);
							$str = '';
							$inDelimiter = false;
						} else $str .= $s;
					} else {
						$str .= $s;
						$strLen++;
						$flagI++;
						$flagArr[] = strtoupper($s);
						if ($flagI == $delimiterLen) {//标记长度一致
							if ($flagArr == $delimiterArr) {//匹配
								$flagI = 0;
								$flagArr = array();
								$sql_list[] = mb_substr($str, 0, $strLen - $delimiterLen);//echo 'sql:', mb_substr($str, 0, $strLen - $delimiterLen), "\r\n";
								$str = '';
								$strLen = 0;
								continue;
							} else {
								$flagI--;
								array_shift($flagArr);
							}
						}
						//echo 's:', $str, '|', $strLen, "\r\n";
						if ($strLen == 10 && strtoupper($str) == 'DELIMITER ') {//echo 'in delimiter', "\r\n";
							$inDelimiter = true;
							$str = '';
							$strLen = 0;
							$flagI = 0;
							$flagArr = array();
							$delimiter = '';
							$delimiterLen = 0;
							$delimiterArr = array();
						}
						if ($isEnd) $sql_list[] = $str;
					}
				}
			}
			//$sql_list=explode(';',$sql);
			$show_result = '';
			foreach($sql_list as $run){
				if($run=trim($run)){
					if(($s=strpos($run,' '))!==false){
						$sql_pre=strtoupper(substr($run,0,$s));
						$show=false;
						if ($sql_pre == 'EXPLAIN') $sql_pre = 'SELECT';
						switch($sql_pre){
							case 'USE':
								db::query($run);
							break;
							case 'SELECT':
								$query = db::query($run);
								if($rs_count = db::queryRows($query)){
									$fields_name = db::getFieldNames($query);
									$columns_count=count($fields_name);
									$show_result.='<table class="table table-bordered table-striped"><tr>';
									foreach($fields_name as $v){
										$show_result.='<td>'.$v.'</td>';
									}
									$show_result.='</tr>';
									while($line = db::fetch($query)){
										//$show_result.="\n";
										//$show_result.=implode("\t",$line);
										$show_result.='<tr>';
										foreach($line as $v){
											$show_result.='<td>'.$v.'</td>';
										}
										$show_result.='</tr>';
									}
									//$show_result.="\n\n共：$rs_count 条";
									$show_result.='<tr><td colspan="'.$columns_count.'">共：'.$rs_count.' 条</td></tr></table>';
								} else {
									$show_result.='<table class="table table-bordered table-striped"><tr><td>共：0 条</td></tr></table>';
								}
							break;
							case 'SHOW':
								$query=db::query($run);
								if($rs_count=db::queryRows($query)){
									$fields_name = db::getFieldNames($query);
									$columns_count = count($fields_name);
									$show_result.='<table class="table table-bordered table-striped" style="white-space: pre"><tr>';
									foreach($fields_name as $v){
										$show_result.='<td>'.$v.'</td>';
									}
									$show_result.='</tr>';
									while($line = db::fetch($query)){
										//$show_result.="\n";
										//$show_result.=implode("\t",$line);
										$show_result.='<tr>';
										foreach($line as $v){
											$show_result.='<td>'.$v.'</td>';
										}
										$show_result.='</tr>';
									}
									//$show_result.="\n\n共：$rs_count 条";
									$show_result.='<tr><td colspan="'.$columns_count.'">共：'.$rs_count.' 条</td></tr></table>';
								} else {
									$show_result.='<table class="table table-bordered table-striped"><tr><td>共：0 条</td></tr></table>';
								}
							break;
							case 'INSERT':
								db::query($run);
								$show_result.='<table class="table table-bordered table-striped"><tr><td>共影响：'.db::affectedRows().' 条</td></tr></table>';
							break;
							case 'UPDATE':
								$show = false;
								db::query($run);
								$show_result.='<table class="table table-bordered table-striped"><tr><td>共影响：'.db::affectedRows().' 条</td></tr></table>';
							break;
							case 'DELETE':
								db::query($run);
								$show_result.='<table class="table table-bordered table-striped"><tr><td>删除了：'.db::affectedRows().' 条数据</td></tr></table>';
							break;
							default:
								db::query($run);
							break;
						}
						if($errno=db::errno()){
							$show_result.='<table class="table table-bordered table-striped"><tr><td>'.$errno.'：'.db::error().'</td></tr></table>';
						}
						switch($sql_pre){
							case 'CREATE':
								if($errno)$show_result.='<table class="table table-bordered table-striped"><tr><td>创建表失败</td></tr></table>';
								else $show_result.='<table class="table table-bordered table-striped"><tr><td>创建表成功</td></tr></table>';
							break;
						}
					}
				}
			}
			if ($var->menuAjax) {
				echo $show_result;
				exit;
			}
		}
		$sqlList1 = $sqlList2 = array();
		foreach (db::select('sql_log', '*', '', 'timestamp DESC') as $line) {
			$sqlList1[$line['id']] = $line['sql'];
			$sqlList2[$line['id']] = $line['name'];
		}
	break;
	case 'import':
		if($del = $var->gp_del){
			checkWrite();
			@unlink($backupDir.$del);
		}
		$import   = $var->gp_import;
		$complate = $var->gp_complate;
		$offsetLine = $var->getInt('gp_offsetLine');
		if(!$import&&!$complate){
			checkWrite();
			$sql_file_list = array();
			if($list=file::listFiles($backupDir)){
				foreach($list as $v){
					$file = $backupDir.$v;
					$sql_file_list[]=array('name'=>$v,'file_size'=>filesize($file),'filemtime'=>date('Y-m-d H:is',filemtime($file)));
				}
			}
		} elseif(!$complate) {
			checkWrite();
			if(db::$connected === true){
				$ln = chr(10);
				$preFix = 'version:1.0;©2013 www.qscms.com;author:373718549@qq.com';
				$preFixLen = strlen($preFix);
				($encoding = $var->gp_encoding) || ($encoding = ENCODING);
				$sqlFile = $backupDir.$import;
				/*if (!importFile($sqlFile, false, $offsetLine)) {
					admin::show_message('读取SQL文件失败');
				}*/
				if($f = fopen($sqlFile, 'rb')){
					fseek($f, $preFixLen);
					$count = trim(fread($f, 10));
					//qscms::setType($count, 'int');
					fclose($f);
				} else admin::show_message('读取文件失败，请检查是否有权限');
			}
			//qscms::gotoUrl(NOW_URL.'&complate=true',true);
		}
	break;
	case 'import2':
		//$complate = false;
		$upload = new upload();
		if (form::is_form_hash()) {
			if ($backupFile = upload::getFile('file', 'sql|')) {
				if (importFile($backupFile, true)) {
					//$complate = true;
					admin::show_message('导入成功', NOW_URL);
				} else {
					admin::show_message('导入失败，请检查上传的文件是否正确');
				}
			} else {
				admin::show_message('上传备份文件失败，请重试！');
			}
		}
	break;
	case 'export':
		//if($var->postData && (!empty($_POST['save_name']) && ($save_name = $_POST['save_name'])) && (!empty($_POST['backup_tables']) && ($tables = $_POST['backup_tables']))){
		if (form::is_form_hash()) {
			checkWrite();
			extract(form::get3(array('backupType', 'int'), 'save_name', 'backup_tables'));
			if (is_array($backup_tables) && count($backup_tables) > 0) {
				$tables = $backup_tables;
				if ($backupType == 2) {
					$backupDir = md('./install/');
					$save_name = 'install';
				}
				$ln = chr(10);
				$savePath = $backupDir;
				file::createFolder($savePath);
				$saveFile = $savePath.$save_name.'.sql';
				$f = fopen($saveFile, 'wb');
				$oneCount = 100;
				foreach($tables as $v){
					$rs = db::fetchFirst("SHOW CREATE TABLE `$v`");
					if($rs['Create Table']){
						fwrite($f, 'DROP TABLE IF EXISTS `'.($backupType == 2 ? str_replace(PRE, '{pre}', $rs['Table']) : $rs['Table']).'`;'.$ln);
						fwrite($f, ($backupType == 2 ? str_replace(PRE, '{pre}', $rs['Create Table']) : $rs['Create Table']).';'.$ln);
						$isId = strpos($rs['Create Table'], '`id`') !== false;
						//db::query('LOCK TABLE `'.$v.'` READ');
						if(($count = intval(db::resultFirst("SELECT COUNT(*) FROM `$v`"))) > 0){
							$readCount = 0;
							while ($readCount < $count) {
								if ($isId) {
									$query = db::query("SELECT * FROM `$v` WHERE id>=(SELECT id FROM `$v` ORDER BY id LIMIT $readCount,1) ORDER BY id LIMIT $oneCount");
								} else {
									$query = db::query("SELECT * FROM `$v` LIMIT $readCount,$oneCount");
								}
								$fields_name = db::getFieldNames($query);
								$insert_one = true;
								while($line = db::fetch($query)){
									$readCount++;
									foreach($line as $k2 => $v2){
										if(is_numeric($v2)){
											$line[$k2] = '\''.$v2.'\'';
										} else {
											$hex = string::str_hex($v2);
											if($hex != '')$line[$k2] = '0x'.$hex;
											else $line[$k2] = '\'\'';
										}
									}
									fwrite($f, 'INSERT INTO `'.($backupType == 2 ? str_replace(PRE, '{pre}', $v) : $v).'` VALUES('.implode(",",$line).');'.$ln);
									$insert_one = false;
								}
							}
						}
						//db::query('UNLOCK TABLES');
					}
				}
			}
			fclose($f);
			qscms::gotoUrl(NOW_URL.'&complate='.base64_encode($save_name).'&backupType='.$backupType, true);
		}
		if(!($complate = $var->gp_complate)){
			checkWrite();
			$tables = array();
			$query = db::query('SHOW TABLES');
			while($line = db::fetch($query, MYSQL_NUM)){
				$tables[] = $line[0];
			}
		} else {
			$complate = base64_decode($complate);
			$backupType = $var->getInt('gp_backupType');
		}
	break;
	case 'exportRun':
		$tables = array();
		$saveFile = '';
		if (form::is_form_hash()) {
			checkWrite();
			extract(form::get3(array('backupType', 'int'), 'save_name', 'backup_tables'));
			if (is_array($backup_tables) && count($backup_tables) > 0) {
				$tables = $backup_tables;
				if ($backupType == 2) {
					$backupDir = md('./install/');
					$save_name = 'install';
				}
				$ln = chr(10);
				$savePath = $backupDir;
				file::createFolder($savePath);
				$save_name && $saveFile = $savePath.$save_name.'.sql';
			}
		}
		if (!$tables || !$saveFile) admin::show_message('请正确操作备份！');
	break;
	case 'exportTrigger':
		$complate = $var->gp_complate;
		if (form::hash()) {
			extract(form::get3('saveName', 'backup'));
			if ($saveName && $backup) {
				$saveFile = $backupDir.$saveName.'.trigger.sql';
				if ($f = file::fopen($saveFile, 'wb')) {
					db_writeHeader($f);
					$count = 0;
					foreach ($backup as $v) {
						foreach (db_getTrigger($v) as $v1) {
							db_writeData($f, $v1);
							$count++;
						}
					}
					db_writeCount($f, $count);
					fclose($f);
					qscms::gotoUrl($baseUrl.'&method='.$method.'&complate='.$saveName, true);
				} else admin::show_message('创建文件失败，请检查是否有权限');
			}
		}
		$list = db::fetchAll('SHOW TRIGGERS');
		$list = qscms::arrid($list, 'Trigger');
	break;
}
?>