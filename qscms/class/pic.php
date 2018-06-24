<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class pic{
	public $id, $datas, $isLoad;
	public function re(){
		$this->id = 0;
		$this->datas = array();
		$this->isLoad = false;
	}
	public function __construct($id){
		$this->re();
		$this->load($id);
	}
	public function getTags(){
		$list = array();
		if ($this->isLoad) {
			if ($this->datas['tag'] > 0) {
				foreach (db::select('tag_pic|tag:id=tid', '|name', "t0.pid='$this->id'") as $v) {
					$list[] = $v['name'];
				}
			}
		}
		return $list;
	}
	public function getTagsInfo($pagesize, $page){
		$list = array();
		if ($this->isLoad) {
			if ($this->datas['tag'] > 0) {
				$list = db::select('tag_pic|tag:id=tid', '|*', "t0.pid='$this->id'");
			}
		}
		return $list;
	}
	public function getTagsStr(){
		return implode(',', $this->getTags());
	}
	public function load($id){
		if (!$id) return false;
		if ($datas = db::one('pic', '*', "id='$id'")) {
			$this->id     = $id;
			$this->datas  = $datas;
			$this->isLoad = true;
		}
	}
	public function click(){
		if ($this->isLoad) {
			db::update('pic', "clicks=clicks+'1'", "id='$this->id'");
			$this->datas['clicks']++;
		}
	}
	public static function addToPost(){
		$datas = form::get3('title', 'tags');
		$title   = !empty($datas['title']) ? $datas['title']:'';
		$tags = !empty($datas['tags']) ? $datas['tags'] : '';
		$upName = 'file';
		$saveDir0 = './'.cfg::get('dir', 'pic');
		$saveDir1 = date('Y/m/d/', time::$timestamp);
		$saveDir  = d($saveDir0.$saveDir1);
		$rs = upload::uploadImage($upName, $saveDir, true);
		if ($rs) {
			$file = $saveDir.$rs['basename'];
			$md5  = md5_file($file);
			if (!db::exists('pic', array('md5' => $md5))) {
				$title || $title = $rs['name'];
				$title && $title = mb_substr($title, 0, 32);
				image::watermark($saveDir.$rs['basename']);
				$datas = array(
					'title'    => $title,
					'url'      => $saveDir1.$rs['basename'],
					'time'     => time::$timestamp,
					'md5'      => $md5
				);
				if ($pid = db::insert('pic', $datas, true)) {
					$tagCount = tag::setPic($tags, $pid);
					db::update('pic', array('tag' => $tagCount), "id='$pid'");
					return true;
				} else {
					@unlink($saveDir.$rs['basename']);
				}
			} else @unlink($file);
		}
		return false;
	}
	public function editToPost(){
		if ($this->isLoad) {
			$datas = form::get3('title', 'tags');
			$title   = !empty($datas['title']) ? $datas['title']:'';
			$tags = !empty($datas['tags']) ? $datas['tags'] : '';
			
			$title || $title = $this->datas['title'];
			$title && $title = mb_substr($title, 0, 32);
			
			$datas = array(
				'title'    => $title,
				'time'     => time::$timestamp
			);
			
			$saveDir0 = './'.cfg::get('dir', 'pic');
			$saveDir1 = date('Y/m/d/', time::$timestamp);
			$saveDir  = d($saveDir0.$saveDir1);
			$rs = upload::uploadImage('pic', $saveDir, true);
			$isUp = false;
			if ($rs) {
				$isUp = true;
				$datas['url'] = $saveDir1.$rs['basename'];
				image::watermark($saveDir.$rs['basename']);
			}
			if (db::update('pic', $datas, "id='$this->id'")) {
				$tagCount = tag::setPic($tags, $this->id);
				db::update('pic', array('tag' => $tagCount), "id='$this->id'");
				if ($isUp && $this->datas['url'] != $datas['url']) {
					@unlink(d('./'.cfg::get('dir', 'pic').$this->datas['url']));
				}
				return true;
			} else {
				$isUp && @unlink($saveDir.$rs['basename']);
			}
		}
		return false;
	}
	public function delete(){
		if ($this->isLoad) {
			tag::delPic($this->id);
			@unlink(d('./'.cfg::get('dir', 'pic').$this->datas['url']));
			return db::del_id('pic', $this->id) ? true: false;
		}
		return false;
	}
	public function getUrl(){
		if ($this->isLoad) {
			return WEB_URL_S1.cfg::get('dir', 'pic').$this->datas['url'];
		}
		return '';
	}
	public function addCommentToPost(){
		if ($this->isLoad) {
			extract(form::get3('name', 'content'));
			$name = trim($name);
			$content = trim($content);
			if ($name && $content) {
				$name = mb_substr($name, 0, 16);
				$datas = array(
					'pid'     => $this->id,
					'name'    => $name,
					'content' => $content,
					'time'    => time::$timestamp,
					'ip'      => qscms::v('_G')->ipint
				);
				if ($cid = db::insert('comment', $datas, true)) {
					db::update('pic', 'comment=comment+\'1\'', "id='$this->id'");
					$this->datas['comment']++;
					return $cid;
				}
			}
		}
		return false;
	}
	public function editCommentToPost($id){
		if ($this->isLoad) {
			extract(form::get3('name', 'content'));
			$name = trim($name);
			$content = trim($content);
			if ($name && $content) {
				$name = mb_substr($name, 0, 16);
				$datas = array(
					'name'    => $name,
					'content' => $content
				);
				if (db::update('comment', $datas, "id='$id' AND pid='$this->id'")) {
					return true;
				}
			}
		}
		return false;
	}
	public function delComment($ids){
		$count = 0;
		if ($this->isLoad) {
			$ids = '\''.implode('\',\'', $ids).'\'';
			if ($count = db::delete('comment', "id IN($ids) AND pid='$this->id'")) {
				db::update('pic', "comment=comment-'$count'", "id='$this->id'");
			}
		}
		return $count;
	}
	public function getComment($cid){
		$rs = array();
		if ($this->isLoad) {
			$rs = db::one('comment', '*', "id='$cid' AND pid='$this->id'");
		}
		return $rs;
	}
	public function getComments($pagesize = 0, $page = 0){
		$list = array();
		if ($this->isLoad) {
			return db::select('comment', '*', "pid='$this->id'", 'time DESC', $pagesize, $page);
		}
		return $list;
	}
	public static function del($ids){
		$count = 0;
		foreach ($ids as $id) {
			$c = new pic($id);
			if ($c->delete()) $count++;
		}
		return $count;
	}
	public static function getTop(){
		$id = db::one_one('pic', 'id', "top='1'");
		return new pic($id);
	}
	public static function getPre($id){
		$id = db::one_one('pic', 'id', "id<'$id'", 'id DESC');
		return new pic($id);
	}
	public static function getNext($id){
		$id = db::one_one('pic', 'id', "id>'$id'", 'id');
		return new pic($id);
	}
	public static function getRandom(){//此处可以优化
		$pre = PRE;
		$id = db::resultFirst("SELECT id FROM `{$pre}pic` ORDER BY RAND() LIMIT 1");
		return new pic($id);
	}
}
?>