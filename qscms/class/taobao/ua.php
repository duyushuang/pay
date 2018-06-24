<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
ini_set('pcre.backtrack_limit', -1);
set_time_limit(0);
class taobao_ua{
	private static $cacheDir, $jsLib, $jsFuncName_encode0, $jsFuncName_encode1, $jsFuncName_encode1_0, $jsFuncName_encode2;
	private static $jsUrl;
	public static function ini($cacheDir, $uaUrlName = 'default'){
		static $urls = array(
			'default' => 'http://uaction.aliyuncdn.com/actionlog/js/ua.js',
			'coin'    => 'http://uaction.aliyuncdn.com/actionlog/js/ua.js',
			'etao'    => 'http://uaction.aliyuncdn.com/js/ua.js',
			'trip'    => array('name' => 'etao', 'url' => 'http://uaction.aliyuncdn.com/js/ua.js')
		);
		$urlInfo = $urls[$uaUrlName];
		if (is_array($urlInfo)) {
			if (!empty($urlInfo['name'])) {
				$cacheDir.= $urlInfo['name'].D;
			}
			self::$jsUrl = $urlInfo['url'];
		} else {
			self::$jsUrl = $urlInfo;
			$cacheDir .= $uaUrlName.D;
		}
		self::$cacheDir = $cacheDir;
		file::createFolder(self::$cacheDir);
	}
	private static function getUaJs(&$jsCode) {
		$cacheFile = self::$cacheDir.'ua.js';
		if (file_exists($cacheFile)) {
			$jsCode = file::read($cacheFile);
		} else {
			$jsCode = winsock::get_html(self::$jsUrl);
			file::write($cacheFile, $jsCode);
		}
	}
	private static function jsFormat(&$jsCode, $tabWidth = 0){
			$cacheFile = self::$cacheDir.'ua.format.js';
			if (file_exists($cacheFile)) {
				$jsCode = file::read($cacheFile);
				return false;
			}
			$l = strlen($jsCode);
			$strFlag = '';
			$flagNum = 0;
			$atK0 = $atStr = false;
			$rs = $str = '';
			for ($i = 0; $i < $l; $i++) {
				$s = substr($jsCode, $i, 1);
				if ($atStr) {
					$str .= $s;
					if ($s == $strFlag && $flagNum % 2 == 0) {
						$atStr = false;
					} elseif ($s == '\\') {
						$flagNum++;
					} else {
						$flagNum = 0;
					}
				} elseif ($atK0) {
					
				} else {
					if ($s == '{') {
						if ($str != '') {
							$rs .= $str;
							$str = '';
						}
						$tabWidth++;
						$rs .= $s."\r\n".str_repeat("\t", $tabWidth);
					} elseif ($s == '}') {
						if ($str != '') {
							$rs .= $str."\r\n".str_repeat("\t", $tabWidth);
							$str = '';
						}
						$tabWidth--;
						$rs .= "\r\n".str_repeat("\t", $tabWidth).$s."\r\n".str_repeat("\t", $tabWidth);
					} elseif ($s == '\'' || $s == '"') {
						$flagNum = 0;
						$str .= $s;
						$atStr = true;
						$strFlag = $s;
					} else {
						$str .= $s;
						if ($s == ';') {
							$rs .= $str."\r\n".str_repeat("\t", $tabWidth);
							$str = '';
						}
					}
				}
			}
			file::write($cacheFile, $rs);
			$jsCode = $rs;
			return true;
		}
	private static function findStrFuncA(&$jsCode, $key){
		$rs = '';
		while ($funName = string::getPregVal('/[^a-zA-Z0-9]'.$key.'=(\w+)/', $jsCode)) {
			if ($funName == 'function') {
				$rs = $key;
				break;
			}
			$key = $funName;
		}
		return $rs;
	}
	private static function _findStrFuncB(&$jsCode, $key){
		$rs = array();
		if (preg_match_all('/(\w+)='.$key.'[^a-zA-Z0-9\(\[]/s', $jsCode, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $v) {
				$rs[] = $v[1];
			}
		}
		foreach ($rs as $v) {
			$rs2 = self::_findStrFuncB($jsCode, $v);
			$rs = array_merge($rs, $rs2);
		}
		return $rs;
	}
	private static function findStrFuncB(&$jsCode, $key){
		$rs = self::_findStrFuncB($jsCode, $key);
		$rs[] = $key;
		return $rs;
	}
	private static function findStrFuncC(&$jsCode, $funName){
		if (preg_match('/[^a-zA-Z0-9]'.$funName.'=function\((.+?)\)/s', $jsCode, $matches)) {
			$str = $matches[0];
			$f = strpos($jsCode, $str);
			$str = string::dg_code($jsCode, '{', '}', $f);
			$func = 'function '.$funName.'('.$matches[1].')'.$str;
			$rs = array('funcName' =>$funName, 'func' => $func);
			return $rs;
		}
	}
	private static function findStrFunc(&$jsCode, $key = 'window'){
		$cacheFile = self::$cacheDir.'ua.str.func';
		if (file_exists($cacheFile)) {
			return unserialize(file::read($cacheFile));
		}
		if ($winStr = string::getPregVal('/^.+?(\w+)='.$key.';/s', $jsCode)) {
			$funName = string::getPregVal('/(?:[^a-zA-z0-9]+)'.$winStr.'\[(\w+)\(\'[a-zA-z]+\',\d+,\d+\)\]/', $jsCode);
			if ($funName) {
				if ($funName = self::findStrFuncA($jsCode, $funName)) {
					//解析字符串的函数名
					//获取所有相关的函数
					$funcs = self::findStrFuncB($jsCode, $funName);
					$rs = self::findStrFuncC($jsCode, $funName);
					if ($rs) {
						$rs['funcNames'] = $funcs;
						file::write($cacheFile, serialize($rs));
						return $rs;
					}
				}
				
			}
		}
	}
	private static function getStrName($fun, $str, $num0, $num1){
		$str = self::$jsLib->Eval("$fun('$str',$num0,$num1)");
		return $str;
	}
	private static function replaceStrName(&$jsCode){
		$cacheFile = self::$cacheDir.'ua.f1.js';
		if (file_exists($cacheFile)) {
			$jsCode = file::read($cacheFile);
			return false;
		}
		$arr = self::findStrFunc($jsCode);
		if (!$arr) {
			return false;
		}
		$funName = $arr['funcName'];
		$funcs = implode('|', $arr['funcNames']);
		$func  = $arr['func'];
		$jsLib = new COM('MSScriptControl.ScriptControl');
		$jsLib->AllowUI = true;
		$jsLib->Language = 'JavaScript';
		$jsLib->AddCode($func);
		self::$jsLib = $jsLib;
		$jsCode = preg_replace('/([^a-zA-Z0-9])(?:'.$funcs.')\(\'(.+?)\'\s*,\s*(\d+)\s*,\s*(\d+)\)/se', "'$1'.'\''.self::getStrName('$funName', '$2', $3, $4).'\''", $jsCode);
		$names = array(
			'length', 
			'charCodeAt', 
			'String', 
			'fromCharCode', 
			'getUTCFullYear', 
			'Date', 
			'getUTCMonth', 
			'getUTCDate', 
			'getUTCHours', 
			'getUTCMinutes', 
			'getUTCSeconds', 
			'document', 
			'getElementById', 
			'lastIndexOf', 
			'indexOf', 
			'substring', 
			'value', 
			'charAt',
			'getTime',
			'Math',
			'ceil',
			'floor',
			'random',
			'userAgent',
			'parseFloat',
			'substr',
			'Array',
			'createElement',
			'push'
		);
		$names = implode('|', $names);
		$jsCode = preg_replace('/\[(\'|")([a-zA-Z]+)\\1\]/s', '.$2', $jsCode);
		$jsCode = preg_replace('/\w+\.(String\.fromCharCode|Boolean)/', '$1', $jsCode);
		//$jsCode = preg_replace('/\["('.$names.')"\]/s', '.$1', $jsCode);
		//$jsCode = preg_replace('/\[\'('.$names.')\'\]/s', '.$1', $jsCode);
		//$jsCode = preg_replace('/\["(?:'.$names.')"\]/s', '.$1', $jsCode);
		file::write($cacheFile, $jsCode);
		return true;
	}
	private static function replaceNum($str){
		$rs = @eval('return '.$str.';');
		$rs === false && $rs = $str;
		return $rs;
	}
	private static function formatNum(&$jsCode){
		$cacheFile = self::$cacheDir.'ua.f0.js';
		if (file_exists($cacheFile)) {
			$jsCode = file::read($cacheFile);
			return false;
		}
		for ($i = 0; $i < 5; $i++) {
			$jsCode = preg_replace('/0x([a-f0-9]+)/ie', 'hexdec(\'$1\')', $jsCode);
			$jsCode = preg_replace('/([^\d\w])0([1-7][0-7]*)/ie', '\'$1\'.octdec(\'$2\')', $jsCode);
			$jsCode = preg_replace('/\((\d+(?:(?:\+|\-|\*|\/|%|\||&)\d+)+)\)/e', '\'(\'.self::replaceNum(\'$1\').\')\'', $jsCode);
			$jsCode = preg_replace('/([^\d\w\]\)])\((\d+)\)/', '$1$2', $jsCode);
		}
		file::write($cacheFile, $jsCode);
		return true;
	}
	private static function getP($p){
		$p = addcslashes($p, '(){}+*');
		$p = str_replace('\s', '\s+', $p);
		$p = str_replace('\w', '\w+', $p);
		$p = str_replace('[data]', '(.+?)', $p);
		$p = str_replace('[str]', '(?:.+?)', $p);
		$p = str_replace('[number]', '(\d+)', $p);
		$p = preg_replace('/\s+/s', '\s*', $p);
		$p = str_replace('\(\\w+\)', '(\w+)', $p);
		return $p;
	}
	private static function replaceErrCode(&$jsCode){
		//祛除混淆代码
		$pList = array(
			array('p' => 'var\s(\w) = 0;
			for (var\s(\w) = 0; \\2 <= 6; \\1 = ++\\2 + \\2+++\\2) {
				if (\\2 == \\1) {
					[data]
				continue;
			}
			if (2 * \\2 == \\1 - 4) {
				[data]
			} else\sif(2 * \\2 == \\1 - 2) {
				[data]
				continue;
			} if (4 * \\2 == \\1 + 8) {
				[data]
			}
		}', 'r' => '$3$5$4$6'),
		array('p' => 'var\s(\w) = 0;
		for (var\s(\w) = 0; \\2 <= 7; \\1 = ++\\2 + \\2 + ++\\2) {
			if (\\2 == \\1) {
				[data]
				continue;
			}
			if (2 * \\2 == \\1 - 5) {
				[data]
				break;
			}
			if (2 * \\2 == \\1 - 2) {
				[data]
				\\2++;
			}
			if (3 * \\2 == \\1 + 2) {
				[data]
			}
		}', 'r' => '$3$6$5$4'),
		array('p' => 'var\s(\w) = \d;
		var\s(\w) = \d;
		switch ([data]) {
		case(0) : [data]
		case(1) : [data]
		default:
			[data]
			break;
		}', 'r' => '$4$5$6'),
		
		array('p' =>
			'var (\w) = [number];
					while (! (\\1 - \\2)) {
						if (\\1 + 0) {
							[data]
							continue;
						}
						[data]
					}','r' => '$3'
		),
			
		array('p' =>
					'for (var\s(\w) = 0; \\1 < 3; \\1++) {
				if (\\1 == 0) {
					[data]
				} else if (\\1 == 2) {
					[data]
				}
				if (\\1 == 1) {
					[data]
				}
				[data]
			}', 'r' => '$2$4$3'),
			//++
			array('p' =>
			'var\s(\w) = 0;
				while (!\\1) {
					if (\\1 + \d) {
						[data]
						\\1=\\1+\d;
						continue;
					}
					[data]
				}', 'r' => '$2'),
			array('p' =>
			'var\s(\w) = 0;
				while (!\\1) {
					if (\\1 + \d) {
						[data]
						continue;
					}
					[data]
				}', 'r' => '$2'),
				array('p' =>
				'if (! 0) {
										var\s(\w) = 0;
									} else {
										\\1 = \d;
									}
									if (!\\1) {
										var\s(\w) = \d;
									}
									if (\\2) {
										[data]
									} else {
										[data]
									}',
					'r' => '$3'), 
				
				array('p' => 'var\s(\w) = \w;
				var\s(\w) = \d;
				switch ([data]) {
				case(0) : [data]
				case(1) : [data]
					break;
				[data]
				}', 'r' => '$4$5')
		);
		foreach ($pList as $k => $v) {
			$p = self::getP($v['p']);
			$jsCode = preg_replace('/'.$p.'/s', $v['r'], $jsCode);
		}
	}
	private static function findEncodeFunc(&$jsCode){
		$cacheFile = self::$cacheDir.'ua.encode.func';
		if (file_exists($cacheFile)) {
			return unserialize(file::read($cacheFile));
		}
		$funName = string::getPregVal('/[^\w](\w+)\(\[1,/', $jsCode);
		$rs = self::findStrFuncC($jsCode, $funName);
		$reList = array();
		if ($rs) {
			/*
			 * 获取几个数组字符
			 */
			$strFuncs = array();
			/*if ($strFunName = string::getPregVal('/[^\w](\w+)=function\(\)\s*\{.*?'.$funName.'\(\[19,/s', $jsCode)) {
				if ($strRs = findStrFuncC($jsCode, $strFunName)) {
					replaceErrCode($strRs['func']);
					if ($strFunNames = string::getPregVal('/try\s*\{\s*var\s+\w+=\[(.+?)\]/', $strRs['func'])) {
						$jsCode0 = file::read('c:\\ua\\ua.js');
						foreach (explode(',', $strFunNames) as $v) {
							$v = trim($v);
							if ($strRs = findStrFuncC($jsCode0, $v)) {
								$strFuncs[] = '"'.addcslashes($strRs['func'], '"\\').'"';
							}
						}
					}
				}
			}*/
			$jsCode1 = $jsCode;
			self::replaceErrCode($jsCode1);
			if ($strFunNames = string::getPregVal('/[^\w](\w+)=function\(\)\s*\{\s*try\s*\{\s*var\s+\w+=\[(.+?)\].*?'.$funName.'\(\[19,/s', $jsCode1, 2)) {
				$jsCode0 = file::read(self::$cacheDir.'ua.js');
				foreach (explode(',', $strFunNames) as $v) {
					$v = trim($v);
					if ($strRs = self::findStrFuncC($jsCode0, $v)) {
						$strFuncs[] = '"'.addcslashes($strRs['func'], '"\\').'"';
					}
				}
			}
			/*
			 * END
			 */
			$func = $rs['func'];
			self::replaceErrCode($func);
			$funName = string::getPregVal('/\.SendMethod&8\)>0\)\s*\{\s*(\w+)\((\w+)\);/', $func);
			if ($rs = self::findStrFuncC($jsCode, $funName)) {
				$func = $rs['func'];
				self::replaceErrCode($func);
				if ($name0 = string::getPregVal('/var (\w+)=\w+\((\w+)\)\+1;/s', $func)) {
					if (preg_match('/(\w+)\((\w+)\((\w+)\(\w+\)\),'.$name0.'\)/', $func, $matches)) {
						$funcFormatArray = $matches[3];
						$funcEncode0 = $matches[2];
						$funcEncode1 = $matches[1];
						if ($name1 = string::getPregVal('/(\w+)=\'123\'\+\w+\+\'|\'\+\w+/', $func)) {
							if ($funcEncode2 = string::getPregVal('/'.$name1.'=(\w+)\('.$name1.'\);/', $func)) {
								$eStrName = string::getPregVal('/var (\w+)="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789\+\/"/', $jsCode);
								$reList['eStrName'] = $eStrName;
								$rs = self::findStrFuncC($jsCode, $funcEncode0);
								//self::replaceErrCode($rs['func']);
								$reList['encode0'] = $rs;
								$rs = self::findStrFuncC($jsCode, $funcEncode1);
								//self::replaceErrCode($rs['func']);
								$funcEncode1_0 = string::getPregVal('/var \w+=\((\w+)!=0\)\?(\w+)\((\w+),\\1\):\\3;/', $rs['func'], 2);
								$reList['encode1'] = $rs;
								$rs = self::findStrFuncC($jsCode, $funcEncode1_0);
								//self::replaceErrCode($rs['func']);
								$reList['encode1_0'] = $rs;
								$rs = self::findStrFuncC($jsCode, $funcEncode2);
								//self::replaceErrCode($rs['func']);
								$reList['encode2'] = $rs;
								
								$reList['strFuncs'] = $strFuncs;
								file::write($cacheFile, serialize($reList));
								return $reList;
							}
						}
					}
				}
			}
		}
	}
	public static function upgrade(){
		file::delFolder(self::$cacheDir);
		file::createFolder(self::$cacheDir);
		$jsCode = '';
		self::getUaJs($jsCode);
		self::jsFormat($jsCode);
		self::formatNum($jsCode);
		self::replaceStrName($jsCode);
		//replaceErrCode($jsCode);
		$funcList = self::findEncodeFunc($jsCode);
		return true;
	}
	public static function loadUAFunc(){
		$funcFile = self::$cacheDir.'ua.encode.func';
		if (file_exists($funcFile)) {
			$arr = @unserialize(file::read($funcFile));
			if ($arr) {
				$jsLib = new COM('MSScriptControl.ScriptControl');
				//$jsLib->AllowUI = true;
				$jsLib->Language = 'JavaScript';
				$jsLib->AddCode('var '.$arr['eStrName'].'=\'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/\';');
				$jsLib->AddCode($arr['encode0']['func']);
				$jsLib->AddCode($arr['encode1']['func']);
				$jsLib->AddCode($arr['encode1_0']['func']);
				$jsLib->AddCode($arr['encode2']['func']);
				self::$jsLib = $jsLib;
				self::$jsFuncName_encode0 = $arr['encode0']['funcName'];
				self::$jsFuncName_encode1 = $arr['encode1']['funcName'];
				self::$jsFuncName_encode1_0 = $arr['encode1_0']['funcName'];
				self::$jsFuncName_encode2 = $arr['encode2']['funcName'];
				return true;
			}
		}
	}
	public static function encode($str){
		$rs = '';
		$sp = qscms::trimExplode("\n", $str);
		$nums = array();
		foreach ($sp as $k => $v) {
			$n = trim(substr($v, 1, strpos($v, ',') - 1));
			$nums[] = $n * $n - count($nums);
			$count = $k + 1;
			$v = '\''.addcslashes($v, '\'\\').'\'';
			$str0 = self::$jsLib->Eval(self::$jsFuncName_encode1.'('.self::$jsFuncName_encode0.'('.$v.'),'.$count.')');
			$v = '['.implode(',', $nums).']';
			$v = '\''.addcslashes($v, '\'\\').'\'';
			$str1 = self::$jsLib->Eval(self::$jsFuncName_encode1.'('.self::$jsFuncName_encode0.'('.$v.'),'.($count + 1).')');
			if ($rs == '') {
				$rs = '123'.$str0.'|'.$str1;
			} else {
				$f = strrpos($rs, '|');
				$rs = substr($rs, 0, $f);
				$rs .= '|'.$str0.'|'.$str1;
			}
			$rs = '\''.addcslashes($rs, '\'\\').'\'';
			$rs = self::$jsLib->Eval(self::$jsFuncName_encode2.'('.$rs.')');
		}
		return $rs;
	}
	public static function random(){
		$r1 = rand(0, 99999999);
		$r2 = rand(0, 99999999);
		return '0.'.sprintf('%08d', $r1).sprintf('%08d', $r2);
	}
	public static function ltime($less = 0){
		list($usec, $sec) = explode(' ', microtime());
	    return (intval($sec) - $less).sprintf('%03d', floatval($usec) * 1000);
	}
	public static function getFunc(){
		$funcFile = self::$cacheDir.'ua.encode.func';
		if (file_exists($funcFile)) return @unserialize(file::read($funcFile));
		return false;
	}
}