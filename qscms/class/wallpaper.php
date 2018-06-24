<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class wallpaper{
	public static $thumbs = array('_ico' => array('width' => 180, 'height' => 180));
	public static function uploadToForm($uid = 0, $controlName = 'pic'){
		if (form::hash()) {
			if ($uid && !member_base::idExists($uid)) return '用户不存在';
			$total = intval(db::one_one('member_count', 'wallpaper', "uid='$uid'"));
			if ($total >= cfg::getInt('member', 'wallpaper')) return '用户最多上传'.cfg::getInt('member', 'wallpaper').'张壁纸';
			$d = dfile::getObj('wallpaper');
			$arr = $d->uploadImage($controlName, '', self::$thumbs);
			if ($arr) {
				$datas = array('uid' => $uid, 'filename' => $arr['filename'], 'suffix' => $arr['suffix'], 'did' => $d->did, 'time' => time());
				if (db::insert('wallpaper', $datas)) {
					return true;
				}
			}
			return '上传失败，请重试';
		}
		return false;
	}
	public static function total($uid = 0){
		if ($uid) return intval(db::one('member_count', 'wallpaper', "uid='$uid'"));
		return db::dataCount('wallpaper', "uid='0'");
	}
	public static function getList($pagesize = 0, $page = 0, $uid = 0){
		return db::select('wallpaper', '*', "uid='$uid'", 'time DESC', $pagesize, $page);
	}
	public static function del($ids, $uid = 0){
		is_array($ids) || $ids = array($ids);
		$list = db::select('wallpaper', 'filename,suffix,did', "uid='$uid' AND id ".sql::getInStr($ids));
		if ($list) {
			foreach ($list as $v) {
				$d = dfile::getObj('wallpaper', $v['did']);
				$d->del('/'.$v['filename'].'.'.$v['suffix']);
				foreach (self::$thumbs as $k1 => $v1) {
					$d->del('/'.$v['filename'].$k1.'.'.$v['suffix']);
				}
			}
			return db::delete('wallpaper', "uid='$uid' AND id ".sql::getInStr($ids));
		}
		return 0;
	}
	public static function exists($id, $uid = 0){
		return db::exists('wallpaper', "id='$id' AND uid='$uid'");
	}
	public static function getUrl($id){
		if ($line = db::one('wallpaper', 'filename,suffix,did', "id='$id'")) {
			if ($line['did'] > 0) return memory::get('disperse_url_'.$line['did']).'/'.$line['filename'].'.'.$line['suffix'];
			else return qscms::getImgUrl('wallpaper').$line['filename'].'.'.$line['suffix'];
		}
		return '';
	}
}
?>