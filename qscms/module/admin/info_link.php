<?php
!defined('IN_QS_PLUGIN') && IN_QS_PLUGIN !== true && exit('error');
//global $list, $multipage;
$top_menu=array(
	'cateList' => '所有分类',
	'addCate'  => '添加分类',
	'editCate' => array('name' => '编辑分类', 'hide' => true),
	'cmsList'  => '友情链接列表',
	'addCms'   => '添加友情链接',
	'editCms'  => array('name' => '编辑友情链接', 'hide' => true)
);
$modelName = 'link';
if ($edit = $var->getInt('gp_edit') ) $method = 'editCate';
if ($editCms = $var->getInt('gp_editCms') ) $method = 'editCms';
$top_menu_key = array_keys($top_menu);
($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
if ($top = $var->getInt('gp_top') ) {
	if ($item = db::one('cms_link', 'id,top', "id='$top'")) {
		db::update('cms_link', 'top=1-top', "id='$top'");
		admin::show_message($item['top'] ? '取消推荐成功' : '推荐成功', $referer);
	} else {
		admin::show_message('对不起，不存在该信息！！');
	}
}
$pre           = PRE;
$timestamp     = time::$timestamp;
$ipint         = $var->ipint;
$sys_dir_image = substr(qscms::getCfgPath('/system/imgRoot'), 2);
$sys_dir_file  = substr(qscms::getCfgPath('/system/fileRoot'), 2);
switch ($method) {
	case 'cateList':
		//admin::getList('cms_link_cate', '*', '', 'sort,addTime desc');
		if(form::is_form_hash()){
			extract($_POST);
			if($del || $sort){
				if($del){
					db::del_ids('cms_link_cate', $del);
					db::del_keys('cms_link', 'cid', $del);
					admin::show_message('删除成功', $baseUrl);
				} elseif($sort && $ids) {
					if($count = form::array_equal($ids, $sort)){
						for($i=0; $i<$count; $i++){
							$id  = $ids[$i];
							$sid = $sort[$i];
							db::update('cms_link_cate', array('sort'=>$sid),"id='$id'");
						}
						admin::show_message('更新成功', $baseUrl.'&page='.$page);
					}
				}
			}
		}
		$list = array();
		if($total=db::data_count('cms_link_cate')){
			$list = db::get_list2('cms_link_cate', '*', '', 'sort,addTime desc', $page, $pagesize);
			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.($method?'&method='.$method:'').'&page={page}', $pagestyle);
		}
	break;
	case 'addCate':
		$name = $ename = '';
		if (form::is_form_hash()) {
			extract(form::get3('name', 'ename'));
			if ($name && $ename) {
				if (db::insert('cms_link_cate', array(
					'name'    => $name,
					'ename'   => $ename,
					'addTime' => $timestamp,
					'editTime' => $timestamp
				))) {
					admin::show_message('添加成功', $baseUrl.'&method=cateList');
				} else {
					admin::show_message('添加失败！！');
				}
			} else {
				admin::show_message('参数错误！！');
			}
		}
	break;
	case 'editCate':
		if (form::is_form_hash()) {
			extract(form::get3('name', 'ename'));
			if ($name && $ename) {
				if (db::update('cms_link_cate', array(
					'name'     => $name,
					'ename'    => $ename,
					'editTime' => $timestamp
				), "id='$edit'")) {
					admin::show_message('修改成功', $baseUrl.'&method=cateList');
				} else {
					admin::show_message('修改失败！！');
				}
			} else {
				admin::show_message('参数错误！！');
			}
		}
		$update = false;
		if ($item = db::one('cms_link_cate', 'name,ename', "id='$edit'")) {
			extract($item);
			$update = true;
		}
	break;
	case 'cmsList':
		$where1 = $where2 = '';
		($cid = $var->getInt('gp_cid')) && ($cid = (int)$cid) && ($where1 = "cid='$cid'") && ($where2 = "t0.cid='$cid'");
		if ($cid) {
			$top_menu['addCms'] = array('name' => $top_menu['addCms'], 'attach' => '&cid='.$cid, 'hide' => false);
			$multipageUrlAdd = '&cid='.$cid;
		}
		$showFieldName = $showFieldAlias = '';
		if ($fields = db::select('cms_model_fields|cms_model:id=mid', 'name,fieldName|', "t0.bListShow='1' and t1.ename='$modelName'", 't0.sort')) {
			foreach ($fields as $v) {
				$showFieldName && $showFieldName .= ',';
				$showFieldAlias && $showFieldAlias .= "\n";
				$showFieldName .= $v['fieldName'];
				$showFieldAlias .= $v['name'].'|'.$v['fieldName'];
			}
		}
		if(form::is_form_hash()){
			extract($_POST);
			if($del || $sort){
				if($del){
					$ids = implode(',', $del);
					db::query("
					update 
						{$pre}cms_link_cate t1
					right join
						(select 
							cid,count(cid) total2
						from
							{$pre}cms_link
						where
							id in($ids)
						group by
							cid
						) t2
					on
						t2.cid=t1.id
					set
						t1.total=t1.total-t2.total2");
					db::del_ids('cms_link', $del);
					admin::show_message('删除成功', $baseUrl.'&method=cmsList'.(!empty($cid) ? '&cid='.$cid:''));
				} elseif($sort && $ids) {
					if($count = form::array_equal($ids, $sort)){
						for($i=0; $i<$count; $i++){
							$id  = $ids[$i];
							$sid = $sort[$i];
							db::update('cms_link', array('sort'=>$sid),"id='$id'");
						}
						admin::show_message('更新成功', $baseUrl.'&method='.$method.(!empty($cid) ? '&cid='.$cid : '').'&page='.$page);
					}
				}
			}
		}
		$list = array();
		if($total = db::data_count('cms_link', $where1)){
			//$list = db::get_list2('cms_link', 'id,sort,title,ename,addTime,editTime,addIp,clicks', $where, 'cid,sort,addTime desc', $page, $pagesize);
			/*$list = array();
			$query = $db->query("select t1.id,t1.sort,t1.title,t1.ename,t1.addTime,t1.editTime,t1.addIp,t1.clicks,t1.top,t2.name from {$pre}cms_link t1 left join {$pre}cms_link_cate t2 on t2.id=t1.cid$where2 order by t1.cid,t1.sort,t1.addTime desc limit ".($page - 1) * $pagesize.','.$pagesize);*/
			$list = db::select('cms_link|cms_link_cate:id=cid', 'id,sort,addTime,editTime,addIp,clicks,top'.($showFieldName ? ','.$showFieldName : '').'|name', $where2, 't0.cid,t0.sort,t0.addTime DESC', $pagesize, $page);
			
			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.($method?'&method='.$method:'').(empty($multipageUrlAdd)?'':$multipageUrlAdd).'&page={page}', $pagestyle);
		}
	break;
	case 'addCms':
		$update = false;
		$formData = false;
		if ($fields = db::select('cms_model_fields|cms_model:id=mid', '*|', "t1.ename='$modelName'", 't0.sort')) {
			foreach ($fields as $k => $v) {
				if (in_array($v['htmlType'], array('file', 'image'))) $formData = true;
				if ($v['htmlListValue']) {
					$v['choose'] = string::parseChoose($v['htmlListValue']);
					$fields[$k] = $v;
				}
			}
		}
		if (form::is_form_hash()) {
		
			$fieldDatas = array();
			$getPics    = array();
			if ($fields) {
				foreach ($fields as $v) {
					switch ($v['htmlType']) {
						case 'hidden':
							$data = !empty($_POST[$v['htmlName']]) ? $_POST[$v['htmlName']] : '';
						break;
						case 'txt':
							$data = $_POST[$v['htmlName']];
						break;
						case 'textarea':
							$data = $_POST[$v['htmlName']];
						break;
						case 'radio':
							$data = $_POST[$v['htmlName']];
						break;
						case 'checkbox':
							$data = string::getCheckBox($_POST[$v['htmlName']]);
						break;
						case 'select':
							$data = $_POST[$v['htmlName']];
						break;
						case 'file':
							$data = $_FILES[$v['htmlName']]['name'];
						break;
						case 'image':
							$_val = !empty($_POST[$v['htmlName']]) ? $_POST[$v['htmlName']] : '';
							if (is_numeric($_val)) {
								$data = $_val;
							} else {
								if (!($data = $_FILES[$v['htmlName']]['name'])) {
									$getPics[$v['htmlName']] = $_POST[$v['htmlName'].'GetPic'];
									if ($getPics[$v['htmlName']]) {
										$editorPics = memory::get('editorPics');
										if (!$editorPics) {
											$tmp = !empty($_POST['content']) ? stripslashes($_POST['content']) : '';
											if (preg_match_all('/<img.*?src="\/(img\/images\/.*?)".*?>/', $tmp, $matches)) {
												$editorPics = $matches[1];
												memory::write('editorPics', $editorPics);
											}
										}
										if ($editorPics) {
											$pic = $editorPics[0];
											$data = d('./'.$pic);
											
										} else {
											$getPics[$v['htmlName']] = false;
										}
									}
								} else {
									$getPics[$v['htmlName']] = false;
								}
							}
						break;
						case 'editor':
							$data = $_POST[$v['htmlName']];
						break;
					}
					if ($v['htmlIsReg'] && $v['htmlRegStr'] && $v['tip']) {
						$tips   = qscms::trimExplode('|', $v['tip']);
						$regStr = $v['htmlRegStr'];
						if (preg_match('/^(\/.*?\/)(\w*)$/', $regStr, $matches)) {
							$regStr = $matches[1];
							if (strpos($matches[2], 'i') !== false) $regStr .= 'i';
							if (strpos($matches[2], 'm') !== false) $regStr .= 's';
						} else {
							$regStr = '';
						}
						if ($regStr) {
							if (!preg_match($regStr, $data)) {
								$tips[1] || $tips[1] = $tips[0];
								admin::show_message($tips[1]);
							}
						}
					}
					switch ($v['htmlType']) {
						case 'hidden':
							$data !== '' && $fieldDatas[$v['htmlName']] = $data;
						break;
						case 'txt':
							$fieldDatas[$v['htmlName']] = $data;
						break;
						case 'textarea':
							$fieldDatas[$v['htmlName']] = $data;
						break;
						case 'radio':
							$fieldDatas[$v['htmlName']] = $data;
						break;
						case 'checkbox':
							$fieldDatas[$v['htmlName']] = $data;
						break;
						case 'select':
							$fieldDatas[$v['htmlName']] = $data;
						break;
						case 'file':
							$saveDir0 = date('Y/m/d/', $timestamp);
							$saveDir  = d(qscms::getCfgPath('/system/fileRoot') . $saveDir0);
							if ($data = upload::uploadFile($v['htmlName'], $saveDir)) {
								$data = $saveDir0 . $data;
								$fieldDatas[$v['htmlName']] = $data;
							}
						break;
						case 'image':
							if (isset($getPics[$v['htmlName']]) && $getPics[$v['htmlName']]) {
								$saveDir0 = date('Y/m/d/', $timestamp);
								$saveDir  = d(qscms::getCfgPath('/system/imgRoot') . $saveDir0);
								file_exists($saveDir) || file::createFolder($saveDir);
								$pathinfo = pathinfo($data);
								$sourcePic = upload::tempname($saveDir, $pathinfo['extension']);
								if (@copy($data, $sourcePic)) {
									$pathinfo = pathinfo($sourcePic);
									$thumbPic  = $pathinfo['dirname'] . D . $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
									$pic1 = $saveDir0 . $pathinfo['basename'];
									$pic2 = '';
									if ($v['imageWidth'] || $v['imageHeight']) {
										if (image::thumb($sourcePic, $thumbPic, array('width' => $v['imageWidth'], 'height' => $v['imageHeight']))) {
											$pic2 = $saveDir0 . $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
										}
										$fieldDatas[$v['htmlName'].'_thumb'] = $pic2;
									}
									//$pic2 || $pic2 = $pic1;
									$fieldDatas[$v['htmlName']] = $pic1;
								}
							} else {
								$saveDir0 = date('Y/m/d/', $timestamp);
								$saveDir  = d(qscms::getCfgPath('/system/imgRoot') . $saveDir0);
								if (is_numeric($data)) {//异步
									$u = new upload();
									file::createFolder($saveDir);
									$rs = $u->move2($data, $saveDir, true);
									if ($rs) {
										$pic1 = $saveDir0.$rs['basename'];
										$pic2 = '';
										if ($v['imageWidth'] && $v['imageHeight']) {
											if (image::thumb($sourcePic, $thumbPic, array('width' => $v['imageWidth'], 'height' => $v['imageHeight']))) {
												$pic2 = $saveDir0 . $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
											}
											$fieldDatas[$v['htmlName'].'_thumb'] = $pic2;
										}
										//$pic2 || $pic2 = $pic1;
										$fieldDatas[$v['htmlName']] = $pic1;
									}
								} else {
									if ($data = upload::uploadFile($v['htmlName'], $saveDir)) {
										$sourcePic = $saveDir . $data;
										$pathinfo = pathinfo($sourcePic);
										$thumbPic  = $pathinfo['dirname'] . D . $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
										$pic1 = $saveDir0 . $data;
										$pic2 = '';
										if ($v['imageWidth'] && $v['imageHeight']) {
											if (image::thumb($sourcePic, $thumbPic, array('width' => $v['imageWidth'], 'height' => $v['imageHeight']))) {
												$pic2 = $saveDir0 . $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
											}
											$fieldDatas[$v['htmlName'].'_thumb'] = $pic2;
										}
										//$pic2 || $pic2 = $pic1;
										$fieldDatas[$v['htmlName']] = $pic1;
									}
								}
							}
						break;
						case 'editor':
							//$data = addslashes(links_add::union(stripslashes($data), db::get_list('union_links', '`key`,url', '', 'sort,timestamp desc', -1)));
							$fieldDatas[$v['htmlName']] = $data;
						break;
					}
					
				}
			}
			//$datas = form::get2('title', 'ename', array('cid', 'int'), 'content');
			$datas = form::get2(array('cid', 'int'));
			$datas += $fieldDatas;
			//if ($datas['title'] && $datas['cid']) {
			if ($datas['cid']) {
				if (db::exists('cms_link_cate', array('id' => $datas['cid']))) {
					//$datas['content'] = addslashes(links_add::union(stripslashes($datas['content']), db::get_list('union_links', '`key`,url', '', 'sort,timestamp desc', -1)));
					$datas += array('addTime' => $timestamp, 'editTime' => $timestamp, 'addIp' => $ipint);
					if (db::insert('cms_link', $datas)) {
						db::update('cms_link_cate', 'total=total+1', "id='$datas[cid]'");
						admin::show_message('添加成功', $baseUrl.'&method=cmsList&cid='.$datas['cid']);
					} else {
						admin::show_message('添加失败！！');
					}
				} else {
					admin::show_message('所属分类不存在！！');
				}
			} else {
				admin::show_message('参数错误！！');
			}
		}
		$cates = array();
		foreach (db::get_list('cms_link_cate', '*', '', 'sort,addTime desc', -1) as $v) {
			$cates[$v['name']] = $v['id'];
		}
	break;
	case 'editCms':
		$formData = false;
		if ($fields = db::select('cms_model_fields|cms_model:id=mid', '*|', "t1.ename='$modelName'", 't0.sort')) {
			foreach ($fields as $k => $v) {
				if (in_array($v['htmlType'], array('file', 'image'))) $formData = true;
				if ($v['htmlListValue']) {
					$v['choose'] = string::parseChoose($v['htmlListValue']);
					$fields[$k] = $v;
				}
			}
		}
		if (form::is_form_hash()) {
			$fieldDatas = array();
			if ($fields) {
				foreach ($fields as $v) {
					switch ($v['htmlType']) {
						case 'hidden':
							$data = !empty($_POST[$v['htmlName']]) ? $_POST[$v['htmlName']] : '';
						break;
						case 'txt':
							$data = $_POST[$v['htmlName']];
						break;
						case 'textarea':
							$data = $_POST[$v['htmlName']];
						break;
						case 'radio':
							$data = $_POST[$v['htmlName']];
						break;
						case 'checkbox':
							$data = string::getCheckBox($_POST[$v['htmlName']]);
						break;
						case 'select':
							$data = $_POST[$v['htmlName']];
						break;
						case 'file':
							if (!($data = $_FILES[$v['htmlName']]['name'])) $v['htmlIsReg'] = false;
						break;
						case 'image':
							//if (!($data = $_FILES[$v['htmlName']]['name'])) $v['htmlIsReg'] = false;
							$_val = !empty($_POST[$v['htmlName']]) ? $_POST[$v['htmlName']] : '';
							if ($_val) {
								$data = $_val;
							} else {
								if (!($data = $_FILES[$v['htmlName']]['name'])) {
									$getPics[$v['htmlName']] = $_POST[$v['htmlName'].'GetPic'];
									if ($getPics[$v['htmlName']]) {
										$editorPics = memory::get('editorPics');
										if (!$editorPics) {
											$tmp = !empty($_POST['content']) ? stripslashes($_POST['content']) : '';
											if (preg_match_all('/<img.*?src="\/(img\/images\/.*?)".*?>/', $tmp, $matches)) {
												$editorPics = $matches[1];
												memory::write('editorPics', $editorPics);
											}
										}
										if ($editorPics) {
											$pic = $editorPics[0];
											$data = d('./'.$pic);
											
										} else {
											$getPics[$v['htmlName']] = false;
											$v['htmlIsReg'] = false;
										}
									} else {
										$v['htmlIsReg'] = false;
									}
								} else {
									$getPics[$v['htmlName']] = false;
								}
							}
						break;
						case 'editor':
							$data = $_POST[$v['htmlName']];
						break;
					}
					if ($v['htmlIsReg'] && $v['htmlRegStr'] && $v['tip']) {
						$tips   = qscms::trimExplode('|', $v['tip']);
						$regStr = $v['htmlRegStr'];
						if (preg_match('/^(\/.*?\/)(\w*)$/', $regStr, $matches)) {
							$regStr = $matches[1];

							if (strpos($matches[2], 'i') !== false) $regStr .= 'i';
							if (strpos($matches[2], 'm') !== false) $regStr .= 's';
						} else {
							$regStr = '';
						}
						if ($regStr) {
							if (!preg_match($regStr, $data)) {
								$tips[1] || $tips[1] = $tips[0];
								admin::show_message($tips[1]);
							}
						}
					}
					switch ($v['htmlType']) {
						case 'hidden':
							$data !== '' && $fieldDatas[$v['htmlName']] = $data;
						break;
						case 'txt':
							$fieldDatas[$v['htmlName']] = $data;
						break;
						case 'textarea':
							$fieldDatas[$v['htmlName']] = $data;
						break;
						case 'radio':
							$fieldDatas[$v['htmlName']] = $data;
						break;
						case 'checkbox':
							$fieldDatas[$v['htmlName']] = $data;
						break;
						case 'select':
							$fieldDatas[$v['htmlName']] = $data;
						break;
						case 'file':
							if ($data) {
								$saveDir0 = date('Y/m/d/', $timestamp);
								$saveDir  = d(qscms::getCfgPath('/system/fileRoot') . $saveDir0);
								if ($data = upload::uploadFile($v['htmlName'], $saveDir)) {
									$data = $saveDir0 . $data;
									$fieldDatas[$v['htmlName']] = $data;
								}
							}
						break;
						case 'image':
							if ($data) {
								if (isset($getPics[$v['htmlName']]) && $getPics[$v['htmlName']]) {
									$saveDir0 = date('Y/m/d/', $timestamp);
									$saveDir  = d(qscms::getCfgPath('/system/imgRoot') . $saveDir0);
									file_exists($saveDir) || file::createFolder($saveDir);
									$pathinfo = pathinfo($data);
									$sourcePic = upload::tempname($saveDir, $pathinfo['extension']);
									if (@copy($data, $sourcePic)) {
										$pathinfo = pathinfo($sourcePic);
										$thumbPic  = $pathinfo['dirname'] . D . $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
										$pic1 = $saveDir0 . $pathinfo['basename'];
										$pic2 = '';
										if ($v['imageWidth'] && $v['imageHeight']) {
											if (image::thumb($sourcePic, $thumbPic, array('width' => $v['imageWidth'], 'height' => $v['imageHeight']))) {
												$pic2 = $saveDir0 . $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
												
											}
											$fieldDatas[$v['htmlName'].'_thumb'] = $pic2;
										}
										$pic2 || $pic2 = $pic1;
										$fieldDatas[$v['htmlName']] = $pic1;
										
									}
								} else {
									$saveDir0 = date('Y/m/d/', $timestamp);
									$saveDir  = d(qscms::getCfgPath('/system/imgRoot') . $saveDir0);
									if (is_numeric($data)) {
										$u = new upload();
										file::createFolder($saveDir);
										$rs = $u->move2($data, $saveDir, true);
										if ($rs) {
											$pic1 = $saveDir0.$rs['basename'];
											$pic2 = '';
											if ($v['imageWidth'] && $v['imageHeight']) {
												if (image::thumb($sourcePic, $thumbPic, array('width' => $v['imageWidth'], 'height' => $v['imageHeight']))) {
													$pic2 = $saveDir0 . $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
												}
												$fieldDatas[$v['htmlName'].'_thumb'] = $pic2;
											}
											//$pic2 || $pic2 = $pic1;
											$fieldDatas[$v['htmlName']] = $pic1;
										}
									} else {
										if ($data = upload::uploadFile($v['htmlName'], $saveDir)) {
											$sourcePic = $saveDir . $data;
											$pathinfo = pathinfo($sourcePic);
											$thumbPic  = $pathinfo['dirname'] . D . $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
											$pic1 = $saveDir0 . $data;
											$pic2 = '';
											if ($v['imageWidth'] && $v['imageHeight']) {
												if (image::thumb($sourcePic, $thumbPic, array('width' => $v['imageWidth'], 'height' => $v['imageHeight']))) {
													$pic2 = $saveDir0 . $pathinfo['filename'] . '_thumb.' . $pathinfo['extension'];
													$fieldDatas[$v['htmlName'].'_thumb'] = $pic2;
												}
											}
											$pic2 || $pic2 = $pic1;
											$fieldDatas[$v['htmlName']] = $pic1;

										}
									}
								}
							}
						break;
						case 'editor':
							//$data = addslashes(links_add::union(stripslashes($data), db::get_list('union_links', '`key`,url', '', 'sort,timestamp desc', -1)));
							$fieldDatas[$v['htmlName']] = $data;
						break;
					}
					
				}
			}
			//$datas = form::get2('title', 'ename', array('cid', 'int'), 'content');
			$datas = form::get2(array('cid', 'int'));
			$datas += $fieldDatas;
			//if ($datas['title'] && $datas['cid']) {
			if ($datas['cid']) {
				if (db::exists('cms_link_cate', array('id' => $datas['cid']))) {
					if ($oldCid = db::one_one('cms_link', 'cid', "id='$editCms'")) {
						//$datas['content'] = addslashes(links_add::union(stripslashes($datas['content']), db::get_list('union_links', '`key`,url', '', 'sort,timestamp desc', -1)));
						$datas += array('editTime' => $timestamp);
						if (db::update('cms_link', $datas, "id='$editCms'")) {
							if ($oldCid != $datas['cid']) {
								db::update('cms_link_cate', 'total=total+1', "id='$datas[cid]'");
								db::update('cms_link_cate', 'total=total-1', "id='$oldCid'");
							}
							admin::show_message('编辑成功', $baseUrl.'&method=cmsList&cid='.$datas['cid']);
						} else {
							admin::show_message('编辑失败！！');
						}
					} else {
						admin::show_message('要编辑的友情链接不存在');
					}
				} else {
					admin::show_message('所属分类不存在！！');
				}
			} else {
				admin::show_message('参数错误！！');
			}
		}
		$update = false;
		if ($datas = db::one('cms_link', '*', "id='$editCms'")) {
			//extract($item);
			//$content = $datas['content'];
			$update = true;
		}
		$cates = array();
		foreach (db::get_list('cms_link_cate', '*', '', 'sort,addTime desc', -1) as $v) {
			$cates[$v['name']] = $v['id'];
		}
	break;
}
?>