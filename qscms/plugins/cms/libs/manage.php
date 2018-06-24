<?php
!defined('IN_QS_PLUGIN') && IN_QS_PLUGIN !== true && exit('error');
//global $list, $multipage;
empty($tab) && $tab = 'model';
if ($fields = $var->getInt('gp_fields') ) $tab = 'field';
elseif ($index = $var->getInt('gp_index') ) $tab = 'index';
switch ($tab) {
	case 'model':
		$top_menu = array(
			'index'  => '文章模型列表',
			'add'    => '添加模型',
			'edit'   => array('name' => '编辑模型', 'hide' => true)
		);
		if ($backup = $var->getInt('gp_backup') ) {
			if ($this->saveModel($backup)) {
				admin::show_message('创建成功！！');
			} else {
				admin::show_message('对不起，创建失败，请检查模块是否存在！');
			}
		}
		if ($edit = $var->getInt('gp_edit') ) $var->gp_method = 'edit';
		$top_menu_key = array_keys($top_menu);
		(($method = $var->gp_method) && in_array($method, $top_menu_key)) || $method = $top_menu_key[0];
		switch ($method) {
			case 'index':
				if(form::is_form_hash()){
					extract($_POST);
					if($del || $sort){
						if($del){
							//删除模块
							$ids = implode(',', $del);
							$query = db::query("select * from {$pre}cms_model where id in ($ids)");
							while ($line = db::fetch($query)) {
								db::querys("
									drop table if exists `{$pre}cms_{$line[ename]}_cate`;
									drop table if exists `{$pre}cms_{$line[ename]}`;
									DELETE FROM `{$pre}cms_model_fields` WHERE mid='$line[id]';
									DELETE FROM `{$pre}cms_model_index` WHERE mid='$line[id]'
								");
								$parentMenuEname = b_nav::getEname($line['parentMenuId']);
								$menuEname       = b_nav::getEname($line['menuId']);
								b_nav::del2($menuEname, $parentMenuEname);
								@unlink(qd('./module/'.ADMIN_FOLDER.'/'.$parentMenuEname.'_'.$menuEname.'.php'));
								@unlink(qd(qscms::getCfgPath('/system/tplRoot').'admin/'.$parentMenuEname.'_'.$menuEname.'.htm'));
							}
							db::del_ids('cms_model', $del);
							admin::show_message('删除成功', $baseUrl);
						} elseif($sort && $ids) {
							if($count = form::array_equal($ids, $sort)){
								for($i=0; $i<$count; $i++){
									$id  = $ids[$i];
									$sid = $sort[$i];
									db::update($tbName, array('sort'=>$sid),"id='$id'");
								}
								admin::show_message('更新成功', $baseUrl.'&page='.$page);
							}
						}
					}
				}
				$list = array();
				if($total = db::data_count('cms_model')){
					$list = db::get_list2('cms_model', '*', '', 'sort,addTime desc', $page, $pagesize);
					$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.($method?'&method='.$method:'').'&page={page}', $pagestyle);
				}
			break;
			case 'add':
				$models = $this->getModels();
				$type = $name = $ename = '';
				if (form::is_form_hash()) {
					$datas = form::get2('type', 'name', 'ename', array('parentMenuId', 'int'));
					$datas && ($datas = qscms::filterHtml($datas)) && extract($datas);
					if ($name && $ename && $parentMenuId) {
						if ($type) {
							if (empty($models[$type])) admin::show_message('所选模块类型不存在！！');
							$installSql = file::read($this->root.'install.model.'.$type.'.sql');
							$filename = 'cms_'.$type;
						} else {
							$filename = 'cms';
							$installSql = file::read($this->root.'install.model.sql');
						}
						
						if (!db::exists('cms_model', array('ename' => $ename))) {
							if ($parentMenuName = b_nav::getName($parentMenuId)) {
								$parentMenuEname = b_nav::getEname($parentMenuId);
								if ($menuId = b_nav::add2($name, $ename, $parentMenuEname)) {
									if ($modelId = db::insert('cms_model', array(
										'name'           => $name,
										'ename'          => $ename,
										'menuId'         => $menuId,
										'menuName'       => $name,
										'parentMenuId'   => $parentMenuId,
										'parentMenuName' => $parentMenuName,
										'addTime'        => $timestamp
									), true)) {
										$installSql = qscms::replaceVars($installSql, array('pre' => PRE, 'modelName' => $ename, 'modelId' => $modelId, 'timestamp' => $timestamp));
										db::querys($installSql);
										$vars = array('modelName' => $ename, 'modelAlias' => $name);
										$str = file::read($pluginRoot.'libs'.D.$filename.'.php');
										$str = qscms::replaceVars($str, $vars);
										file::write(qd('./module/'.ADMIN_FOLDER.'/'.$parentMenuEname.'_'.$ename.'.php'), $str);
										
										$str = file::read($pluginRoot.'templates'.D.$filename.'.htm');
										$str = qscms::replaceVars($str, $vars);
										file::write(qd(qscms::getCfgPath('/system/tplRoot').'admin/'.$parentMenuEname.'_'.$ename.'.htm'), $str);
										admin::show_message('添加成功！', $baseUrl);
									}
									//echo db::error();exit;
								} else {
									admin::show_message('添加菜单失败！！');
								}
							} else {
								admin::show_message('获取上级菜单失败！');
							}
						} else {
							admin::show_message('对不起，该模块已经存在！！');
						}
					} else {
						admin::show_message('参数错误！！');
					}
				}
				$listMenus = b_nav::getMenus();
			break;
			case 'edit':
				$update = false;
				if ($item = db::one('cms_model', 'name,ename,menuId,parentMenuId', "id='$edit'")) {
					if (form::is_form_hash()) {
						$datas = form::get2('name', 'ename', array('parentMenuId', 'int'));
						$datas && ($datas = qscms::filterHtml($datas)) && extract($datas);
						if ($name && $ename && $parentMenuId) {
							if ($name != $item['name'] || $ename != $item['ename'] || $parentMenuId != (int)$item['parentMenuId']) {
								if (!db::exists('cms_model', "ename='$ename' and id<>'$edit'")) {
									//if ($parentMenuId != $item['parentMenuId']) {
										if ($parentMenuName = b_nav::getName($parentMenuId)) {
											$parentMenuEname = b_nav::getEname($parentMenuId);
											if ($menuId = b_nav::add2($name, $ename, $parentMenuEname)) {
												if (db::update('cms_model', array(
													'name'           => $name,
													'ename'          => $ename,
													'menuId'         => $menuId,
													'menuName'       => $name,
													'parentMenuId'   => $parentMenuId,
													'parentMenuName' => $parentMenuName,
													'lastEditTime'   => $timestamp
												), "id='$edit'")) {
													$tmpName = b_nav::getEname($item['parentMenuId']).'_'.$item['ename'];
													//b_nav::del($item['parentMenuId']);
													b_nav::del($item['menuId']);
													$oldFile = qd('./module/'.ADMIN_FOLDER.'/'.$tmpName.'.php');
													$newFile = qd('./module/'.ADMIN_FOLDER.'/'.$parentMenuEname.'_'.$ename.'.php');
													if (!@rename($oldFile, $newFile)) {
														admin::show_message('很抱歉，文件重命名失败，请检查服务器是否有写的权限。<br />旧文件：'.$oldFile.'<br />新文件：'.$newFile);
													}
													$oldFile = qd(qscms::getCfgPath('/system/tplRoot').'admin/'.$tmpName.'.htm');
													$newFile = qd(qscms::getCfgPath('/system/tplRoot').'admin/'.$parentMenuEname.'_'.$ename.'.htm');
													if (!@rename($oldFile, $newFile)) {
														admin::show_message('很抱歉，文件重命名失败，请检查服务器是否有写的权限。<br />旧文件：'.$oldFile.'<br />新文件：'.$newFile);
													}
													admin::show_message('修改成功！', $baseUrl);
												}
											} else {
												admin::show_message('添加菜单失败！！');
											}
										} else {
											admin::show_message('获取上级菜单失败！');
										}
									
								} else {
									admin::show_message('对不起，该模块已经存在！！');
								}
							} else {
								admin::show_message('没有任何修改！', $baseUrl);
							}
						} else {
							admin::show_message('参数错误！！');
						}
					}
					$listMenus = b_nav::getMenus();
					extract($item);
				} else admin::show_message('很抱歉，你要修改的模型不存在！');
			break;
		}
	break;
	case 'field':
		$mid      = $fields;
		$model    = db::one('cms_model', '*', "id='$mid'");
		$cmsTable = 'cms_'.$model['ename'];
		$top_menu = array(
			'index' => array('name' => '返回文章模型', 'hide' => false, 'url' => $baseUrl),
			'list'  => $model['name'].'字段列表',
			'addField'   => '添加'.$model['name'].'字段',
			'editField'  => array('name' => '编辑'.$model['name'].'字段', 'hide' => true)
		);
		$baseUrl .= '&fields='.$fields;
		if ($editField = $var->getInt('gp_editField') ) $method = 'editField';
		$top_menu_key = array_keys($top_menu);
		($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[1];
		switch ($method) {
			case 'list':
				if (form::is_form_hash()) {
					extract(form::get3('del', 'sort', 'ids'));
					if ($del) {
						//删除
						$ids = '\'' . implode('\',\'', $del) . '\'';
						foreach (db::select('cms_model_fields', 'fieldName,htmlType,imageWidth,imageHeight', "mid='$mid' and id in($ids)") as $v) {
							$sql = "ALTER TABLE `".PRE."$cmsTable` DROP `$v[fieldName]`";
							db::query($sql);
							if ($v['htmlType'] == 'image' && ($v['imageWidth'] || $v['imageHeight'])) {
								$sql = "ALTER TABLE `".PRE."$cmsTable` DROP `{$v[fieldName]}_thumb`";
								db::query($sql);
							}
						}
						db::del_ids('cms_model_fields', $del);
						admin::show_message('删除成功', $baseUrl.'&method=list');
					} else {
						//排序
						/*$newIds = $ids;
						array_multisort($sort, SORT_ASC, SORT_NUMERIC, $newIds);
						$oldFields = db::select('cms_model_fields', 'id,fieldName,fieldType,sort', "mid='$mid'", 'sort');
						$oldIds = qscms::arrid($oldFields, 'id');
						$oldIdsStr = implode(',', $oldIds);
						$newIdsStr = implode(',', $newIds);
						if ($oldIdsStr != $newIdsStr) {
							$count = count($oldIds);
							for ($i = 0; $i < $count; $i ++) {
								$count1 = $count - $i;
								$lastEqual = false;
								for ($j = 0; $j < $count1; $j ++) {
									$k = $j + $i;
									if ($newIds[$j] != $oldIds[$k]) {
										if ($lastEqual) break;
									} else {
										$lastEqual = true;
									}
									if ($newIds[$j] != $oldIds[$k] && $lastEqual) break;
									else $lastEqual = true;
								}
							}
							exit;
						}*/
						if ($count = form::array_equal($ids, $sort)) {
							for ($i = 0; $i < $count; $i++) {
								$id = $ids[$i];
								$st = $sort[$i];
								db::update('cms_model_fields', array('sort' => $st), "id='$id'");
							}
							$fields = db::select('cms_model_fields', 'id,fieldName,fieldType,sort', "mid='$mid'", 'sort');
							$lastName = '';
							foreach ($fields as $k => $v) {
								if ($k > 0) {
									$sql = "ALTER TABLE `".PRE."$cmsTable` CHANGE `$v[fieldName]` `$v[fieldName]` $v[fieldType] AFTER `$lastName`";
									db::query($sql);
									echo $sql , '<br />';
								}
								$lastName = $v['fieldName'];
							}
						}
						admin::show_message('更新排序完毕！', $referer);
					}
				}
				$list = db::select('cms_model_fields', '*', "mid='$mid'", 'sort');
			break;
			case 'addField':
				$datas = array(
					'name' => '', 
					'fieldName'        => '',
					'fieldType'        => '', 
					'htmlName'         => '', 
					'htmlType'         => '', 
					'htmlWidth'        => '', 
					'htmlHeight'       => '',
					'imageWidth'       => '',
					'imageHeight'      => '',
					'htmlIsReg'        => 0,
					'htmlListValue'    => '',
					'htmlDefaultValue' => '',
					'htmlRegStr'       => '',
					'tip'              => '',
					'bListShow'        => 0
				);
				if (form::is_form_hash()) {
					$datas = form::get3(
						'name', 
						'fieldName', 
						'fieldType', 
						'htmlName', 
						'htmlType', 
						array('htmlWidth', 'int'), 
						array('htmlHeight', 'int'),
						array('imageWidth', 'int'),
						array('imageHeight', 'int'),
						'htmlListValue',
						'htmlDefaultValue',
						array('htmlIsReg', 'int'),
						'htmlRegStr',
						'tip',
						array('bListShow', 'int')
					);
					$datas && ($datas = qscms::stripslashes($datas)) && extract($datas);
					$next = true;
					if ($next) {
						if (db::exists('cms_model_fields', array('mid' => $mid, 'fieldName' => $fieldName))) {
							admin::show_message('该字段名已经存在了');
							$next = false;
						}
					}
					if ($next) {
						if (db::fetchFirst("SELECT COLUMN_NAME FROM  `information_schema`.`COLUMNS` where `TABLE_SCHEMA`='".qscms::getCfgPath('/global/db_name')."' and `TABLE_NAME`='".PRE."$cmsTable' and `COLUMN_NAME`='$fieldName'")) {
							admin::show_message('该字段是系统字段已经存在了，请换一个');
						}
					}
					if ($next) {
						if (db::exists('cms_model_fields', array('mid' => $mid, 'htmlName' => $htmlName))) {
							admin::show_message('该HTML名已经存在了');
							$next = false;
						}
					}
					if ($next) {
						//获取排序ID
						$sort = intval(db::one_one('cms_model_fields', 'max(sort)', "mid='$mid'")) + 1;
					}
					if ($next) {
						$sql = "ALTER TABLE `".PRE."$cmsTable` ADD `$fieldName` $fieldType";
						if (!db::query($sql)) {
							admin::show_message('错误：<br />' . db::error());
							$next = false;
						} else {
							if ($htmlType == 'image' && ($imageWidth || $imageHeight)) {
								$sql = "ALTER TABLE `".PRE."$cmsTable` ADD `{$fieldName}_thumb` $fieldType";
								if (!db::query($sql)) {
									$sql = 'ALTER TABLE `'.PRE.$cmsTable.'` DROP `{$fieldName}_thumb`';
									db::query($sql);
									admin::show_message('错误：<br />' . db::error());
									$next = false;
								}
							}
						}
					}
					if ($next) {
						$datas = array('mid' => $mid, 'sort' => $sort) + $datas;
						$datas = qscms::addslashes($datas);
						if (db::insert('cms_model_fields', $datas)) {
							admin::show_message('添加成功', $baseUrl.'&method=list');
						} else {
							$sql = "ALTER TABLE `".PRE."$cmsTable` DROP `$fieldName`";
							db::query($sql);
							admin::show_message('添加失败，请重试！');
							$next = false;
						}
					}
				}
			break;
			case 'editField':
				$fid = $editField;
				$update = false;
				if ($datas = db::one('cms_model_fields', '*', "id='$fid'")) {
					if (form::is_form_hash()) {
						$datas0 = $datas;
						$datas = form::get3(
							'name', 
							'fieldName', 
							'fieldType', 
							'htmlName', 
							'htmlType', 
							array('htmlWidth', 'int'), 
							array('htmlHeight', 'int'),
							array('imageWidth', 'int'),
							array('imageHeight', 'int'),
							array('htmlIsReg', 'int'),
							'htmlListValue',
							'htmlDefaultValue',
							'htmlRegStr',
							'tip',
							array('bListShow', 'int')
						);
						$datas && ($datas = qscms::stripslashes($datas)) && extract($datas);
						$next = true;
						if ($next) {
							if (db::exists('cms_model_fields', "mid='$mid' and fieldName='$fieldName' and id<>'$fid'")) {
								admin::show_message('该字段名已经存在了');
								$next = false;
							}
						}
						if ($next) {
							if ($datas0['fieldName'] != $fieldName) {
								if (db::fetchFirst("SELECT COLUMN_NAME FROM  `information_schema`.`COLUMNS` where `TABLE_SCHEMA`='".DB_NAME."' and `TABLE_NAME`='".PRE."$cmsTable' and `COLUMN_NAME`='$fieldName'")) {
									admin::show_message('该字段是系统字段已经存在了，请换一个');
								}
							}
						}
						if ($next) {
							if (db::exists('cms_model_fields', "mid='$mid' and htmlName='$htmlName' and id<>'$fid'")) {
								admin::show_message('该HTML名已经存在了');
								$next = false;
							}
						}
						if ($next) {
							$sql = "ALTER TABLE `".PRE."$cmsTable` CHANGE `$datas0[fieldName]` `$fieldName` $fieldType";
							if (!db::query($sql)) {
								admin::show_message('错误：<br />' . db::error());
								$next = false;
							} else {
								if ($htmlType == 'image') {
									if (($imageWidth || $imageHeight)) {
										//有尺寸
										if ($datas0['imageWidth'] || $datas0['imageHeight']) {
											$sql = "ALTER TABLE `".PRE."$cmsTable` CHANGE `{$datas0[fieldName]}_thumb` `{$fieldName}_thumb` $fieldType";
											if (!db::query($sql)) {
												$sql = "ALTER TABLE `".PRE."$cmsTable` CHANGE `$fieldName` `$datas0[fieldName]` $fieldType";
												db::query($sql);
												admin::show_message('错误：<br />' . db::error());
												$next = false;
											}
										} else {
											$sql = "ALTER TABLE `".PRE."$cmsTable` ADD `{$fieldName}_thumb` $fieldType";
											if (!db::query($sql)) {
												
												admin::show_message('错误：<br />' . db::error());
												$next = false;
											}

										}
									} else {
										//没有尺寸
										if ($datas0['imageWidth'] || $datas0['imageHeight']) {
											//现在有尺寸
											$sql = "ALTER TABLE `".PRE."$cmsTable` DROP `{$fieldName}_thumb`";
											if (!db::query($sql)) {
												
												admin::show_message('错误：<br />' . db::error());
												$next = false;
											}
										}
									}
								}
							}
						}
						if ($next) {
							$datas = qscms::addslashes($datas);
							if (db::update('cms_model_fields', $datas, "id='$fid'")) {
								admin::show_message('编辑成功', $baseUrl.'&method=list');
							} else {
								$sql = "ALTER TABLE `".PRE."$cmsTable` CHANGE `$fieldName` `$datas0[fieldName]` $datas0[fieldType]";
								db::query($sql);
								admin::show_message('编辑失败，请重试！');
								$next = false;
							}
						}
					}
					$update = true;
				} else {
					admin::show_message('该字段不存在！');
				}
			break;
		}
	break;
	case 'index':
		$mid      = $index;
		$model    = db::one('cms_model', '*', "id='$mid'");
		$cmsTable = 'cms_'.$model['ename'];
		$top_menu = array(
			'index'     => array('name' => '返回文章模型', 'hide' => false, 'url' => $baseUrl),
			'list'      => $model['name'].'索引列表',
			'addIndex'  => '添加'.$model['name'].'索引',
			'editIndex' => array('name' => '编辑'.$model['name'].'索引', 'hide' => true)
		);
		$baseUrl .= '&index='.$index;
		if ($editIndex = $var->getInt('gp_editIndex')) $method = 'editIndex';
		$top_menu_key = array_keys($top_menu);
		($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[1];
		switch ($method) {
			case 'list':
				if (form::is_form_hash()) {
					extract(form::get3('del', 'sort', 'ids'));
					if ($del) {
						//删除
						$ids = '\'' . implode('\',\'', $del) . '\'';
						foreach (db::get_keys('cms_model_index', 'indexName', "mid='$mid' and id in($ids)") as $v) {
							$sql = 'DROP INDEX `'.$v.'` ON `'.PRE.$cmsTable.'`';
							db::query($sql);
						}
						db::del_ids('cms_model_index', $del);
						admin::show_message('删除成功', $baseUrl.'&method=list');
					} else {
						if ($count = form::array_equal($ids, $sort)) {
							for ($i = 0; $i < $count; $i++) {
								$id = $ids[$i];
								$st = $sort[$i];
								db::update('cms_model_index', array('sort' => $st), "id='$id'");
							}
						}
						admin::show_message('更新排序完毕！', $referer);
					}
				}
				$list = db::select('cms_model_index', '*', "mid='$mid'", 'sort');
			break;
			case 'addIndex':
				if (form::is_form_hash()) {
					$datas = form::get2(
						'name', 
						'indexName', 
						'indexFields', 
						array('indexType', 'int')
					);
					$datas && extract($datas);
					$next = true;
					if ($next) {
						if (db::exists('cms_model_index', array('mid' => $mid, 'indexName' => $indexName))) {
							admin::show_message('该字索引已经存在了');
							$next = false;
						}
					}
					if ($next) {
						$indexList = db::fetchAll('SHOW INDEX FROM `'.PRE.$cmsTable.'`');
						$indexList = array_unique(qscms::arrid($indexList, 'Key_name'));
						if (in_array($indexName, $indexList)) {
							admin::show_message('该索引是系统索引，请换一个！');
							$next = false;
						}
					}
					if ($next) {
						//获取排序ID
						$sort = intval(db::one_one('cms_model_fields', 'max(sort)', "mid='$mid'")) + 1;
					}
					if ($next) {
						$sql = 'CREATE '.($indexType == 1 ? ' UNIQUE ' : '').'INDEX `'.$indexName.'` ON `'.PRE.$cmsTable.'`('.$indexFields.')';
						if (!db::query($sql)) {
							admin::show_message('错误：<br />' . db::error());
							$next = false;
						}
					}
					if ($next) {
						$datas = array('mid' => $mid, 'sort' => $sort) + $datas;
						if (db::insert('cms_model_index', $datas)) {
							admin::show_message('添加成功', $baseUrl.'&method=list');
						} else {
							$sql = 'DROP INDEX `'.$indexName.'` ON `'.PRE.$cmsTable.'`';
							db::query($sql);
							admin::show_message('添加失败，请重试！');
							$next = false;
						}
					}
				}
				$cusFieldsAll = array();
				$cusFields = array();
				foreach (db::select('cms_model_fields', 'name,fieldName', "mid='$mid'") as $v) {
					$cusFieldsAll[$v['fieldName']] = $v['name'];
					$cusFields[] = $v['fieldName'];
				}
				$allFields = array();
				$query = db::query("SELECT COLUMN_NAME FROM  `information_schema`.`COLUMNS` where `TABLE_SCHEMA`='".DB_NAME."' and `TABLE_NAME`='".PRE."$cmsTable'");
				while ($f = db::fetchArrayFirst($query)) {
					$allFields[] = array(
						'name' => $f,
						'sys'  => !in_array($f, $cusFields),
						'tip'  => !empty($cusFieldsAll[$f]) ? $cusFieldsAll[$f] : ''
					);
				}
			break;
			case 'editIndex':
				$iid = $editIndex;
				$update = false;
				if ($datas = db::one('cms_model_index', '*', "id='$iid'")) {
					$update = true;
					if (form::is_form_hash()) {
						$datas0 = $datas;
						$datas = form::get2(
							'name', 
							'indexName', 
							'indexFields', 
							array('indexType', 'int')
						);
						$datas && extract($datas);
						$next = true;
						if ($next) {
							if (db::exists('cms_model_index', "mid='$mid' and indexName='$indexName' and id<>'$iid'")) {
								admin::show_message('该字索引已经存在了');
								$next = false;
							}
						}
						if ($next && $indexName != $datas0['indexName']) {
							$indexList = db::fetchAll('SHOW INDEX FROM `'.PRE.$cmsTable.'`');
							$indexList = array_unique(qscms::arrid($indexList, 'Key_name'));
							if (in_array($indexName, $indexList)) {
								admin::show_message('该索引是系统索引，请换一个！');
								$next = false;
							}
						}
						if ($next) {
							$sql = 'DROP INDEX `'.$datas0['indexName'].'` ON `'.PRE.$cmsTable.'`';
							if (!db::query($sql)) {
								admin::show_message('错误：<br />' . db::error());
								$next = false;
							}
						}
						if ($next) {
							$sql = 'CREATE '.($indexType == 1 ? ' UNIQUE ' : '').'INDEX `'.$indexName.'` ON `'.PRE.$cmsTable.'`('.$indexFields.')';
							
							if (!db::query($sql)) {
								$sql = 'CREATE '.($datas0['indexType'] == 1 ? ' UNIQUE ' : '').'INDEX `'.$datas0['indexName'].'` ON `'.PRE.$cmsTable.'`('.$datas0['indexFields'].')';
								db::query($sql);
								admin::show_message('错误：<br />' . db::error());
								$next = false;
							}
						}
						if ($next) {
							if (db::update('cms_model_index', $datas, "id='$iid'")) {
								admin::show_message('修改成功', $baseUrl.'&method=list');
							} else {
								$sql = 'DROP INDEX `'.$indexName.'` ON `'.PRE.$cmsTable.'`';
								db::query($sql);
								$sql = 'CREATE '.($datas0['indexType'] == 1 ? ' UNIQUE ' : '').'INDEX `'.$datas0['indexName'].'` ON `'.PRE.$cmsTable.'`('.$datas0['indexFields'].')';
								db::query($sql);
								admin::show_message('添加失败，请重试！');
								$next = false;
							}
						}
					}
					$cusFieldsAll = array();
					$cusFields = array();
					foreach (db::select('cms_model_fields', 'name,fieldName', "mid='$mid'") as $v) {
						$cusFieldsAll[$v['fieldName']] = $v['name'];
						$cusFields[] = $v['fieldName'];
					}
					$allFields = array();
					$query = db::query("SELECT COLUMN_NAME FROM  `information_schema`.`COLUMNS` where `TABLE_SCHEMA`='".DB_NAME."' and `TABLE_NAME`='".PRE."$cmsTable'");
					$fs = !empty($datas) ? explode(',', $datas['indexFields']) : array();
					while ($f = db::fetchArrayFirst($query)) {
						$allFields[] = array(
							'name'    => $f,
							'sys'     => !in_array($f, $cusFields),
							'tip'     => $cusFieldsAll[$f],
							'checked' => in_array($f, $fs)
						);
					}
				} else {
					admin::show_message('该索引不存在！');
				}
			break;
		}
	break;
}
include(template::load('admin_manage'));
?>