<?php
class proxy{
	public static function add($str){
		$count = 0;
		if (!empty($str)) {
			foreach (qscms::trimExplode("\n", $str) as $v) {
				if ($v && preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}:\d{1,5}$/', $v)) {
					if (!db::exists('proxy', array('ipPort' => $v))) {
						if (db::insert('proxy', array('ipPort' => $v, 'addTime' => time()))) {
							$count++;
						}
					}
				}
			}
		}
		return $count;
	}
	public static function getRand(){
		$sql = db::getRandSql('proxy_index', 'pid');
		if ($line = db::selectFirst("($sql)|proxy:id=pid", '|ipPort')) {
			return $line['ipPort'];
		}
		return false;
	}
}
?>