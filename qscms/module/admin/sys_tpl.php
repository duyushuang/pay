<?php
(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
$top_menu=array(
	'list1' => '列表',
	'list2' => '快速',
	'add'   => '添加标记',
	'edit'  => array('name' => '编辑/查看', 'isHide' => true)
);
$top_menu_key = array_keys($top_menu);
($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];
function __updateCache(){
	$rs = array();
	foreach(db::select('tpl_marker', '*', '', 'sort') as $v) {
		$rs[] = array(
			$v['marker'], $v['code'], $v['output'] == '1' ? true : false, $v['parameter'] ? @eval('return '.$v['parameter'].';'): array()
		);
	}
	$parameter = '$replace = '.string::formatArray($rs).';';
	$file      = qd('./class/parse_php.php');
	$phpcode   = file::read($file);
	$parameter = addcslashes($parameter, '\\$');
	$phpcode   = preg_replace('/(\/\*marker start\*\/).*?(\/\*marker end\*\/)/s','$1'.$parameter.'$2',$phpcode);
	file::write($file, $phpcode);
}
switch($method) {
	case 'list1':
		if (form::is_form_hash()) {
			extract(form::get3('del', 'sort', 'ids'));
			if($del || $sort){
				if($del){
					db::del_ids('tpl_marker', $del);
					__updateCache();
					admin::show_message('删除成功', $baseUrl.'&method=list1');
				} elseif($sort && $ids) {
					if($count = form::array_equal($ids, $sort)){
						for($i=0; $i<$count; $i++){
							$id  = $ids[$i];
							$sid = $sort[$i];
							db::update('tpl_marker', array('sort'=>$sid),"id='$id'");
						}
						admin::show_message('更新成功', $baseUrl.'&method=list1');
					}
				}
			}
		}
		
		if ($total = db::data_count('tpl_marker')) {
			$list = db::select('tpl_marker', 'id,sort,marker,remark', '', 'sort', $pagesize, $page);
			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.'&method='.$method.'&page={page}', $pagestyle);
		}
	break;
	case 'add':
		$marker = $code = $output = $parameter = $remark = '';
		if (form::is_form_hash()) {
			$datas = form::get2('marker', 'code', 'output', 'parameter', 'remark');
			if (db::insert('tpl_marker', $datas)) {
				__updateCache();
				admin::show_message('添加成功！', $baseUrl.'&method=list1');
			} else admin::show_message('添加失败！');
		}
		$output = 0;
	break;
	case 'edit':
		$update = true;
		$id = $var->gp_id;
		if ($item = db::one('tpl_marker', 'marker,code,output,parameter,remark', "id='$id'")) {
			if (form::is_form_hash()) {
				$datas = form::get2('marker', 'code', 'output', 'parameter', 'remark');
				if (db::update('tpl_marker', $datas, "id='$id'")) {
					__updateCache();
					admin::show_message('更新成功！', $baseUrl.'&method=list1&page='.$page);
				} else admin::show_message('更新失败！');
			}
			extract($item);
		} else admin::show_message('很抱歉，该标记不存在！');
	break;
	case 'list2':
		if(form::is_form_hash()) {
			$_POST   = qscms::stripslashes($_POST);
			$m_      = $_POST['m'];
			$d_      = $_POST['d'];
			$o_      = array_values($_POST['o']);
			$a_      = $_POST['a'];
			$b_      = $_POST['b'];
			$count   = count($m_);
			$configs = $arr = $rs = array();
			$replace = '';
			db::clear('tpl_marker', true);
			for($i = 0; $i < $count; $i ++){
				$m = $m_[$i];
				$d = $d_[$i];
				$o = $o_[$i];
				$a = $a_[$i];
				$b = $b_[$i];
				if($m !== '' && $d !== '') {
					$arr['m']  = $m;
					$arr['d']  = $d;
					$arr['o']  = $o == 1 ? true : false;
					$arr['a']  = $a;
					$arr['b']  = $b;
					$rs[] = array(
						$arr['m'], $arr['d'], $arr['o'], $arr['a'] ? @eval('return '.$arr['a'].';'): array()
					);
					//$replace  .= '$replace[]=array(\''.addcslashes($arr['m'],'\'\\').'\',\''.addcslashes($arr['d'],'\'\\').'\','.($arr['o']?'true':'false').','.($arr['a']?$arr['a']:'array()').');';
					$configs[] = $arr;
				}
			}//print_r($rs);exit;
			$replace = $rs ? '$replace = '.string::formatArray($rs).';' : '';
			foreach ($configs as $v) {
				$v = qscms::addslashes($v);
				db::insert('tpl_marker', array(
					'marker'    => $v['m'],
					'code'      => $v['d'],
					'output'    => $v['o'] ? '1' : '0',
					'parameter' => $v['a'],
					'remark'    => $v['b']
				));
			}
			//echo $replace;exit;
			//exit;
			/*if($replace){
				$file    = d('./class/parse_php.php');
				$phpcode = file::read($file);
				$replace = addcslashes($replace, '\\$');
				$phpcode = preg_replace('/(\/\*marker start\*\/).*?(\/\*marker end\*\/)/s','$1'.$replace.'$2',$phpcode);
				file::write($file, $phpcode);
			}*/
			__updateCache();
			//cache::write_data('sys_tpl_marker','<?php $cache_sys_tpl_marker='.string::format_array($configs).';');
			qscms::refresh();
		}
		$markerList = db::select('tpl_marker', '*', '', 'sort');
	break;
}
?>