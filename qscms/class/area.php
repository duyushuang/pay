<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class area{
	public static function city_id($name){
		static $cache = array();
		$id = 0;
		if (isset($cache[$name])) $id = $cache[$name];
		else {
			$id = db::one_one('area', 'id', "name='$name'");
			$cache[$name] = $id;
		}
		return $id;
	}
	public static function get_city($city_id, $key = '', $cache=true){
		static $cacheList = array();
		if(!$cache || 
			(
				(
					$cache && !(
						isset($cacheList[$city_id]) && ($line = $cacheList[$city_id])
					)
				) && (
						$cache && !($line = cache::get_area('area_city'.$city_id))
				)
			)
		) {
			$line = categories::cate_this('area', $city_id);
			$cache && cache::write_area('area_city'.$city_id, $line);
			$cache && ($cacheList[$city_id] = $line);
		}
		if($line){
			if($key)return $line[$key];
			return $line;
		}
		return false;
	}
	public static function get_cities($city_id=0,$return_map = false, $return_id = false, $cache = true){
		$list = array();
		if (!$cache || ($cache && !($list = cache::get_area('area'.$city_id)))) {
			if ($tmp_list = categories::cate_first('area', $city_id)) {
				foreach($tmp_list as $line){
					if ($return_map) {
						if (!$return_id) $list[$line['id']] = array('name' => $line['name'], 'sub_num' => $line['sub_num'], 'max_x' => $line['map_x'], 'map_y' => $line['map_y'], 'map_z' => $line['map_z']);
						else $list[] = array('id' => $line['id'], 'name' => $line['name'], 'sub_num' => $line['sub_num'], 'max_x' => $line['map_x'], 'map_y' => $line['map_y'], 'map_z' => $line['map_z']);
					} else {
						if (!$return_id) $list[$line['id']] = array('name' => $line['name'], 'sub_num' => $line['sub_num']);
						else $list[] = array('id' => $line['id'], 'name' => $line['name'], 'sub_num' => $line['sub_num']);
					}
				}
				$cache && cache::write_area('area'.$city_id, $list);
			}
		}
		return $list;
	}
	public static function get_cities_old($city_id = 0, $cache = true){
		if (!($list = cache::get_area('area_'.$city_id))) {
			if ($tmp_list = categories::cate_first('area', $city_id)){
				foreach ($tmp_list as $line) {
					$list[] = array('id' => $line['id'], 'name' => $line['name'], 'count' => $line['sub_num'], 'max_x' => $line['map_x'], 'map_y' => $line['map_y'], 'map_z' => $line['map_z'], 'l'=> $line['l'], 'r' => $line['r']);
				}
				cache::write_area('area_'.$city_id, $list);
			}
		}
		return $list;
	}
	public static function get_parent($id, $key = '', $cache = true){
		static $cacheList = array();
		$list = array();
		$cache && ($list = cache::get_area('parent'.$id));
		if (!$list) {
			$cacheList || ($cacheList = self::all_parent_id());
			if (isset($cacheList[$id]) && ($parent_id = $cacheList[$id])) {
				$list = self::get_city($parent_id, '', $cache);
				$cache && $list && cache::write_area('parent'.$id, $list);
			}
		}
		if($list){
			if($key)return $list[$key];
			return $list;
		}
		return false;
		
	}
	public static function all_parent_id($cache = true){
		$list = cache::get_area('parent_id_list');
		if (!$list) {
			$list = $info = array();
			if($ids = categories::cate_all_ids('area')){
				$i = 0;
				foreach($ids as $id){
					$i++;
					$line = categories::cate_parent('area', $id, 'id');
					if ($line) $pid = $line['id'];
					else $pid = 0;
					$list[$id] = $pid;
				}
			}
			$cache && cache::write_area('parent_id_list', $list);
		}
		return $list;
	}
	public static function all_id_info($cache = true){
		$list = cache::get_area('id_info_list');
		if (!$list) {
			$list = $info = array();
			if ($ids = categories::cate_all_ids('area')) {
				$i = 0;
				foreach($ids as $id){
					$i++;
					$line = categories::cate_parent('area', $id, 'id');
					if ($line) $info['parent'] = $line['id'];
					else $info['parent'] = 0;
					$line = categories::cate_first('area', $id, 'id');
					if ($line) {
						foreach($line as $v) $info['sub_list'][] = $v['id'];
					}
					$list[$id] = $info;
				}
			}
			$cache && cache::write_area('id_info_list', $list);
		}
		return $list;
	}
	public static function all_province(){
		static $province_list = array();
		if (!$province_list) {
			$province_list = cache::get_area('province');
			if (!$province_list) {
				$province_list = self::get_cities(0);
				cache::write_area('province', $province_list);
			}
		}
		return $province_list;
	}
	public static function province(){
		return self::get_cities(0);
	}
	public static function cities($province_id){
		return self::get_cities($province_id);
	}
	public static function counties($city_id){
		return self::get_cities($city_id);
	}
	public static function get_cities_all($city_id){
		if($list = self::get_cities($city_id)){
			foreach ($list as $k => $v){
				if($v['sub_num'] > 0){
					$v['sub_list'] = self::get_cities_all($k);
				}
				$list[$k] = $v;
			}
			return $list;
		}
		return false;
	}
	public static function counties_all($city_id, $cache = true){
		static $cities = array();
		if ($cache) {
			if (!($cities = cache::get_area('city'.$city_id))) {
				$cities = self::get_cities_all($city_id);
				cache::write_area('city'.$city_id, $cities);
			}
			return $cities;
		} else return self::get_cities_all($city_id);
	}
	public static function all_cities($cache = true){
		$list = array();
		if ($province_list = self::all_province()) {
			foreach (array_keys($province_list) as $p_id) {
				if ($tmp_list = self::get_cities($p_id, false, false, $cache)){
					$list += $tmp_list;
				}
			}
		}
		return $list;
	}
	public static function all_province_json($cache = true){
		$json = false;
		$cache && ($json = cache::get_text('json_privince'));
		if (!$json) {
			$cities = self::province();
			if ($cities) {
				$list = $list_e = array();
				foreach($cities as $id=>$val){
					$list[] = array('id' => $id, 'name' => $val['name'], 'sub_num' => $val['sub_num']);
				}
				//array_multisort($list_e, SORT_ASC,SORT_STRING, $list);
				$json = json_encode($list);
				$cache && cache::write_text('json_privince', $json);
			}
		}
		return $json;
	}
	public static function all_cities_json($cache = true){
		$json = false;
		$cache && ($json = cache::get_text('json_cities'));
		if (!$json) {
			$cities = self::all_cities();
			if ($cities) {
				$list = $list_e = array();
				foreach($cities as $id => $val){
					$list[] = array('id' => $id, 'name' => $val['name'], 'sub_num' => $val['sub_num']);
				}
				//array_multisort($list_e, SORT_ASC,SORT_STRING, $list);
				$json = json_encode($list);
				$cache && cache::write_text('json_cities', $json);
			}
		}
		return $json;
	}
	public static function get_cities_json($city_id, $cache = true){
		$json = false;
		$cache&&($json = cache::get_text('json_cities'.$city_id));
		if(!$json){
			$cities = self::get_cities($city_id, false, false, true);
			if ($cities) {
				$list = $list_e = array();
				foreach($cities as $id => $val){
					$list[] = array('id' => $id, 'name' => $val['name'], 'sub_num' => $val['sub_num']);
				}
				//array_multisort($list_e, SORT_ASC, SORT_STRING, $list);
				$json = json_encode($list);
				$cache && cache::write_text('json_cities'.$city_id, $json);
			}
		}
		return $json;
	}
	public static function getParentIdAll($cityId){
		$parent_id = $ids = array();
		while ($parent = self::get_parent($cityId)) {
			$child_id = $cityId;
			$cityId = $parent['id'];
			$parent_id[] = $cityId;
		}
		return string::json_encode($parent_id);
	}
	public static function getParentNameAll($cityId){
		$datas = array();
		while ($parent = self::get_parent($cityId)) {
			$child_id = $cityId;
			$data = $parent['name'];
			$cityId = $parent['id'];
			$datas[] = $data;
		}
		return $datas;
	}
	public static function get_cities_json_parent_all($city_id, $cache=true){
		$parent_id = $ids = array();
		while ($parent = self::get_parent($city_id)) {
			$child_id = $city_id;
			$city_id = $parent['id'];
			$ids[] = array('cid' => $child_id, 'pid' => $city_id);
			$parent_id[] = $city_id;
		}
		if ($parent_id) {
			//array_pop($parent_id);
			//if($parent_id){
				//krsort($parent_id,SORT_NUMERIC);
				array_multisort(array_keys($ids), SORT_DESC, SORT_NUMERIC, $ids);
				//$parent_id=array_values($parent_id);
				$rn = '';
				foreach ($ids as $k => $id){
					$city_id = $id['pid'];
					$rn && ($rn .= ',');
					if ($k == 0) $rn .= '{cid:'.$id['cid'].',list:'.self::all_cities_json().'}';
					else $rn .= '{cid:'.$id['cid'].',list:'.self::get_cities_json($city_id).'}';
				}
				return '['.$rn.']';
			//}
		}
		return false;
	}
	public static function test(){
		ob_clean();
		time::timer_start();
		echo self::city_id('wuhouqu0');
		time::timer_end(true);
	}
	public static function insertStr($str, $pid = 0){
		$list = qscms::trimExplode("\n", $str);
		qscms::arrayUnsetEmpty($list);
		$count = 0;
		foreach ($list as $v) {
			$arr = qscms::trimExplode('>', $v);
			qscms::arrayUnsetEmpty($arr);
			$lastPid = $pid;
			foreach ($arr as $v) {
				$aid = categories::cate_firstChildId('area', "name='$v'", $lastPid);
				if ($aid) $lastPid = $aid;
				else $lastPid = categories::cate_insert(array('name' => $v), 'area', $lastPid);
				if (!$lastPid) break;
				$count++;
			}
		}
		return $count;
	}
}
?>