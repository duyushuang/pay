<?php
$iids=$var->gp_ids;
$ids= explode('|',$iids);
array_shift($ids);
$ids_count = count($ids);
if($ids_count>1){
	$first_one =db::one('byself_msg','*',"id='$ids[0]' and texttype=1");
	$list=array();
	for($i=1;$i<$ids_count;$i++){
		$this_list = db::select('byself_msg','*',"id='$ids[$i]' and texttype=1");
		$list =array_merge($list,$this_list);
	}
}else{
	$first_one=db::one('byself_msg','*',"id='$ids[0]' and texttype=1");
}

?>