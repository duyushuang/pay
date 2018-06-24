<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class string{
	public static function save_define($arr,$path,$isStr=false){
		if(is_array($arr)){
			$define_str="<?php";
			foreach($arr as $k=>$v){
				$tmp=$v;
				if(!$isStr){
					if(!is_numeric($tmp)&&$tmp!='true'&&$tmp!='false')$tmp="'".$tmp."'";
				}else $tmp="'".$tmp."'";
				$define_str.="\ndefine('$k',$tmp);";
			}
			$define_str.="\n?>";
			savefile($path,$define_str);
		}
	}
	public static function parse_define($path){
		if(file_exists($path)){
			$data=bf_readfile($path);
			if(preg_match_all("/define\((.+?),(.+?)\);/",$data,$matches,PREG_SET_ORDER)){
				foreach($matches as $v){
					$bk_k=$v[1];
					$bk_v=$v[2];
					$bk_k=trim($bk_k);
					$bk_k=trim($bk_k,"'");
					$bk_k=trim($bk_k,'"');
					$bk_v=trim($bk_v);
					$bk_v=trim($bk_v,"'");
					$bk_v=trim($bk_v,'"');
					$rn[$bk_k]=$bk_v;
				}
				return $rn;
			}
		}
	}
	public static function formatArray($arr){
		$rn = '';
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
				return '\''.addcslashes($arr,'\'\\').'\'';
			break;
			case 'array':
				foreach($arr as $k=>$v){
					$rn&&$rn.=',';
					switch(gettype($k)){
						case 'boolean':
							$k=$k?"true":"false";
						break;
						case 'string':
							$k='\''.addcslashes($k,'\'\\').'\'';
						break;
					}
					$rn.=$k."=>".self::formatArray($v);
				}
				return "array($rn)";
			break;
			case 'object':
				return '\'\'';
			break;
			case 'resource':
				return '\'\'';
			break;
			case 'NULL':
				return '\'\'';
			break;
			case 'user function':
				return '\'\'';
			break;
			case 'unknown type':
				return '\'\'';
			break;
		}
	}
	public static function show_message($message){
		
	}
	public static function str_hex($str){
		$len=strlen($str);
		$ord=0;
		$hex='';
		$a=$b=0;
		for($i=0;$i<$len;$i++){
			$ord=ord(substr($str,$i,1));
			$b=$ord%16;
			$a=($ord-$b)/16;
			($a>9&&($a=chr(97+$a-10)))||($a=chr(48+$a));
			($b>9&&($b=chr(97+$b-10)))||($b=chr(48+$b));
			$hex.=$a.$b;
		}
		return $hex;
	}
	public static function hex_str($hex){
		$len=strlen($hex);
		$a=$b=0;
		$str='';
		for($i=0;$i<$len;$i+=2){
			$a=ord(substr($hex,$i,1));
			$b=ord(substr($hex,$i+1,1));
			($a>=97&&($a=$a-97+10))||($a-=48);
			($b>=97&&($b=$b-97+10))||($b-=48);
			$str.=chr($a*16+$b);
		}
		return $str;
	}
	/*
	public static function get_object_vars_final($obj){
		if(is_object($obj)){
			$obj=get_object_vars($obj);
		}
		if(is_array($obj)){
			foreach ($obj as $key=>$value){
				$obj[$key]=self::get_object_vars_final($value);
			}
		}
		return $obj;
	}
	*/
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
	public static function json_encode($arr){
		if(function_exists('json_encode'))return json_encode($arr);
		else {
			static $json;
			isset($json) || $json = new MY_JSON();
			return $json->encode($arr);
		}
	}
	public static function json_decode($str){
		$arr = array();
		if(function_exists('json_decode')) {
			$arr = json_decode($str, true);
		}
		if (!$arr) {
			static $json;
			isset($json) || $json = new MY_JSON();
			$arr = $json->decode($str);
		}
		return $arr;
	}
	public static function ubbDecode($str){
		loadFunc('ubb2html');
		return ubb2html($str);
	}
	public static function getUBBPic($id){
		global $weburl2;
		if ($line = db::one('pics', '*', "id='$id'")) {
			return '<a href="'.$weburl2.'item/'.$id.'"><img width="400" src="'.$weburl2.$line['path'].$line['name'].'_big.'.$line['suffix'].'" /></a>';
		}
		return '';
	}
	public static function getUBBAlbum($id){
		global $weburl2;
		if ($line = db::one('albums', '*', "id='$id'")) {
			return '<div style="text-align:center"><a href="'.$weburl2.'album/'.$id.'"><img src="'.$weburl2.$line['avatar'].'"><span>专辑：'.$line['name'].'</span></a></div>';
		}
		return '';
	}
	public static function getUBB($str){
		$str = preg_replace('/\[item\](\d+)\[\/item\]/e', 'self::getUBBPic($1)', $str);
		$str = preg_replace('/\[bold\](.*?)\[\/bold\]/s', '<b>$1</b>', $str);
		$str = preg_replace('/\[album\](\d+)\[\/album\]/e', 'self::getUBBAlbum($1)', $str);
		$str = str_replace("\r\n", "\n", $str);
		$str = nl2br($str);
		return $str;
	}
	private static function _gbookParseFace($name){
		static $su;
		static $faceCache;
		if (!isset($faceCache)) {
			$faceCache = string::json_decode(file::read(s('images/face/config.php')));
		}
		if (!isset($su)) $su = su('images/face/');
		if (isset($faceCache[$name])) {
			return '<img src="'.$su.$faceCache[$name].'" />';
		}
		return '[/'.$name.']';
	}
	private static function _gbookCallName($id){
		static $cacheList = array();
		if (isset($cacheList[$id])) $name = $cacheList[$id];
		else {
			$name = member_base::getNick($id);
			$cacheList[$id] = $name;
		}
		if ($name) return '<a class="cBlue" href="'.$id.'">'.$name.'</a>';
		return '[@'.$id.']';
	}
	public static function gbookUBBDecode($str){
		$str = preg_replace('/\[\/(.+?)\]/e', 'self::_gbookParseFace(\'$1\')', $str);
		$str = preg_replace('/\[@(\d+)\]/e', 'self::_gbookCallName($1)', $str);
		//$str = str_replace("\r\n", "\n", $str);
		$str = nl2br($str);
		return $str;
	}
	public static function getVarString($str){
		return '\''.qscms::addcslashes($str).'\'';
	}
	public static function getJsVar($str){
		$str = '\''.qscms::addcslashes($str).'\'';
		$str = str_replace("\r", '\r', $str);
		$str = str_replace("\n", '\n', $str);
		return $str;
	}
	public static function alert($str){
		echo '<script>alert('.self::getJsVar($str).');</script>';
	}
	public static function getXin($str, $start = 0, $len = -1){
		$strLen = mb_strlen($str);
		if ($start >=0 && $start <= $strLen - 1) {
			$len == -1 && $len = $strLen - $start;
			$len + $start > $strLen && $len = $strLen - $start;
			$rn = '';
			$rn .= mb_substr($str, 0, $start);
			$rn .= str_repeat('*', $len);
			$rn .= mb_substr($str, $start + $len);
			return $rn;
		}
		return str_repeat('*', $strLen);
	}
	public static function getXin2($str){
		$strLen = mb_strlen($str);
		if ($strLen >= 3) {
			return self::getXin($str, 1, $strLen - 2);
		}
		return $str;
	}
	public static function getXin3($str, $len = 1) {
		$strLen = mb_strlen($str);
		//if ($strLen == 1) return $str;
		$_l = $len;
		while ($strLen - $_l * 2 <= 0 && $_l > 0) {
			$_l--;
		}
		if ($_l > 0) {
			return self::getXin($str, $_l, $strLen - $_l * 2);
		}
		return $str;
	}
	public static function getStaticCode($arr){
		$str = '';
		foreach ($arr as $k => $v) {
			$var = '$'.$k.'=';
			switch(gettype($v)){
				case 'boolean':
					$var .= $v?"true":"false";
				break;
				case 'integer':
					$var .= $v;
				break;
				case 'double':
					$var .= $v;
				break;
				case 'string':
					$var .= '\''.addcslashes($v, '\'\\').'\'';
				break;
				case 'array':
					$var .= self::formatArray($v);
				break;
				case 'object':
					$var .= '\'\'';
				break;
				case 'resource':
					$var .= '\'\'';
				break;
				case 'NULL':
					$var .= '\'\'';
				break;
				case 'user function':
					$var .= '\'\'';
				break;
				case 'unknown type':
					$var .= '\'\'';
				break;
			}
			$var.=';';
			$str .= $var;
		}
		$str = '<?php '.$str.'?>';
		return $str;
	}
	public static function setColors($string, $keys, $color){
		foreach ($keys as $key) {
			$string = str_replace($key, '<span style="color:'.$color.'">'.$key.'</span>', $string);
		}
		return $string;
	}
	public static function paraFilter($para) {
		$para_filter = array();
		while (list ($key, $val) = each ($para)) {
			if ($key == "sign" || $key == "sign_type" || $val == "") continue;
			else $para_filter[$key] = $para[$key];
		}
		return $para_filter;
	}
	public static function argSort($para) {
		ksort($para);
		reset($para);
		return $para;
	}
	public static function createLinkstring($para) {
		$arg  = "";
		while (list ($key, $val) = each ($para)) {
			$arg.=$key."=".$val."&";
		}
		//去掉最后一个&字符
		$arg = substr($arg,0,count($arg)-2);
		
		//如果存在转义字符，那么去掉转义
		if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
		//print_r($arg);exit;
		return $arg;
	}
	public static function md5Sign($prestr, $key) {
		$prestr = $prestr . $key;
		return md5($prestr);
	}
	public static function ArrtoStr($prestr){
		$prestr = self::paraFilter($prestr);
		$prestr = self::argSort($prestr);
		$prestr = self::createLinkstring($prestr);
		return $prestr;
	}
	public static function keymd5Sign($prestr, $key){
		$prestr = self::ArrtoStr($prestr);
		return self::md5Sign($prestr, $key);
	}
	public static function getRandStr($len=4,$type=7){
		$return='';
		$set_C_list=array();
		($type&1)&&($set_C_list[]=1);
		($type&2)&&($set_C_list[]=2);
		($type&4)&&($set_C_list[]=3);
		$set_C=count($set_C_list);
		$set_C>0&&($set_C--);
		for($i=0;$i<$len;$i++){
			switch($set_C_list[mt_rand(0,$set_C)]){
				case 1:
				//数字
				$return.=chr(mt_rand(0x30,0x39));
				break;
				case 2:
				//大写字母
				$return.=chr(mt_rand(0x41,0x5A));
				break;
				case 3:
				//小写字母
				$return.=chr(mt_rand(0x61,0x7A));
				break;
			}
		}
		return $return;
	}
	public static function dg_string($data,$flagA, $flagB, $start = 0){
		$flagAL=strlen($flagA);
		$flagBL=strlen($flagB);
		$rn='';
		$a=$b=0;
		if(($findA=strpos($data,$flagA, $start))!==false){
			$a=1;
			$tmpA=$findA;
			$findB=$findA+$flagAL;
			$findA=$findB;
			while($a!=$b){
				if(($findB = strpos($data, $flagB, $findB))!==false){
					$b++;
					if(($findA = strpos($data, $flagA, $findA))!==false){
						if($findA>$findB){
							if($a==$b){
								//结束
								$findB+=$flagBL;
								$rn=substr($data,$tmpA,$findB-$tmpA);
							} else {
								$a++;
								$findB=$findA+$flagAL;
								$findA=$findB;
							}
						} else {
							$a++;
							$findA+=$flagAL;
							$findB+=$flagBL;
						}
					} else {
						if($a==$b){
							//结束
							$findB+=$flagBL;
							$rn=substr($data,$tmpA,$findB-$tmpA);
						} else {
							//标记不完整
							$findB+=$flagBL;
						}
					}
				} else {
					//标记不完整
					$rn=substr($data,$tmpA);
					$rn.=str_repeat($flagB,$a-$b);
					break;
				}
			}
		}
		return $rn;
	}
	public static function dg_code($data,$flagA, $flagB, $start = 0){
		$flagAL=strlen($flagA);
		$flagBL=strlen($flagB);
		$rn = '';
		$l = strlen($data);
		$a = $b = 0;
		$atStr = $atStart = false;
		$strFlag = '';
		$strXNum = 0;
		for ($i = $start; $i < $l; $i++) {
			$s = substr($data, $i, 1);
			if (!$atStart) {
				if ($s == $flagA) {
					$atStart = true;
					$rn .= $flagA;
					$a++;
				}
				continue;
			}
			$rn .= $s;
			if ($atStr) {
				if ($s == $strFlag && $strXNum % 2 == 0) {
					$atStr = false;
				} elseif ($s == '\'') {
					$strXNum++;
				} else {
					$strXNum = 0;
				}
			} else {
				if (in_array($s, array('\'', '"'))) {
					$atStr = true;
					$strXNum = 0;
					$strFlag = $s;
				} else {
					$s == $flagA && $a++;
					$s == $flagB && $b++;
					if ($a > 0 && $a == $b) break;
				}
			}
		}
		return $rn;
	}
	public static function dg_string2($data,$flag,$start){
		$flagA='<'.$flag;
		$flagB='</'.$flag.'>';
		$flagAL=strlen($flagA);
		$flagBL=strlen($flagB);
		$rn='';
		$a=$b=0;
		if(($findA=strpos($data,$start))!==false){
			$a=1;
			$tmpA=$findA;
			$findB=$findA+$flagAL;
			$findA=$findB;
			while($a!=$b){
				if(($findB=strpos($data,$flagB,$findB))!==false){
					$b++;
					if(($findA=strpos($data,$flagA,$findA))!==false){
						if($findA>$findB){
							if($a==$b){
								//结束
								$findB+=$flagBL;
								$rn=substr($data,$tmpA,$findB-$tmpA);
							} else {
								$a++;
								$findB=$findA+$flagAL;
								$findA=$findB;
							}
						} else {
							$a++;
							$findA+=$flagAL;
							$findB+=$flagBL;
						}
					} else {
						if($a==$b){
							//结束
							$findB+=$flagBL;
							$rn=substr($data,$tmpA,$findB-$tmpA);
						} else {
							//标记不完整
							$findB+=$flagBL;
						}
					}
				} else {
					//标记不完整
					$rn=substr($data,$tmpA);
					$rn.=str_repeat($flagB,$a-$b);
					break;
				}
			}
		}
		return $rn;
	}
	public static function formatAlert($str){
		$str = str_replace('"', '\x22', $str);
		$str = str_replace('\'', '\x27', $str);
		$str = str_replace("\r\n", "\n", $str);
		$str = str_replace("\n", '\n', $str);
		return $str;
	}
	public static function getXmlData ($strXml) {
		$pos = strpos($strXml, 'xml');
		if ($pos) {
			$xmlCode   = simplexml_load_string($strXml, 'SimpleXMLElement', LIBXML_NOCDATA);
			$arrayCode = self::get_object_vars_final($xmlCode);
			return $arrayCode ;
		} else {
			return '';
		}
	}
	public static function get_html($action){
		extract($GLOBALS, EXTR_SKIP);
		$html0 = ob_get_contents();
		qscms::ob_clean();
		@include(d('./lib/'.$action.'.php'));
		$html = ob_get_contents();
		qscms::ob_clean();
		echo $html0;
		return $html;
	}
	public static function cuthtml($body, $size){ 
	  $_size = mb_strlen($body); 
	  if($_size <= $size) return $body; 
	  $strlen_var = strlen($body); 
	  // 不包含 html 标签 
	  if(strpos($body, '<') === false){ 
		return mb_substr($body, 0, $size); 
	  } 
	  // 包含截断标志，优先 
	  if($e = strpos($body, '<!-- break -->')){ 
		return mb_substr($body, 0, $e); 
	  }
	  // html 代码标记 
	  $html_tag = 0;
	  // 摘要字符串 
	  $summary_string = '';
	  /** 
	   * 数组用作记录摘要范围内出现的 html 标签 
	   * 开始和结束分别保存在 left 和 right 键名下 
	   * 如字符串为：<h3><p><b>a</b></h3>，假设 p 未闭合 
	   * 数组则为：array('left' => array('h3', 'p', 'b'), 'right' => 'b', 'h3'); 
	   * 仅补全 html 标签，<? <% 等其它语言标记，会产生不可预知结果 
	   */ 
	  $html_array = array('left' => array(), 'right' => array()); 
	  for($i = 0; $i < $strlen_var; ++$i) { 
		if(!$size){ 
		  break;
		}
		$current_var = substr($body, $i, 1); 
		  
		if($current_var == '<'){ 
		  // html 代码开始 
		  $html_tag = 1; 
		  $html_array_str = ''; 
		}else if($html_tag == 1){ 
		  // 一段 html 代码结束 
		  if($current_var == '>'){ 
			/** 
			 * 去除首尾空格，如 <br /  > < img src="" / > 等可能出现首尾空格 
			 */ 
			$html_array_str = trim($html_array_str); 
			  
			/** 
			 * 判断最后一个字符是否为 /，若是，则标签已闭合，不记录 
			 */ 
			if(substr($html_array_str, -1) != '/'){ 
				
			  // 判断第一个字符是否 /，若是，则放在 right 单元 
			  $f = substr($html_array_str, 0, 1); 
			  if($f == '/'){ 
				// 去掉 / 
				$html_array['right'][] = str_replace('/', '', $html_array_str); 
			  }else if($f != '?'){ 
				// 判断是否为 ?，若是，则为 PHP 代码，跳过 
				  
				/** 
				 * 判断是否有半角空格，若有，以空格分割，第一个单元为 html 标签 
				 * 如 <h2 class="a"> <p class="a"> 
				 */ 
				if(strpos($html_array_str, ' ') !== false){ 
				  // 分割成2个单元，可能有多个空格，如：<h2 class="" id=""> 
				  $html_array['left'][] = strtolower(current(explode(' ', $html_array_str, 2))); 
				}else{ 
				  /** 
				   * * 若没有空格，整个字符串为 html 标签，如：<b> <p> 等 
				   * 统一转换为小写 
				   */ 
				  $html_array['left'][] = strtolower($html_array_str); 
				} 
			  } 
			} 
			  
			// 字符串重置 
			$html_array_str = ''; 
			$html_tag = 0; 
		  }else{ 
			/** 
			 * 将< >之间的字符组成一个字符串 
			 * 用于提取 html 标签 
			 */ 
			$html_array_str .= $current_var; 
		  } 
		}else{ 
		  // 非 html 代码才记数 
		  --$size; 
		} 
		  
		$ord_var_c = ord($body{$i}); 
		  
		switch (true) { 
		  case (($ord_var_c & 0xE0) == 0xC0): 
			// 2 字节 
			$summary_string .= substr($body, $i, 2); 
			$i += 1; 
		  break; 
		  case (($ord_var_c & 0xF0) == 0xE0): 
			  
			// 3 字节 
			$summary_string .= substr($body, $i, 3); 
			$i += 2; 
		  break; 
		  case (($ord_var_c & 0xF8) == 0xF0): 
			// 4 字节 
			$summary_string .= substr($body, $i, 4); 
			$i += 3; 
		  break; 
		  case (($ord_var_c & 0xFC) == 0xF8): 
			// 5 字节 
			$summary_string .= substr($body, $i, 5); 
			$i += 4; 
		  break; 
		  case (($ord_var_c & 0xFE) == 0xFC): 
			// 6 字节 
			$summary_string .= substr($body, $i, 6); 
			$i += 5; 
		  break; 
		  default: 
			// 1 字节 
			$summary_string .= $current_var; 
		} 
	  } 
	  
	  if($html_array['left']){ 
		/** 
		 * 比对左右 html 标签，不足则补全 
		 */ 
		  
		/** 
		 * 交换 left 顺序，补充的顺序应与 html 出现的顺序相反 
		 * 如待补全的字符串为：<h2>abc<b>abc<p>abc 
		 * 补充顺序应为：</p></b></h2> 
		 */ 
		$html_array['left'] = array_reverse($html_array['left']); 
		  
		foreach($html_array['left'] as $index => $tag){ 
		  // 判断该标签是否出现在 right 中 
		  $key = array_search($tag, $html_array['right']); 
		  if($key !== false){ 
			// 出现，从 right 中删除该单元 
			unset($html_array['right'][$key]); 
		  }else{ 
			// 没有出现，需要补全 
			$summary_string .= '</'.$tag.'>'; 
		  } 
		} 
	  } 
	  return $summary_string; 
	} 
	public static function formatSize($size){
		if ($size < 1024) $sizeStr = $size.'Byte';
		elseif ($size < 1024 * 1024) $sizeStr = (floor($size / 1024 * 10 + 0.5) / 10).'KB';
		elseif ($size < 1024 * 1024 * 1024) $sizeStr = (floor($size / 1024 / 1024 * 10 + 0.5) / 10).'MB';
		else $sizeStr = (floor($size / 1024 / 1024 / 1024 * 10 + 0.5) / 10).'GB';
		return $sizeStr;
	}
	public static function getSize($str){
		if (preg_match('/(\d+(?:\.\d+)?)m(?:.*?(\d+(?:\.\d+)?)k(?:.*?(\d+(?:\.\d+)?)b)?)?/i', $str, $matches)) {
			$m = isset($matches[1]) ? floatval($matches[1]) : 0;
			$k = isset($matches[2]) ? floatval($matches[2]) : 0;
			$b = isset($matches[3]) ? floatval($matches[3]) : 0;
			return $m * 1024 * 1024 + $k * 1024 + $b;
		} elseif (preg_match('/(\d+(?:\.\d+)?)k(?:.*?(\d+(?:\.\d+)?)b)?/i', $str, $matches)) {
			$k = isset($matches[1]) ? floatval($matches[1]) : 0;
			$b = isset($matches[2]) ? floatval($matches[2]) : 0;
			return $k * 1024 + $b;
		} elseif (preg_match('/(\d+(?:\.\d+)?)b/i', $str, $matches)) {
			$b = isset($matches[1]) ? floatval($matches[1]) : 0;
			return $b;
		}
		return 0;
	}
	public static function parseChoose($str, $key1 = true){
		$sp = qscms::trimExplode("\n", $str);
		$list = array();
		foreach ($sp as $v) {
			if ($v) {
				$sp1 = qscms::trimExplode('=', $v);
				if ($key1) {
					$list[] = array('key' => $sp1[1], 'value' => $sp1[0]);
				} else {
					$list[] = array('key' => $sp1[0], 'value' => $sp1[1]);
				}
			}
		}
		return $list;
	}
	public static function parseChooseArray($str, $key1 = false){
		$sp = qscms::trimExplode("\n", $str);
		$list = array();
		foreach ($sp as $v) {
			if ($v) {
				$sp1 = qscms::trimExplode('=', $v);
				if ($key1) {
					$list[array_pop($sp1)] = implode('=', $sp1);
				} else {
					$list[array_shift($sp1)] = implode('=', $sp1);
				}
			}
		}
		return $list;
	}
	public static function parseThumb($str){
		$list = array();
		foreach (qscms::trimExplode(';', $str) as $v) {
			if ($v) {
				@list($name, $str) = qscms::trimExplode(',', $v);
				if (!empty($name) && !empty($str)) {
					@list($width, $height) = qscms::trimExplode('x', $str);
					if (!empty($width) && !empty($height)) {
						$list[$name] = array('width' => $width, 'height' => $height);
					}
				}
			}
		}
		return $list;
	}
	public static function getCheckBox($arr){
		$rn = 0;
		if ($arr) {
			qscms::setType($arr, 'int');
			foreach ($arr as $v) {
				$rn |= 1 << ($v - 1);
			}
		}
		return $rn;
	}
	public static function getPregVal($pattern, $str, $index = 1){
		if (preg_match($pattern, $str, $matches)) {
			if (isset($matches[$index])) return $matches[$index];
		}
		return '';
	}
	public static function getJsonData($data, $decode = false){
		$data = trim($data);
		if (substr($data, 0, 1) != '{' || substr($data, -1) != '}') {
			$f = strpos($data, '(');
			if ($f !== false) {
				$a = $f + 1;
				$f = strrpos($data, ')');
				$data = trim(substr($data, $a, $f - $a));
			}
		}
		if ($decode) return self::json_decode($data);
		return $data;
	}
	public static function str_split($str, $getLen = false){
		static $mbTable = array(
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2,
        	2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2,
        	3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3,
        	4, 4, 4, 4, 4, 4, 4, 4, 5, 5, 5, 5, 6, 6, 1, 1
		);
		$list = array();
		if (ENCODING == 'utf-8') {
			$len = strlen($str);
			for ($i = 0; $i < $len; $i++) {
				$s = $str{$i};//substr($str, $i, 1);
				$n = ord($s);
				//$n = 0;
				$c = $mbTable[$n];
				$list[] = $getLen ? array(substr($str, $i, $c), $c > 1 ? 2 : 1) : substr($str, $i, $c);
				$i += $c - 1;
				/*if (!($n & 0x80)) {
					$list[] = $getLen ? array($s, 1) : $s;
				} else {
					$j = 0;
					$c = 0;
					$c = $n & 0x10 ? 4 : ( $n & 0x20 ? 3 : 2);
					$list[] = $getLen ? array(substr($str, $i, $c), 2) : substr($str, $i, $c);
					$i += $c - 1;
					
				}*/
			}
		}
		return $list;
	}
	public static function cutstr($data, $len){
		static $mbTable = array(
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
        	2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2,
        	2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2,
        	3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3,
        	4, 4, 4, 4, 4, 4, 4, 4, 5, 5, 5, 5, 6, 6, 1, 1
		);
		if (!$len) return '';
		$slen = strlen($data);
		$str = '';
		$getLen = 2;
		for ($i = 0; $i < $slen; $i++) {
			$s = $data{$i};
			$n = ord($s);
			$c = $mbTable[$n];
			$cw = $c > 1 ? 2: 1;
			if ($getLen + $cw > $len) {
				$str .= '…';
				return $str;
			} else {
				$str .= substr($data, $i, $c);
				$getLen += $cw;
			}
			$c = 3;
			$i += $c - 1;
		}
		return $str;
		$arr = self::str_split($data, true);
		$dataLen = array_sum(qscms::arrid($arr, 1));
		if ($dataLen > $len) {
			$count = count($arr);
			$arr[] = array('', 0);
			$getLen = 2;
			$data = '';
			for ($i = 0; $i < $count; $i++) {
				if ($getLen + $arr[$i][1] > $len) {
					$data .= '…';
					return $data;
				} else {
					$data .= $arr[$i][0];
					$getLen += $arr[$i][1];
				}
			}
		}
		return $data;
	}
	public static function cutUBB(&$str, $count){
		$sp = self::str_split($str);
		$len = count($sp);
		$inFlag = $flatStart = $flagEnd = $findFlagName = false;
		$flagName = '';
		$flagNum  = 0;
		$txtLen   = 0;
		$flagStr = '';
		$rs = '';
		$flagNameList = array();
		$isEnd = true;
		$imageMaxCount = 1;
		$imageCount = 0;
		for ($i = 0; $i < $len; $i++) {
			$s = $sp[$i];
			if ($inFlag) {//在标签中
				if ($flagStart) {//开始标签
					//$rs .= $s;
					$flagStr .= $s;
					if (!$findFlagName) {//还未找到标签名
						if (in_array($s, array(' ', "\r", "\n", "\t", '=', ']'))) {
							$findFlagName = true;
							$flagNameList[] = $flagName;
							if ($s == ']') {
								//$rs .= $flagStr;
								$inFlag = false;
							}
						} else {
							$flagName .= $s;
						}
					} elseif ($s == ']') {
						//$rs .= $flagStr;
						$inFlag = false;
					}
				} elseif ($flagEnd) {//结束标签
					if ($s == ']') {
						$c = count($flagNameList);
						if ($c > 0) {
							$f = array_search($flagName, array_reverse($flagNameList));
							if ($f !== false) {//有上级标签
								$f = $c - 1 - $f;
								for ($j = $c - 1; $j >= $f; $j--) {
									//$rs .= '[/'.$flagNameList[$j].']';
									$flagStr .= '[/'.$flagNameList[$j].']';
									array_pop($flagNameList);
								}
							}
						}
						if ($flagName == 'img') {
							$imageCount++;
							if ($imageCount <= $imageMaxCount) {
								$rs .= $flagStr;
							}
						} else {
							$rs .= $flagStr;
						}
						$flagStr = '';
						$inFlag = false;
						$findFlagName = false;
					} else $flagName .= $s;
				} else {
					if ($s == '/') $flagEnd = true;
					else {
						$flagName .= $s;
						$flagStart = true;
						//$rs .= '['.$s;
						$flagStr .= '['.$s;
					}
				}
			} else {
				if ($s == '[') {
					$inFlag = true;
					$flagName = '';
					//$flagStr = '';
					$flagStart = $flagEnd = $findFlagName = false;
				} else {
					if ($findFlagName) {
						$flagStr .= $s;
					} else {
						$rs .= $s;
					}
					if (!$findFlagName || $flagName != 'img') {
						$txtLen++;//字符数量加一
						if ($txtLen == $count) {
							$rs .= $flagStr;
							foreach (array_reverse($flagNameList) as $v) $rs .= '[/'.$v.']';
							$flagNameList = array();
							$isEnd = false;
							break;
						}
					}
				}
			}
		}
		if ($flagNameList) {
			foreach (array_reverse($flagNameList) as $v) $rs .= '[/'.$v.']';
		}
		$str = $rs;
		return $isEnd;
	}
	public static function getFileRandStr($dir, $cacheFile = false, $cache = false){
		static $cacheArr;
		if ($cache && isset($cacheArr['d_'.$dir.'_'.$cache])) return $cacheArr['d_'.$dir.'_'.$cache];
		$files = isset($cacheArr['file_'.$dir]) ? $cacheArr['file_'.$dir] : file::getFiles(s('data/'.$dir), '/\.txt$/s');
		$count = count($files);
		if ($count == 0) return '';
		$file = ($cacheFile && isset($cacheArr['fn_'.$dir.'_'.$cacheFile])) ? $cacheArr['fn_'.$dir.'_'.$cacheFile] : s('data/'.$dir).$files[rand(0, $count - 1)];
		$cacheFile && $cacheArr['fn_'.$dir.'_'.$cacheFile] = $file;
		if (isset($cacheArr['f_'.$dir.'_'.$file])) {
			$datas = $cacheArr['f_'.$dir.'_'.$file];
		} else {
			$datas = file::read($file);
			$datas = qscms::trimExplode("\n", $datas);
			qscms::arrayUnsetEmpty($datas);
			$cacheArr['f_'.$dir.'_'.$file] = $datas;
		}
		$count = count($datas);
		$data = iconv('GBK', ENCODING, $datas[rand(0, $count - 1)]);
		$cache && $cacheArr['d_'.$dir.'_'.$cache] = $data;
		return $data;
	}
	/*public static function cuthtml($html, $len){
		$text_tags = "P|DIV|H1|H2|H3|H4|H5|H6|ADDRESS|PRE|TABLE|TR|TD|TH|A|UL|OL|LI|SPAN|IMG|HR|BR";
		$d_tags    = 'IMG|HR|BR';
		if(!$html) return false;
		$html = preg_replace("/<(?!$text_tags)([a-z]+)(?:\s+.*)?>(?>(?!<\\1[^>]*>).+|(?R))*<\/\\1>/isU", '', $html);
		$html = preg_replace("/<\/?(?!$text_tags)([a-z]+)(?:\s+.*)?>/isU", '', $html);
		//$html=preg_replace("/<g>(? >(?!<g>|<\/g>).+|(?R))*<\/g>/isU",'',$html);
		//echo "<pre>",htmlspecialchars($html),"</pre>";
		//return;
		if($count = preg_match_all("/<(?:(\\/?)([a-z]+)(?:\s+[^>]*)?)>/is", $html, $matchs, PREG_OFFSET_CAPTURE)){
			$tmp = substr($html, 0, $matchs[0][0][1]);
			$tag = $matchs[0][0][0];
			if(strpos($text_tags, strtoupper($matchs[2][0][0])."|") === false)$tag = '';
			$tmp_html = $tmp.$tag;
			$tmp_txt  = preg_replace("/\s/s",'',$tmp);
			if(mb_strlen($tmp_txt) > $len)return $tmp_html;
			for($i=0; $i < $count; $i++){
				$tagname=strtoupper($matchs[2][$i][0]);
					if($b_a==$b_b){
						if($matchs[3][$i][0]=='/'){
							$tags[$tagname]['a']['a']++;
							$tags[$tagname]['a']['i']=$i;
							$tags[$tagname]['a']['b']++;
						} else {
							if($matchs[1][$i][0]=='/'){
								$tags[$tagname]['a']['b']++;
								$tags[$tagname]['a']['i']=$i;
							} else {
								$tags[$tagname]['a']['a']++;
								$tags[$tagname]['a']['i']=$i;
							}
						}
						if($i>0){
							$tag=$matchs[0][$i][0];
							$tmp_start=$matchs[0][$i-1][1]+strlen($matchs[0][$i-1][0]);
							$tmp_end=$matchs[0][$i][1];
							$txt=substr($html,$tmp_start,$tmp_end-$tmp_start);
							if(($n_len=mb_strlen($tmp_txt.$txt,ENCODING))>=$len){
								$tmp_html.=mb_substr($txt,0,$len-mb_strlen($tmp_txt,ENCODING));
								foreach($tags as $k=>$v){
									if(strpos($d_tags,$k."|")===false){
										if($v['a']['a']||$v['a']['b']){
											if(($l=$v['a']['a']-$v['a']['b'])>0){
												$nxt=str_repeat("</".$matchs[2][$v['a']['i']][0].">",$l).$nxt;
											}
										}
									}
								}
								$tmp_html.=$tag.$nxt;
								return $tmp_html;
							} else {
								$tmp_txt.=$txt;
								$tmp_html.=$txt.$tag;
							}
						}
					}
			}
			//print_r($tags);
			return $tmp_html;
			//print_r($matchs);
			//print_r($tags);
		} else {
			$dataLen = mb_strlen($html);
			if ($dataLen > $len) {
				$html = mb_substr($html, 0, $len - 1).'…';
			}
			return $html;
		}
	}*/
	public static function _getCallStr($name){
		$id = self::getRandStr(10, 2);
		$str = '<span id="'.$id.'"></span><script>';
		$str .= '$(function(){
			$(\'[name='.$name.']\').click(function(){$(\'#'.$id.'\').html($(this).find(\'option:selected\').html())}).click();
		});';
		$str .= '</script>';
		return $str;
	}
	public static function getCallStr($str){
		return preg_replace('/\{(\w+)\}/e', 'self::_getCallStr(\'$1\')', $str);
	}
}
?>