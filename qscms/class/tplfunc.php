<?php
/**

 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class tplfunc{
	public static function isStr($str){
		$s = substr($str, 0, 1);
		if (in_array($s, array('$', '\'', '"'))) return false;
		if (strpos($str, '$') !== false) return false;
		return true;
	}
	public static function imageThumb($str){
		static $saveDir;
		if (!isset($saveDir)) {
			$saveDir = d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/thumb'));
			file::createFolder($saveDir);
		}
		if (substr($str, 0, 1) != '{') $info = array('url' => $str);
		else $info = string::json_decode($str);
		if (!empty($info['url'])) {
			$url = $info['url'];
			unset($info['url']);
			if (!empty($info['thumb'])) {
				$sp = explode('_', $info['thumb']);
				unset($info['thumb']);
				$name = '';
				$type = '';
				$thumb = '';
				switch (count($sp)) {
					case 1:
						$thumb = $sp[0];
					break;
					case 2:
						if (is_numeric($sp[0]) || preg_match('/\d+x\d+/i', $sp[0])) {
							$thumb = $sp[0];
							$type = $sp[1];
						} else {
							$name = $sp[0];
							$thumb = $sp[1];
						}
					break;
					case 3:
						$name = $sp[0];
						$type = $sp[2];
						$thumb = $sp[1];
					break;
				}
				!in_array($type, array('cotout', 'zoom')) && $type = 'cutout';
				$sp = qscms::trimExplode('x', strtolower($thumb));
				qscms::setType($sp, 'int');
				$flag = array(
					'width' => $sp[0],
					'height' => !empty($sp[1]) ? $sp[1] : 0
				);
				$str = '';
				foreach ($info as $k => $v) {
					$str && $str .= ' ';
					$str .= $k.'="'.$v.'"';
				}
				$str && $str = ' '.$str;
				if (!self::isStr($url)) {
					echo '<?php
					$___s = ud('.$url.');
					$___pinfo = pathinfo($___s);
					$___d = \''.qscms::addcslashes($saveDir).($name ? (self::isStr($name) ? qscms::addslashes($name) : '\'.'.$name.'.\'') : '\'.$___pinfo[\'filename\'].\'').'_'.$flag['width'].'_'.$flag['height'].'_'.$type.'.\'.$___pinfo[\'extension\'];
					!file_exists($___d) && image::thumb($___s, $___d, '.string::formatArray($flag).', \''.$type.'\');
					echo \'<img src="\'.u($___d, true).\'"'.($str ? qscms::addcslashes($str) : '').' />\';
					?>';
				} else {
					$s = ud($url);
					$pinfo = pathinfo($s);
					if (!$name) $name = $pinfo['filename'];
					$d = $saveDir.$name.'_'.$flag['width'].'_'.$flag['height'].'_'.$type.'.'.$pinfo['extension'];
					if (!file_exists($d)) image::thumb($s, $d, $flag, $type);
					
					echo '<img src="'.u($d, true).'"'.$str.' />';
				}
			} else {
				$str = '';
				foreach ($info as $k => $v) {
					$str && $str .= ' ';
					$str .= $k.'="'.$v.'"';
				}
				$str && $str = ' '.$str;
				if (substr($url, 0, 1) != '$') $url = '\''.$url.'\'';
				echo '<img src="<?php echo '.$url.';?>"'.$str.' />';
			}
		}
	}
	public static function imageThumbUrl($url, $flag = ''){
		static $saveDir;
		if (!isset($saveDir)) {
			$saveDir = d(qscms::getCfgPath('/system/cacheDirRoot+/system/cacheDirs/thumb'));
			file::createFolder($saveDir);
		}
		if ($flag) {
			$sp = explode('_', $flag);
			$name = '';
			$type = '';
			$thumb = '';
			switch (count($sp)) {
				case 1:
					$thumb = $sp[0];
				break;
				case 2:
					if (is_numeric($sp[0]) || preg_match('/\d+x\d+/i', $sp[0])) {
						$thumb = $sp[0];
						$type = $sp[1];
					} else {
						$name = $sp[0];
						$thumb = $sp[1];
					}
				break;
				case 3:
					$name = $sp[0];
					$type = $sp[2];
					$thumb = $sp[1];
				break;
			}
			!in_array($type, array('cotout', 'zoom')) && $type = 'cutout';
			$sp = qscms::trimExplode('x', strtolower($thumb));
			qscms::setType($sp, 'int');
			$flag = array(
				'width' => $sp[0],
				'height' => !empty($sp[1]) ? $sp[1] : 0
			);
			if (!self::isStr($url)) {
				echo '<?php
				$___s = ud('.$url.');
				$___pinfo = pathinfo($___s);
				$___d = \''.qscms::addcslashes($saveDir).($name ? (self::isStr($name) ? qscms::addslashes($name) : '\'.'.$name.'.\'') : '\'.$___pinfo[\'filename\'].\'').'_'.$flag['width'].'_'.$flag['height'].'_'.$type.'.\'.$___pinfo[\'extension\'];
				!file_exists($___d) && image::thumb($___s, $___d, '.string::formatArray($flag).', \''.$type.'\');
				echo u($___d, true);
				?>';
			} else {
				$s = ud($url);
				$pinfo = pathinfo($s);
				if (!$name) $name = $pinfo['filename'];
				$d = $saveDir.$name.'_'.$flag['width'].'_'.$flag['height'].'_'.$type.'.'.$pinfo['extension'];
				if (!file_exists($d)) image::thumb($s, $d, $flag, $type);
				echo u($d, true);
			}
		} else {
			if (self::isStr($url)) echo $url;
			else echo '<?php echo '.$url.';?>';
		}
	}
}