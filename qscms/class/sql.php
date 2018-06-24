<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class sql{
	public static function getInStr($arr){
		return 'IN(\''.implode('\',\'', $arr).'\')';
	}
	public static function getWhereAt($arr, $key){
		if (($count = count($arr)) > 0) {
			if ($count == 1) return '`'.$key.'`=\''.$arr[0].'\'';
			return '`'.$key.'` '.self::getInStr($arr);
		}
		return '';
	}
	public static function getWhere($arr){
		$where = '';
		if (is_array($arr)) {
			foreach($arr as $k => $v){
				if (strpos($k, '.') === false) $k = '`'.$k.'`';
				$where && $where.=' AND ';
				if (is_array($v)) {
					if (!empty($v['type'])) {
						switch ($v['type']) {
							case 'like':
								$where .= "$k LIKE '%$v[value]".(!isset($v['end']) || !$v['end'] ? '%' : '' )."'";
							break;
							case 'a-b'://范围取值
								$where0 = '';
								$v['min'] && $where0 = "$k>='$v[min]'";
								$v['max'] && (!$where0 || ($where0 .= ' AND ')) && $where0 .= "$k<='$v[max]'";
								$where .= $where0;
							break;
						}
					} else $where .= $k.(count($v) > 1 ? self::getInStr($v) : '=\''.$v[0].'\'');
				} else $where .= $k.'=\''.$v.'\'';
			}
		} else $where = $arr;
		return $where;
	}
	public static function groupCount($tb, $key, $where = ''){
		$where && $where = self::getWhere($where);
		return 'SELECT `'.$key.'`,COUNT(`'.$key.'`) total FROM `'.db::table($tb).'`'.($where ? ' WHERE '.$where : '').' GROUP BY `'.$key.'`';
	}
	public static function groupCount1($tb, $key, $where = ''){
		return 'SELECT * FROM ('.self::groupCount($tb, $key, $where).') t WHERE total>0';
	}
	public static function unionUpdate($utb, $ukey, $uwhere, $uptb, $upkey, $upkey1, $isPlus = true){
		$f = $isPlus ? '+' : '-';
		$sp = qscms::trimExplode(',', $upkey1);
		if ($f == '-') {
			$set = '';
			foreach ($sp as $v) {
				$set && $set .= ',';
				$set .= 't0.`'.$v.'`=IF(t0.`'.$v.'`>t1.total,t0.`'.$v.'`-t1.total,0)';
			}
			return 'UPDATE `'.db::table($uptb).'` t0 RIGHT JOIN ('.self::groupCount1($utb, $ukey, $uwhere).') t1 ON t1.`'.$ukey.'`=t0.`'.$upkey.'` SET '.$set;
		}
		$set = '';
		foreach ($sp as $v) {
			$set && $set .= ',';
			$set .= 't0.`'.$v.'`=t0.`'.$v.'`+t1.total';
		}
		return 'UPDATE `'.db::table($uptb).'` t0 RIGHT JOIN ('.self::groupCount1($utb, $ukey, $uwhere).') t1 ON t1.`'.$ukey.'`=t0.`'.$upkey.'` SET '.$set;
	}
}
?>