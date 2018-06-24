<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class data_taobaoShop extends data_taobaoBase {
	public static function getShop($num_iid){
		if ($rs = memory::get('taobao_shop_'.$num_iid)) return $rs;
		$args = array(
			'method' => 'taobao.item.get',
			'num_iid'=>$num_iid,
			'fields'=>'detail_url,num_iid,title,nick,price,location'
		);
		if(($rs = parent::get($args)) && !empty($rs['item'])){
			memory::write('taobao_shop_'.$num_iid, $rs['item']);
			return $rs['item'];
		} else {
			return false;
		}
	}
	public static function getShopPromotion($num_iid){
		$cacheKey = 'taobao_ump_promotion_'.$num_iid;
		if ($rs = memory::get($cacheKey)) return $rs;
		if ($rs = self::getShop($num_iid)) {
			$args = array(
				'method' => 'taobao.ump.promotion.get',
				'item_id' => $num_iid
			);
			if(($rs2 = parent::get($args))){
				if (isset($rs2['promotions']['promotion_in_item']['promotion_in_item'])) {
					if (!isset($rs2['promotions']['promotion_in_item']['promotion_in_item'][0])) {
						$rs2['promotions']['promotion_in_item']['promotion_in_item'] = array($rs2['promotions']['promotion_in_item']['promotion_in_item']);
					}
				}
				if (isset($rs2['promotions']['promotion_in_item']['promotion_in_item'][0]['item_promo_price'])) {
					$rs['pricePromo'] = $rs2['promotions']['promotion_in_item']['promotion_in_item'][0]['item_promo_price'];
				} else {
					$rs['pricePromo'] = $rs['price'];
				}
				memory::write($cacheKey, $rs);
				return $rs;
			} else {
				return false;
			}
		}
		return false;
	}
	public static function getStore($nick){
		$cacheKey = 'taobao_shop_get_'.$nick;
		if ($rs = memory::get($cacheKey)) return $rs;
		$args = array(
			'method' => 'taobao.shop.get',
			'nick'   => $nick,
			'fields' => 'sid,title'
		);
		if(($rs2 = parent::get($args))){
			if (!empty($rs2['shop'])) {
				$rs = $rs2['shop'];
				memory::write($cacheKey, $rs);
				return $rs;
			}
		} else {
			return false;
		}
		return false;
	}
	public static function getShopFull($num_iid){
		$rs = self::getShopPromotion($num_iid);
		if ($rs) {
			$rs2 = self::getStore($rs['nick']);
			if ($rs2) {
				$rs['storeTitle'] = $rs2['title'];
				return $rs;
			} else {
				$rs = false;
			}
		}
		return false;
	}
	public static function getNick($num_iid){
		if (parent::$useApi) {
			if (($rs = self::getShop($num_iid)) && $rs['nick']) {
				return $rs['nick'];
			} else {
				return false;
			}
		} else {
			//用网页读取
			$shopUrl = parent::getShopUrl($num_iid);
			if ($html = winsock::get_html($shopUrl)) {
				if(preg_match('/nickName:\s*\'(.+?)\'/', $html, $matches)){//匹配昵称
					return urldecode($matches[1]);
				}
			} else {
				return false;
			}
		}
	}
	public static function getPrice($num_iid){
		//if (($rs = self::getShop($num_iid)) && $rs['price']) {
		if (($rs = self::getShopPromotion($num_iid)) && $rs['pricePromo']) {
			return $rs['pricePromo'];
		} else {
			return false;
		}
	}
	public static function getTitle($num_iid){
		if (($rs = self::getShop($num_iid)) && $rs['title']) {
			return $rs['title'];
		} else {
			return false;
		}
	}
	public static function exists($num_iid){
		if (($rs = self::getShop($num_iid)) && $rs['title']) {
			return true;
		} else {
			return false;
		}
	}
	public static function getShop2($url){
		$cacheId = md5($url);
		if ($rs = memory::get('taobao_shop2_'.$cacheId)) return $rs;
		if ($html = winsock::get_html($url)) {
			//if (preg_match('/<input type="hidden" name="title" value="(.+?)" \/>/', $html, $matches)) {
			if (preg_match('/"title":"(.+?)"/', $html, $matches)) {
				$title = $matches[1];
				if (preg_match('/"apiItemViews": "(.+?)"/', $html, $matches)) {
					$url = $matches[1];
					$sign = substr($url, -32);
					$datas = array('title' => $title, 'sign' => $sign);
					memory::write('taobao_shop2_'.$cacheId, $datas);
					return $datas;
				}
			}
		}
		return false;
	}
	public static function getStoreCollect($url){
		$cacheId = md5($url);
		if ($rs = memory::get('taobao_storeCollect_'.$cacheId)) return $rs;
		if ($html = winsock::get_html($url)) {
			if(preg_match('/nickName:\s*\'(.+?)\'/', $html, $matches)){//匹配昵称
				$nickName = urldecode($matches[1]);
				if (preg_match('/<a\s*id="xshop_collection_href"[^>]*href="(.+?)"[^>]*>/', $html, $matches)) {
					$url = $matches[1];
					$sp = explode('?', $url);
					//array_shift($sp);
					$url = $sp[1];
					return array('nickname' => $nickName, 'url' => $url);
				}
			}
		}
		return false;
	}
	public static function getShopCollect($url){
		$cacheId = md5($url);
		if ($rs = memory::get('taobao_storeCollect_'.$cacheId)) return $rs;
		if ($html = winsock::get_html($url)) {
			if(preg_match('/nickName:\s*\'(.+?)\'/', $html, $matches)){//匹配昵称
				$nickName = urldecode($matches[1]);
				if (preg_match('/<div class="collection-btn">\s*<a[^>]*href="(.+?)"[^>]*>\s*<span class="collection-title"><s class="ico-item"><\/s>收藏宝贝<\/span>.*?<\/a>/s', $html, $matches)) {
					$url = $matches[1];
					$sp = explode('?', $url);
					//array_shift($sp);
					$url = $sp[1];
					return array('nickname' => $nickName, 'url' => $url);
				} elseif (preg_match('/<a\s+id="J_Favorite"  href="(.+?)".*?title="收藏该商品">/', $html, $matches)) {
					$url = $matches[1];
					$sp = explode('?', $url);
					$url = $sp[1];
					return array('nickname' => $nickName, 'url' => $url);
				}
			}
		}
		return false;
	}
}
?>