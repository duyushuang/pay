<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class p{
	private static $tplRoot, $cfgRoot, $cacheRoot, $tplSuffix, $cacheSuffix, $cfg = array(), $subTpl = array();
	public static function test(){
		echo 'hellow p test';
	}
	public static function initialize(){
		self::$cfgRoot = qd(qscms::getCfgPath('/system/programRoot').'config/');
		self::set(
			qd(qscms::getCfgPath('/system/programRoot').'code/'), 
			d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/program')), 
			'.php', 
			'.php'
		);
	}
	public static function set($tplRoot, $cacheRoot, $tplSuffix, $cacheSuffix){
		self::$tplRoot   = $tplRoot;
		self::$tplSuffix = $tplSuffix;
		self::$cacheRoot = $cacheRoot;
		self::$cacheSuffix = $cacheSuffix;
	}
	public static function addPath($tplPath, $cachePath){
		$tplPath = strtr($tplPath, '/', D);
		$cachePath = strtr($cachePath, '/', D);
		substr($tplPath, -1) != D && $tplPath .= D;
		substr($cachePath, -1) != D && $cachePath .= D;
		self::$tplRoot .= $tplPath;
		self::$cachePath .= $cachePath;
	}
	public static function load($tplName){
		self::$subTpl = array();
		$tplName = strtr($tplName, '/', D);
		$tpl     = self::$tplRoot.$tplName.self::$tplSuffix;
		$cache   = self::$cacheRoot.$tplName.self::$cacheSuffix;
		if (file_exists($tpl)) {
			$goParse = !file_exists($cache) || filemtime($cache) < filemtime($tpl);
			if (!$goParse) return $cache;//.'.ck.php';
			else {//开始解释程序模版
				if (file::createFolderToFile($cache)) {
					$cacheLock = $cache.'.lock';
					if (!file_exists($cacheLock)) {//解析
						touch($cacheLock);//锁定解析
						$cacheStatus = self::parse($tpl, $cache);
						unlink($cacheLock);
						if ($cacheStatus === true) return $cache;//.'.ck.php';
						throw new e_qscms('解析程序文件'.u($tpl).'失败:'.$cacheStatus);
					} else throw new e_qscms('程序文件'.u($tpl).'正在解析，请稍后...');
				} else throw new e_qscms('创建缓存文件夹失败，请检查是否有权限');
			}
		} elseif (file_exists($cache)) {
			return $cache;//.'.ck.php';
		} else {
			throw new e_qscms('程序模版'.u($tpl).'不存在');
		}
	}
	private static function trimExit($data){
		$data = preg_replace('/^\s*<\?php\s+exit(\(\))?\s*;\s*\?>/', '', $data);
		$data = ltrim($data);
		return $data;
	}
	private static function parseCfg($name){
		$cfgFile = self::$cfgRoot.$name.'.php';
		if (file_exists($cfgFile)) {
			$data = self::trimExit(file::read($cfgFile));
			$len = strlen($data);
			$atStart = $atStr = false;
			$atName = false;
			$name = $str = '';
			$arr = array();
			$atKuo = $atKey = $atVal = false;
			$key = $val = '';
			$atStr2 = false;
			$str2Flag = '';
			$zhuanC = 0;
			$lastS = '';
			$l = $r = 0;
			for ($i = 0; $i < $len; $i++) {
				$s = $data{$i};
				if ($s == '\\') $zhuanC++;
				if (!$atStart) {
					if (!$atName) {
						if ($s == '[') {
							$atName = true;
							$name = '';
						}
					} else {
						if ($s != ']') $name .= $s;
						else {
							$arr[$name] = array();
							$atStart = true;
							$atName = false;
							$atKey = true;
							$atVal = false;
							$key = $val = '';
						}
					}
				} else {
					if (!$atStr) {
						if (!in_array($s, array(' ', "\t", "\r", "\n"))) {
							$key = $s;
							$atStr = true;
						}
					} else {
						if (!$atName) {
							if ($atKey) {
								if ($s == '[') {
									$key = trim($key);
									if ($key) $arr[$name][$key] = false;
									$atName = true;
									$name = '';
								} else {
									if ($s != '=') {
										$key .= $s;
									} else {
										$atVal = true;
										$atKey = false;
										$val = '';
										if ($i + 1 < $len) {
											if ($data{$i + 1} == '{') {
												$atKuo = true;
												$atStr2 = false;
												$val = $data{$i+1};
												$l = 1;
												$r = 0;
												$i++;
											}
										} else {//结束
											$key = trim($key);
											$arr[$name][$key] = false;
										}
									}
								}
							} elseif ($atVal) {
								if ($atKuo) {//中括号
									if (!$atStr2) {
										$val .= $s;
										if ($s == '\'' || $s == '"') {
											$atStr2 = true;
											$str2Flag = $s;
											$zhuanC = 0;
										} else {
											if ($s == '{') $l++;
											elseif ($s == '}') {
												$r++;
												if ($l == $r) {
													$key = trim($key);
													$arr[$name][$key] = trim(substr($val, 1, -1));
													$atKey = true;
													$key = '';
												}
											}
										}
									} else {
										$val .= $s;
										if ($s == $str2Flag && $zhuanC % 2 == 0) {
											$atStr2 = false;
										}
									}
								} else {
									if ($s == "\n" || $s == '[' || $i + 1 == $len) {
										if ($i + 1 == $len) $val .= $s;
										$key = trim($key);
										$val = trim($val);
										$arr[$name][$key] = $val;
										if ($s == '[') {
											$atName = true;
											$name = '';
										} else {
											$atKey = true;
											$key = '';
										}
									} else $val .= $s;
								}
							}
						} else {
							if ($s != ']') $name .= $s;
							else {
								isset($arr[$name]) || $arr[$name] = array();
								$atName = false;
								$atKey = true;
								$atVal = false;
								$key = $val = '';
							}
						}
					}
				}
				if ($s != '\\') $zhuanC = 0;
				$lastS = $s;
			}
			return $arr;
		} else {
			return '配置文件:'.u($cfgFile).'不存在';
		}
	}
	private static function addCfg($type, $name, $data, $vars = array()){
		isset(self::$cfg[$type]) || self::$cfg[$type] = array();
		if (!empty($vars)) {
			self::$cfg[$type][$name] = array($data, $vars);
		} else {
			self::$cfg[$type][$name] = $data;
		}
	}
	private static function getCfg($type, $name){
		if (isset(self::$cfg[$type][$name])) return self::$cfg[$type][$name];
		return '';
	}
	private static function _name($str){
		return substr(md5($str), 8, 16);
	}
	public static function parse($tpl, $cache){
		$data = file::read($tpl);
		$code = self::parseStr($data);
		//echo $code;
		$code0 = '$__tplModify=filemtime(\''.addcslashes($cache, '\\').'\');';
		$time = '';
		foreach (self::$subTpl as $file) {
			$time && $time .= '||';
			$time .= 'filemtime(\''.addcslashes($file, '\\').'\')>$__tplModify';
		}
		//$code0 .= 'if ('.$time.'){p::parse(\''.addcslashes($tpl, '\\').'\',\''.addcslashes($cache, '\\').'\');include(\''.addcslashes($cache, '\\').'\');exit;}include(\''.addcslashes($cache, '\\').'\');';
		$time && $code0 .= 'if ('.$time.'){p::parse(\''.addcslashes($tpl, '\\').'\',\''.addcslashes($cache, '\\').'\');include(\''.addcslashes($cache, '\\').'\');exit;}';
		$code = '<?php '.$code0.$code.'?>';
		/*$code0 = '<?php '.$code0.'?>';*/
		//file::write($cache.'.ck.php', $code0);
		file::write($cache, $code);
		return true;
	}
	private static function parseStr(&$data){
		$data = self::trimExit($data);
		$list = self::parseTab($data);
		$code = self::parseCmd($list);
		return $code;
	}
	private static function formatVar($data){
		$len = strlen($data);
		$var = '';
		$atVar = false;
		$code = '';
		$lastS = '';
		$zhuanC = 0;
		for ($i = 0; $i < $len; $i++) {
			$s = $data{$i};
			$o = ord($s);
			$end = $i + 1 == $len;
			if ($s == '\\') $zhuanC++;
			if (!$atVar) {
				if ($s == '$' && $zhuanC % 2 == 0) {
					$o = ord($data{$i + 1});
					if ($o == 0x5f || ($o >= 0x41 && $o <= 0x5a) || ($o >= 0x61 && $o <= 0x7a) || ($o >= 0x7f && $o <=0xff)) {
						$atVar = true;
					} else $code .= $s;
				} else $code .= $s;
			} else {
				if($o == 0x5f  || $o == 0x3e || ($o >= 0x30 && $o <= 0x39) || ($o >= 0x41 && $o <= 0x5a) || ($o >= 0x61 && $o <= 0x7a) || ($o >= 0x7f && $o <= 0xff)){
					$var .= $s;
					if ($end) {
						$vName = isset(self::$cfg['变量'][$var]) ? '$'.self::$cfg['变量'][$var] : '$'.$var;//'$v'.self::_name($var);
						//echo 'v='.$var, '|',$vName, "\n";
						$code .= $vName;
					}
				} else {
					$atVar = false;
					//print_r(self::$cfg['变量']);
					$vName = isset(self::$cfg['变量'][$var]) ? '$'.self::$cfg['变量'][$var] : '$'.$var;//'$v'.self::_name($var);
					//echo 'v='.$var, '|',$vName, "\n";
					//echo $var, '=', $vName, "\n\n";
					$code .= $vName.$s;
					$var = '';
					
				}
			}
			if ($s != '\\') $zhuanC = 0;
			$lastS = $s;
		}
		return $code;
	}
	private static function parseIF($data){
		$data = str_replace('且', '&&', $data);
		$data = str_replace('或', '||', $data);
		return $data;
	}
	private static function replaceMethod($data){
		$data = self::formatVar($data);
		$sp = explode('->', $data);
		foreach ($sp as &$v) {
			if ($v !== '' && substr($v, 0, 1) != '$') {
				if (preg_match('/(.+?)(\(.*)/', $v, $m)) {
					$funName0 = $m[1];
					$funName = self::getCfg('方法', $funName0);
					$funName || $funName = 'f'.self::_name($funName0);
					$v = $funName.$m[2];
				} else {
					$v = 'v'.self::_name($v);
				}
			}
		}
		return implode('->', $sp);
	}
	private static function encodeName($data){
		//echo $data, "\n";
		$data = self::formatVar($data);
		//$data = preg_replace('/([\x5f\x3e\x30-\x39\x41-\x5a\x61-\x7a\x7f-\xff]+?)\(\*\)/e', '(isset(self::$cfg[\'函数\'][\'$1\']) ? self::$cfg[\'函数\'][\'$1\'] : \'f\'.self::_name(\'$1\'))', $data);
		$data = preg_replace('/([\x5f\x3e\x30-\x39\x41-\x5a\x61-\x7a\x7f-\xff]+?)\(\*\)/e', '(isset(self::$cfg[\'函数\'][\'$1\']) ? self::$cfg[\'函数\'][\'$1\'] : \'$1\')', $data);
		$data = preg_replace('/([\x5f\x3e\x30-\x39\x41-\x5a\x61-\x7a\x7f-\xff]+?)\(/e', '(isset(self::$cfg[\'函数\'][\'$1\']) ? self::$cfg[\'函数\'][\'$1\'] : \'$1\').\'(\'', $data);
		//$data = preg_replace('/(\$[^\s->]+)((?:->[\x5f\x3e\x30-\x39\x41-\x5a\x61-\x7a\x7f-\xff()]+)+)/e', '\'$1\'.self::replaceMethod(\'$2\')', $data);
		//$data = preg_replace('/new\s+(.+?)\(/e', '\'new c\'.self::_name(\'$1\').\'(\'', $data);
		//echo $data, "\n\n";
		return $data;
	}
	private static function parseCmd($list, $type = ''){
		$code = '';
		if (is_array($list)) {
			if (isset($list[0])) {
				foreach ($list as $v) {
					//$code = self::parseCmd($v);
					/*$s = new p_stream($v['h']);
					$cmd = $s->getCmd();
					if ($cmd == '引用配置') {
						$arr = self::parseCfg($s->getStr());
						if (!is_array($arr)) return $arr;
						self::$cfg = array_merge(self::$cfg, $arr);
					} else {
						switch ($cmd) {
							case '函数':
								$arr = $s->getFuncName();
								$code = 'function f'.self::_name($arr[0]).'(';
								$vars = '';
								foreach ($arr[1] as $k1 => $v1) {
									$vars && $vars .= ',';
									$vars .= '$v'.self::_name($k1);
									if ($v1 !== false) {
										$vars .= '=';
										if (is_numeric($v1)) $vars .= $v1;
										else $vars .= '\''.addcslashes($v1, '\\\'').'\'';
									}
								}
								$code .= $vars;
								$code .= '){';
								if (isset($v['c'])) {
									$code .= self::parseCmd($v['c']);
								}
								$code .= '}';
							break;
							default:
								$s->reset();
								//print_r($s->getFuncName());
							break;
						}
					}*/
					$h = $v['h'];
					$h = self::encodeName($h);
					//$eArr = self::encodeCmdStr($h);
					//$data = $eArr[0];
					$data = $h;
					$isMatch = false;
					if (preg_match('/^([^\s]+)(?:\s+(.+))?$/s', $data, $matches)) {
						switch ($matches[1]) {
							case 'config':
							case '引用配置':
								$s = new p_stream($matches[2]);
								$arr = self::parseCfg($s->getStr());
								if (!is_array($arr)) return $arr;
								self::$cfg = array_merge(self::$cfg, $arr);
								$isMatch = true;
							break;
							case 'require':
							case 'sub':
							case '包含':
								$s = new p_stream($matches[2]);
								$file = self::$tplRoot.$s->getStr().self::$tplSuffix;
								$code .= self::parseStr(file::read($file));
								self::$subTpl[] = $file;
								$isMatch = true;
							break;
							case 'include':
							case '引用':
								$s = new p_stream($matches[2]);
								$code .= 'include(p::load(\''.addcslashes($s->getStr(), '\\\'').'\'));';
								$isMatch = true;
							break;
							case 'class':
							case '类':
								$className1 = $matches[2];
								//$className1 = 'c'.self::_name($className);
								//echo $className, '|', $className1;
								//self::addCfg('类', $className, $className1);
								$code .= 'if(!class_exists(\''.$className1.'\')){class '.$className1.'{';
								if (isset($v['c'])) {
									$code .= self::parseCmd($v['c'], '类/');
								}
								$code .= '}}';
								$isMatch = true;
							break;
							case 'private':
							case '私有':
								if (isset($v['c'])) {
									$code .= self::parseCmd($v['c'], $type.'私有/');
								}
								$isMatch = true;
							break;
							case 'public':
							case '公共':
								if (isset($v['c'])) {
									$code .= self::parseCmd($v['c'], $type.'公共/');
								}
								$isMatch = true;
							break;
							case 'var':
							case '变量':
								$vType = '动态';
								if (!empty($matches[2])) {
									if ($matches[2] == '静态' || $matches[2] == 'static') $vType = '静态';
								}
								if (isset($v['c'])) {
									$code .= self::parseCmd($v['c'], $type.'变量/'.$vType.'/');
								}
								$isMatch = true;
							break;
							case 'construct':
							case '构造':
								$data = self::encodeName($data);
								$s = new p_stream($data);
								$arr = $s->getFuncName();
								$code .= 'public function __construct(';
								$vars = '';
								foreach ($arr[1] as $k1 => $v1) {
									$vars && $vars .= ',';
									//$vName = '$v'.self::_name($k1);
									//self::$cfg['变量'][$k1] = $vName;
									$vars .= $k1;//$vName;
									if ($v1 !== false) {
										$vars .= '=' . $v1;
										//if (is_numeric($v1)) $vars .= $v1;
										//else $vars .= '\''.addcslashes($v1, '\\\'').'\'';
									}
								}
								$code .= $vars;
								$code .= '){';
								if (isset($v['c'])) {
									$code .= self::parseCmd($v['c']);
								}
								$code .= '}';
								$isMatch = true;
							break;
							case 'destruct':
							case '析构':
								$data = self::encodeName($data);
								$s = new p_stream($data);
								$arr = $s->getFuncName();
								$code .= 'public function __destruct(';
								$vars = '';
								foreach ($arr[1] as $k1 => $v1) {
									$vars && $vars .= ',';
									//$vName = '$v'.self::_name($k1);
									//self::$cfg['变量'][$k1] = $vName;
									$vars .= $k1;//$vName;
									if ($v1 !== false) {
										$vars .= '=' . $v1;
										//if (is_numeric($v1)) $vars .= $v1;
										//else $vars .= '\''.addcslashes($v1, '\\\'').'\'';
									}
								}
								$code .= $vars;
								$code .= '){';
								if (isset($v['c'])) {
									$code .= self::parseCmd($v['c']);
								}
								$code .= '}';
								$isMatch = true;
							break;
							case 'method':
							case '方法':
								$jType = '';
								if (!empty($matches[2])) {
									$jType = $matches[2] == '静态' || $matches[2] == 'static' ? 'static ' : '';
								}
								if (isset($v['c'])) {
									$mType = '';
									switch ($type) {
										case '类/私有/':
											$mType = 'private ';
										break;
										case '类/公共/':
											$mType = 'public ';
										break;
									}
									$mType .= $jType;
									foreach ($v['c'] as $__v) {
										$h = $__v['h'];
										$h = self::encodeName($h);
										$s = new p_stream($h);
										$arr = $s->getFuncName();
										$funName0 = $arr[0];
										$funName = self::getCfg('方法', $funName0);
										if (!$funName) {
											//$funName = 'f'.self::_name($arr[0]);
											//self::addCfg('方法', $funName0, $funName);
											$funName = $funName0;
										}
										$code .= $mType."function $funName(";
										$vars = '';
										foreach ($arr[1] as $k1 => $v1) {
											$vars && $vars .= ',';
											//$vName = '$v'.self::_name($k1);
											//self::$cfg['变量'][$k1] = $vName;
											$vars .= $k1;//$vName;
											if ($v1 !== false) {
												$vars .= '=' . $v1;
												//if (is_numeric($v1)) $vars .= $v1;
												//else $vars .= '\''.addcslashes($v1, '\\\'').'\'';
											}
										}
										$code .= $vars;
										$code .= '){';
										if (isset($__v['c'])) {
											$code .= self::parseCmd($__v['c']);
										}
										$code .= '}';
									}
								}
								$isMatch = true;
							break;
							case 'function':
							case 'func':
							case '函数':
								$s = new p_stream($matches[2]);
								$arr = $s->getFuncName();
								/*if (!preg_match('/^f[a-z0-9]{16}$/', $arr[0])) {
									$funName = 'f'.self::_name($arr[0]);
									self::$cfg['函数'][$arr[0]] = $funName;
								} else $funName = $arr[0];*/
								$funName = $arr[0];
								$code .= 'if(!function_exists(\''.$funName.'\')){function '.$funName.'(';
								$vars = '';
								foreach ($arr[1] as $k1 => $v1) {
									$vars && $vars .= ',';
									//$vName = '$v'.self::_name($k1);
									//self::$cfg['变量'][$k1] = $vName;
									$vars .= $k1;//$vName;
									if ($v1 !== false) {
										$vars .= '=' . $v1;
										//if (is_numeric($v1)) $vars .= $v1;
										//else $vars .= '\''.addcslashes($v1, '\\\'').'\'';
									}
								}
								$code .= $vars;
								$code .= '){';
								if (isset($v['c'])) {
									$code .= self::parseCmd($v['c']);
								}
								$code .= '}}';
								$isMatch = true;
							break;
							case 'if':
							case '如果':
								$code .= 'if (';
								$code .= self::parseIF($matches[2]);
								$code .= '){';
								if (isset($v['c'])) {
									$code .= self::parseCmd($v['c']);
								}
								$code .= '}';
								$isMatch = true;
							break;
							case 'else':
							case '或者':
								if (!empty($matches[2])) {
									$code .= 'elseif('.self::parseIF($matches[2]).'){';
								} else {
									$code .= 'else {';
								}
								if (isset($v['c'])) {
									$code .= self::parseCmd($v['c']);
								}
								$code .= '}';
								$isMatch = true;
							break;
							case 'return':
							case '返回':
								$code .= 'return';
								if (isset($matches[2])) {
									$code .= ' '.$matches[2];
								}
								$code .= ';';
								$isMatch = true;
							break;
							case 'echo':
							case '输出':
								$code .= 'echo '.$matches[2].';';
								$isMatch = true;
							break;
							case 'print':
							case '打印':
								$code .= 'print_r('.$matches[2].');';
								$isMatch = true;
							break;
							case 'loop':
							case '循环':
								$where = $matches[2];
								if (preg_match('/^(\$[^\s]+?)\s*(.+?)\s*(?:到|to)\s*(.+)$/u', $where, $m)) {
									$code .= 'for ('.$m[1].'='.$m[2].';'.$m[1].'<='.$m[3].';'.$m[1].'++){';
								} else {
									$where = self::parseIF($where);
									$code .= 'while('.$where.'){';
								}
								if (isset($v['c'])) {
									$code .= self::parseCmd($v['c']);
								}
								$code .= '}';
								$isMatch = true;
							break;
							case 'foreach':
							case 'each':
							case '遍历':
								$where = $matches[2];
								if (preg_match('/^(.+?)\s*(?:到|to)\s*(.+?)\s*(?:和|and)\s*(.+)$/', $where, $m)) {
									$code .= 'foreach('.$m[1].' as '.$m[2].'=>'.$m[3].'){';
								} elseif (preg_match('/^(.+?)\s*到\s*(.+)$/', $where, $m)) {
									$code .= 'foreach('.$m[1].' as '.$m[2].'){';
								} else {
									$code .= 'foreach('.$where.' as $k=>$v){';
								}
								if (isset($v['c'])) {
									$code .= self::parseCmd($v['c']);
								}
								$code .= '}';
								$isMatch = true;
							break;
							case 'switch':
							case '判断':
								$where = $matches[2];
								$where = self::parseIF($where);
								$code .= 'switch('.$where.'){';
								if (isset($v['c'])) {
									$code .= self::parseCmd($v['c']);
								}
								$code .= '}';
								$isMatch = true;
							break;
							case 'case':
							case '等于':
								$code .= 'case '.$matches[2].':';
								if (isset($v['c'])) {
									$code .= self::parseCmd($v['c']);
								}
								$code .= 'break;';
								$isMatch = true;
							break;
							case 'default':
							case '默认':
								$code .= 'default:';
								if (isset($v['c'])) {
									$code .= self::parseCmd($v['c']);
								}
								$code .= 'break;';
								$isMatch = true;
							break;
							default:
								//echo $matches[1], "\n";
							break;
						}
					}
					if (!$isMatch) {
						switch ($type) {
							case '类/私有/变量/动态/':
								$code .= 'private '.$data.';';
							break;
							case '类/公共/变量/动态/':
								$code .= 'public '.$data.';';
							break;
							case '类/私有/变量/静态/':
								$code .= 'private static '.$data.';';
							break;
							case '类/公共/变量/静态/':
								$code .= 'public static '.$data.';';
							break;
							default:
								if ($data{0} == '#') {
									if (preg_match('/^#([^\s]+)(?:\s+(.+))?$/s', $data, $m)) {
										switch ($m[1]) {
											case 'define':
											case '预定义':
												if (isset($v['c'])) {
													$c = self::parseCmd($v['c']);
													if ($c) {
														$s = new p_stream($m[2]);
														$arr = $s->getFuncName();
														self::addCfg('宏', $arr[0], $c, $arr[1]);
													}
												}
											break;
											default:
												$s = new p_stream(substr($data, 1));
												$arr = $s->getFuncName();
												$data = $arr[0];
												$c = self::getCfg('宏', $data);
												if ($c) {
													if (is_array($c)) {
														$vars = $c[1];
														if (!empty($arr[1]) && count($arr[1]) == count($vars)) {
															$vals = array_keys($arr[1]);
															$__i = 0;
															foreach ($vars as &$__v) {
																$__v = $vals[$__i];
																$__i++;
															}
														}
														$c = $c[0];
														foreach ($vars as $__k => $__v) {
															$c = str_replace($__k, $__v, $c);
														}
														$code .= $c;
													} else {
														$code .= $c;
													}
												}
											break;
										}
									}
								} else {
									$code .= self::parseIF($data).';';
								}
							break;
						}
						//$code .= $data.';'."\n";
					}
					/*//匹配函数
					if (!$isMatch) {
						if (preg_match('/^([^\s=]+?)(?:\s*\((.*?)\))?$/', $data, $matches)) {
							
							
							$s = new p_stream($data);
							$arr = $s->getFuncName();
							$funName = $arr[0];
							isset(self::$cfg['函数'][$funName]) && $funName = self::$cfg['函数'][$funName];
							$vars = '';
							if (!empty($arr[1])) {
								foreach ($arr[1] as $__k => $__v) {
									$vars && $vars .= ',';
									if (!self::isVar($__k)) {
										//isset(self::$cfg['变量'][$__k]) && $__k = self::$cfg['变量'][$__k];
										$__k = '$v'.self::_name($__k);
									}
									if ($__v === false) $vars .= $__k;
									else $vars .= $__k.'='.$__v;
								}
							}
							$vars = self::decodeCmdStr($vars, $eArr[1]);
							$code .= $funName.'('.$vars.');';
							$isMatch = true;
						}
					}
					//变量赋值匹配
					if (!$isMatch) {
						if (preg_match('/^([^=]+)\s*=\s*([^=]+)$/', $data, $m)) {
							$key = $m[1];
							$val = $m[2];
							$key = '$v'.self::_name($key);
							if (!self::isVar($val)) {
								if (!is_numeric($val)) {
									$val = '$v'.self::_name($val);
								}
							}
							$var = $key.'='.$val.';';
							$var = self::decodeCmdStr($var, $eArr[1]);
							$code .= $var;
							$isMatch = true;
						}
					}*/
				}
			}
		}
		return $code;
	}
	private static function isVar($data){
		return preg_match('/^{var:\d+}$/', $data) > 0;
	}
	public static function encodeCmdStr($data){
		$strs = array();
		$len = strlen($data);
		$atStr = false;
		$newData = '';
		$flag = '';
		$str = '';
		$lastS = '';
		$zhuanCount = 0;
		$varIndex = 0;
		for ($i = 0; $i < $len; $i++) {
			$s = $data{$i};
			if ($s == '\\') $zhuanCount++;
			if (!$atStr) {
				if ($s == '\'' || $s == '"') {
					$atStr = true;
					$str = $s;
					$flag = $s;
				} else $newData .= $s;
			} else {
				if ($s == $flag && $zhuanCount % 2 == 0) {
					$str .= $s;
					$atStr = false;
					$strs[$varIndex] = $str;
					$newData .= '{var:'.$varIndex.'}';
					$varIndex++;
				} else $str .= $s;
			}
			if ($s != '\\') $zhuanCount = 0;
			$lastS = $s;
		}
		return array($newData, $strs);
	}
	public static function decodeCmdStr($data, $vars){
		return preg_replace('/{var:(\d+)}/e', '$vars[$1]', $data);
	}
	private static function parseLine(&$line){
		$len = strlen($line);
		$tabCount = 0;
		$atStr = false;
		$str = '';
		for ($i = 0; $i < $len; $i++) {
			$s = $line{$i};
			if (!$atStr) {
				if ($s == "\t") $tabCount++;
				else {
					$str = $s;
					$atStr = true;
				}
			} else $str .= $s;
		}
		return array(trim($str), $tabCount);
	}
	public static function encodeKuo($data){
		$l = $r = 0;
		$atKuo = false;
		$str = $kuoStr = '';
		$strs = array();
		$index = 0;
		foreach (string::str_split($data) as $s) {
			if (!$atKuo) {
				if ($s == '(') {
					$atKuo = true;
					$kuoStr = $s;
					$l = 1;
					$r = 0;
				} else $str .= $s;
			} else {
				$kuoStr .= $s;
				if ($s == ')') $r++;
				elseif ($s == '(') $l++;
				if ($l == $r) {
					$atKuo = false;
					$str .= '{kuo:'.$index.'}';
					$strs[$index] = $kuoStr;
					$index++;
				}
			}
		}
		return array($str, $strs);
	}
	public static function decodeKuo(&$data, &$datas){
		if (is_array($data)) {
			foreach ($data as &$v) {
				self::decodeKuo($v, $datas);
			}
		} else {
			//echo $data, "\n";
			$data = preg_replace('/{kuo:(\d+)}/e', '$datas[$1]', $data);
			//echo $data, "\n\n";
		}
	}
	private static function parseTab(&$data){
		$datas = array();
		$strs = array();
		$len = strlen($data);
		$atStr = false;
		$newData = '';
		$flag = '';
		$str = '';
		$lastS = '';
		$zhuanCount = 0;
		$varIndex = 0;
		for ($i = 0; $i < $len; $i++) {
			$s = $data{$i};
			if ($s == '\\') $zhuanCount++;
			if (!$atStr) {
				if ($s == '\'' || $s == '"') {
					$atStr = true;
					$str = $s;
					$flag = $s;
				} else {
					$newData .= $s;
				}
			} else {
				if ($s == $flag && $zhuanCount % 2 == 0) {
					$str .= $s;
					$atStr = false;
					$strs[$varIndex] = $str;
					$newData .= '{var:'.$varIndex.'}';
					$varIndex++;
				} else $str .= $s;
			}
			if ($s != '\\') $zhuanCount = 0;
			$lastS = $s;
		}
		$kuoArr = self::encodeKuo($newData);
		foreach (explode("\n", $kuoArr[0]) as $line) {
			if (trim($line)) {
				$arr = self::parseLine($line);
				$datas[] = $arr;
			}
		}
		$list = self::_parseTab($datas);
		self::decodeKuo($list, $kuoArr[1]);
		self::_setStr($list, $strs);
		return $list;
	}
	private static function _setStr(&$datas, &$strs){
		foreach ($datas as &$v) {
			if (is_array($v)) {
				self::_setStr($v, $strs);
			} else $v = preg_replace('/{var:(\d+)}/e', '$strs[$1]', $v);
		}
	}
	private static function _parseTab(&$datas, &$index = 0, $tabCount = 0){
		$count = count($datas);
		$rn = array();
		while ($index < $count) {
			$arr = $datas[$index];
			$index++;
			if ($arr[1] == $tabCount) {
				if ($index < $count) {
					$arr1 = $datas[$index];
					if ($arr1[1] == $tabCount + 1) {
						$rn[] = array('h' => $arr[0], 'c' => self::_parseTab($datas, $index, $tabCount + 1));
					} else {
						$rn[] = array('h' => $arr[0]);
						if ($arr1[1] < $arr[1]) break;
					}
				} else {
					$rn[] = array('h' => $arr[0]);
				}
			} elseif ($arr[1] < $tabCount) {
				$index--;
				break;
			}
		}
		return $rn;
	}
	private static function getToTab(&$data, $tabCount = 0){
		$len = strlen($data);
		$arr = array();
		$atGet = $atStr = false;
		$str = $getStr = '';
		$thisTabCount = 0;
		for ($i = 0; $i < $len; $i++) {
			$s = $data{$i};
			$str .= $s;
			if (!$atStr) {
				if ($s == "\t") $thisTabCount++;
				else $atStr = true;
			} else {
				if ($s == "\n" || $i + 1 == $len) {
					$str = rtrim($str);
					if (!$atGet) {
						if ($thisTabCount == $tabCount) {
							$atGet = true;
							$getStr && $getStr .= "\n";
							$getStr .= $str;
						}
					} else {
						if ($thisTabCount > $tabCount) {
							$getStr && $getStr .= "\n";
							$getStr .= $str;
						} else {
							$getStr && $arr[] = $getStr;
							if ($thisTabCount == $tabCount) {
								$getStr = $str;
							} else {
								$atGet = false;
								$getStr = '';
							}
						}
					}
					$str = '';
					$atStr = false;
					$thisTabCount = 0;
				}
			}
		}
		$getStr && $arr[] = $getStr;
		return $arr;
	}
}
p::initialize();
?>