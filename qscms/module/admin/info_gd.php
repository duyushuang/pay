<?php


(!defined('IN_ADMIN') || IN_ADMIN!==true) && die('error');
$top_menu=array(
	'list' => '工单类型',
	'list1' => '列表列表',
	'edit' => array('name' => '修改工单类型名称', 'hide' => true),
	'add' => '添加工单类型',
	'item' => array('name' => '工单详情', 'hide' => true),
	'chenge' => array('name' => '处理工单', 'hide' => true)
);
$top_menu_key = array_keys($top_menu);
($method && in_array($method,$top_menu_key)) || $method=$top_menu_key[0];

$list = array();
$multipage = '';
switch ($method) {
	case 'list':
		if ($total = db::dataCount('gdtype')) {
			$list = db::select('gdtype', '*', '', 'id DESC', $pagesize, $page);
		}
		$total && $multipage = multipage::parse($total, $pagesize, $page, $baseUrl.'&method='.$method.($urlVar ? '&'.$urlVar : '').'&type=so&page={page}', $pagestyle);
	break;
	case 'add':
		if (form::hash()){
			$datas = form::get3('name');
			if (!$datas['name']) admin::showMessage('请填写添加工单类型名称', $baseUrl.'&method='.$method);
			db::insert('gdtype', array('name' => $datas['name']));
			admin::showMessage('添加工单类型成功', $baseUrl.'&method=list');
		}
	break;
	case 'edit':
		if ($id = $var->gp_id){
			$item = db::one('gdtype', '*', "id='$id'");
			if (!$item) admin::showMessage('没有该工单类型', $baseUrl.'&method=list');
			extract($item);
			if (form::hash()){
				$datas = from::hash('name');
				if (!$datas['name']) admin::showMessage('请填写编辑工单类型名称', $baseUrl.'&method='.$method);
				db::update('gdtype', array('name' => $datas['name']), "id='$id'");
				admin::showMessage('编辑工单类型成功', $baseUrl.'&method=list');
			}
		}
	break;
	case 'list1':
		$keys = array('type', 'status');
		$wh = "";
		$wh1 = "";
		$urlVar = '';
		$vars = array();

		foreach ($keys as $v) {

			$val = $var->{'gp_'.$v};
			
			$vars[$v] = $val;

			!is_null($val) && $val !== '' && (($urlVar && $urlVar .= '&') || !$urlVar) && $urlVar .= $v.'='.urlencode($val);

		}
		$id = $var->gp_id;
		if ($id && $var->gp_change){
			db::update('gdlist', "type=2", "id='$id'");	
			
			admin::showMessage('确认成功', $baseUrl.'&method='.$method.($urlVar ? '&'.$urlVar : '').'&page='.$page);
		}
		
		extract($vars);
		if (isset($status) && in_array($status,array(0,1,2))){
			$wh && $wh .= ' AND ';
			$wh .= "type='$status'";
			$wh1 && $wh1 .= ' AND ';
			$wh1 .= "t0.type='$status'";
		}
		if ($type && db::exists('gdtype', "id='$type'")){
			$wh && $wh .= ' AND ';
			$wh .= "gid='$type'";
			$wh1 && $wh1 .= ' AND ';
			$wh1 .= "t0.gid='$type'";
		}
		if ($total = db::dataCount('gdlist', $wh)){
			$list = db::select('gdlist|gdtype:id=gid', '*|name', $wh1, 'addTime DESC', $pagesize, $page);
			$multipage = multipage::parse($total, $pagesize, $page, $baseUrl.'&method='.$method.($urlVar ? '&'.$urlVar : '')."&page={page}", $pageStyle);
		}
	break;
	case 'item':
		if ($id = $var->gp_id){
			if (form::hash()){
				$arr = array('status' => false, 'msg' => '');
				$datas = form::get2('content');
				
				if ($datas['content']){
						db::update('gblist', 'type=1', "id='$id'");
						db::insert('gdback', array(
							'uid' => '',
							'pid' => $id,
							'content' => $datas['content'],
							'addTime' => time()
						));
					$arr['status'] = true;
					$arr['url'] = $baseUrl.'&method='.$method.'?id='.$id;
				}else $arr['msg'] = '请填写反馈内容';	
				exit(string::json_encode($arr));
			}
			if (!empty($_FILES['file'])){
				$d = dfile::getObj('ticket');
				$rs = $d->uploadImage('file');
				if (isset($rs['filename'])) {
					$imgs = qscms::getImgUrl('ticket').$rs['filename'].'.'.$rs['suffix'];
					exit('{
						"code": 0
						,"msg": ""
						,"title" : ""
						,"data": {
						  "src": "'.$imgs.'","title": ""
						}
					} ');
				}	
			}
			
			
			$item = db::one('gdlist', '*', "id='$id'");
			if (!$item) admin::showMessage('没有找到该工单');
			$item['name'] = db::one_one('gdtype', 'name', "id='$item[gid]'");
			$m = db::one('member', 'id,name,mobile', "id='$item[uid]'");
			$list = db::select('gdback', '*', "pid='$item[id]'");
		}
	break;
	case 'chenge':
		$id  = $var->getInt('gp_id');
		if ($id > 0){
			db::update('gdlist', 'type=2', "id='$id'");
			admin::showMessage('处理成功', $baseUrl.'&method=item&id='.$id);
		}
	break;
}
?>