<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class member_level{
	public static $cacheName = 'member_level';
	public static function splitCredit($data){
		if (is_array($data)) {
			foreach ($data as &$v) {
				$v = self::splitCredit($v);
			}
			return $data;
		} else {
			$arr = qscms::trimExplode('/', $data);
			!isset($arr[1]) && $arr[1] = '';
			!isset($arr[2]) && $arr[2] = '';
			return array('credit' => $arr[0], 'coin' => $arr[1], 'max' => $arr[2]);
		}
	}
	public static function setLevel($datas){
		$datas = qscms::filterArray($datas, array('creditMin', 'creditMax', 'ico', 'name', 'loginCredit', 'userCommentCredit', 'infoCommentCredit', 'ykjShopCredit', 'everyDayCredit', 'ykjSXF'));
		if (($count = form::arrayEqual($datas['creditMin'], $datas['creditMax'], $datas['ico'], $datas['name'], $datas['loginCredit'], $datas['userCommentCredit'], $datas['infoCommentCredit'], $datas['everyDayCredit'])) > 0) {
			$datas['loginCredit'] = self::splitCredit($datas['loginCredit']);
			$datas['userCommentCredit'] = self::splitCredit($datas['userCommentCredit']);
			$datas['infoCommentCredit'] = self::splitCredit($datas['infoCommentCredit']);
			$datas['ykjShopCredit'] = self::splitCredit($datas['ykjShopCredit']);
			$datas['everyDayCredit'] = self::splitCredit($datas['everyDayCredit']);
			qscms::setType($datas['ykjSXF'], 'float');
			qscms::setType($datas['creditMin'], 'int');
			qscms::setType($datas['creditMax'], 'int');
			qscms::setType($datas['loginCredit'], 'int');
			qscms::setType($datas['userCommentCredit'], 'int');
			qscms::setType($datas['infoCommentCredit'], 'int');
			qscms::setType($datas['everyDayCredit'], 'int');
			foreach ($datas['ico'] as &$v) {
				$v = qscms::trimExplode("\n", $v);
				qscms::arrayUnsetEmpty($v);
			}
			foreach ($datas as &$v) {
				$v = array_values($v);
			}
			$cfgArr = array();
			for ($i = 0; $i < $count; $i++) {
				$arr = array();
				$arr['index'] = $i == 0 ? 0 : ($i + 1 == $count ? 2 : 1);
				$arr['atCredit'] = array(
					'min' => $datas['creditMin'][$i],
					'max' => $datas['creditMax'][$i]
				);
				$arr['ico'] = $datas['ico'][$i];
				$arr['name'] = $datas['name'][$i];
				$arr['add'] = array(
					'login' => array(
						'credit' => $datas['loginCredit'][$i]['credit'],
						'coin' => $datas['loginCredit'][$i]['coin'],
						'max' => $datas['loginCredit'][$i]['max']
					),
					'comment' => array(
						'user' => array(
							'credit' => $datas['userCommentCredit'][$i]['credit'],
							'coin' => $datas['userCommentCredit'][$i]['coin'],
							'max' => $datas['userCommentCredit'][$i]['max']
						),
						'info' => array(
							'credit' => $datas['infoCommentCredit'][$i]['credit'],
							'coin' => $datas['infoCommentCredit'][$i]['coin'],
							'max' => $datas['infoCommentCredit'][$i]['max']
						)
					),
					'shop' => array(
						'ykj' => array(
							'credit' => $datas['ykjShopCredit'][$i]['credit'],
							'coin' => $datas['ykjShopCredit'][$i]['coin'],
							'max' => $datas['ykjShopCredit'][$i]['max'],
							'sxf' => $datas['ykjSXF'][$i]
						)
					)
				);
				$arr['limit'] = array(
					'day' => array(
						'credit' => $datas['everyDayCredit'][$i]['credit'],
						'coin'   => $datas['everyDayCredit'][$i]['coin']
					)
				);
				$arr['level'] = $i + 1;
				$arr['subMaxLevel'] = count($arr['ico']);
				$arr['subLevelSpace'] = floor(($arr['atCredit']['max'] - $arr['atCredit']['min'] + 1) / $arr['subMaxLevel']);
				$cfgArr[] = $arr;
			}
			self::writeCfg($cfgArr);
			return true;
		}
		return '数据错误';
	}
	public static function setLevelToForm(){
		if (form::hash()) {
			return self::setLevel($_POST);
		}
		return false;
	}
	public static function writeCfg($cfg){
		cache::write_array(self::$cacheName, $cfg);
		memory::write(self::$cacheName, $cfg);
	}
	public static function getCfg(){
		static $cfg;
		if (isset($cfg)) return $cfg;
		$cfg = memory::get(self::$cacheName);
		if (is_null($cfg)) {
			$cfg = cache::get_array(self::$cacheName);
			memory::write(self::$cacheName, $cfg);
		}
		return $cfg;
	}
	public function __construct($credit){
		$this->cfg = member_level::getCfg();
		$this->maxLevel = count($this->cfg);
		$this->levelCfg = array();
		$this->credit = intval($credit);
		$this->nextLevelCredit = $this->nextLevelNeed = 0;
		$this->subLevel = 1;
		foreach ($this->cfg as $v) {
			switch ($v['index']) {
				case 0:
					if ($this->credit <= $v['atCredit']['max']) {
						$this->levelCfg = $v;
						break;
					}
				break;
				case 1:
					if ($this->credit >= $v['atCredit']['min'] && $this->credit <= $v['atCredit']['max']) {
						$this->levelCfg = $v;
						break;
					}
				break;
				case 2:
					if ($this->credit >= $v['atCredit']['min']) {
						$this->levelCfg = $v;
						break;
					}
				break;
			}
		}
		!$this->levelCfg && $this->levelCfg = $this->cfg[$this->maxLevel - 1];
		if ($this->levelCfg['level'] == 1) {
			$this->preLevelCfg = array();
		} else {
			$this->preLevelCfg = $this->cfg[$this->levelCfg['level'] - 2];
		}
		if ($this->levelCfg['level'] == $this->maxLevel) {
			$this->nextLevelCfg = array();
		} else {
			$this->nextLevelCfg = $this->cfg[$this->levelCfg['level']];
		}
		if ($this->nextLevelCfg) {
			$this->nextLevelCredit = $this->nextLevelCfg['atCredit']['min'];
			$this->nextLevelName = $this->nextLevelCfg['name'];
			$this->nextLevelNeed = $this->nextLevelCredit - $this->credit;
			$this->nextLevelFloat = $this->credit / $this->nextLevelCredit;
			$this->nextLevelFloat = floor($this->nextLevelFloat * 100) / 100;
			$this->nextLevelPercent = sprintf('%01.0f', $this->nextLevelFloat * 100).'%';
		}
		$this->thisLevelCredit = $this->credit - $this->levelCfg['atCredit']['min'];
		$ico = $this->levelCfg['ico'][0];
		if ($this->levelCfg['subMaxLevel'] > 1) {
			$this->subLevel = floor($this->thisLevelCredit / $this->levelCfg['subLevelSpace']) + 1;
			$ico = $this->levelCfg['ico'][$this->subLevel - 1];
		}
		$this->ico = su('images/level/'.$ico);
		$this->levelName = $this->levelCfg['name'];
	}
}
?>