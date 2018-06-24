<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
/*
 * 定义模板目录
 */
set_time_limit(0);
$tplRoot = qd(self::getCfgPath('/system/tplRoot'));
$cacheRoot = d(self::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/tpl'));
template::initialize($tplRoot, $cacheRoot);
template::addPath($var->p0, $var->p0);
language::load('install');
language::$lang_name='install';
$var = qscms::v('_G');
$step = $var->getInt('gp_step', 1);
$install_locked=file_exists(MODULE_ROOT.'install.lock');
if(!$install_locked)$step++;
if($step>2){
	$install_step=$step-2;
} else $install_step=0;
if($install_step==3||$install_step==2)unset($config);
$install_locked && ($step=1) && ($install_step=0);
$baseUrl = '/'.$var->p0.'/';
switch($install_step){
	case 1:
		$next_step=true;
		$func_check_list = server::function_exists('mysql_connect','fsockopen','gethostbyname','file_get_contents','xml_parser_create');
		foreach($func_check_list as $v){
			if(!$v){
				$next_step=false;
				break;
			}
		}
		$dir_file_list=array(
			array('type'=>'dir' , 'path' => d(qscms::getCfgPath('/system/cacheDirRoot')) ),
			//array('type'=>'dir' , 'path' => qd(qscms::getCfgPath('/system/tplRoot')) ),
			array('type' => 'dir', 'path' => md('install')),
			array('type'=>'file', 'path' => qd(qscms::getCfgPath('/system/cfgRoot')).'global.php' )
		);
		$next_step && ($next_step = server::dirfile_check($dir_file_list));
		$env_list = array
		(
			'os' => array('c' => 'PHP_OS', 'r' => 'notset', 'b' => 'unix'),
			'php' => array('c' => 'PHP_VERSION', 'r' => '4.0', 'b' => '5.0'),
			'attachmentupload' => array('r' => 'notset', 'b' => '2M'),
			'gdversion' => array('r' => '1.0', 'b' => '2.0'),
			'diskspace' => array('r' => '10M', 'b' => 'notset'),
		);
		if ($next_step) $next_step = server::env_check($env_list);
		else server::env_check($env_list);
	break;
	case 2:
		$post = false;
		$config = qscms::getConfig('global');
		$config['sys_user'] = !empty($config['sys_user']) ? $config['sys_user'] : '';
		$config['sys_pwd']  = '';//!empty($config['sys_pwd']) ? $config['sys_pwd'] : '';
		$config['auth_key']         = md5($var->domains['domain0']);
		$config['sys_admin_folder'] = !empty($config['sys_admin_folder']) ? $config['sys_admin_folder'] : 'admin';
		$config['db_host']          = !empty($config['db_host']) ? $config['db_host']: 'localhost';
		$config['db_port']          = !empty($config['db_port']) ? $config['db_port']: '3306';
		$config['db_name']          = !empty($config['db_name']) ? $config['db_name']: 'qscms';
		$config['db_user']          = !empty($config['db_user']) ? $config['db_user']: 'root';
		$config['db_table_pre']     = !empty($config['db_table_pre'])? $config['db_table_pre'] : 'qs_';
		$config['db_pwd']           = !empty($config['db_pwd']) ? $config['db_pwd']: '';
		$msql_user = $msql_pwd = '';
	break;
	case 3:
		$post = false;
		$_POST && ($post = true) && extract($_POST);
		if($post){
			if($config['sys_user']&&$config['sys_pwd']&&$config['db_host']&&$config['db_port']&&$config['db_name']&&$config['db_pwd']&&$config['db_table_pre']){
				$link  = @mysql_connect($config['db_host'].':'.$config['db_port'],$config['db_user'],$config['db_pwd']);
				$error = '';
				$db_user_exists = true;
				if(!$link && $msql_user && $msql_pwd){
					if(!$link = @mysql_connect($config['db_host'].':'.$config['db_port'], $msql_user, $msql_pwd)){
						$errno = mysql_errno($link);
						$error = '数据库帐号密码不存在或错误，且数据库管理员帐号密码不存在或错误';
						$error .='<br />'. mysql_error($link);
						if($errno == 1045) {
							$error.='<br />'.language::get('database_errno_1045');
						} elseif($errno == 2003) {
							$error.='<br />'.language::get('database_errno_2003');
						} else {
							$error.='<br />'.language::get('database_connect_error');
						}
					} else $db_user_exists = false;
				} elseif(!$link) {
					$errno = mysql_errno($link);
					$error = mysql_error($link);
					if($errno == 1045) {
						$error.='<br />'.language::get('database_errno_1045');
					} elseif($errno == 2003) {
						$error.='<br />'.language::get('database_errno_2003');
					} else {
						$error.='<br />'.language::get('database_connect_error');
					}
				}
				if($link){
					if(mysql_get_server_info() > '4.1') {
						$serverset = 'character_set_connection='.DB_ENCODING.', character_set_results='.DB_ENCODING.', character_set_client=binary';
						$serverset .= mysql_get_server_info() > '5.0.1' ? ', sql_mode=\'\'' : '';
						$serverset && mysql_query("SET $serverset", $link);
						mysql_query("CREATE DATABASE IF NOT EXISTS `$config[db_name]` DEFAULT CHARACTER SET ".DB_ENCODING, $link);
					} else {
						mysql_query("CREATE DATABASE IF NOT EXISTS `$config[db_name]`", $link);
					}
					$query = mysql_query("SHOW DATABASES LIKE '$config[db_name]'", $link);
					if(mysql_num_rows($query)>0){
						!$db_user_exists&&mysql_query("grant all privileges on `$config[db_name]`.* to '$config[db_user]'@'$config[db_host]' identified by '$config[db_pwd]'",$link);
					} else $error='数据库无法选择或不存在，请检查是否具备权限';
					if(mysql_select_db($config['db_name'], $link)){
						$sqlFile = MODULE_ROOT.'install.sql';
						if(file_exists($sqlFile)){
							/*$ln = chr(10);
							if($f = fopen($sqlFile, 'rb')){
								$sql = '';
								while (!feof($f)) {
									$str = fgets($f);
									if (substr($str, -2)==';'.$ln) {
										$sql .= substr($str, 0, -2);
										$sql = str_replace('{pre}', $config['db_table_pre'], $sql);
										mysql_query($sql, $link);
										$sql = '';
									} else {
										$sql .= $str;
									}
								}
								fclose($f);
							}*/
							$preFix    = 'version:1.0;©2013 www.qscms.com;author:373718549@qq.com';
							$preFixLen = strlen($preFix);
							$readLen   = $fsize = 0;
							if ($f = @fopen($sqlFile, 'rb')) {
								fseek($f, $preFixLen);
								$readLen += $preFixLen;
								$allCount = intval(trim(fread($f, 10)));
								$readLen += 10;
								$c = 0;
								fseek($f, 0, SEEK_END);
								$fsize = ftell($f);
								fseek($f, $readLen);
								while ($readLen < $fsize) {
									$size = intval(trim(fread($f, 10)));
									$readLen += $size + 10;
									$sql = fread($f, $size);
									//$sql = str_replace('`{pre}', '`'.$config['db_table_pre'], $sql);
									$sql = str_replace('{pre}', $config['db_table_pre'], $sql);
									mysql_query($sql, $link);
									$c++;
								}
								@fclose($f);
							}
						}
					}
					$config['sys_salt'] = qscms::salt();
					$config['sys_pwd']  = qscms::saltPwd($config['sys_salt'], $config['sys_pwd']);
					file::write(qd(qscms::getCfgPath('/system/cfgRoot')).'global.php', '<?php !defined(\'IN_QSCMS\')&&exit(\'error\');$config='.string::formatArray($config).';?>');
					$step++;
					$install_step++;
					mysql_free_result($query);
					mysql_close($link);
				}
			}
		}
	break;
	case 4:
		touch(MODULE_ROOT.D.'install.lock');
		qscms::gotoUrl('/'.qscms::getCfgPath('/global/sys_admin_folder').'/');
	break;
}
//include template::load('install');
?>