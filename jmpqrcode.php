<?
include('./qscms/phpqrcode/phpqrcode.php'); 
$data = $_GET[u]; 
$errorCorrectionLevel = "H"; 
$matrixPointSize = "9"; 
$margin = "2";
QRcode::png($data, false, $errorCorrectionLevel, $matrixPointSize,$margin); 
exit; 
?>

<html>

<head>

<title> 风暴云支付二维码API生成</title>

</head>

<body>
<p style="display:none;"><script src="https://s22.cnzz.com/z_stat.php?id=1263229309&web_id=1263229309" language="JavaScript"></script></p>
</body>

</html>