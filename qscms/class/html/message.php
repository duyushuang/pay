<?php
/**

 */
class html_message{
	private $htmlDoc;
	public function __construct($message, $title){
		$this->htmlDoc = new html_document();
		//$title || $title = SOFTWARE_NAME.'运行遇到错误！';
		$this->htmlDoc->setTitle($title);
		$this->htmlDoc->setStyle(array(
			'body' => 'margin:0;font-size:.7em;font-family:Verdana, Arial, Helvetica, sans-serif;background:#EEEEEE;',
			'fieldset' => 'padding:0 15px 10px 15px;',
			'h1' => 'font-size:2.4em;margin:0;color:#FFF;',
			'h2' => 'font-size:1.7em;margin:0;color:#CC0000;',
			'h3' => 'font-size:1.2em;margin:10px 0 0 0;color:#000000;',
			'#header' => 'width:96%;margin:0 0 0 0;padding:6px 2% 6px 2%;font-family:"trebuchet MS", Verdana, sans-serif;color:#FFF;
background-color:#555555;',
			'#content' => 'margin:0 0 0 2%;position:relative',
			'.content-container' => 'background:#FFF;width:96%;margin-top:8px;padding:10px;position:relative;'
		));
		$this->htmlDoc
		->addToBody('div')
		->addAti('id', 'header')
		->addChild('h1', $title);
		$this->htmlDoc->addToBody('div')->addAti('id', 'content')
		->addChild('div')->addAti('class', 'content-container')
		->addChild('fieldset')->addChild('h2', $title)
		->addParent('h3', $message)
		;
	}
	public function getHTML(){
		return $this->htmlDoc->getHTML();
	}
	public function show(){
		echo $this->getHTML();
		exit;
	}
}
?>