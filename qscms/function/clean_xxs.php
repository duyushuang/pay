<?php  
function filterXSS($html){
	$html = qscms::stripslashes($html);
	$html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);//替换script
	$html = preg_replace('/<script[^>]*>/', '', $html);//把单独的<script>也给干掉
	$html = preg_replace('/<\s*\/\s*script>/', '', $html);//把单独的< / script>也给干掉
	$len = strlen($html);
	$atMarker = $atStr = $isEnd = false;
	$str = $marker = $flag = $lastFlag = $mstr = '';
	$flagNum = 0;
	for ($i = 0; $i < $len; $i++) {
		$s = $html{$i};
		$isEnd = $i + 1 == $len;
		if ($atMarker) {//如果在标签中
			if ($atStr) {//如果在字符串中
				$mstr .= $s;
				if ($s == $flag && $flagNum % 2 == 0) {//如果当前字符为字符串结束符("或')并且前面的斜杠数整出2等于0，那么结束字符串
					$atStr = false;
					$marker .= $mstr;
				} else {
					if ($s == '\\') $flagNum++;
					else $flagNum = 0;
					if ($isEnd) {//如果已经到最后一个字符，字符串还没结束，那么补全字符和标签
						$flagNum > 0 && $flagNum % 2 == 1 && $mstr .= '\\';//如果有反斜杠，切不为偶数，那么补一个\
						$mstr .= $flag;//补全字符引号
						$marker .= $mstr;
						$marker .= '>';//补全标签
						$marker = preg_replace('/\s+on[a-z]+\s*=/i', '', $marker);//直接暴力替换onxxxx=为空 看他妈啥脚本能执行
						$str .= $marker;
					}
				}
			} else {
				if ($s == '"' || $s == '\'') {//如果当前字符是"或者'那么开始进入字符串
					$flag = $s;
					$atStr = true;
					$mstr = $s;
					$flagNum = 0;
				} elseif ($s == '>') {//如果是>那么结束标签
					$marker .= $s;
					$atMarker = false;
					$marker = preg_replace('/\s+on[a-z]+\s*=/i', '', $marker);//直接暴力替换onxxxx=为空 看他妈啥脚本能执行
					$str .= $marker;
				} else {
					$marker .= $s;
					if ($isEnd) {//如果已经到最后一个字符还没有结束标签，那么补全一下
						$marker .= '>';
						$marker = preg_replace('/\s+on[a-z]+\s*=/i', '', $marker);//直接暴力替换onxxxx=为空 看他妈啥脚本能执行
						$str .= $marker;
					}
				}
			}
		} else {
			if ($s == '<') {
				$atMarker = true;
				$marker = $s;
			} else $str .= $s;
		}
		$lastFlag = $s;
	}
	return qscms::addslashes($str);
}
?>