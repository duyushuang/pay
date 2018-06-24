<?php exit;?>
引用配置 test
函数 下载($文件名)
	如果 文件存在($文件名)
		$info = 路径信息($文件名)
		$fsize = 文件大小($文件名)
		如果 变量存在($环境['HTTP_RANGE']) 且 ($环境['HTTP_RANGE'] != "") 且 正则匹配("/^bytes=([0-9]+)-$/i", $环境['HTTP_RANGE'], $match) 且 ($match[1] < $fsize)
			$start = $match[1]
		或者
			$start = 0
		头("Cache-control: public")
		头("Pragma: public")
		如果 $start > 0
			头("HTTP/1.1 206 Partial Content")
			头("Accept-Ranges: bytes")
			头("Content-Ranges: bytes ".$start."-".($fsize - 1) . "/" . $fsize)
			头("Content-Length: ".($fsize - $start))
		或者
			头("Accept-Ranges: bytes")
			头("Accept-Length: $fsize")
			头("Content-Length: $fsize")
		头("Content-Type: application/octet-stream"); 
		头("Content-Disposition:attachment;filename=".$info['basename']);
		如果 $f=打开文件($文件名,'rb')
			文件指针($f, $start)
			循环 $r = 文件读取($f, 1024)
				输出 $r
			文件关闭($f)
		或者
			输出 'error'
		返回 true
	或者
		返回 false
输出 "第一个<br />"