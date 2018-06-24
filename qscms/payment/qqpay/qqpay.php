<?php
@header('Content-Type: text/html; charset=UTF-8');
require_once("qqpay.config.php");
require_once("RequestHandler.class.php");
/* 创建支付请求对象 */
$reqHandler = new RequestHandler();
$reqHandler->init();
$reqHandler->setKey($tenpay_config['key']);
$reqHandler->setGateUrl("https://myun.tenpay.com/cgi-bin/wappayv2.0/wappay_init.cgi");


//----------------------------------------
//设置支付参数 
//----------------------------------------
$reqHandler->setParameter("ver", "2.0"); //版本号，ver默认值是1.0
$reqHandler->setParameter("charset", "1"); //1 UTF-8, 2 GB2312
$reqHandler->setParameter("bank_type", "0"); //银行类型
$reqHandler->setParameter("desc", $subject); //商品描述，32个字符以内
$reqHandler->setParameter("pay_channel", "1"); //描述支付渠道
$reqHandler->setParameter("bargainor_id", trim($tenpay_config['mch']));
$reqHandler->setParameter("sp_billno", $out_trade_no);
$reqHandler->setParameter("total_fee", $total_fee*100);  //总金额
$reqHandler->setParameter("fee_type", "1");               //币种
$reqHandler->setParameter("notify_url", WEB_URL."/qscms/payment/qqpay/notify_url.php");//请求的URL
//print_r($reqHandler);exit;
$reqUrl = $reqHandler->getRequestURL();
//echo $reqUrl;exit;
$data = winsock::open($reqUrl);
$data || $data = file_get_contents($reqUrl);
if(preg_match("!<token_id>(.*?)</token_id>!",$data,$match)){
	$code_url='https://myun.tenpay.com/mqq/pay/qrcode.html?_wv=1027&_bid=2183&t='.$match[1];
}else{
	preg_match("!<err_info>(.*?)</err_info>!",$data,$match);
	echo '<table class="table"><tobdy><tr><td>QQ钱包支付下单失败！</td></tr><tr><td>'.(!empty($match[1]) ? $match[1] : '').'</td></tr></tbody></table>';
	exit;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="zh-cn">
<meta name="renderer" content="webkit">
<title>QQ钱包安全支付 - <?php echo $site_name;?></title>
<link rel="shortcut icon" href="<?php echo WEB_URL; ?>/favicon.ico" type="image/x-icon" /> 
<link href="<?php echo WEB_URL_S1.WDU.'static/pay/static/'; ?>assets/css/wechat_pay.css" rel="stylesheet" media="screen">

<style>
.mod-ct .qr-image img {
    width: 230px;
    height: 230px;
}
</style>

</head>
<body>
<div class="body">
<h1 class="mod-title">
<span class="text"><img style="width:181px;height:33px;" alt="QQ钱包支付" src="<?php echo WEB_URL_S1.WDU.'static/'; ?>assets/img/mqq_logo.png"></span>
</h1>
<div class="mod-ct">
<div class="order">
</div>
<div class="amount">￥<?php echo $total_fee;?></div>
<div class="qr-image" id="qrcode">
</div>
 
<div class="detail" id="orderDetail">
<dl class="detail-ct" style="display: none;">
<dt>商家</dt>
<dd id="storeName"><?php echo $item['site_name'];?></dd>
<dt>商户订单号</dt>
<dd id="billId"><?php echo $item['sn'];?></dd>
<dt>创建时间</dt>
<dd id="createTime"><?php echo date('Y-m-d H:i:s', $item['addTime']);?></dd>
</dl>
<a href="javascript:void(0)" class="arrow"><i class="ico-arrow"></i></a>
</div>
<div class="tip">
<span class="dec dec-left"></span>
<span class="dec dec-right"></span>
<div class="ico-scan"></div>
<div class="tip-text">
<p>请使用手机QQ扫一扫</p>
<p>扫描二维码完成支付</p>
</div>
</div>
<div class="tip-text">
</div>
</div>
<div class="foot">
<div class="inner">
<p>手机用户可保存上方二维码到手机中</p>
<p>在手机QQ扫一扫中选择“相册”即可</p>
</div>
</div>
</div>
<script src="<?php echo WEB_URL_S1.WDU.'static/'; ?>pay/static/assets/js/qrcode.min.js"></script>
<script src="<?php echo WEB_URL_S1.WDU.'static/'; ?>pay/static/assets/js/qcloud_util.js"></script>
<script>
    var code_url = '<?php echo $code_url?>';
    var qrcode = new QRCode("qrcode", {
        text: code_url,
        width: 230,
        height: 230,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    var	tencentSeries = 'mqqapi://forward/url?src_type=web&style=default&=1&version=1&url_prefix='+window.btoa(code_url);
	/*
    var iframe = document.createElement("iframe");
        iframe.setAttribute('frameborder', '0', 0);
        iframe.src = tencentSeries;
        document.body.appendChild(iframe);
    // 订单详情
	*/
    $('#orderDetail .arrow').click(function (event) {
        if ($('#orderDetail').hasClass('detail-open')) {
            $('#orderDetail .detail-ct').slideUp(500, function () {
                $('#orderDetail').removeClass('detail-open');
            });
        } else {
            $('#orderDetail .detail-ct').slideDown(500, function () {
                $('#orderDetail').addClass('detail-open');
            });
        }
    });
    // 检查是否支付完成
    function loadmsg() {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "<?php echo WEB_URL;?>/getshop",
            timeout: 10000, //ajax请求超时时间10s
            data: {type: "qqpay", out_trade_no: "<?php echo $out_trade_no;?>"}, //post数据
            success: function (rs, textStatus) {
                //从服务器得到数据，显示数据并继续查询
                if (rs.status == true) {
					if (confirm("您已支付完成，需要跳转到用户中心吗？")) {
                        window.location.href=rs.url;
                    } else {
                        // 用户取消
                    }
                }else{
                    setTimeout("loadmsg()", 4000);
                }
            },
            //Ajax请求超时，继续查询
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                if (textStatus == "timeout") {
                    setTimeout("loadmsg()", 1000);
                } else { //异常
                    alert('请及时支付订单,以免订单失效！');
                }
            }
        });
    }
    window.onload = loadmsg();
</script>

</body>
</html>