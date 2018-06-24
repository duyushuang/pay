<?php exit;?>
引用配置 global
输出 '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>“行程”规划表</title>
<style type="text/css">
body{
	font-family:"Microsoft YaHei";
	font-size:12px;
}
table{
	width:600px;
	margin:auto;
	border:0;border-collapse:collapse;border-spacing:0;
	margin-top:20px;
}
table th{
	background:#BFE8F9;
}
table td{
	background:#F394E2;
}
table th,table td{
	text-align:center;
	border:1px solid #ffffff;
}
table th:first-child,table td:first-child{
	width:100px;
}
</style>
</head>

<body>'
函数 获取每周时间戳($第几周 = 0)
	$当前时间 = 时间()
	$当前星期几 = 转换为数字(日期('w', $当前时间))
	如果 $当前星期几 == 0
		$当前星期几 = 7
	$本周开始时间 = $当前时间 - ($当前星期几 - 1) * 24 * 3600
	$本周开始时间 += $第几周 * 7 * 24 * 3600
	$返回数组 = 数组()
	循环 $周 0 到 6
		$返回数组[] = $本周开始时间 + $周 * 24 * 3600
	返回 $返回数组
函数 获取每周值日表($周 = 0, $备注 = '')
	$开始时间 = 构造时间(0, 0, 0, 3, 10, 2015)
	如果 不是空数据($备注)
		$备注 = '('.$备注.')'
	$每周名称 = 数组('星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期日')
	$卫生 = 数组('刘胜雨', '沈辉', '刘凯', '周忠滔', '侯希桐')
	$做饭 = 数组('侯静', '刘凯', '刘胜雨', '周忠滔')
	$洗碗 = 数组('沈辉', '侯希桐')
	$该周时间 = 获取每周时间戳($周)
	$备注 = '开始于：'.日期('m月d日', $该周时间[0]).$备注
	$当前周几 = 转换为数字(日期('w', $开始时间))
	如果 $当前周几 == 0
		$当前周几 = 7
	$当前周几--
	$html = '<table>'
	$html .= '<tr><th colspan="4">'.$备注.'</th></tr>'
	$html .= '<tr><th></th><th>卫生</th><th>做饭</th><th>洗碗</th></tr>'
	循环 $i 0 到 count($每周名称) - 1
		$w = $该周时间[$i]
		$t = $w - $开始时间
		$day = floor($t / 24 / 3600)
		$html .= '<tr>'
		$html .= '<td>'.$每周名称[$i].'('.日期('m.d', $w).')</td>'
		$html .= '<td>'
		如果 $day < 0
			$html .= '-'
		或者
			$index = $day % count($卫生)
			如果 $周 == 0 且 $i == $当前周几
				$html .= '<span style="color:#ff0000">'.$卫生[$index].'</span>'
			或者
				$html .= $卫生[$index]
		$html .= '</td>'
		$html .= '<td>'
		如果 $day < 0
			$html .= '-'
		或者
			$index = $day % count($做饭)
			如果 $周 == 0 且 $i == $当前周几
				$html .= '<span style="color:#ff0000">'.$做饭[$index].'</span>'
			或者
				$html .= $做饭[$index]
		$html .= '</td>'
		$html .= '<td>'
		如果 $day < 0
			$html .= '-'
		或者
			$index = $day % count($洗碗)
			如果 $周 == 0 且 $i == $当前周几
				$html .= '<span style="color:#ff0000">'.$洗碗[$index].'</span>'
			或者
				$html .= $洗碗[$index]
		$html .= '</td>'
		$html .= '</tr>'
	$html .= '</table>'
	返回 $html
输出 获取每周值日表(-1, '上周')
输出 获取每周值日表(0, '本周')
输出 获取每周值日表(1, '下周')
输出 '</body>
</html>'