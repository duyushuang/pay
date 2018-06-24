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

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />

<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />

<meta content="no-cache,must-revalidate" http-equiv="Cache-Control" />

<meta content="no-cache" http-equiv="pragma" />

<meta content="0" http-equiv="expires" />

<meta content="telephone=no, address=no" name="format-detection" />

<meta content="width=device-width, initial-scale=1.0" name="viewport" />

<meta name="apple-mobile-web-app-capable" content="yes" /> 

<!-- apple devices fullscreen -->

<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

<!-- Mobile Devices Support @end -->

<title>QQ钱包安全支付 - <?php echo $site_name;?></title>

<link rel="shortcut icon" href="<?php echo WEB_URL; ?>/favicon.ico" type="image/x-icon" /> 

<link href="<?php echo WEB_URL_S1.WDU.'static/'; ?>pay/static/assets/css/wechat_pay.css" rel="stylesheet" media="screen">

<link href="<?php echo WEB_URL; ?>/style/css/jinmipay_qqpay_m.css" rel="stylesheet" media="screen">

</head>



<body>

<h1 class="mod-title">

<span class="text"><img style="width:43px;height:43px;margin-bottom: 6px;" alt="QQ钱包支付" src="/qscms/static/images/pay/logom.png?spa=v1">QQ钱包收银台</span>

</h1>

        <div class="orderList">

            <ul>

                <li style="text-align:center;">

                    <label>商户订单号：<?php echo $item['sn'];?> <br><span>(请您在5分钟内完成支付，否则订单会自动取消)</span></label>

                    <br />

                     <div class="qr-image"  id="qrcode" style="border-width:0px;margin-top:10px;" /></div>

                    <br>

                    <label1>应付金额：<?php echo $total_fee;?>元<br><span>(登录手机QQ,进入"扫一扫",瞄准二维码扫码并支付)</span></label1>

                </li>

            </ul>

        </div>

        



<div class="weui_progress_inner_bar" id="progWait" style="width: 96%;"></div>

<div style="height:30px;padding: 1em;color: #666666;text-align: center;"><p>没有自动拉起QQ钱包？</p>请点击<span style="font-size: 1.2em;">↓</span><span style="color: red;">立即支付</span>手动拉起</div>

<div style="text-align:center;margin-top:15px;"><a class="jinmipay_lj" href="<?php echo '//'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']; ?>" target="_blank">立即支付</a><br></div>





        <div class="orderList" style="padding-top:10px;">

            <ul>

                <li style="text-align: center;">

                    <label><span style="color: #055f17; ">1、用手指按住二维码,等弹出文字了点【识别二维码】 </span></label><br/>

                    <label><span style="color: #055f17; ">2、也可截图本页面然后在通过选择相册图片扫码支付 </span></label><br/>

                    <label><span style="color: #055f17; ">3、本页面会自动弹窗打开QQ支付界面，请放心使用  </span></label><br/>

                    <label><span style="color: #f10505; ">提示：如果充值到遇到问题请联系网站客服获取帮助!</span></label>

                    <br />                    



                </li>

            </ul>

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
	
	<?php
		/*
		if (!empty($_GET['bug']) && $_GET['bug'] == 1){
			?>
			var tencentSeries1 = '<?php echo base64_encode($code_url);?>';
			//window.location.href=tencentSeries;
			$('.orderList').html('<a  target="_blank"href="mqqapi://forward/url?url_prefix=aHR0cHM6Ly9zYy5xcS5jb20vZngvdD9yPTh2WEFnckImX3d2PTM=&version=1&s=1484403638412&plg_auth=1&src_type=web&_wv=3">打开</a>');
			//alert(tencentSeries);
			 <?php	
		}
		*/
	?>
	

    var iframe = document.createElement("iframe");

        iframe.setAttribute('frameborder', '0', 0);

        iframe.src = tencentSeries;

        document.body.appendChild(iframe);

    // 订单详情

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

                    alert('支付链接已经创建,您可直接扫码完成支付！');

                }

            }

        });

    }

    window.onload = loadmsg();

</script>

</body>

</html>