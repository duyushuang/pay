<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class word{
	public static function union(&$str, $type = 0){
		$len = mb_strlen($str);
		$atA = $atA0 = $atA1 = $atA2 = $atM = $atS = $zs = false;
		$s = $lastS = $nextS = $sFlag = $tmp = $txt =  $rs = '';
		$list = string::str_split($str);
		$len = count($list);
		$list[] = '';
		$list[] = '';
		$setCount = 0;
		for ($i = 0; $i < $len; $i++) {
			//$s = mb_substr($str, $i, 1);
			//$nextS = mb_substr($str, $i + 1, 1);
			$s     = $list[$i];
			$nextS = $list[$i + 1];
			if ($atA) {//在连接内
				if ($atA0) {
					if ($atS) {
						if ($s == $sFlag) {//if ($s == $sFlag && $zs % 2 == 0) {
							$atS = false;
							$tmp .= $s;
						} else {
							if ($s == '\\') {
								if ($lastS == '\\') $zs++;
								else $zs = 1;
							}
							$tmp .= $s;
						}
					} else {
						if ($s == '\'' || $s == '"') {
							$atS = true;
							$zs  = 0;
							$sFlag = $s;
							$tmp .= $s;
						} else {
							$tmp .= $s;
							if ($s == '>') {
								$atA0 = false;
								$atA1 = true;
							}
						}
					}
				} elseif ($atA1) {
					$tmp .= $s;
					//if ($s == '/' && $lastS == '<' && ($nextS == 'a' || $nextS == 'A') && mb_substr($str, $i + 2, 1) == '>') {
						if ($s == '/' && $lastS == '<' && ($nextS == 'a' || $nextS == 'A') && $list[$i + 2] == '>') {
						$atA1 = false;
						$atA2 = false;
						$atA = false;
						$tmp .= $nextS.$list[$i + 2];
						$i += 2;
						//echo "\r\na:\r\nrs=$rs\r\ntmp=$tmp\r\nend\r\n";
						$rs .= $tmp;
					}
				} elseif ($atA2) {
					
				}
			} elseif ($atM) {//在其它标签内
				if ($atS) {
					if ($s == $sFlag) {//if ($s == $sFlag && $zs % 2 == 0) {
						$atS = false;
						$tmp .= $s;
					} else {
						if ($s == '\\') {
							if ($lastS == '\\') $zs++;
							else $zs = 1;
						}
						$tmp .= $s;
					}
				} else {
					if ($s == '\'' || $s == '"') {
						$atS = true;
						$zs  = 0;
						$sFlag = $s;
						$tmp .= $s;
					} else {
						$tmp .= $s;
						if ($s == '>') {
							$atM = false;
							//echo "\r\nm:$tmp\r\n";
							$rs .= $tmp;
						}
					}
				}
			} else {//没有标签
				if ($lastS == '<') {
					if (($s == 'a' || $s == 'A') && in_array($nextS, array("\r", "\n", "\t", ' ', '>'))) {//进入连接
						$atA = $atA0 = true;
						$atS = false;
						//$rs .= $s;
						$tmp = $lastS.$s;
						$txt = substr($txt, 0, -1);//mb_substr($txt, 0, -1);
						if ($txt) {
							$setCount += self::union_($txt, $type);
							$rs .= $txt;
							$txt = '';
						}
					} elseif ($s >= 'a' && $s <= 'z' || $s >= 'A' && $s <= 'Z') {
						$atM = true;
						$atS = false;
						//$rs .= $s;
						$txt = substr($txt, 0, -1);//mb_substr($txt ,0, -1);
						$tmp = $lastS.$s;
						if ($txt) {
							$setCount += self::union_($txt, $type);
							$rs .= $txt;
							$txt = '';
						}
					}
				} else {
					//$rs .= $s;
					$txt .= $s;
				}
			}
			$lastS = $s;
		}
		if ($txt) {
			$setCount += self::union_($txt, $type);
			$rs .= $txt;
			$txt = '';
		}
		$str = $rs;
		return $setCount;
	}
	private static function keyExists($key, $isLike = true){
		static $cacheA = array();
		static $cacheB = array();
		if ($isLike) {
			if (isset($cacheA[$key])) $rs = $cacheA[$key];
			else {
				$rs = db::exists('word', 'txt LIKE \''.$key.'%\'');
				$cacheA[$key] = $rs;
			}
		} else {
			if (isset($cacheB[$key])) $rs = $cacheB[$key];
			else {
				$rs = db::one_one('word', 'alias', 'txt=\''.$key.'\'');
				$cacheB[$key] = $rs;
			}
		}
		return $rs;
		return $isLike ? db::exists('word', 'txt LIKE \''.$key.'%\'') : db::one_one('word', 'id', 'txt=\''.$key.'\'');//db::exists('word', 'txt=\''.addslashes($key).'\'');
		$len = mb_strlen($key);
		return $isLike ? db::exists('word', 'len>='.$len.' AND txt LIKE \''.$key.'%\'') : db::exists('word', 'len='.$len.' AND txt=\''.addslashes($key).'\'');
	}
	private static function union_(&$txt, $type = 0, $keyMinLen = 4){
		$l = mb_strlen($txt);
		$key = '';
		$keyLen = 0;
		$lastFind = $thisFind = false;
		$rs = '';
		$safe = 0;
		$setCount = 0;
		for ($i = 0; $i < $l; $i++) {
			//$safe++;
			//if ($safe == 100000) break;
			$isEnd = $i + 1 == $l;
			$s = mb_substr($txt, $i, 1);
			$key .= $s;
			$keyLen++;
			if ($keyLen >= $keyMinLen) {
				//echo "\r\nkey:$key\r\n";
				$thisFind = self::keyExists($key);
				if ($thisFind) {
					$lastFind = true;
					if ($isEnd) {
						$find = false;
						do {
							$find = self::keyExists($key, false);
							//echo "\r\nfind key:$key\r\n";
							if (!$find) {
								$key = mb_substr($key, 0, -1);
								$keyLen--;
								$i--;
							}
						} while(!$find && $keyLen >= $keyMinLen);
						if ($find) {
							if ($type == 0) {
								$rs .= '<a href="'.u('./shop/'.$find.'/').'" title="点击查看'.$key.'相关商品">'.$key.'</a>';
							} elseif ($type == 1) {
								$rs .= '<a data-type="2" data-keyword="'.$key.'" data-rd="1" data-style="1" data-tmpl="290x380" target="_blank">'.$key.'</a>';
							}
							$setCount++;
							$key = '';
							$keyLen = 0;
						} else {
							$key = '';
							$keyLen = 0;
							$rs .= $key;
							//$i -= $keyLen - 1;
						}
						$lastFind = false;
					}
				} else {
					if ($lastFind) {//上次有匹配到
						$key = mb_substr($key, 0, -1);
						$i--;
						$keyLen--;
						//echo "\r\nlast key:$key,key length:$keyLen|$keyMinLen\r\n";
						if ($keyLen >= $keyMinLen) {
							$find = false;
							do {
								$find = self::keyExists($key, false);
								//echo "\r\nfind key:$key\r\n";
								if (!$find) {
									$key = mb_substr($key, 0, -1);
									$keyLen--;
									$i--;
								}
							} while(!$find && $keyLen >= $keyMinLen);
							if ($find) {
								if ($type == 0) {
									$rs .= '<a href="'.u('./shop/'.$find.'/').'" title="点击查看'.$key.'相关商品">'.$key.'</a>';
								} elseif ($type == 1) {
									$rs .= '<a data-type="2" data-keyword="'.$key.'" data-rd="1" data-style="1" data-tmpl="290x380" target="_blank">'.$key.'</a>';
								}
								$setCount++;
								$key = '';
								$keyLen = 0;
							} else {
								$key = '';
								$keyLen = 0;
								$rs .= $key;
								//$i -= $keyLen - 1;
							}
							$lastFind = false;
						} else {
							$rs .= $key;
						}
					} else {//上次没有匹配到
						$rs .= mb_substr($key, 0, -1);
						$key = '';
						$keyLen = 0;
						$i--;
					}
				}
			}
		}
		$rs .= $key;
		$txt = $rs;
		return $setCount;
	}
}
?>