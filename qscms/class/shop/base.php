<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class shop_base extends ext_base{
	public static $type = array(
		1 => '每日比逛',
		2 => '热卖品类'
	);
	public function __construct($id, $needLogin = true){// 参数二 是否必须登录 默认登录 获取shop uid等于登录用户的数据
		$var = qscms::v('_G');
		if (!$needLogin || $var->member) {
			$where = "id='$id'";
			if ($needLogin) {
				$uid = $var->member->uid;
				$where .= " AND uid='$uid'";
			}
			if ($shop = db::one('shop', '*', $where)) {
				$this->isLoad = true;
				foreach ($shop as $k => $v) $this->{'s_'.$k} = $v;
				//$this->m = new shopModule(intval($this->s_cid));
			} else $this->isLoad = false;
		} else $this->isLoad = false;
	}
	public static function getThumbAll(){//图片裁剪大小
		static $list;
		if (isset($list)) return $list;
		$list = array(
			'index' => array('width' => 550, 'height' => 230),
			'index_m' => array('width' => 710, 'height' => 300),
			'goods1' => array('width' => 800, 'height' => 800),
			'goods' => array('width' => 420, 'height' => 420),
			'ico' => array('width' => 186, 'height' => 186),
			'ico1' => array('width' => 93, 'height' => 93),
		);
		
		return $list;
	}
	public static function getThemeThumbAll(){//专题裁剪大小
		static $list;
		if (isset($list)) return $list;
		$list = array(
			'host_m' => array('width' => 730, 'height' => 210),
			'index_m' => array('width' => 710, 'height' => 350),
			'index' => array('width' => 570, 'height' => 280),
			'special' => array('width' => 750, 'height' => 400),
		);
		return $list;
	}
	public static function addTheme($datas){
		$datas = qscms::filterArray($datas, array(
			'id', 'type', 'name', 'alias', 'color', 'result'
		));
		$id = '';
		$item = array();
		$isUpdate = false;
		if (empty(self::$type[$datas['type']])) return '请选择专题类型';
		if ($datas['id'] && $item = db::one('theme', '*', "id='$datas[id]'")){
			$id = $datas['id'];
			$isUpdate = true;
		}
		if ($datas['result']){
			$arr = explode(',', $datas['result']);
			if (is_array($arr)){
				foreach ($arr as $v){
					if (!db::exists('shop', "id='$v'")) return '操作失败';
				}	
			}
		}
		if (!$datas['name'] || !$datas['alias']) return '请填写分类名称，或别名';
		if (db::exists('theme', "alias='$datas[alias]'".($id ? "AND id!='$id'" : ''))) return '别名不可重复';
		$insert = array(
			'type'  => $datas['type'],
			'name' => $datas['name'],
			'alias' => $datas['alias'],
			'color' => $datas['color']
		);
		$d = dfile::getObj('img');
		$arr = $d->uploadImage('file', date('/Y/m/d/', time::$timestamp), self::getThemeThumbAll());
		if ($arr['filename'] && $arr['basename']) {
			if (!empty($item['filename'])){
				@unlink($d->localDir.$item['filename'].'.'.$item['suffix']);
				$delImgType = array_keys(self::getThemeThumbAll());
				foreach($delImgType as $k1 => $v1){
					@unlink($d->localDir.$item['filename'].$v1.'.'.$item['suffix']);
				}
			}
			$insert['filename']  = $arr['filename'];
			$insert['suffix'] 	= $arr['suffix'];
			$insert['did']		= $d->did;
		}
		$arr = $d->uploadImage('file1', date('/Y/m/d/', time::$timestamp.'wap'));
		if ($arr['filename'] && $arr['basename']) {
			if (!empty($item['filename1'])){
				@unlink($d->localDir.$item['filename1'].'.'.$item['suffix1']);
			}
			$insert['filename1']  = $arr['filename'];
			$insert['suffix1'] 	= $arr['suffix'];
			$insert['did1']		= $d->did;
		}
		if (!$id){
			$insert['sort'] = db::dataCount('theme') + 1;
			$insert['addTime'] = time();
			$id = db::insert('theme', $insert, true);
		}elseif ($isUpdate){
			$insert['editTime'] = time();
			if (!db::update('theme', $insert, "id='$id'")) return '来自数据库的错误信息，请联系工作人员。';
			db::del_key('theme_sid', 'tid', $id);
		}
		if (!$id) return '来自数据库的错误信息，请联系工作人员。';
		/*其它表数据处理*/
		if ($datas['result']){
			$arr = explode(',', $datas['result']);
			if (is_array($arr)){
				foreach ($arr as $v){
					db::insert('theme_sid', array('tid' => $id,'sid' => $v));
				}	
			}
		}
		return true;
	}
	public static function confirmPay($sn, $payment, $trade_no){//确认订单 调用member_center类的这个方法了 这个留在这里生蛋
		//订单号，类型，接口返回的交易号
		$time = time();
		if (db::update('buy', "status=1,paymoney=money,money=0,trade_no='$trade_no',payTime='$time'", "sn='$sn' AND status=0 AND payment=$payment")){
			return true;
		}
		return false;
	}
	public static function addCate($datas){//添加分类
		$datas = qscms::filterArray($datas, array(
			'id', 'aid', 'name', 'alias', 'is_index', 'tag', 
			'imgDir'
		));
		$id = $aid = '';
		$item = array();
		$isUpdate = false;
		
		if ($datas['aid'] > 0 && !db::exists('cate', "id='$datas[aid]'")) return '没有找到上级分类';
		$aid = $datas['aid'];
		if ($datas['id'] && $item = db::one('cate', '*', "id='$datas[id]'")){
			$id = $datas['id'];
			$isUpdate = true;
		}
		if (!$datas['name']) return '请填写分类名称';
		if ($datas['alias'] && db::exists('cate', "alias='$datas[alias]'".($id ? "AND id!='$id'" : ''))) return '别名不可重复';
		/*
			分类图标处理
		*/
		$insert = array(
			'name'     => $datas['name'],
			'alias'    => $datas['alias'],
			'is_index' => $datas['is_index'],
		);
		$d = dfile::getObj('img');
		$ds = dfile::getObj('img_cache');
		$arr = $d->uploadImage('file', date('/Y/m/d/', time::$timestamp));
		if ($arr['filename'] && $arr['basename']) {
			if (!empty($item['filename'])){
				@unlink($d->localDir.$item['filename'].'.'.$item['suffix']);
			}
			$insert['filename']  = $arr['filename'];
			$insert['suffix'] 	= $arr['suffix'];
			$insert['did']		= $d->did;
		}
		//print_r($datas);exit;
		/*
		if ($datas['tag']){
			$arr = explode(',', $datas['tag']);
			if (is_array($arr)){
				db::del_key('cate_tag', 'pid', $id);
				foreach ($arr as $v){
					db::insert('cate_tag', array('pid' => $id, 'name' => $v));	
				}	
			}
		}
		*/
		if (!$id){
			$insert['sort'] = db::dataCount('cate') + 1;
			if ($id = treeDB::insert('cate', $insert, $aid)) {
				if ($datas['tag']){
					$arr = explode(',', $datas['tag']);
					if (is_array($arr)){
						foreach ($arr as $v){
							db::insert('cate_tag', array('pid' => $id, 'name' => $v));	
						}	
					}
				}
				return '添加成功';
			}else return '来自数据库的错误信息，请联系工作人员。';
		}elseif ($isUpdate){
			if (!db::update('cate', $insert, "id='$id'")) return '来自数据库的错误信息，请联系工作人员。';
			
			$ids = '';
			if (!empty($datas['imgDir']['id']) && is_array($datas['imgDir']['id'])){//删除旧数据
				
				foreach($datas['imgDir']['id'] as $k => $v){
					$ids && $ids .= ',';
					$ids .= $v;
				}
			}
			$delImg = db::select('cate_img', '*', "sid='$id'".($ids ? " AND id not in ($ids)" : ''));
			if ($delImg){
				$delImgType = array_keys(self::getThumbAll());
				foreach($delImg as $k => $v){
					db::del_key('cate_img', 'id', $v['id']);
					@unlink($d->localDir.$v['filename'].'.'.$v['suffix']);
					foreach($delImgType as $k1 => $v1){
						@unlink($d->localDir.$v['filename'].$v1.'.'.$v['suffix']);
					}
				}	
			}
		}
		/*
				分类幻灯图处理
		*/
		if (!empty($datas['imgDir']['file']) && is_array($datas['imgDir']['file'])){
			$dsDir = $ds->localDir;
			$dDir  = $d->localDir;
			foreach($datas['imgDir']['file'] as $k => $v){
				if ($v && isset($datas['imgDir']['did'][$k])){
					$sfile = $dsDir.$v;//文件源
					$dfile = $dDir.$v;//移动到什么路径
					if (file_exists($sfile) || true){
						$simgInfo = pathinfo($sfile);
						$dimgInfo = pathinfo($dfile);
						$cut = self::getThumbAll();
						if ($cut && is_array($cut)){
							foreach ($cut as $k1 => $v1){
								$fs = $fd = '';
								$fs = $simgInfo['dirname'].'/'.$simgInfo['filename'].$k1.'.'.$simgInfo['extension'];
								$fd = $dimgInfo['dirname'].'/'.$dimgInfo['filename'].$k1.'.'.$dimgInfo['extension'];
								if (file_exists($fs)) $ds->moveFiles($fs, $fd);
							}
						}
						$dbFile = pathinfo($v);
						db::insert('cate_img', array(
							'sid' 	   => $id,
							'filename' => $dbFile['dirname'].'/'.$dbFile['filename'],
							'suffix'   => $dbFile['extension'],
							'did' 	   => $d->did
						));
						$ds->moveFiles($sfile, $dfile);
					}
				}
			}	
		}
		return true;
	}
	
	public static function shopMoneys($item){
		$time = time();
		$money = 0;
		if (isset($item['robEndTime']) && isset($item['money']) && isset($item['number'])){
			if (!isset($item['sid'])){
				if (isset($item['goodsId'])) $item['sid'] = $item['goodsId'];
				else return false;
			}
			if ($item['robEndTime'] > $time && $item['robMoney'] < $item['money']){
				$money = $item['robMoney'];
			}else $money = $item['money'];
			if ($item['number'] > 1 && $list = db::select('shop_dis', 'number,money', "sid='$item[sid]'")){
				foreach($list as $v){
					if ($item['number'] >= $v['number']){
						$money -= $v['money'];
					}	
				}	
			}
			
			return round($money * $item['number'], 2);
			
		}
		return $money;
	}
	
	public static function shopMoney($sid, $number){
		$time = time();
		if ($item = db::one('shop', 'id,name,money,num,total,robMoney,robStartTime,robEndTime', "id='$sid' AND isOn=1")){
			if ($item['robEndTime'] > $time && $item['robMoney'] < $item['money']){
				$money = $item['robMoney'];
			}else $money = $item['money'];
			if ($number > 1 && $list = db::select('shop_dis', 'number,money', "sid='$sid'")){
				foreach($list as $v){
					if ($number >= $v['number']){
						$money -= $v['money'];
					}	
				}	
			}
			return round($money * $number, 2);
		}
		return false;
	}
	
	public static function shopMoney_one($sid, $number){
		$time = time();
		if ($item = db::one('shop', 'id,name,money,num,total,robMoney,robStartTime,robEndTime', "id='$sid' AND isOn=1")){
			if ($item['robEndTime'] > $time && $item['robMoney'] < $item['money']){
				$money = $item['robMoney'];
			}else $money = $item['money'];
			
			if ($number > 1 && $list = db::select('shop_dis', 'number,money', "sid='$sid'")){
				foreach($list as $v){
					if ($number >= $v['number']){
						$money -= $v['money'];
					}	
				}	
			}
			return round($money, 2);
		}
		return false;
	}
	public static function shopMoneys_one($item){
		$time = time();
		$money = 0;
		if (isset($item['robEndTime']) && isset($item['money']) && isset($item['number'])){
			if (!isset($item['sid'])){
				if (isset($item['goodsId'])) $item['sid'] = $item['goodsId'];
				else return false;
			}
			if ($item['robEndTime'] > $time && $item['robMoney'] < $item['money']){
				$money = $item['robMoney'];
			}else $money = $item['money'];
			if ($item['number'] > 1 && $list = db::select('shop_dis', 'number,money', "sid='$item[sid]'")){
				foreach($list as $v){
					if ($item['number'] >= $v['number']){
						$money -= $v['money'];
					}	
				}	
			}
			
			return round($money, 2);
			
		}
		return $money;
	}
	public static function editShop1($datas){
		$datas = qscms::filterArray($datas, array(
			'id', 'name', 'des', 'cid', 'money', 'money1', 'onNum', 'Bnumber', 'Bmoney', 'type', 'key', 'credit', 'robMoney', 'robStartTime', 'robEndTime', 
			'content', 
			'imgDir', 'file', 'goods_id', 'imgList',
			'num', 'wigNumber', 'isBest', 'isNew', 'isHost', 'isShipping', 'isOn'
		), true);	
		$insert = array();
		$id = db::one_one('shop', 'id', "goods_id='$datas[goods_id]'");
		if (!$id) return false;
		$imgDir = qscms::getImgDir('img');
		$dUrl = date('Y/m/d/', time::$timestamp);
		$name = time().rand(100, 999);
		$d = dfile::getObj('img');
		if ($datas['file']){
			$sfile = 'http://www.cdyro.com/'.$datas['file'];
			$sInfo = pathinfo($sfile);
			$dfile = $d->localDir.$dUrl.$name.'.'.$sInfo['extension'];
			file::createFolderToFile($dfile);
			if (file::copyFile($sfile, $dfile)){
				foreach (self::getThumbAll() as $k => $v) {
					$dfiles = $d->localDir.$dUrl.$name.$k.'.'.$sInfo['extension'];
					if ($v['width'] && $v['height']) {//裁剪
						image::thumb($dfile, $dfiles, $v, 'cutout');
					} else image::thumb($dfile, $dfiles, $v, 'zoom');//缩小
				}	
				$insert['filename'] = $dUrl.$name;
				$insert['suffix'] = $sInfo['extension'];
				$insert['did'] = 0;
			}
			$dDir = d(qscms::getCfgPath('/system/imgRoot'));
			$rands = mt_rand(10000000,99999999);
			if ($datas['content']){
				preg_match_all('/src\=\"(.*)\"/U', $datas['content'], $imgs);
				if (!empty($imgs[1])){
					$lailu = 'http://www.cdyro.com';
					foreach($imgs[1] as $v){
						$sfile = $lailu.$v;
						$sInfo = pathinfo($sfile);
						$dfile = 'u_'.date("YmdHis").$rands.'.'.$sInfo['extension'];
						file::createFolderToFile($dDir);
						if (file::copyFile($sfile, $dDir.'images/'.$dfile)){
							$datas['content'] = str_replace($v, '/img/images/'.$dfile,$datas['content']);
						}
					}
				}
			}
			$insert['content'] = qscms::addslashes($datas['content']);
			
			if ($insert){
				db::update('shop', $insert, "id='$id'");
			}
						
			if ($datas['imgList']){
				$d = dfile::getObj('img');
				$dUrl = date('Y/m/d/', time::$timestamp);
				db::del_key('shop_img', 'sid', $datas['id']);
				foreach($datas['imgList'] as $v){
					$name = time().rand(100, 999);
					if ($v['img_url']){
						$sfile = 'http://www.cdyro.com/'.$v['img_url'];
						$sInfo = pathinfo($sfile);
						$dfile = $d->localDir.$dUrl.$name.'.'.$sInfo['extension'];
						file::createFolderToFile($dfile);
						//echo $sfile.'-----'.$dfile.(file_exists($d->localDir.$dUrl.$name.'.'.$sInfo['extension']) ? 1 : 2).'<br />';
						if (file::copyFile($sfile, $dfile)){
							foreach (self::getThumbAll() as $k => $v) {
								$dfiles = $d->localDir.$dUrl.$name.$k.'.'.$sInfo['extension'];
								if ($v['width'] && $v['height']) {//裁剪
									image::thumb($dfile, $dfiles, $v, 'cutout');
								} else image::thumb($dfile, $dfiles, $v, 'zoom');//缩小
							}
							db::insert('shop_img', array(
								'sid' 	   => $id,
								'filename' => $dUrl.$name,
								'suffix'   => $sInfo['extension'],
								'did' 	   => 0
							));
						}
					}	
				}
			}
			echo '$goods_id:'.$datas['goods_id'].' '.'$id:'.$id.'<br />';
			return true;
		}
		return true;
	}
	public static function addShop1($datas){//添加商品
		$datas = qscms::filterArray($datas, array(
			'id', 'name', 'des', 'cid', 'money', 'money1', 'onNum', 'Bnumber', 'Bmoney', 'type', 'key', 'credit', 'robMoney', 'robStartTime', 'robEndTime', 
			'content', 
			'imgDir', 'file', 'goods_id', 'imgList',
			'num', 'wigNumber', 'isBest', 'isNew', 'isHost', 'isShipping', 'isOn'
		), true);
		$id = '';
		$imgDir = qscms::getImgDir('img');
		//echo $imgDir;exit;
		$item = array();
		$isUpdate = false;
		if ($datas['id'] && $item = db::one('shop', '*', "id='$datas[id]'")){
			$id = $datas['id'];
			$isUpdate = true;
		}
		if ($datas['money'] < 1) return '请填写出售金额';
		if ($datas['money1'] < 1) return '请填写市场金额';
		if (!is_array($datas['cid']) || (!isset($datas['cid'][0]))) return '请选择商品分类';
		$datas['cid'] = array_unique($datas['cid']);
		$time = time();
		
		$dDir = d(qscms::getCfgPath('/system/imgRoot'));
		$rands = mt_rand(10000000,99999999);
		if ($datas['content']){
			preg_match_all('/src\=\"(.*)\"/U', $datas['content'], $imgs);
			if (!empty($imgs[1])){
				$lailu = 'http://www.cdyro.com';
				foreach($imgs[1] as $v){
					$sfile = $lailu.$v;
					$sInfo = pathinfo($sfile);
					$dfile = 'u_'.date("YmdHis").$rands.'.'.$sInfo['extension'];
					file::createFolderToFile($dDir);
					if (file::copyFile($sfile, $dDir.'images/'.$dfile)){
						$datas['content'] = str_replace($v, '/img/images/'.$dfile,$datas['content']);
					}
				}
			}
		}
		$datas['content'] = qscms::addslashes($datas['content']);
		$insert = array(
			'isOn' 			=> ($datas['isOn'] ? 1 : 0),
			'name' 			=> $datas['name'],
			'des'			=> $datas['des'],
			'num'  			=> (int)$datas['num'],
			'wigNumber' 	=> (int)$datas['wigNumber'],
			'isBest'    	=> ($datas['isBest'] ? 1 : 0),
			'isNew'			=> ($datas['isNew'] ? 1 : 0),
			'isHost'		=> ($datas['isHost'] ? 1 : 0),
			'isShipping'	=> ($datas['isShipping'] ? 1: 0),
			'money'			=> $datas['money'],
			'money1'		=> $datas['money1'],
			'onNum'			=> $datas['onNum'],
			'credit'		=> (int)$datas['credit'],
			'content'		=> $datas['content'],
			'goods_id'		=> $datas['goods_id'],
		);
		$dUrl = date('Y/m/d/', time::$timestamp);
		$name = time().rand(100, 999);
		$d = dfile::getObj('img');
		if ($datas['file']){
			$sfile = 'http://www.cdyro.com/'.$datas['file'];
			$sInfo = pathinfo($sfile);
			$dfile = $d->localDir.$dUrl.$name.'.'.$sInfo['extension'];
			file::createFolderToFile($dfile);
			if (file::copyFile($sfile, $dfile)){
				foreach (self::getThumbAll() as $k => $v) {
					$dfiles = $d->localDir.$dUrl.$name.$k.'.'.$sInfo['extension'];
					if ($v['width'] && $v['height']) {//裁剪
						image::thumb($dfile, $dfiles, $v, 'cutout');
					} else image::thumb($dfile, $dfiles, $v, 'zoom');//缩小
				}	
				$insert['filename'] = $dUrl.$name;
				$insert['suffix'] = $sInfo['extension'];
				$insert['did'] = 0;
			}
		}
		$insert['sort'] = db::dataCount('shop') + 1;
		$tb = 'shop';
		db::query('LOCK TABLE `'.db::table($tb).'` WRITE');//锁定 免得造成重复sn
		do {
			$sn = db::createSn();
		} while(db::exists($tb, array('sn' => $sn), '', true));
		
		$insert['sn'] = $sn;
		$insert['addTime'] = $time;
		$id = db::insert($tb, $insert, true);
		db::query('UNLOCK TABLES');//解除锁定
		if ($id){
			if ($datas['imgList']){
				$d = dfile::getObj('img');
				$dUrl = date('Y/m/d/', time::$timestamp);
				foreach($datas['imgList'] as $v){
					$name = time().rand(100, 999);
					if ($v['img_url']){
						$sfile = 'http://www.cdyro.com/'.$v['img_url'];
						$sInfo = pathinfo($sfile);
						$dfile = $d->localDir.$dUrl.$name.'.'.$sInfo['extension'];
						file::createFolderToFile($dfile);
						//echo $sfile.'-----'.$dfile.(file_exists($d->localDir.$dUrl.$name.'.'.$sInfo['extension']) ? 1 : 2).'<br />';
						if (file::copyFile($sfile, $dfile)){
							foreach (self::getThumbAll() as $k => $v) {
								$dfiles = $d->localDir.$dUrl.$name.$k.'.'.$sInfo['extension'];
								if ($v['width'] && $v['height']) {//裁剪
									image::thumb($dfile, $dfiles, $v, 'cutout');
								} else image::thumb($dfile, $dfiles, $v, 'zoom');//缩小
							}
							db::insert('shop_img', array(
								'sid' 	   => $id,
								'filename' => $dUrl.$name,
								'suffix'   => $sInfo['extension'],
								'did' 	   => 0
							));
						}
					}	
				}
			}
			foreach($datas['cid'] as $v){
				$cid = db::one_one('cate', 'id', "cat_id='$v'");
				db::insert('shop_cid', array(
					'cid' => $cid,
					'sid' => $id
				));
			}
			return true;
		}else return false;
	}
	public static function addShop($datas){//添加商品
		$datas = qscms::filterArray($datas, array(
			'id', 'name', 'des', 'cid', 'money', 'money1', 'onNum', 'Bnumber', 'Bmoney', 'type', 'key', 'credit', 'robMoney', 'robStartTime', 'robEndTime', 
			'content', 
			'imgDir',
			'num', 'wigNumber', 'isBest', 'isNew', 'isHost', 'isShipping', 'isOn'
		), true);
		$id = '';
		$item = array();
		$isUpdate = false;
		if ($datas['id'] && $item = db::one('shop', '*', "id='$datas[id]'")){
			$id = $datas['id'];
			$isUpdate = true;
		}
		if ($datas['money'] < 1) return '请填写出售金额';
		if ($datas['money1'] < 1) return '请填写市场金额';
		if (!is_array($datas['cid']) || (!isset($datas['cid'][0]))) return '请选择商品分类';
		$datas['cid'] = array_unique($datas['cid']);
		foreach($datas['cid'] as $v){
			if (!db::exists('cate', "id='$v'")) return '没有该商品分类，可能该分类已被删除';
		}
		$time = time();
		$d = dfile::getObj('img');
		$ds = dfile::getObj('img_cache');
		$delImgType = array_keys(self::getThumbAll());
		$insert = array(
			'isOn' 			=> ($datas['isOn'] ? 1 : 0),
			'name' 			=> $datas['name'],
			'des'			=> $datas['des'],
			'num'  			=> (int)$datas['num'],
			'wigNumber' 	=> (int)$datas['wigNumber'],
			'isBest'    	=> ($datas['isBest'] ? 1 : 0),
			'isNew'			=> ($datas['isNew'] ? 1 : 0),
			'isHost'		=> ($datas['isHost'] ? 1 : 0),
			'isShipping'	=> ($datas['isShipping'] ? 1: 0),
			'money'			=> $datas['money'],
			'money1'		=> $datas['money1'],
			'onNum'			=> $datas['onNum'],
			'credit'		=> (int)$datas['credit'],
			'content'		=> $datas['content']
		);
		//print_r($datas);exit;
		$arr = $d->uploadImage('file', date('/Y/m/d/', time::$timestamp), self::getThumbAll());
		if ($arr['filename'] && $arr['basename']) {
			if (!empty($item['filename'])){
				@unlink($d->localDir.$item['filename'].'.'.$item['suffix']);
				foreach($delImgType as $k1 => $v1){
					@unlink($d->localDir.$item['filename'].$v1.'.'.$item['suffix']);
				}
			}
			$insert['filename'] = $arr['filename'];
			$insert['suffix'] 	= $arr['suffix'];
			$insert['did']		= $d->did;
		}
		/*
			限时抢购处理
		*/
		if ($datas['robMoney'] && $datas['robMoney'] > 0 && $datas['robStartTime'] && $datas['robEndTime']){
			$robStartTime = strtotime($datas['robStartTime']);
			$robEndTime   = strtotime($datas['robEndTime']);
			if ($robEndTime > $time){
				$insert['robMoney'] 		= $datas['robMoney'];
				$insert['robStartTime'] 	= $robStartTime;
				$insert['robEndTime']		= $robEndTime;
			}
		}
		if (!$id){
			/*插入数据才执行这个*/
			$insert['sort'] = db::dataCount('shop') + 1;
			$tb = 'shop';
			db::query('LOCK TABLE `'.db::table($tb).'` WRITE');//锁定 免得造成重复sn
			do {
				$sn = db::createSn();
			} while(db::exists($tb, array('sn' => $sn), '', true));
			
			$insert['sn'] = $sn;
			
			$insert['addTime'] = $time;
			
			$id = db::insert($tb, $insert, true);
			db::query('UNLOCK TABLES');//解除锁定
		
		}elseif ($isUpdate){
			if (!db::update('shop', $insert, "id='$id'")) return '来自数据库的错误信息，请联系工作人员。';
			//修改的话就把之前的数据干掉 重新插
			db::del_key('shop_dis', 'sid', $id);//成功干掉商品优惠
			db::del_key('shop_cid', 'sid', $id);//成功干掉旧分类
			$delOption = db::select('shop_option', 'id', "sid='$id'");
			if ($delOption){//有这个数据才删
				foreach($delOption as $v){
					db::del_key('shop_option_val', 'oid', $v['id']);
				}
				db::del_key('shop_option', 'sid', $id);
			}
			$ids = '';
			if (!empty($datas['imgDir']['id']) && is_array($datas['imgDir']['id'])){
				foreach($datas['imgDir']['id'] as $k => $v){
					$ids && $ids .= ',';
					$ids .= $v;
				}
			}
			$delImg = db::select('shop_img', '*', "sid='$id'".($ids ? " AND id not in ($ids)" : ''));
			if ($delImg){
				foreach($delImg as $k => $v){
					db::del_key('shop_img', 'id', $v['id']);
					@unlink($d->localDir.$v['filename'].'.'.$v['suffix']);
					foreach($delImgType as $k1 => $v1){
						@unlink($d->localDir.$v['filename'].$v1.'.'.$v['suffix']);
					}
				}	
			}
		}
		if (!$id) return '来自数据库的错误信息，请联系工作人员。';
		/*其他表数据处理*/
		/*
			商品分类处理
		*/
		foreach($datas['cid'] as $v){
			db::insert('shop_cid', array(
				'cid' => $v,
				'sid' => $id
			));
		}
		/*
			商品选项处理
		*/
		if (is_array($datas['type']) && is_array($datas['key'])){
			foreach($datas['type'] as $k => $v){
				if ($v && !empty($datas['key'][$k]) && is_array($datas['key'][$k])){
					$oid = db::insert('shop_option', array(
						'sid'   => $id,
						'title' => $v
					), true);
					if($oid){
						foreach($datas['key'][$k] as $k1 => $v1){
							if ($v1){
								db::insert('shop_option_val', array(
									'oid' => $oid,
									'val' => $v1
								));
							}
						}
					}
				}
			}
		}
		
		/*
			商品幻灯图处理
		*/
		if (!empty($datas['imgDir']['file']) && is_array($datas['imgDir']['file'])){
			$dsDir = $ds->localDir;
			$dDir  = $d->localDir;
			foreach($datas['imgDir']['file'] as $k => $v){
				if ($v && isset($datas['imgDir']['did'][$k])){
					$sfile = $dsDir.$v;//文件源
					$dfile = $dDir.$v;//移动到什么路径
					if (file_exists($sfile) || true){
						$simgInfo = pathinfo($sfile);
						$dimgInfo = pathinfo($dfile);
						$cut = self::getThumbAll();
						if ($cut && is_array($cut)){
							foreach ($cut as $k1 => $v1){
								$fs = $fd = '';
								$fs = $simgInfo['dirname'].'/'.$simgInfo['filename'].$k1.'.'.$simgInfo['extension'];
								$fd = $dimgInfo['dirname'].'/'.$dimgInfo['filename'].$k1.'.'.$dimgInfo['extension'];
								if (file_exists($fs)) $ds->moveFiles($fs, $fd);
							}
						}
						$dbFile = pathinfo($v);
						db::insert('shop_img', array(
							'sid' 	   => $id,
							'filename' => $dbFile['dirname'].'/'.$dbFile['filename'],
							'suffix'   => $dbFile['extension'],
							'did' 	   => $d->did
						));
						$ds->moveFiles($sfile, $dfile);
					}
				}
			}	
		}
		
		
		/*
			商品购买多少个优惠多少钱
		*/
		if (is_array($datas['Bnumber']) && is_array($datas['Bmoney'])){
			foreach($datas['Bnumber'] as $k => $v){
				if ($v > 0 && !empty($datas['Bmoney'][$k]) && $datas['Bmoney'][$k] > 0){
					db::insert('shop_dis', array(
						'sid' 	 => $id,
						'number' => $v,
						'money'  => $datas['Bmoney'][$k]
					));
				}	
			}
		}
		return true;
		
		
	}
}
