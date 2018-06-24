<?php
exit;
if ($var->postData) {
	$datas = form::get3('unicode', 'jianti', 'fanti', 'pinyin', 'bihua');
	if ($datas['unicode'] && $datas['pinyin'] && $datas['bihua']) {
		$datas['wenzi'] = @iconv("UCS-2BE","UTF-8",pack("H4", $datas['unicode']));
		if ($datas['wenzi']) {
			$datas['jianti'] && $datas['jianti'] = @iconv("UCS-2BE","UTF-8",pack("H4", $datas['jianti']));
			$datas['fanti'] && $datas['fanti'] = @iconv("UCS-2BE","UTF-8",pack("H4", $datas['fanti']));
			if (preg_match('/^([a-z]+)(\d+)$/', $datas['pinyin'], $ms)) {
				$datas['pinyin'] = $ms[1];
				$shendiao = $ms[2];
			} else $shendiao = '0';
			$s = 0;
			$len = strlen($shendiao);
			for ($i = 0; $i < $len; $i++) {
				$n = intval($shendiao{$i}) + 1;
				$s |= 1 << $n - 1;
			}
			$datas['shendiao'] = $s;
			unset($datas['unicode']);
			//print_r($datas);
			if (!db::exists('hanzi', array('wenzi' => $datas['wenzi']))) {
				if (db::insert('hanzi', $datas)) {
					echo 'true';
				} else echo 'insert database error:'.db::error();
			} else echo 'exists';
		} else echo 'unicode error';
	} else echo 'data error';
} else echo 'not found data';
?>