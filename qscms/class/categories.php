<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class categories{
	public static function cate_first($tbname, $pid = 0, $fields = '*'){
		if (!$pid) {
			db::query("SET @l=1");
		} else {
			if (!db::query("SELECT @l:=l+1 from ".db::table($tbname)." WHERE id='$pid'")) return;
		}
		$fields=preg_replace('/([^`,]+)/i','t.\\1',$fields);
		return db::fetchAll("SELECT @l:=r+1,$fields,floor((r-l-1)/2) sub_num FROM (SELECT * FROM ".db::table($tbname)." ORDER BY l) t WHERE l=@l");
		/*if(!$pid){
			$line=$mysql->fetch_first("select max(r) r from {$p}$tbname");
			$line['l']=1;
		} else {
			$line=$mysql->fetch_first("select l,r from {$p}$tbname where id='$pid'");
		}
		if($line){
			extract($line);
			if($r-$l>1){
				$l++;$r--;
				$ids='';
				for($i=$l;$i<=$r;$i++){
					$ids&&($ids.=',');
					$ids.=$i;
					$tmp_r=$mysql->result_first("select r from {$p}$tbname where l='$i'");
					$i=$tmp_r;
					$j++;
					if($j>200)break;
				}
				echo $j,'|';
				echo $ids;exit;
				return $mysql->fetch_all("select $fields,floor((r-l-1)/2) sub_num from {$p}$tbname where l in ($ids)");
			}
		}*/
	}
	public static function cate_firstChildId($tb, $where, $pid = 0){
		if (!$pid) {
			db::query("SET @l=1");
		} else {
			if (!db::query("SELECT @l:=l+1 from ".db::table($tb)." WHERE id='$pid'")) return;
		}
		return db::resultFirst("SELECT id FROM (SELECT @l:=r+1,t.* FROM (SELECT * FROM ".db::table($tb)." ORDER BY l) t WHERE l=@l) t WHERE $where");
	}
	public static function cate_nextChild($tbname, $pid=0, $fields='*'){
		if ($item = db::one($tbname, "l,r", "id=$pid")) {
			extract($item);
			if ($r - $l == 1) return array($pid);
			else {
				return db::get_ids($tbname, "l>$l AND r<$r AND r-l=1");
			}
		}
	}
	public static function cate_firstChild2($tbname,$pid=0,$fields='*'){
		global $mysql,$pre;
		if(!$pid){
			$mysql->nexe("SET @l=1");
		} else {
			if (!db::query("select @l:=l+1 from ".db::table($tbname)." where id=$pid"));
		}
		$fields = preg_replace('/([^`,]+)/i','t.\\1',$fields);
		$rn = array();
		$query = db::query("SELECT @l:=r+1,$fields,FLOOR((r - l - 1) / 2) count FROM (SELECT * FROM ".db::table($tbname)." ORDER BY l) t WHERE l=@l");
		while($line = db::fetch($query)){
			if($line['count'] == 0){
				$rn[] = $line;
			}
		}
		return $rn;
	}
	public static function cate_firstChild_Count($tbname,$pid=0){
		$tbname = db::table($tbname);
		if(!$pid){
			db::query("SET @l=1");
		} else {
			if(!db::query("select @l:=l+1 from $tbname where id=$pid")) return;
		}
		return db::resultFirst("select count(*) c from (select @l:=r+1 from (select l,r from $tbname order by l) t where l=@l) t2");
	}
	public static function cate_Child_Count($tbname,$pid){
		$tb = $tbname;
		$tbname = db::table($tbname);
		if ($line = db::one($tb, 'l,r', "id='$pid'")) {
			@extract($line);
			return db::resultFirst("select count(*) c from $tbname where l>$l and r<$r");
		}
		return false;
	}
	public static function cate_parent($tbname,$id,$fields='*'){
		$tb = $tbname;
		$tbname = db::table($tbname);
		if($line = db::one($tb, 'l,r', "id='$id'")){
			@extract($line);
			return db::one($tb, $fields, "l<$l AND r>$r", 'l DESC');
		}
	}
	public static function cate_parent_all($tbname,$id,$endid=0,$fields='*',$order='desc',$len=0){
		$tb = $tbname;
		$tbname = db::table($tbname);
		if($line = db::one($tb, 'l,r', "id='$id'")){
			@extract($line);
			$endl = $endlr = 0;
			if($endid){
				if($line = db::one($tb, 'l,r', "id='$endid'")){
					$endl = $line['l'];
					$endr = $line['r'];
				}
			}
			//$fields=preg_replace('/([^`,]+)/i','t.\\1',$fields);
			return db::fetchAll("select $fields from $tbname where (l<=$l and r>=$r)".($endl&&$endr?" and (l>$endl and r<$endr)":"")." order by l $order".($len?" limit $len":""));
		}
	}
	public static function cate_insert($list,$tbname,$pid=0){
		$tb = $tbname;
		$tbname = db::table($tbname);
		if ($list) {
			if ($pid) {
				if($line = db::one($tb, 'l,r', "id='$pid'")){
					@extract($line);
					if (db::update($tb, 'l=l+2', "l>$r")) {
						if (db::update($tb, 'r=r+2', "r>=$r")) {
							$list['l'] = $r;
							$list['r'] = $r + 1;
							if ($id = db::insert($tb, $list, true)) {
								return $id;
							} else {
								db::update($tb, 'l=l-2', "l>$r+2");
								db::update($tb, 'r=r-2', "r>=$r+2");
								return false;
							}
						} else {
							db::update($tb, 'l=l-2', "l>$r+2");
							return false;
						}
					}
				}
			} else {
				$r = intval(db::one_one($tb, 'MAX(r)'));
				$list['l'] = $r + 1;
				$list['r'] = $r + 2;
				if ($id = db::insert($tb, $list, true)) return $id;
				return false;
			}
		}
	}
	public static function cate_delete($tbname,$ids){
		if($ids){
			if(!is_array($ids)) $ids = array($ids);
			foreach($ids as $v){
				if($line = db::one($tbname, 'l,r', "id='$v'")){
					@extract($line);
					if($l && $r){
						db::delete($tbname, "l>=$l AND r<=$r");
						$len = $r - $l + 1;
						db::update($tbname, "l=l-$len", "l>$r");
						db::update($tbname, "r=r-$len", "r>$r");
					}
				}
			}
		}
	}
	public static function cate_this($tbname,$id=0,$fields='*'){
		return db::one($tbname, $fields.',FLOOR((r - l - 1) / 2) sub_num', "id='$id'");
	}
	public static function cate_child_all($tbname,$id,$fields='*'){
		if($line = db::one($tbname, 'l,r', "id='$id'")){
			@extract($line);
			return db::select($tbname, $fields, "l>$l AND r<$r");
		}
	}
	public static function cate_all_ids($tbname){
		return db::get_ids($tbname);
	}
	public static function cate_child_ids($tbname,$id){
		$items = self::cate_child_all($tbname,$id,'id');
		if(!$items)return;
		$ids = array();
		foreach($items as $v){
			$ids[]=$v['id'];
		}
		return $ids;
	}
	public static function cate_thisChild($tbname,$id,$fields='*'){
		$parent = self::cate_parent($tbname,$id,'id');
		if($parent){
			return self::cate_firstChild($tbname,$parent['id'],$fields);
		}
	}
}
?>