<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class e_qscms extends Exception{
	private $showInfo;
	// 重定义构造器使 message 变为必须被指定的属性
    public function __construct($message, $code = 0, $showInfo = true) {
        // 自定义的代码
		$this->showInfo = $showInfo;
        // 确保所有变量都被正确赋值
        parent::__construct($message, $code);
    }

    // 自定义字符串输出的样式
    public function __toString() {
		$file = u($this->getFile());
		$line = $this->getLine();
		$msg  = $this->message;
		$code = $this->code;
		if ($this->showInfo) {
			$message  = "file:$file<br />line:$line<br /><div style=\"color:#FF5555\">$msg</div>";
		} else {
			$message  = "<div style=\"color:#FF5555\">$msg</div>";
		}
		$msgObj = new html_error($message);
        return $msgObj->getHTML();
    }
}
?>