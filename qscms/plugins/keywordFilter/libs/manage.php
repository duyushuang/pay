<?php
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
//global $list, $multipage;
$top_menu=array(
	'index' => '过滤配置'
);
$top_menu_key = array_keys($top_menu);
($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
switch ($method) {
	case 'index':
		//admin::getList('article_model', '*', '', 'sort,addTime desc');
		if(form::hash()){
			$flag = form::get2('where', 'keys', 'tip');
			$flag && $flag = qscms::stripslashes($flag);
			$keys = $flag['keys'];
			$keys = preg_replace('/,|，|\||\s+|;/s', '|', $keys);
			$flag['keys'] = $keys;
			$flag['urlencode'] = strpos($keys, '%');
			$wheres = array();
			if ($where = $flag['where']) {
				foreach (qscms::trimExplode("\n", $where) as $v) {
					if ($v = trim($v)) {
						$sp0 = qscms::trimSplit('/\s+/', $v);
						$fs  = $sp0[1] ? qscms::trimExplode(',', $sp0[1]) : array();
						$wheres[] = array(
							'p'       => $sp0[0],
							'ajax'    => !empty($sp0[2]) && $sp0[2] == 'ajax' ? true : false,
							'fields'  => $fs
						);
					}
				}
			}
			$flag['wheres'] = $wheres;
			extract($flag);
			$this->writeArray('cacheDatas', $flag);
		} else {
			extract($this->getArray('cacheDatas'));
		}
	break;
}
include(template::load('admin_manage'));
?>