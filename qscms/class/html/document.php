<?php
/**

 */
class html_document{
	private $docTypeCode, $xmlns, $headDatas, $bodyDatas, $headObj, $bodyObj, $htmlObj, $style;
	public function __construct($type = 'XHTML Transitional DTD', $xmlns = 'http://www.w3.org/1999/xhtml'){
		$this->setDocType($type);
		$this->xmlns = $xmlns;
		$this->headDatas = $this->bodyDatas = array();
		$this->headObj = new html_label('head');
		$this->bodyObj = new html_label('body');
		$this->htmlObj = new html_label('html');
		$this->htmlObj->addAti('xmlns', $this->xmlns);
		$this->headObj = $this->htmlObj->addChild('head');
		$this->headObj->addChild('meta', '', false)->addAti(array('http-equiv' => 'Content-Type', 'content' => 'text/html; charset=utf-8'));
		$this->bodyObj = $this->htmlObj->addChild('body');
		$this->style   = new css_tb();
	}
	public function setDocType($type){
		static $types = array(
			'HTML Strict DTD' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
			'HTML Transitional DTD' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
			'Frameset DTD' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
			'XHTML Strict DTD' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
			'XHTML Transitional DTD' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
			'XHTML Frameset DTD' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">');
		$keys = array_keys($types);
		(empty($type) || !in_array($type, $keys)) && $type = $keys[4];
		$this->docTypeCode = $types[$type];
	}
	public function setStyle($key, $val = false){
		return $this->style->add($key, $val);
	}
	public function addToHead($name, $val = '', $simple = false){
		return $this->headObj->addChild($name, $val, $simple);
	}
	public function addToBody($name, $val = '', $simple = false){
		return $this->bodyObj->addChild($name, $val, $simple);
	}
	public function setTitle($title){
		$this->headObj->reChild('title', $title);
	}
	public function setKeywords($keywords){
		$this->headObj->reChild2('meta', array('name' => 'keywords'), array('content' => $keywords), '', true);
	}
	public function setDescription($description){
		$this->headObj->reChild2('meta', array('name' => 'description'), array('content' => $description), '', true);
	}
	public function setStyleType($type, $cacheName = ''){
		$this->style->setType($type, $cacheName);
		return $this;
	}
	public function getHTML(){
		if ($this->style->count > 0) {
			if ($this->style->type == 'code') {
				$this->headObj->addChild('style', $this->style->getCode())->addAti(array('type' => 'text/css'));
			} else {
				$this->headObj->addChild('link', '', true)->addAti(array('href' => $this->style->getCacheUrl(), 'rel' => 'stylesheet', 'type' => 'text/css'));
			}
		}
		return $this->docTypeCode.$this->htmlObj->getHTML();
	}
	public function __toString(){
		return $this->getHTML();
	}
}
?>