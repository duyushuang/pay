<?php
class admin extends ext_base{
	public static function show_message($message,$goto='',$parent=false){
		qscms::ob_clean();
		$var = qscms::v('_G');
		if ($var->menuAjax) {
			echo $message;
		} else {
			template::initialize(qd(qscms::getCfgPath('/system/tplRoot')).$var->adminTplDir.D, d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/tpl')).$var->adminTplDir.D);
			if(!$goto)$goto='javascript:history.back(-1);';
			include(template::load('show_message'));
		}
		exit;
	}
	public static function showMessage($message, $goto = '', $parent = false) {
		self::show_message($message, $goto, $parent);
	}
	public static function post_start($url=''){
		echo '<form method="post" class="table-responsive" enctype="application/x-www-form-urlencoded"'.($url?' action="'.$url.'"':'').'>
	<input type="hidden" name="hash" value="'.$GLOBALS['sys_hash'].'" />';
	}
	public static function post_end(){
		echo '</form>';
	}
	public static function login(){
		if($_POST){
			//extract($_POST);
			extract(form::get3('username', 'password', 'postType', 'vcode'));
			if(!empty($postType) && $postType == 'login' && $username && $password && $vcode){
				
				if(vcode::check()){
					if(qscms::getCfgPath('/global/sys_user') == $username){
						if(qscms::saltPwdCheck( qscms::getCfgPath('/global/sys_salt'), $password, qscms::getCfgPath('/global/sys_pwd') )){
							//qscms::setcookie('founder_login', true, 600);
							qscms::setcookie('backAdmin', $username.'|'.qscms::getCfgPath('/global/sys_pwd').'|'.qscms::v('_G')->sys_hash);
							return true;
						}
						else return '创始人密码错误';
					} else {
						//登陆数据库用户
						$rs = self::loginUser($username, $password);
						if (is_numeric($rs)) {
							//登陆成功
							$admin = self::get($rs);
							//qscms::setcookie('admin_login', $rs, 600);
							qscms::setcookie('backAdmin', $admin['username'].'|'.$admin['password'].'|'.qscms::v('_G')->sys_hash);
							qscms::v('_G')->admin = $admin;
							return true;
						} elseif ($rs === false) {
							return '创始人不存在';
						} else return $rs;
					}
				} else return '验证码错误';
			} else return '参数错误';
		}
	}
	public static function logout(){
		if (IN_ADMIN === true) {
			qscms::unsetcookie('backAdmin');
		}
	}
	public static function loginUser($username, $password, $isCookie = false) {
		$timestamp = time::$timestamp;
		if ($user = db::one('admins', 'id,username,salt,password', "username='$username'")) {
			if (!$isCookie) {
				if ($user['password'] == qscms::saltPwd($user['salt'], $password)) {
					db::update('admins',"lastLoginTimestamp='$timestamp',loginTimes=loginTimes+1", "id='$user[id]'");
					return $user['id'];
				}
			} else {
				if ($user['password'] == $password) return $user['id'];
			}
			return '管理员密码错误';
		}
		return false;
	}
	public static function loginCookie(){
		if (isset($_COOKIE['backAdmin'])) {
			$admin = array();
			qscms::v('_G')->admin = $admin;
			@list($username, $password, $hash) = explode('|', $_COOKIE['backAdmin']);
			if ($hash != qscms::v('_G')->sys_hash) return false;
			if ($username == qscms::getCfgPath('/global/sys_user') && $password == qscms::getCfgPath('/global/sys_pwd')) {
				//创始人
				if ($password == qscms::getCfgPath('/global/sys_pwd')) {
					//密码正确
					$admin = array(
						'username' => $username,
						'password' => $password,
						'type'     => 'founder'
					);
					qscms::v('_G')->admin = $admin;
					self::updateLogin();
					return true;
				}
			} else {
				$rs = self::loginUser($username, $password, true);
				if (is_numeric($rs)) {
					$admin = self::get($rs);
					$admin['type'] = 'admin';
					qscms::v('_G')->admin = $admin;
				}
				return $rs;
				/*
				$rs = self::loginUser($username, $password, true);
				if (is_numeric($rs)) {
					if (!db::exists('admins', "id<'$rs'")){
						$item = db::one('admins', 'username,password', "id='$rs'");
						$admin = array(
							'username' => $item['username'],
							'password' => $item['password'],
							'type'     => 'founder'
						);
						qscms::v('_G')->admin = $admin;
						self::updateLogin();
					}else{
						$admin = self::get($rs);
						$admin['type'] = 'admin';
						qscms::v('_G')->admin = $admin;
					}
				}
				return $rs;
				*/
			}
		}
		return false;
	}
	public static function get($uid){
		if ($user = db::one('admins', 'id,username,password', "id='$uid'")) {
			$user['authority'] = array();
			if ($line = db::get_list('admin_authority', '`key`,value', "aid='$user[id]'", '', 0)) {
				foreach ($line as $v) {
					$user['authority'][$v['key']] = (int)$v['value'];
				}
			}
			return $user;
		}
		return false;
	}
	public static function updateLogin(){
		$time = 1800;
		$admin = qscms::v('_G')->admin;
		if (!empty($admin)) {
			qscms::setcookie('backAdmin', $admin['username'].'|'.$admin['password'].'|'.qscms::v('_G')->sys_hash, $time);
			return true;
		}
		return false;
		/*if (IN_ADMIN === true) {
			if (IN_FOUNDER === true) {
				qscms::setcookie("founder_login", true, $time);//刷新登录时间 10分钟
			} else {
				qscms::setcookie('admin_login', $adminId, $time);
			}
		}*/
	}
	public static function confirm($message, $title = SOFTWARE_NAME){
		if(form::is_form_hash()){
			return true;
		}
		$var = qscms::v('_G');
		//qscms::ob_clean();
		include(template::load('confirm'));
		exit;
	}
	public static function form($data){
		$sys_hash_code = qscms::g('sys_hash_code');
		$weburl = WEB_URL_S1;
		$rn = $tip = '';
		$isTip = false;
		$maxTitle = 0;
		$isUpload = false;
		$dataBox = false;
		if($data = trim($data)){
			$tags = array();
			$sp = qscms::trimExplode("\n", $data);//分割表单标记
			foreach ($sp as $str) {
				if($str = trim($str)) {
					if ($str=='upload') {//如果是上传标记
						$isUpload = true;
						continue;
					}
					if ($str == 'preg') {//正则表达式
						$isTip = true;
						continue;
					}
					$sp2 = qscms::trimExplode('|', $str);
					if ($sp2[0] == 'data-box') {
						array_shift($sp2);
						$dataBox = implode('|', $sp2);
						continue;
					}
					if ($sp2[1] == 'tip' && count($sp2) == 2) {//判断是否为标题
						$tip = $sp2[0];
					} else {
						$tag = array();
						$sp3 = qscms::trimExplode(',', $sp2[0]);//分割标题项
						array_shift($sp2);//移除标题
						$tag['title'] = $sp3[0];//元素标题
						$len = mb_strlen($tag['title']);
						if ($len>$maxTitle)$maxTitle = $len;
						array_shift($sp3);//移除标题
						$tag['help'] = false;
						if (count($sp3)>0) {//有正则表达式
							$tag['tip'] = implode(',', $sp3);
							$isTip = true;//preg_match('/^[a-z]+=.+/', $tag['tip']) > 0;
							
						} else {
							$tag['tip'] = '';
						}
						$tag['varName'] = $sp2[0];//元素ID
						array_shift($sp2);//移除元素ID
						$sp3 = qscms::trimExplode(',', $sp2[0]);//分割元素类型
						$tag['type'] = $sp3[0];//元素类型
						array_shift($sp3);//移除类型
						array_shift($sp2);//移除标记元素类型
						if(count($sp3)>0)$tag['other'] = $sp3;//元素类型处 其它标记
						else $tag['other'] = array();
						if(count($sp2)>0)$tag['other2'] = $sp2;
						else $tag['other2'] = array();
						$tags[] = $tag;
					}
				}
			}
			$titleWidth = ($maxTitle + 1 ) * 12;
			foreach($tags as $tag){
				$help = false;
				$emptyRunReg = true;
				if ($tag['tip']) {
					$sp = qscms::trimExplode(',', $tag['tip']);
					$tag['tip'] = $sp[0];
					if (isset($sp[1]) && $sp[1] == 'false') $emptyRunReg = false;
					if (isset($sp[2]) && $sp[2] != '') $help = $sp[2];
				}
				switch($tag['type']){
					case 'password':
					case 'text':
						$html = '<input type="'.$tag['type'].'" name="'.$tag['varName'].'" id="'.$tag['varName'].'" value="{var $'.$tag['varName'].'}" class="form-control"'.($tag['other2'][0]?' style="width:'.$tag['other2'][0].'px"':'').($tag['other2'][1]?' maxlength="'.$tag['other2'][1].'"':'').($tag['tip']?' preg="'.$tag['tip'].'"'.($emptyRunReg ? '' : ' emptyRunReg="false"'):'').' />';
					break;
					case 'file':
						$html = '{if $var->menuAjax}<input type="hidden" name="'.$tag['varName'].'" value="$'.$tag['varName'].'" />
                        <iframe src="$baseUrl/ifUpload/'.$tag['varName'].'" style="border:0px;" id="'.$tag['varName'].'_if"></iframe>
                        <div id="'.$tag['varName'].'_img"></div>
						<script>
                        $(\'#'.$tag['varName'].'_if\').load(function(){
							var mainheight = $(this).contents().find("body").height();
							$(this).height(mainheight);
						});
						{if $'.$tag['varName'].'}
						setUpload(\''.$tag['varName'].'\', \'$'.$tag['varName'].'\');
						{/if}
                        </script>{else}<input type="file" name="'.$tag['varName'].'" id="'.$tag['varName'].'"'.($tag['other2'][0]?' style="width:'.$tag['other2'][0].'px"':'').($tag['other2'][1]?' maxlength="'.$tag['other2'][1].'"':'').($tag['tip']?' preg="'.$tag['tip'].'"'.($emptyRunReg ? '' : ' emptyRunReg="false"'):'').' />{if !empty($'.$tag['varName'].')}<br /><img src="$'.$tag['varName'].'" width="100" />{/if}{/if}';
					break;
					break;
					case 'textarea':
						$html = '<textarea name="'.$tag['varName'].'" id="'.$tag['varName'].'" class="form-control"'.($tag['tip']?' preg="'.$tag['tip'].'"'.($emptyRunReg ? '' : ' emptyRunReg="false"'):'').'>{html $'.$tag['varName'].'}</textarea>';
					break;
					case 'radio':
						if($tag['other']){
							if (substr($tag['other'][0], 0, 1)=='$') {
								$varName = $tag['other'][0];
								$html = '<div class="radio-list">{eval $i=0;}{loop '.$varName.' $k $v}{eval $i++;}';
								$html.= '<label class="radio-inline"><input type="radio" name="'.$tag['varName'].'" id="'.$tag['varName'].'$i" value="$v"{if isset($'.$tag['varName'].') && $'.$tag['varName'].'==$v || ($i==1 && !isset($'.$tag['varName'].'))} checked{/if} />{if $k}$k{/if}</label>';
								$html.= '{/loop}</div>';
							} else {
								$html = '';
								$i = 0;
								foreach ($tag['other'] as $k => $v) {
									if($v = trim($v)) {
										$i++;
										$sp = qscms::trimExplode('=', $v);
										$html.= '<label class="radio-inline">
										
												<input type="radio"{if isset($'.$tag['varName'].') && $'.$tag['varName'].'==\''.$sp[0].'\''.($i==1?' || !isset($'.$tag['varName'].')':'').'} checked{/if} value="'.$sp[0].'" id="'.$tag['varName'].$i.'" name="'.$tag['varName'].'" />';
										if (count($sp) == 2 && $sp[1]) {
											$html.=$sp[1];
										}
										$html .= '</label>';
									}
								}
								$html = '<div class="radio-list">'.$html.'</div>';
							}
						} else {
							$html = '';
						}
					break;
					case 'check':
					case 'checkbox':
						if($tag['other']){
							if (substr($tag['other'][0], 0, 1)=='$') {
								$varName = $tag['other'][0];
								$html = '<div class="checkbox-list">{eval $i=0;}{loop '.$varName.' $k $v}{eval $i++;}';
								$html.= '<label class="checkbox-inline"><input type="checkbox" name="'.$tag['varName'].'" id="'.$tag['varName'].'$i" value="$v"{if isset($'.$tag['varName'].') && $'.$tag['varName'].'==$v} checked{/if} />{if $k}$k{/if}</label>';
								$html.= '{/loop}</div>';
							} else {
								$html = '';
								$i = 0;
								foreach ($tag['other'] as $v) {
									if($v = trim($v)) {
										$i++;
										$sp = qscms::trimExplode('=', $v);
										$html.= '<label class="checkbox-inline">
										<input type="checkbox" name="'.$tag['varName'].'" id="'.$tag['varName'].$i.'" value="'.$sp[0].'"{if isset($'.$tag['varName'].') && $'.$tag['varName'].'==\''.$sp[0].'\'} checked{/if} />';
										if (count($sp) == 2 && $sp[1]) {
											$html.=$sp[1];
										}
										$html.='</label>';
									}
								}
								$html = '<div class="checkbox-list">'.$html.'</div>';
							}
						} else {
							$html = '';
						}
					break;
					case 'select':
						if($tag['other']){
							if (substr($tag['other'][0], 0, 1)=='$') {
								$varName = $tag['other'][0];
								$html = '<select name="'.$tag['varName'].'" id="'.$tag['varName'].'"'.($tag['tip']?' preg="'.$tag['tip'].'"':'').' class="form-control">{eval $__group=false;}{loop '.$varName.' $k $v}{if is_array($v) && $v[type] == \'-\'}';
								$html.= '{if $__group}</optgroup>{/if}{eval $__group=true;}<optgroup label="$v[name]">';
								$html.= '{else}<option value="$v"{if isset($'.$tag['varName'].') && $'.$tag['varName'].' == $v} selected{/if}>$k</option>';
								$html.= '{/if}{/loop}{if $__group}</optgroup>{/if}</select>';
							} else {
								$html = '';
								foreach ($tag['other'] as $v) {
									if($v = trim($v)) {
										$sp = qscms::trimExplode('=', $v);
										if (count($sp) == 2) {
											$html .= '<option value="'.$sp[0].'"{if isset($'.$tag['varName'].') && $'.$tag['varName'].'==\''.$sp[0].'\'} selected{/if}>'.$sp[1].'</option>';
										}
									}
								}
								$html = '<select name="'.$tag['varName'].'" id="'.$tag['varName'].'"'.($tag['tip']?' preg="'.$tag['tip'].'"':'').' class="form-control">'.$html.'</select>';
							}
						} else {
							$html = '';
						}
					break;
					case 'xheditor':
						$tag['other'][0] && $xheditorName   = $tag['other'][0];
						$tag['other'][1] && $xheditorWidth  = $tag['other'][1];
						$tag['other'][2] && $xheditorHeight = $tag['other'][2];
						$xheditorName   || $xheditorName   = 'full';
						$xheditorWidth  || $xheditorWidth  = '800';
						$xheditorHeight || $xheditorHeight = '600';
						$html = xheditor::getEditor($tag['varName'], $xheditorWidth, $xheditorHeight, $xheditorName);
					break;
					case 'fckeditor':
						$tag['other'][0] && $fckeditorWidth  = $tag['other'][0];
						$tag['other'][1] && $fckeditorHeight = $tag['other'][1];
						$fckeditorWidth  || $fckeditorWidth  = '800';
						$fckeditorHeight || $fckeditorHeight = '600';
						$html = '<?php include(qd(\'./editor/ckeditor/ckeditor.php\'));
	include(qd(\'./editor/ckfinder/ckfinder.php\'));
		$CKEditor=new CKEditor();
		$CKEditor->basePath= qu(\'./editor/ckeditor/\');
		$CKEditor->config[\'width\']='.$fckeditorWidth.';
		$CKEditor->config[\'height\']='.$fckeditorHeight.';
		$CKEditor->config[\'skin\']=\'office2003\';
		$CKEditor->returnOutput=true;
		CKFinder::SetupCKeditor($CKEditor, qu(\'./editor/ckfinder/\'));
		$editor_html=$CKEditor->editor(\''.$tag['varName'].'\',!empty($'.$tag['varName'].')?$'.$tag['varName'].':\'\');
		echo $editor_html;
		?>';
					break;
					case 'var':
						$html = '$'.$tag['varName'];
					break;
					case 'code':
						$_code = array_merge(array(implode(',', $tag['other'])), $tag['other2']);
						$html = implode('|', $_code);//implode(',', $tag['other']);//$tag['other'][0];
					break;
				}
				$rn .= '<div class="form-group" id="'.$tag['varName'].'_color"><label class="control-label" for="'.$tag['varName'].'">'.$tag['title'].'</label>
				<div class="input-icon right">'.($isTip ? '<i data-container="body" data-original-title="" class="fa tooltips" id="'.$tag['varName'].'_tip"></i>' : '').$html.($help ? '<span class="help-block">'.$help.'</span>' : '').'</div>
				</div>';
			}
		}
		$rn = '
<form class="form-horizontal qsQuickForm ajaxForm table-responsive" enctype="'.($isUpload?'multipart/form-data':'application/x-www-form-urlencoded').'" method="post" id="qsQuickForm"'.($dataBox ? " data-box=\"$dataBox\"" : '').'>
{echo $var->sys_hash_code}{if !empty($update) && $update}<input type="hidden" name="isEdit" value="yes" />{/if}
	<div class="col-md-1"></div>
	<div class="col-md-10">
		<div class="form-body">
			'.($tip ? '<h3 class="form-section">'.$tip.'</h3>' : '').'
			<div class="col-md-1"></div>
			<div class="col-md-9">'.$rn.'</div>
		</div>
		<div class="form-actions">
			<div class="row">
				<div class="col-md-offset-1 col-md-9">
					<button class="btn green" type="submit">{if !empty($update) && $update}编辑{else}提交{/if}</button>
					<button class="btn default" type="button">取消</button>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-1"></div>
</form><div class="clearfix"></div>';
		$rn = '<?php '.parse_php::parse($rn).'?>';
		return $rn;
	}
	public static function getList($tbName, $f = '*', $where = '', $order = '', $changeReturn = false){
		$var       = qscms::v('_G');
		$page      = $var->gp_page;
		$pagesize  = $var->gp_pagesize;
		$pagestyle = $var->pagestyle;
		$baseUrl   = $var->baseUrl;
		$method    = $var->method;
		$multipageUrlAdd = $var->multipageUrlAdd;
		if(form::is_form_hash()){
			extract($_POST);
			if(!empty($del) || !empty($sort)){
				if($del){
					db::del_ids($tbName, $del);
					if(!$changeReturn){
						admin::show_message('删除成功', $baseUrl);
					} else {
						return 2;
					}
				} elseif(!empty($sort) && !empty($ids)) {
					if($count = form::array_equal($ids, $sort)){
						for($i=0; $i<$count; $i++){
							$id  = $ids[$i];
							$sid = $sort[$i];
							db::update($tbName, array('sort'=>$sid),"id='$id'");
						}
						if(!$changeReturn){
							admin::show_message('更新成功', $baseUrl.'&page='.$page);
						} else {
							return 3;
						}
					}
				}
			}
		}
		if($total=db::data_count($tbName, $where)){
			$var->list = db::get_list2($tbName, $f, $where, $order, $page, $pagesize);
			$var->multipage = multipage::parse($total, $pagesize, $page, $baseUrl.($method?'&method='.$method:'').(empty($multipageUrlAdd)?'':$multipageUrlAdd).'&page={page}', $pagestyle);
		}
		return 1;
	}
	public static function updateVars(){
		if($list = db::get_list2('vars', '`key`,`val`')){
			$vars = array();
			foreach($list as $v){
				$vars[$v['key']] = $v['val'];
			}
			cache::write_array('vars', $vars);
		}
	}
	public static function updateUserGroups(){
		if($list = db::get_list2('user_groups', '*')){
			$userGroups = array();
			foreach($list as $v){
				$userGroups[$v['key']] = $v;//array('id' => $v['id'], 'sort' => $v['sort'], 'name' => $v['name'], 'key' => $v['key']);
			}
			$userGroups2 = array();
			if ($userGroups) {
				foreach($userGroups as $k => $v) {
					$userGroups2[$v['id']] = $v;
				}
			}
			cache::write_array('userGroups', $userGroups);
			cache::write_array('userGroups2', $userGroups2);
		}
	}
	public static function updateQuestions(){
		if($list = db::get_list2('member_questions', 'id,question', 'sort')){
			$questions = array();
			foreach($list as $v){
				$questions[$v['id']] = $v['question'];
			}
			cache::write_array('questions', $questions);
		}
	}
	public static function getTplList($data){
		$rn = '';
		if($data=trim($data)){
			$sp = qscms::trimExplode("\n", $data);
			$tags = array();
			$isDel = false;
			$delId = '';
			$checkNames = array();
			foreach($sp as $v){
				if($v){//每一元素的规则
					$sp2 = qscms::trimExplode('|',$v);//分割元素
					$tag = array();
					$tag['title'] = $sp2[0];//字段标题
					$tag['key']   = $sp2[1];//数组的字段KEY
					if (!empty($sp2[2])) {//特殊类型
						$tmpSp = explode(',', $sp2[2]);
						switch ($tmpSp[0]){
							case 'code':
								$tag['type'] = $tmpSp[0];
								array_shift($tmpSp);
								array_shift($sp2);
								array_shift($sp2);
								array_shift($sp2);
								array_unshift($sp2, implode(',', $tmpSp));
								$tag['code'] = implode('|', $sp2);
							break;
							default:
								$types = qscms::trimExplode(';', $sp2[2]);//分割元素
								foreach($types as $typeIndex => $type){
									$sp3 = qscms::trimExplode(',', $type);//分割特殊类型
									$tag['type'][$typeIndex] = $sp3[0];//元素类型
									switch($tag['type'][$typeIndex]){
										case 'link'://超链接
											$sp4 = qscms::trimExplode('=', $sp3[1]);//分割超链接属性
											$tag['linkName'][$typeIndex] = $sp4[1];
											$tag['linkUrl'][$typeIndex]  = '$baseUrl&'.$sp4[0].'=$v['.$tag['key'].']';
										break;
										case 'link2':
											$tag['linkName'][$typeIndex] = $sp3[1];
											$tag['linkUrl'][$typeIndex]  = '$baseUrl&'.$sp3[2];
										break;
										case 'link3':
											$sp4 = qscms::trimExplode('=', $sp3[1]);//分割超链接属性
											$tag['linkName'][$typeIndex] = $sp4[1];
											$tag['linkUrl'][$typeIndex]  = '$baseUrl&'.$sp4[0].'=$v['.$tag['key'].']';
											$tag['linkId'][$typeIndex] = $sp4[0];
										break;
										default:
											array_shift($sp3);
											if (count($sp3) > 0) $tag['attach'][$typeIndex] = $sp3;
										break;
									}
								}
							break;
						}
						
					}
					if(isset($tag['type'])){
						$html = '';
						if (is_array($tag['type'])) {
							foreach($tag['type'] as $tId => $t){
								switch($t){
									case 'del':
										$html .= '<input type="checkbox" name="del[]" value="$v[\''.$tag['key'].'\']" class="del" />';
										$isDel = true;
										$delId = $tag['key'];
									break;
									case 'check':
										$html .= '<input type="checkbox" name="'.$sp2[3].'[]" value="$v[\''.$tag['key'].'\']" />';
										$checkNames[] = $sp2[3];
									break;
									case 'sort':
										$idId = ($delId?$delId:'id');
										$html .= '<input type="text" name="sort[]" value="$v[\''.$tag['key'].'\']" class="form-control" style="width:48px" /><input type="hidden" name="ids[]" value="$v[\''.$idId.'\']" />';
									break;
									case 'link':
										$html .= '<a href="'.$tag['linkUrl'][$tId].'" class="btn default btn-xs blue{if $var->menuAjax} ajaxify{/if}">'.$tag['linkName'][$tId].'</a>';
									break;
									case 'link2':
										$html .= '<a href="'.$tag['linkUrl'][$tId].'" class="btn default btn-xs blue{if $var->menuAjax} ajaxify{/if}">'.$tag['linkName'][$tId].'</a>';
									break;
									case 'link3':
										$_sp = explode('/', $tag['linkName'][$tId]);
										$html .= '<a href="'.$tag['linkUrl'][$tId].'" class="btn default btn-xs blue{if $var->menuAjax} ajaxify{/if}">{if $v['.$tag['linkId'][$tId].']}'.$_sp[1].'{else}'.$_sp[0].'{/if}</a>';
									break;
									case 'datetime':
										$html .= '{date $v[\''.$tag['key'].'\']}';
									break;
									case 'flag':
										$html .= '{'.$tag['attach'][$tId][0].' $v[\''.$tag['key'].'\']}';
									break;
									case 'var':
										$html .= $tag['attach'][$tId][0];
									break;
									case 'code':
										$html .= implode(',', $tag['attach'][$tId]);//$tag['attach'][$tId][0];
									break;
									case 'style':
										$html = '<div style="'.implode(';', $tag['attach'][$tId]).'">$v['.$tag['key'].']</div>';
									break;
									default:
										$html .= '$v['.$tag['key'].']';
									break;
								}
							}
						} else {
							switch ($tag['type']) {
								case 'code':
									$html .= $tag['code'];
								break;
							}
						}
					} else {
						$html = '$v['.$tag['key'].']';
					}
					$tag['html'] = $html;
					$tags[] = $tag;
				}
			}
			$tagCount = count($tags);
			$head = $body = '';
			$titles = qscms::arrid($tags, 'title');
			$htmls  = qscms::arrid($tags, 'html');
			foreach($titles as $title){
				$head.='<th>'.$title.'</th>';
			}
			$head = '<thead>
						<tr>'.$head.'</tr></thead>';
			foreach($htmls as $html){
				$body .= '<td>'.$html.'</td>';
			}
			$body  = '<tr>'.$body.'</tr>';
			$body = '<tbody>{loop $list $k $v}'.$body.'{/loop}</tbody>';
			if ($isDel) {
				if (!$checkNames) $body .= '<tr><td colspan="'.$tagCount.'"><input type="checkbox" id="checkDel" onclick="check_all(this,\'del[]\')" /><label for="checkDel">全选</label></td></tr>';
				else {
					$body .= '<tr><td><input type="checkbox" id="checkDel" onclick="check_all(this,\'del[]\')" /><label for="checkDel">全选</label></td>';
					$__count = count($checkNames);
					for ($__i = 0; $__i < $__count; $__i++) {
						$__isEnd = $__i + 1 == $__count;
						$body .= '<td'.($__isEnd ? ' colspan="'.($tagCount - $__count).'"' : '').'><input type="checkbox" id="check_'.$checkNames[$__i].'" onclick="check_all(this,\''.$checkNames[$__i].'[]\')" /><label for="check_'.$checkNames[$__i].'">全选</label></td>';
					}
					$body .= '</tr>';
				}
			} elseif ($checkNames) {
				$body .= '<tr>';
				$__count = count($checkNames);
				for ($__i = 0; $__i < $__count; $__i++) {
					$__isEnd = $__i + 1 == $__count;
					$body .= '<td'.($__isEnd ? ' colspan="'.($tagCount - $__count + 1).'"' : '').'><input type="checkbox" id="check_'.$checkNames[$__i].'" onclick="check_all(this,\''.$checkNames[$__i].'[]\')" /><label for="checkDel">全选</label></td>';
				}
				$body .= '</tr>';
			}
			$body .= '<tr><td colspan="'.$tagCount.'">{echo !empty($multipage) ? $multipage : \'\'}</td></tr>';
			$body .= '<tr><td colspan="'.$tagCount.'"><input type="submit" value="提交" class="btn btn-default" /></td></tr>';
			$rn = '
			<form class="form-horizontal ajaxForm table-responsive" method="post" enctype="application/x-www-form-urlencoded" onsubmit="return confirm(\'确定提交吗？\')">{echo $var->sys_hash_code}
	<table class="table table-striped table-bordered table-hover" id="qsList">'.$head.$body.'</table>
	</form>';
			$rn = '<?php '.parse_php::parse($rn).'?>';
			return $rn;
		}
	}
	public static function insert($tbName, $vars, $returnId = false){
		if(form::is_form_hash()){
			$datas = array();
			foreach($vars as $k=>$v){
				if(is_array($v)){
					$k2   = $v['name'];
					$must = $v['must'];
				} else {
					$k2   = $v;
					$must = true;
				}
				$v2 = $_POST[$k2];
				if($v2==='' && $must)return -1;//有字段为必填，找到有没有填写的
				$datas[$k] = $v2;
			}
			if($datas){
				return db::insert($tbName, $datas, $returnId);
			} else return -2;//没有数据可以插入
		}
		return 0;//没有提交
	}
	public static function update($tbName, $vars, $id, $idKey = 'id'){
		if(form::is_form_hash()){
			if($_POST['isEdit']){
				$datas = array();
				foreach($vars as $k=>$v){
					if(is_array($v)){
						$k2   = $v['name'];
						$must = $v['must'];
					} else {
						$k2   = $v;
						$must = true;
					}
					$v2 = $_POST[$k2];
					if($v2 === '' && $must)return -1;//有字段为必填，找到有没有填写的
					$datas[$k] = $v2;
				}
				if($datas){
					$where = $idKey.'=\''.$id.'\'';
					return db::update($tbName, $datas, $where);
				} else return -2;//没有数据可以插入
			} else return -3;//非法操作，没有编辑的属性
		} else {
			$var = qscms::v('_G');
			if($item = db::one($tbName, '*', $idKey.'=\''.$id.'\'')){
				$datas = array();
				$var->update = true;
				foreach($vars as $k=>$v) $datas[$v]=$item[$k];
				$var->item = $datas;
			} else {
				$var->item = array();
				$var->update = false;
			}
		}
	}
	public static function parseTxt($txt){
		$txt = preg_replace('/\s+/s', '', $txt);
		$rn = array();
		$f1 = 0;
		$i = 0;
		do {
			$i ++;
			$f2 = strpos($txt, '{', $f1);
			if ($f2 === false) {
				$t = substr($txt, $f1);
				if ($t) {
					$sp = explode(';', $t);
					foreach ($sp as $v){
						$rn[] = array('name' => $v);
					}
					//$rn[] = array('name' => $t);
				}
			} else {
				$t = string::dg_string($txt, '{', '}', $f2);
				if ($t) {
					$name = substr($txt, $f1, $f2 - $f1);
					$sp = qscms::trimExplode(';', $name);
					$name = array_pop($sp);
					foreach($sp as $v) $rn[] = array('name' => $v);
					$arr = self::parseTxt(substr($t, 1, -1));
					if ($arr) {
						$rn[] = array('name' => $name, 'sub' => self::parseTxt(substr($t, 1, -1)));
					} else $rn[] = array('name' => $name);
				} else $rn[] = array('name' => substr($txt, $f1, $f2 - $f1));
				$f1 = $f2 + strlen($t);
			}
			if ($i == 50) break;
		} while($f2 !== false);
		return $rn;
	}
	public static function updateExpress($id) {
		if ($e = db::one('express', '*', "id='$id'")) {
			$arr = self::parseTxt($e['city1']);
			if (is_array($arr)) {
				cache::write_text('citys_'.$id.'_city1', string::json_encode($arr));
			}
			$arr = self::parseTxt($e['city2']);
			if (is_array($arr)) {
				cache::write_text('citys_'.$id.'_city2', string::json_encode($arr));
			}
			$arr = self::parseTxt($e['city3']);
			if (is_array($arr)) {
				cache::write_text('citys_'.$id.'_city3', string::json_encode($arr));
			}
		}
	}
	/**
	 * IFRAME 图片上传
	 */
	public static function ifUpload($datas){
		$datas = form::get4($datas, array('upName'));
		$upName = $datas['upName'];
		$u = new upload();
		$rs = $u->toupload($upName, 'image');
		if ($rs['count'] == 1) {
			return $rs['info'][$upName]['db_id'];
		}
		return '上传失败';
	}
}
?>