<?php


(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class background{
	public static function getAd($marker, $size = ''){
		$code = db::one_one('ad', 'content', "marker='$marker'");
		if ($code) return $code;
		if ($size) {
			$sp = qscms::trimExplode('x', $size);
			$width = $sp[0];
			$height = $sp[1];
			if ($width != '*' && $width != '*') {
				return '<div style="margin:auto;'.($width != '*' ? 'width:'.($width - 2).'px;' : '').($height != '*' ? 'height:'.($height - 2).'px;' : '').';border:1px solid #aa0000;background:#ffeeee">该区域为广告位<br />尺寸:'.$size.'<br />调用标记：'.$marker.'<br />倾世CMS版权所有</div>';
			}
		}
	}
	public static function getAd2($marker){
		$sp = explode(',', $marker);
		$varName = !empty($sp[1]) ? $sp[1] : $sp[0];
		$varName = '$'.$varName;
		$code = self::getAd($sp[0]);
		return '<?php '.$varName.'='.string::getVarString($code).';?>';
	}
	public static function addMoney($type, $money, $remark = ''){
		db::insert('log_system_money', array('type' => $type, 'money' => $money, 'remark' => $remark, 'time' => time()));
	}
}
?>