<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class tkd{
	public static $varTypes = array('系统(_G)', '模块', 'GET($_GET)', 'POST($_POST)', 'COOKIE($_COOKIE)');
	private static function formatData($datas){
		$datas = qscms::filterArray($datas, array('name', 'marker', 'varType', 'varName', 'varCheck'));
		$whereList = array();
		if (isset($datas['varType']) && isset($datas['varName']) && isset($datas['varCheck'])) {
			if (is_array($datas['varType']) && is_array($datas['varName']) && is_array($datas['varCheck'])) {
				if (count($datas['varType']) == count($datas['varName']) && count($datas['varName']) == count($datas['varCheck'])) {
					$datas['varType']  = array_values($datas['varType']);
					$datas['varName']  = array_values($datas['varName']);
					$datas['varCheck'] = array_values($datas['varCheck']);
					$count = count($datas['varType']);
					$typeKeys = array_keys(self::$varTypes);
					for ($i = 0; $i < $count; $i++) {
						$varType  = intval($datas['varType'][$i]);
						$varName  = $datas['varName'][$i];
						$varCheck = $datas['varCheck'][$i];
						if (in_array($varType, $typeKeys) && !empty($varName) && !empty($varCheck)) {
							$whereList[] = array(
								'varType'  => $varType,
								'varName'  => $varName,
								'varCheck' => qscms::stripslashes($varCheck)
							);
						}
					}
				}
			}
		}
		$where = '';
		foreach ($whereList as $k => $v) {
			$var = '';
			switch ($v['varType']) {
				case 0:
					if (strstr($v['varName'], '@')){
						$var = '$var->'.substr($v['varName'], 1);	
					}else{
						$var = '$var->gp_'.$v['varName'];
					}
				break;
				case 1:
					$var = '$'.$v['varName'];
				break;
				case 2:
					$var = '$_GET[\''.$v['varName'].'\']';
				break;
				case 3:
					$var = '$_POST[\''.$v['varName'].'\']';
				break;
				case 4:
					$var = '$_COOKIE[\''.$v['varName'].'\']';
				break;
			}
			$w = $v['varCheck'];
			$w = str_replace('{var}', $var, $w);
			$where && $where .= ' && ';
			$where .= '('.$w.')';
		}
		$datas = array(
			'name'   => $datas['name'],
			'marker' => $datas['marker'],
			'whereList' => qscms::addslashes(serialize($whereList)),
			'where'     => qscms::addslashes($where)
		);
		return $datas;
	}
	public static function addItem($datas){
		$datas = self::formatData($datas);
		if (db::insert('tkd', $datas)) {
			return true;
		}
		return false;
	}
	public static function editItem($datas, $id){
		$datas = self::formatData($datas);
		if (db::update('tkd', $datas, "id='$id'")) {
			return true;
		}
		return false;
	}
	public static function setData($datas){
		$datas = qscms::filterArray($datas, array('title', 'keywords', 'description'));
		$count = 0;
		if (isset($datas['title']) && isset($datas['keywords']) && isset($datas)) {
			
			$title       = $datas['title'];
			$keywords    = $datas['keywords'];
			$description = $datas['description'];
			
			if ($count = form::arrayEqual($title, $keywords, $description)) {
				foreach ($title as $_k => $v) {
					$t = $v;
					$k = $keywords[$_k];
					$d = $description[$_k];
					db::update('tkd', array('title' => $t, 'title1' => $t1, 'keywords' => $k, 'description' => $d), "id='$_k'");
				}
			}
		}
		self::updateCache();
		return $count;
	}
	public static function setSort($ids, $sorts){
		$count = form::array_equal($ids, $sorts);
		$rsCount = 0;
		if ($count > 0) {
			for ($i = 0; $i < $count; $i++) {
				$id   = $ids[$i];
				$sort = $sorts[$i];
				if (db::update('tkd', array('sort' => $sort), "id='$id'")) {
					$rsCount++;
				}
			}
		}
		return $rsCount;
	}
	public static function delete($ids){
		$count = db::del_ids('tkd', $ids);
		self::updateCache();
		return $count;
	}
	private static function formatTKD($key, $data, &$arr){
		$data = preg_replace('/\{(.+?)\}/e', 'self::_formatTKD($key, \'$1\', $arr)', $data);
		return $data;
	}
	private static function _formatTKD($k, $key, &$arr){
		$key = qscms::stripslashes($key);
		if (strpos($key, ':') !== false) {
			$sp = qscms::trimExplode(':', $key);
			switch ($sp[0]) {
				case 'cfg':
					$sp2 = qscms::trimExplode(',', $sp[1]);
					if (count($sp2) == 2) {
						return cfg::get($sp2[0], $sp2[1]);
					}
				break;
				case 'call':
					$sp2 = qscms::trimExplode(',', $sp[1]);
					if (count($sp2) == 2) {
						if (!empty($arr[$sp2[0]])) {
							return self::formatTKD($sp2[0], $arr[$sp2[0]][$sp2[1]], $arr);
						}
					}
				break;
				case 'this':
					return self::formatTKD($k, $arr[$k][$sp[1]], $arr);
				break;
			}
		} elseif (strpos($key, '|') !== false) {
			$sp = qscms::trimExplode('|', $key);
			$var = '$'.$sp[0];
			if (!empty($sp[1])) {
				return '{(!empty('.$var.')?'.$var.':\''.qscms::addcslashes($sp[1]).'\')}';
			}
		} else {
			return '{$'.$key.'}';
		}
		return '';
	}
	public static function updateCache(){
		$list = array();
		foreach (self::getList(0, 0, 'marker,`where`,title,keywords,description') as $v) {
			$list[$v['marker']] = $v;
		}
		//print_r($_POST);exit;
		//print_r($list);exit;
		if ($list) {
			foreach ($list as $k => $v) {
				$v['_title']        = '\''.preg_replace('/\{(.+?)\}/', '\'.(!empty($1)?$1:\'\').\'', self::formatTKD($k, $v['title']      , $list)).'\'';
				$v['_keywords']     = '\''.preg_replace('/\{(.+?)\}/', '\'.(!empty($1)?$1:\'\').\'', self::formatTKD($k, $v['keywords']   , $list)).'\'';
				$v['_description']  = '\''.preg_replace('/\{(.+?)\}/', '\'.(!empty($1)?$1:\'\').\'', self::formatTKD($k, $v['description'], $list)).'\'';
				$list[$k] = $v;
			}
			$code1 = $code2 = '';
			foreach ($list as $v) {
				if ($v['where']) {
					$code2 && $code2 .= 'else';
					$code2 .= 'if ('.$v['where'].') {$webTitle = '.$v['_title'].';$webKeywords = '.$v['_keywords'].';$webDescription = '.$v['_description'].';}';
				} else {
					$code1 .= '$webTitle = '.$v['_title'].';$webKeywords = '.$v['_keywords'].';$webDescription = '.$v['_description'].';';
				}
				$code2 .= 'if (!empty($web[\'title\'])) $webTitle = $web[\'title\'];if (!empty($web[\'webKeywords\'])) $webKeywords = $web[\'webKeywords\']; if (!empty($web[\'webDescription\'])) $webDescription = $web[\'webDescription\'];';
			}
			//print_r($code1.$code2);exit;
			cache::write_code('tkd', $code1.$code2);
		} else {
			cache::del_code('tkd');
		}
	}
	public static function total(){
		return db::data_count('tkd');
	}
	public static function getList($pagesize = 0, $page = 0, $fields = 'id,sort,name,marker'){
		return db::select('tkd', $fields, '', 'sort', $pagesize, $page);
	}
	public static function get($id){
		return db::one('tkd', '*', "id='$id'");
	}
}
?>