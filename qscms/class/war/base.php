<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class war_base{
	public static function getQu1($alias){
		static $types = array('d' => '电信', 'w' => '网通', 'j' => '教育');
		/**
		 * 获取盒子线路，如：电信一，网通一，教育一
		 */
		$type = substr($alias, 0, 1);
		$number = intval(substr($alias, 1));
		return $types[$type].number::chinese($number);
	}
	public function getMatchIds($name, $qu){
		$url = 'http://lolbox.duowan.com/matchList.php?serverName='.urlencode(self::getQu1($qu)).'&playerName='.urlencode($name).'&page=0';
		$html = winsock::get_html($url);
		if ($html) {
			//$date = date('m-d', $time);
			$ids = array();
			if (preg_match_all('/onclick="loadMatchDetail\((\d+).*?<p class="info"><span>.*?<\/span>(.+?)<\/p>/s', $html, $matches, PREG_SET_ORDER)) {
				foreach ($matches as $v) {
					//$v[2] == $date && $ids[] = $v[1];
					$ids[] = $v[1];
				}
			}
			return $ids;
		}
		return '获取数据失败，请重试';
	}
	public static function getFightData($name, $qu, $time){
		$ids = self::getMatchIds($name, $qu);
		if (!is_array($ids)) return $ids;
		foreach ($ids as $id) {
			$url = 'http://api.lolbox.duowan.com/lol/match/detail?matchId='.$id;
			$html = winsock::get_html($url);
			if (($f = strpos($html, '{')) !== false) {
				$html = substr($html, $f, strrpos($html, '}') + 1);
				$arr = string::json_decode($html);
				if (!$arr['code']) {
					if ($arr['matchDetail']['gameLength'] + $time <= $arr['matchDetail']['endTimestamp']) {
						$winer = $loser = array();
						$player = array();
						$winTeamId = '';
						foreach ($arr['matchDetail']['player'] as $v) {
							$player[$v['teamId']][$v['summonerName']] = $v['TOTAL_DAMAGE_DEALT'];//array('name' => , 'damage' => $v['TOTAL_DAMAGE_DEALT']);
							if (isset($v['WIN'])) $winTeamId = $v['teamId'];
						}
						$winer = $player[$winTeamId];
						$loser = $player[$winTeamId == '100' ? '200' : '100'];
						/*echo "胜利：".implode('|', $winer);
						echo '<br />';
						echo '失败：'.implode('|', $loser);
						echo '<br /><br />';print_r($arr);*/
						//print_r($winer);
						//print_r($loser);
						print_r($arr);
					}
				}
			}
		}
	}
	public static function checkFight($aArr, $bArr, $qu, $time){
		
		/**
		 * 格式化时间
		 */
		if (!is_numeric($time)) $time = time::getGeneralTimestamp($time);
		else $time = intval($time);
		
		/**
		 * 判定是否为1V1 并且设置观战员
		 */
		$is_1v1 = false;
		$attend = 0;
		$countA = count($aArr);
		$countB = count($bArr);
		if ($countA + $countB == 3) {
			if ($countA == 2) {
				if ($aArr[1] == '*') {
					$attend = 1;
					$is_1v1 = true;
					array_pop($aArr);
					$countA--;
				}
			} else {
				if ($bArr[1] == '*') {
					$attend = 2;
					$is_1v1 = true;
					array_pop($bArr);
					$countB--;
				}
			}
		}
		
		/**
		 * 合并两队人员
		 */
		$allUser = array_merge($aArr, $bArr);
		
		/**
		 * 设定检测的入口名字
		 */
		$useName = $allUser[0];
		
		/**
		 * 获取比赛ID集
		 */
		$ids = self::getMatchIds($useName, $qu);
		if (!is_array($ids)) return $ids;
		
		/**
		 * 遍历ID集获取数据 获取最终的结果
		 */
		$getWiner = $getLoser = array();
		foreach ($ids as $id) {
			
			/**
			 * 获取比赛数据
			 */
			$url = 'http://api.lolbox.duowan.com/lol/match/detail?matchId='.$id;
			$html = winsock::get_html($url);
			if (!$html) return '获取失败，请重试';
			if (($f = strpos($html, '{')) !== false) {
				$html = substr($html, $f, strrpos($html, '}') + 1);
				$arr = string::json_decode($html);
				if (!$arr['code']) {
					
					/**
					 * 判定时间是否合理
					 */
					if ($arr['matchDetail']['gameLength'] + $time <= $arr['matchDetail']['endTimestamp']) {
						$winer = $loser = array();
						$player = array();
						$winTeamId = '';
						foreach ($arr['matchDetail']['player'] as $v) {
							$player[$v['teamId']][$v['summonerName']] = $v['TOTAL_DAMAGE_DEALT'];//array('name' => , 'damage' => $v['TOTAL_DAMAGE_DEALT']);
							if (isset($v['WIN'])) $winTeamId = $v['teamId'];
						}
						$winer = $player[$winTeamId];
						$loser = $player[$winTeamId == '100' ? '200' : '100'];
						$users = array_merge(array_keys($winer), array_keys($loser));
						
						/**
						 * 检查参展员是否都在读取到的用户里面
						 */
						$status = true;
						foreach ($allUser as $user) {
							if (!in_array($user, $users)) {
								$status = false;
								break;
							}
						}
						
						/**
						 * 判断是否检测到了
						 */
						if ($status) {
							$getWiner = $winer;
							$getLoser = $loser;
							break;
						}
					}
				}
			}
		}
		if ($getWiner && $getLoser) {
			$winType = 0;
			$winerNames = array_keys($getWiner);
			if (in_array($aArr[0], $winerNames)) $winType = 1;
			else $winType = 2;
			if ($is_1v1 && $winType == $attend) {
				$attendName = $winerNames[0] == $aArr[0] ? $winerNames[1] : $winerNames[0];
				if ($getWiner[$attendName] > 0) $winType = 0;
			}
			return $winType;
		}
		return false;
	}
}