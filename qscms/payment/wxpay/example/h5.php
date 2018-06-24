<?php 
header('Content-type: text/html; charset=utf8');
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<script src="//wx.gtimg.com/wxpay_h5/fingerprint2.min.1.4.1.js"></script>

<script type="text/javascript">
var fp=new Fingerprint2();
fp.get(function(result){
    $.getJSON("<?php echo WEB_URL; ?>/qscms/payment/wxpay/example/h5.json.php?code="+result, function(d){
		if(d.msg == ''){
			window.location.href = d.url
			//$('#getBrandWCPayRequest').attr("href",d.url);//+'&redirect_url=http%3a%2f%2fwxpay.    wxutil.com%2fmch%2fpay%2fh5jumppage.php
		 }else{
		  	alert(d.msg);			
		}       
    });                                                            
}
 );
</script>
<title>微信支付</title>
</head>
<body>
</html>