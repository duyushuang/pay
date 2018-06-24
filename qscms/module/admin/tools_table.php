<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
!db::$connected && admin::show_message('数据库没有链接，不能操作！');
set_time_limit(0);
if ($tid = $var->getInt('gp_cusTableField')) {
	$table = table::getTable($tid);
	$table || admin::show_message('不存在该表');
	$tab = 'field';
} elseif ($tid = $var->getInt('gp_cusTableIndex')) {
	$table = table::getTable($tid);
	$table || admin::show_message('不存在该表');
	$tab = 'index';
}
else $tab = 'default';
switch ($tab) {
	case 'default':
		$top_menu = array(
	'index'          => '数据库表列表',
	'replace'        => '替换字符',
	'replaceAll'     => '批量替换字符',
	'info'           => array('name' => '表结构', 'hide' => true),
	'rename'         => array('name' => '更改表名', 'hide' => true),
	'copyTable'      => array('name' => '复制表', 'hide' => true),
			'cusTable'       => '自定义表',
			'cusTableCreate' => '创建自定义表',
			'cusTableEdit'   => array('name' => '修改自定义表', 'hide' => true)
		);
		$top_menu_key = array_keys($top_menu);
		($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
		$method || $method='index';
		switch($method){
			case 'index':
				if(form::is_form_hash()){
					if($del=$_POST['del']) {
						foreach($del as $v) {
							db::query('DROP TABLE `'.$v.'`');
						}
					}
					qscms::refresh();
				}
				$list    = array();
				$query   = db::query("SHOW TABLES", MYSQL_NUM);
				$pre_len = strlen(PRE);
				while($table0 = db::fetchArrayFirst($query)){
					//$table1=preg_replace("/^$pre/",'<span style="color:red">'.$pre.'</span>',$table0);
					if(substr($table0,0,$pre_len) == PRE) $table1='<span style="color:red">'.PRE.'</span>'.substr($table0,$pre_len);
					else $table1='<span style="color:green">'.$table0.'</span>';
					$list[]=array('table0'=>$table0,'table1'=>$table1);
				}
			break;
			case 'info':
				$table = $var->gp_table;
				if($line = db::fetchFirst("show create table `$table`")){
					$create_info = $line['Create Table'];
					/*if(preg_match('/^[^(]*\((.+)\)[^)]*$/s',$create_info,$matches)){
						if(preg_match('/^(.+?)((?:PRIMARY\s)?KEY\s\(.*?)?$/s',$matches[1],$matches)){
							print_r($matches);exit;
						}
					}*/
				}
			break;
			case 'rename':
				$name = $var->gp_table;
				if (form::hash()) {
					$data = form::get3('name');
					$newName = trim($data['name']);
					if ($newName && $newName != $name) {
						db::query("ALTER TABLE `$name` RENAME `$newName`");
						admin::show_message('修改成功', $referer);
					} else admin::show_message('表名不能为空，且不能同原名相同');
				}
			break;
			case 'replace':
				$table = $var->gp_table;
				$dbName = qscms::getCfgPath('/global/db_name');
				$source = $destination = '';
				if (form::is_form_hash()) {
					extract($_POST);
					if ($source && $destination) {
						if($line = db::fetchArrayFirstAll("SELECT COLUMN_NAME
		FROM  `information_schema`.`COLUMNS` 
		WHERE `TABLE_SCHEMA`='$dbName' AND `TABLE_NAME`='$table' AND (DATA_TYPE='varchar' OR DATA_TYPE='text' OR DATA_TYPE='char' OR DATA_TYPE='tinytext' OR DATA_TYPE='bigtext' OR DATA_TYPE='mediumtext')
		ORDER BY COLUMN_NAME;")){
							$rs = array();
							foreach ($line as $v) {
								$sql = "UPDATE `$table` SET `$v`=REPLACE(`$v`,'$source','$destination')";
								$rs[$v] = db::queryUnbuffered($sql);
							}
							$msg = '';
							foreach ($rs as $k => $v) {
								$msg && $msg .= '<br />';
								$msg .= $k.':更新了'.$v.'条';
							}
							$msg = '更新表《'.$table.'》中字段结果如下：<br />'.$msg;
							admin::show_message($msg);
						}
					} else {
						$info = '参数错误';
					}
				}
			break;
			case 'replaceAll':
				$dbName = qscms::getCfgPath('/global/db_name');
				$source = $destination = '';
				if (form::is_form_hash()) {
					extract($_POST);
					if ($source && $destination) {
						$config = qscms::getConfig('global');
						$query = db::query('SHOW TABLES LIKE \''.str_replace('_', '\_', PRE).'%\'');
						$rs = array();
						while ($tableName = db::fetchArrayFirst($query)) {
							if($line = db::fetchArrayFirstAll("SELECT COLUMN_NAME
		FROM  `information_schema`.`COLUMNS` 
		where `TABLE_SCHEMA`='$dbName' and `TABLE_NAME`='$tableName' and (DATA_TYPE='varchar' or DATA_TYPE='text' or DATA_TYPE='char' or DATA_TYPE='tinytext' or DATA_TYPE='bigtext' or DATA_TYPE='mediumtext')
		order by COLUMN_NAME;")){
								foreach ($line as $v) {
									$sql = "UPDATE `$tableName` SET `$v`=REPLACE(`$v`,'$source','$destination')";
									$rs[$tableName][$v] = db::queryUnbuffered($sql);
								}
							}
						}
						$msg = '';
						foreach ($rs as $k => $v) {
							$msg .= '表：'.$k;
							foreach ($v as $k2 => $v2) {
								$msg && $msg .= '<br />';
								$msg .= $k2.':更新了'.$v2.'条';
							}
							$msg .= '<br /><br />';
						}
						$msg = '所有表中字段替换结果如下：<br />'.$msg;
						admin::show_message($msg);
					} else {
						$info = '参数错误';
					}
				}
			break;
			case 'copyTable':
				!db::tableExists($var->gp_copy, false) && admin::show_message('数据库表：'.$var->gp_copy.'不存在');
				if (form::hash()) {
					$datas = form::get3('tableName', array('resetAuto', 'int'));
					if ($datas['tableName']) {
						db::tableExists($datas['tableName'], false) && admin::show_message('数据库表：'.$datas['tableName'].'已经存在了');
						$tableInfo = db::showCreateTable($var->gp_copy, false);
						if ($datas['resetAuto']) $tableInfo = preg_replace('/AUTO_INCREMENT=\d+/', 'AUTO_INCREMENT=1', $tableInfo);
						$tableInfo = str_replace('CREATE TABLE `'.$var->gp_copy.'`', 'CREATE TABLE `'.$datas['tableName'].'`', $tableInfo);
						db::query($tableInfo);
						admin::show_message('复制完毕');
					} else admin::show_message('参数错误');
				}
			break;
			case 'cusTable':
				if (form::hash()) {
					extract(form::get3('del', 'ids', 'sort'));
					if (!empty($del)) {
						$count = table::dropTable($del);
						admin::show_message('删除了'.$count.'个表', $baseUrl.'&method='.$method);
					} else {
						$count = form::arrayEqual($ids, $sort);
						if ($count) {
							for ($i = 0; $i < $count; $i++) {
								$id = $ids[$i];
								$st = $sort[$i];
								db::update('sys_table', "sort='$st'", "id='$id'");
							}
							admin::show_message('设置排序成功', $baseUrl.'&method='.$method);
						}
					}
				}
				$list = db::select('sys_table', 'id,sort,name,fieldCount', '', 'sort,id');
			break;
			case 'cusTableCreate':
				$tables = table::getTables();
				if (form::hash()) {
					$rs = table::createTable($_POST);
					if ($rs === true) {
						admin::show_message('创建成功', $baseUrl.'&method=cusTable');
					} else admin::show_message('创建失败：<br >'.$rs);
				}
			break;
			case 'cusTableEdit':
				$tables = table::getTables();
				$id = $var->getInt('gp_id');
				$datas = db::one('sys_table', '*', "id='$id'");
				$datas || admin::show_message('不存在该表');
				if (form::hash()) {
					$rs = table::modifyTable($_POST, $id);
					if ($rs === true) admin::show_message('修改成功', $baseUrl.'&method=cusTable');
					else admin::show_message('修改失败：<br >'.$rs);
				}
			break;
		}
	break;
	case 'field':
		$menu_sub_name = '自定义表：'.$table['name'].' 字段管理';
		$top_menu = array(
			'_1'   => array('name' => '返回自定义表列表', 'url' => $baseUrl.'&method=cusTable', 'hide' => false),
			'list' => '字段列表',
			'add'  => '添加字段',
			'edit' => array('name' => '修改字段', 'hide' => true)
		);
		$top_menu_key = array_keys($top_menu);
		($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[1];
		$baseUrl .= '&cusTableField='.$tid;
		switch ($method) {
			case 'list':
				if (form::hash()) {
					extract(form::get3('del', 'sort', 'ids'));
					if (!empty($del)) {
						admin::show_message('删除了'.table::dropField($del).'个字段');
					} else {
						table::setSort($tid, $ids, $sort);
						admin::show_message('设置排序完毕', $baseUrl.'&method=list');
					}
				}
				$list = table::getFields($tid);
			break;
			case 'add':
				if (form::hash()) {
					$rs = table::addField($_POST, $tid);
					if ($rs === true) admin::show_message('添加成功', $baseUrl.'&method=list');
					else admin::show_message('添加失败：<br >'.$rs);
				}
			break;
			case 'edit':
				$fid = $var->getInt('gp_fid');
				$datas = table::getField($fid);
				$datas || admin::show_message('不存在该字段');
				$update = true;
				if (form::hash()) {
					$rs = table::editField($_POST, $fid);
					if ($rs === true) admin::show_message('修改成功', $baseUrl.'&method=list');
					else admin::show_message('修改失败：<br >'.$rs);
				}
			break;
		}
	break;
	case 'index':
		$menu_sub_name = '自定义表：'.$table['name'].' 索引管理';
		$top_menu = array(
			'_1'   => array('name' => '返回自定义表列表', 'url' => $baseUrl.'&method=cusTable', 'hide' => false),
			'list' => '索引列表',
			'add'  => '添加索引',
			'edit' => array('name' => '修改索引', 'hide' => true)
		);
		$top_menu_key = array_keys($top_menu);
		($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[1];
		$baseUrl .= '&cusTableIndex='.$tid;
		switch ($method) {
			case 'list':
				if (form::is_form_hash()) {
					extract(form::get3('del', 'sort', 'ids'));
					if ($del) {
						//删除
						
						admin::show_message('删除了'.table::dropIndex($tid, $del).'个索引', $baseUrl.'&method=list');
					} else {
						table::indexSetSort($ids, $sort);
						admin::show_message('更新排序完毕！', $referer);
					}
				}
				$list = table::getIndexs($tid);
			break;
			case 'add':
				if (form::hash()) {
					$rs = table::createIndex($tid, $_POST);
					if ($rs === true) {
						admin::show_message('添加成功', $baseUrl.'&method=list');
					} else admin::show_message('添加失败<br />'.$rs);
				}
			break;
			case 'edit':
				$iid = $var->getInt('gp_iid');
				$datas = table::getIndex($iid);
				$datas || admin::show_message('该索引不存在');
				$update = true;
				if (form::hash()) {
					$rs = table::modifyIndex($iid, $_POST);
					if ($rs === true) admin::show_message('修改成功', $baseUrl.'&method=list');
					else admin::show_message('修改失败：<br />'.$rs);
				}
			break;
		}
	break;
}
?>