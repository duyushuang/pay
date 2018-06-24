<?php
/*
print_r(string::json_decode('{
     "button":[
     {	
          "type":"click",
          "name":"今日歌曲",
          "key":"V1001_TODAY_MUSIC"
      },
      {
           "name":"菜单",
           "sub_button":[
           {	
               "type":"view",
               "name":"搜索",
               "url":"http://www.soso.com/"
            },
            {
               "type":"view",
               "name":"视频",
               "url":"http://v.qq.com/"
            },
            {
               "type":"click",
               "name":"赞一下我们",
               "key":"V1001_GOOD"
            }]
       }]
 }'));exit;
 */
if(form::hash()){
	$datas = form::get3('button');	
}

$weObj =  new weixin();
$access_token=$weObj->get_token();
//echo $access_token;exit;
/*
$nav1=$var->gp_name;
$nav2=$var->gp_sub;
$nav_keys=!empty($nav2)?array_keys($nav2):array();
$arr=array();
$n_1=array();$i=0;
if(empty($nav1)) exit('菜单不能为空');
*/
if ($datas['button'] && is_array($datas['button'])){
	foreach($datas['button'] as $k => $v){
		if ($v['type'] == 'view') $datas['button'][$k]['url'] = $v['key'];
		if (empty($v['name']) && empty($v['key'])){//没有内容的直接删除
			unset($datas['button'][$k]);
		}else{
			if ($v['name']){//有名称没有 值的 
				if ($v['sub_button'] && is_array($v['sub_button'])){//判断是否有二级菜单、
					$erji = false;
					foreach($v['sub_button'] as $k1 => $v1){
						if ($v1['type'] == 'view') $datas['button'][$k]['sub_button'][$k1]['url'] = $v1['key'];
						if (empty($v1['name']) || empty($v1['key'])){//没有内容的二级菜单直接删除
							//echo $k1.'|||'.$datas['button'][$k]['name'].'||||';
							//print_r($datas['button'][$k]['sub_button'][$k1]);
							//print_r( $datas['button'][$k]['sub_button'][$k1]);exit;
							unset($datas['button'][$k]['sub_button'][$k1]);
						}else {
							$erji = true;
							if ($v1['type'] == 'view') unset($datas['button'][$k]['sub_button'][$k1]['key']);
						}
					}
					if (!$erji){//没有下级的直接删除下级菜单数组
						unset($datas['button'][$k]['sub_button']);
					}else{
						unset($datas['button'][$k]['type']);
						unset($datas['button'][$k]['key']);
					}
					
				}else unset($datas['button'][$k]);//没有的话直接删除
			}
		}
		if ($v['type'] == 'view') unset($datas['button'][$k]['key']);
	}
}
//print_r($datas);
/*
foreach($nav1 as $k=>$v){
	$nav_1=explode(',',$v);
	$n_1[$k]['name']=$nav_1[0];
	//array_pop($nav_1);
	//if($ke=array_search('',$nav_1)){unlink($nav_1[$ke]);}
	//print_r($nav_1);exit;
	if(in_array($k,$nav_keys)){
		
			//print_r($nav2[0]);exit;
			foreach($nav2[$k] as $kk=>$vv){
				//$n_1[$n_k]['sub_button'][]=explode('|',$vv);
				$n_2=array();
				$n_2=explode('|',$vv);
				preg_match('/(http|www)/i',$n_2[1])?$arr['type']='view':$arr['type']='click';
					$arr['name']=$n_2[0];
				preg_match('/(http:|www\.)/i',$n_2[1])?$arr['url']=$n_2[1]:$arr['key']=$n_2[1];
				$n_1[$k]['sub_button'][]=$arr;
				}
		
		}else{
			if(preg_match('/(http|www)/i',$nav_1[1])){
				$n_1[$k]['url']=$nav_1[1];
				$n_1[$k]['type']='view';
				}else{
					$n_1[$k]['type']='click';
					$n_1[$k]['key']=$nav_1[1];
					}
			}
		
		//$n_1[$k]['url']=$nav2[$n_k];
		
	}
*/		
$back_arr=array();
$back_arr['button']=$datas['button'];//$n_1;//print_r($back_arr);exit;
function encode_json($str){  
    $code = string::json_encode($str);  
    $code = @preg_replace("#\\\u([0-9a-f]+)#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $code);  
	//$code =preg_replace('#\\+#ie','1',$code);
	return $code;
} 
$fanhui=json_encode($back_arr, JSON_UNESCAPED_UNICODE);
$fanhui || $fanhui = encode_json($back_arr);
//echo gethostbyname('api.weixin.qq.com');exit;
$rs=winsock::open("https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$access_token",$fanhui);

$rs=string::json_decode($rs);

echo $rs['errcode'].":".$weObj->getErrText($rs['errcode']);
exit;
?>