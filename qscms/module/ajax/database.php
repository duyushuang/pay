<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
include_once(m(ADMIN_FOLDER.'/module/ini'));
loadFunc('ajax');
$rs = ajax_return_false();
set_time_limit(0);
$preFix = 'version:1.0;©2013 www.qscms.com;author:373718549@qq.com';
$preFixLen = strlen($preFix);
$var->preFixLen = $preFixLen;
function writeData($f, $data){
	$len = strlen($data);
	fwrite($f, sprintf('% 10d', $len));
	fwrite($f, $data);
}
function dataSeek($f, $offset, $whence = SEEK_SET){
	$preFixLen = qscms::v('_G')->preFixLen;
	if ($whence == SEEK_SET) {
		if (!$offset) {
			fseek($f, $preFixLen + 10);
			return;
		}
		fseek($f, $preFixLen + 10);
		$index = 0;
		do {
			if (feof($f)) break;
			$count = intval(trim(fread($f, 10)));
			fseek($f, $count, SEEK_CUR);
			$index++;
		} while ($index < $offset);
	} elseif ($whence == SEEK_CUR) {
		$index = 0;
		do {
			if (feof($f)) break;
			$count = intval(trim(fread($f, 10)));
			fseek($f, $count, SEEK_CUR);
			$index++;
		} while ($index < $offset);
	}
}
loadFunc('database');
if (qscms::defineTrue('IN_ADMIN')) {
	switch ($var->v0) {
		case 'export':
			$size = 30000;
			$table = $var->gp_table;
			$isLast = $var->getBoolean('gp_isLast');
			if ($table) {
				$backupType = $var->getInt('gp_backupType');
				$saveName   = $var->gp_saveName;
				$backupDir = d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/data').'sql/backup/');
				if ($backupType == 2) {
					$backupDir = md('./install/');
					$saveName = 'install';
				}
				$ln = chr(10);
				$savePath = $backupDir;
				$saveFile = '';
				file::createFolder($savePath);
				$saveName && $saveFile = $savePath.$saveName.'.sql';
				//********************************
				if ($saveFile) {
					$start = $var->getInt('gp_start');
					$isId  = false;//判断数据库中是否有ID
					$count = db::data_count($table, '', false);
					$c = 0;//备份数据条数
					$preFix = 'version:1.0;©2013 www.qscms.com;author:373718549@qq.com';
					$preFixLen = strlen($preFix);
					$tableIndex = $var->getInt('gp_tableIndex');
					if ($f = file::fopen($saveFile, !$tableIndex && !$start ? 'wb' : 'a+')) {
						fseek($f, $preFixLen);
						$nowCount = trim(fread($f, 10));
						fseek($f, 0, SEEK_END);
						qscms::setType($nowCount, 'int');
						/*
						 * 开始备份
						 */
						$status = true;
						$error  = '';
						if ($start == 0) {//备份结构先
							if (db::tableExists($table, false)) {
								$tableCreate = db::showCreateTable($table, false);
								if (!$tableIndex) fwrite($f, $preFix.str_repeat(' ', 10));
								writeData($f, 'DROP TABLE IF EXISTS `'.($backupType == 2 ? str_replace(PRE, '{pre}', $table) : $table));
								writeData($f, ($backupType == 2 ? str_replace(PRE, '{pre}', $tableCreate) : $tableCreate));
								$isId = strpos($tableCreate, '`id`') !== false;
								$nowCount += 2;
							} else {
								$status = false;
								$error  = '数据库表['.$table.']不存在';
							}
						} else $isId = $var->getBoolean('gp_isId');
						if ($status) {
							/*if ($isId) {
								$query = db::query("SELECT * FROM `$table` WHERE id>=(SELECT id FROM `$table` ORDER BY id LIMIT $start,1) ORDER BY id LIMIT $size");
							} else {
								$query = db::query("SELECT * FROM `$table` LIMIT $start,$size");
							}*/
							$query = db::query("SELECT * FROM `$table` LIMIT $start,$size");
							//$fieldsName = db::getFieldNames($query);
							$insertOne = true;
							while($line = db::fetch($query)){
								$c++;
								foreach($line as $k2 => $v2){
									if(is_numeric($v2)){
										$line[$k2] = '\''.$v2.'\'';
									} else {
										//$hex = string::str_hex($v2);
										//if($hex != '')$line[$k2] = '0x'.$hex;
										//else $line[$k2] = '\'\'';
										$line[$k2] = '\''.qscms::addcslashes($v2).'\'';
									}
								}
								writeData($f, 'INSERT INTO `'.($backupType == 2 ? str_replace(PRE, '{pre}', $table) : $table).'` VALUES('.implode(",",$line).')');
								$insertOne = false;
							}
							if ($c > 0) {
								$nowCount += $c;
							}
							$nowCount = sprintf('% 10d', $nowCount);
							fseek($f, $preFixLen);
							fwrite($f, $nowCount);
						}
						@fclose($f);
						if ($status) {
							if ($start + $c == $count) {
								$rs = ajax_return_true(array('complate' => true, 'count' => $c));
								if ($isLast && $backupType == 2) {//备份触发器
									if ($f = file::fopen($saveFile, 'a+')) {
										$count = db_getCount($f);
										fseek($f, 0, SEEK_END);
										$list = db::fetchAll('SHOW TRIGGERS LIKE \''.str_replace('_', '\_', PRE).'%\'');
										$list = qscms::arrid($list, 'Trigger');
										foreach ($list as $v) {
											foreach (db_getTrigger($v, true) as $v1) {
												db_writeData($f, $v1);
												$count++;
											}
										}
										db_writeCount($f, $count);
										fclose($f);
									}
								}
							} else {
								$rs = ajax_return_true(array('complate' => false, 'count' => $c, 'isId' => $isId));
							}
						} else $rs = ajax_return_false($error);
						/*
						 * 备份结束
						 */
					} else $rs = ajax_return_false('打开备份文件失败，检查路径是否有权限');
				} else $rs = ajax_return_false('参数错误');
			} else $rs = ajax_return_false('参数错误');
		break;
		case 'dataCount':
			$table = $var->gp_table;
			$count = 0;
			if ($table) $count = db::data_count($table, '', false);
			$rs = ajax_return_true(array('count' => $count));
		break;
		case 'import':
			$import    = $var->gp_import;
			$importSize = 10000;
			if ($import) {
				$backupDir = d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/data').'sql/backup/');
				$sqlFile = $backupDir.$import;
				$start = $var->getInt('gp_start');
				$seek  = $var->getInt('gp_seek');
				if (file_exists($sqlFile)) {
					if ($f = @fopen($sqlFile, 'rb')) {
						fseek($f, $preFixLen);
						$allCount = intval(trim(fread($f, 10)));
						if ($seek) fseek($f, $seek);
						else dataSeek($f, $start);
						$c = 0;
						for ($i = 0; $i < $importSize; $i++) {
							$size = intval(trim(fread($f, 10)));
							$sql = fread($f, $size);
							//echo $sql, "\r\n\r\n\r\n\r\n\r\n";
							db::queryUnbuffered($sql);
							$start++;
							$c++;
							if ($start >= $allCount) break;
						}
						$seek = ftell($f);
						@fclose($f);
						$rs = ajax_return_true(array('complate' => $start == $allCount ? true : false, 'count' => $c, 'seek' => $seek));
					} else $rs = ajax_return_false('打开数据库文件失败，请检查是否具备权限');
				} else $rs = ajax_return_false('参数错误');
			} else $rs = ajax_return_false('参数错误');
		break;
	}
}
echo string::json_encode($rs);
exit;
?>