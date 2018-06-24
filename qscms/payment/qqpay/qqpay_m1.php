<?php
@header('Content-Type: text/html; charset=UTF-8');
require_once("qqpay.config.php");
require_once("RequestHandler.class.php");
/* 创建支付请求对象 */
$reqHandler = new RequestHandler();
$reqHandler->init();
$reqHandler->setKey($tenpay_config['key']);
$reqHandler->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");
$reqHandler->setParameter("partner", trim($tenpay_config['mch']));
$reqHandler->setParameter("out_trade_no", $out_trade_no);
$reqHandler->setParameter("total_fee", $total_fee * 100);  //总金额
$reqHandler->setParameter("return_url", $return_url);
$reqHandler->setParameter("notify_url", WEB_URL."/qscms/payment/qqpay/return_url.php");
$reqHandler->setParameter("body", $subject);
$reqHandler->setParameter("bank_type", "DEFAULT");     //银行类型，默认为财付通
//用户ip
$reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']); //客户端IP
$reqHandler->setParameter("fee_type", "1");               //币种
$reqHandler->setParameter("subject", $subject);          //商品名称，（中介交易时必填）
//系统可选参数
$reqHandler->setParameter("sign_type", "MD5");       //签名方式，默认为MD5，可选RSA
$reqHandler->setParameter("service_version", "1.0");    //接口版本号
$reqHandler->setParameter("input_charset", "utf-8");      //字符集
$reqHandler->setParameter("sign_key_index", "1");       //密钥序号
//业务可选参数
$reqHandler->setParameter("attach", "");                //附件数据，原样返回就可以了
$reqHandler->setParameter("product_fee", "");           //商品费用
$reqHandler->setParameter("transport_fee", "0");         //物流费用
$reqHandler->setParameter("time_start", date("YmdHis"));  //订单生成时间
$reqHandler->setParameter("time_expire", "");             //订单失效时间
$reqHandler->setParameter("buyer_id", "");                //买方财付通帐号
$reqHandler->setParameter("goods_tag", "");               //商品标记
$reqHandler->setParameter("trade_mode", 1);              //交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））
$reqHandler->setParameter("transport_desc", "");              //物流说明
$reqHandler->setParameter("trans_type", "1");              //交易类型
$reqHandler->setParameter("agentid", "");                  //平台ID
$reqHandler->setParameter("agent_type", "");               //代理模式（0.无代理，1.表示卡易售模式，2.表示网店模式）
$reqHandler->setParameter("seller_id", "");                //卖家的商户号
//请求的URL
$reqUrl = $reqHandler->getRequestURL();
$data = winsock::open($reqUrl);
$data || $data = file_get_contents($reqUrl);
print_r($data);exit;
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
</head>
<style>
*{padding:0;margin:0;}  
body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,form,fieldset,input,p,blockquote,th,td{margin:0;padding:0; border:0}
img,input,select,button{border:none;vertical-align:middle;}  
body{font-family:"微软雅黑","宋体",Arial;font-size:12px;text-align:center;background:#f5f5f5;color:#999999; padding:0px; margin:0px} 
ul,ol{list-style-type:none;}  
th,td,input{font-size:12px;}  
fieldset,img{border:0;}
input,textarea,select{border:solid 1px #d4d4d4; font-size:14px; color:#000000; line-height:18px; font-family:"微软雅黑","宋体",Arial; vertical-align:middle}
/*input:hover,textarea:hover,select:hover,input:focus,textarea:focus,select:focus{border:solid 2px #96d0ee; font-size:14px; color:#000000; line-height:18px; font-family:"微软雅黑","宋体",Arial;}
*/
input{padding-left:5px;}
button{border:none;cursor:pointer;font-size:12px;background-color:transparent;}  
.clear{clear:both;font-size:1px;height:0;visibility:hidden;line-height:0;}  
.clearfix:after{content:"";display:block;clear:both;}  
.clearfix{zoom:1;}  
a {text-decoration:none;color:#666666;}  
a:hover{text-decoration:none;color:#ff8900;} 
td{word-break: break-all;}
.choose-none{border:none}

/*html{overflow-y:scroll;}  */
.fll{float:left;}
.flr{float:right;}
.fln{float:none;}
*{	
	margin: 0;
	outline: 0;
	padding: 0;
	font-size: 100%;
	-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
}
a {
    text-decoration: none;
    -webkit-tap-highlight-color: rgba(0, 0, 0, 0.35);
}
html {
	height: 100%;
	font-size: 100%;
	-webkit-text-size-adjust: 100%;
	-ms-text-size-adjust: 100%;
}
body {
	margin: 0;
	padding: 0;
	width: 100%;
	height: 100%;
	min-height: 100%;
	font-size: 14px;
	line-height: 1.231;
	-webkit-touch-callout: none;
	display: -webkit-box;
	-webkit-box-orient: vertical;
	-webkit-box-align: stretch;
	position: relative;
}
img {
	-ms-interpolation-mode: bicubic;
	vertical-align: middle;}
	.orderid{width:100%; height:40px; border:solid 1px #e0e0e0; background:#ffffff;}
.jubaoPayTitle{background:#303030; height:48px; width:100%; font-size:18px; color:#fff; line-height:48px;top:0px;position:fixed;left:0;top:0; z-index:99}
.totalSum{background:#fff; padding-top:59px; padding-bottom:16px; font-size:12px;}
.totalSum h2{color:#999999; font-size:14px; padding-bottom:5px; font-weight:normal}
.totalSum span{color:#fe5400; font-size:22px;}
.receiveList{background:#f6f6f6; font-size:12px; padding:6px 0}
.receiveList ul li{height:18px; line-height:18px; padding:0px 15px;}
.receiveList ul li .reTitle{float:left; color:#999999;}
.receiveList ul li .reDetail{float:right; color:#999999;}
.borderBtm{border-bottom:solid 1px #f6f6f6;}
.payqdList{background:#fff;}
.payqdList ul li{border-bottom:solid 1px #eaeaea; padding-top:10px; padding-bottom:10px; text-align:left; height:36px; padding-left:16px; padding-right:13px; clear:both}
.payqdPic{width:36px; height:36px; margin-right:10px;}
.payqdName{}
.payqdName h2{color:#4f4f50; font-size:14px; padding-top:2px; padding-bottom:1px; font-weight:normal}
.payqdName h2 span{background:#ff0000; color:#fff; font-size:12px; padding:0px 2px}
.payqdName p{color:#999999; font-size:12px;}
.copyR{background:#f5f5f5; height:60px; line-height:60px; font-size:12px; color:#999999; margin-top:5px;}
.paycsList{padding:18px; text-align:left; padding-top:65px; width:274px; margin:0 auto}
.tds{color:#666666; font-size:13px; line-height:50px;}
.tds_1{color:#666666; font-size:13px; line-height:40px;}
.prizeEach{border:solid 1px #dfdfdf; display:block; background:#fff; width:49px; height:49px; text-align:center; line-height:49px; color:#666666; font-size:13px; float:left; margin-bottom:10px;}
.marR{margin-right:10px;}
.prizeEachCurrent{position:relative; border:solid 1px #53a71d; z-index:1}
.dg{position:absolute; top:-1px; right:-1px;}
.srdiv{border:solid 1px #dfdfdf; width:100%; height:38px; line-height:38px; color:#999; font-family:"微软雅黑"; font-size:14px; margin-bottom:10px;}
.next_s{margin:0px 18px; height:45px; text-align:center; background:#fe5400; line-height:45px; color:#fff; font-size:16px;  border-radius:5px; -moz-box-shadow:0 1px 0 0px #d32600;
    -webkit-box-shadow:0 1px 0 0px #d32600;
    box-shadow:0 1px 0 0px #d32600}
.next_s a{display:block; height:45px; line-height:45px; color:#fff;}
.next_s_gray{margin:0px 18px; height:45px; text-align:center; background:#dadada; line-height:45px; color:#fff; font-size:16px;  border-radius:5px; -moz-box-shadow:0 1px 0 0px #c5c5c5;-webkit-box-shadow:0 1px 0 0px #c5c5c5;
    box-shadow:0 1px 0 0px #c5c5c5}
.next_s_gray a{display:block; height:45px; line-height:45px; color:#fff;}
.cancle{margin:0px 18px; height:45px; text-align:center; background:#868686; line-height:50px; color:#fff; font-size:16px;  border-radius:5px; -moz-box-shadow:0 1px 0 0px #6a6a6a;
    -webkit-box-shadow:0 1px 0 0px #6a6a6a;
    box-shadow:0 1px 0 0px #6a6a6a}
.cancle a{display:block; height:45px; line-height:45px; color:#fff;}

.chargeTips{text-align:left; color:#666666; font-size:12px; padding:10px 18px 20px 18px; line-height:22px;}
.redstyle{color:#F00;}
.payWayList{padding-bottom:10px;}
.payWayList li{border:solid 1px #dedede; width:130px; height:53px; overflow:hidden; float:left; margin-bottom:10px;}
.payWayListCurrent{position:relative; border:solid 1px #19d221; z-index:1}
#fullbg{background-color: Gray;display:none;z-index:1002;position:absolute;left:0px;top:0px;filter:Alpha(Opacity=40);
/* IE */-moz-opacity:0.4; /* Moz + FF */opacity: 0.4;}
#dialog {z-index: 1005;top:50%; position:absolute; display:none;/* filter:Alpha(Opacity=80);
-moz-opacity:0.8;opacity: 0.8;*/ }
.bankCardList{padding-top:45px; background:#fff; text-align:left;}
.bankCardList ul li{border-bottom:solid 1px #eaeaea; padding-top:15px; height:33px; padding-left:14px; padding-right:14px;}
.bankName{color:#3f3f3f; font-size:16px; float:left}
.bankName span{color:#999;}
.bankListNews{color:#1678e5; font-size:16px; float:left}
.j_tips{background:#fff;color:#2e2e2e; padding:20px 25px; font-size:16px; text-align:center; width:200px}
.SubOp{background:#fff;-moz-border-radius: 2px;-webkit-border-radius: 2px; border-radius:2px; width:295px; height:250px;}
.SubTitle{border-bottom:solid 1px #a0a0a0; color:#de7243; font-size:14px; height:38px; line-height:38px;}
.SubOPBorber{border-bottom:solid 1px #e6e6e6;}
.SubOpList{text-align:left; }
.SubOpList ul li{padding:10px 20px 10px 20px; color:#3f3f3f;  }
.yzm_s{border:solid 1px #cfcfcf; height:37px; line-height:37px; padding-left:5px; padding-right:12px;}
.yzm_input{border:solid 1px #ffffff; background:transparent; width:161px; height:32px; line-height:32px;}
.spanblue{color:#1678e5;}
.maro{margin:0px;}
.successTips{padding-top:85px; text-align:center; padding-bottom:30px;}
.successTips_cha{color:#333; font-size:14px; padding-top:6px; padding-left:10px;}
.successTips img{vertical-align:middle}
.fail_yy{margin:0 auto; width:280px; color:#666; text-align:center; padding-bottom:30px;}
.cwts{padding-top:20px; font-size:16px; padding-bottom:15px;}
.erro_tips{background:#202020; padding:10px; color:#fff; font-size:14px;-moz-border-radius: 4px;-webkit-border-radius: 4px; border-radius:4px; width:200px;}
.mat{margin-top:15px;}
.orderid{width:100%; height:40px; border:solid 1px #e0e0e0; background:#ffffff;}
.orderList{text-align:left; padding-top:52px; padding-left:25px; padding-right:25px; font-size:14px; padding-bottom:10px;}
.orderList label{font-size:14px; color:#099fdd; padding-bottom:5px; line-height:25px;}
.orderList label span{color:#a0a0a0; font-size:12px;}
.orderList label1 {
    font-size: 20px;
    color: #fe5400;
    padding-bottom: 5px;
    line-height: 25px;
}
    .orderList label1 span {
        color: #a0a0a0;
        font-size: 12px;
    }
.orderList ul li{padding-bottom:8px;}
.up_s{margin:0px 21px; height:40px; text-align:center; background:#099fdd; line-height:40px; color:#fff; font-size:16px;  border-radius:5px; -moz-box-shadow:0 1px 0 0px #005173;
    -webkit-box-shadow:0 1px 0 0px #005173;
    box-shadow:0 1px 0 0px #005173}
.up_s a{display:block; height:40px; line-height:40px; color:#fff;}
.up_s_gray{margin:0px 18px; height:45px; text-align:center; background:#dadada; line-height:45px; color:#fff; font-size:16px;  border-radius:5px; -moz-box-shadow:0 1px 0 0px #c5c5c5;-webkit-box-shadow:0 1px 0 0px #c5c5c5;
    box-shadow:0 1px 0 0px #c5c5c5}
.up_s_gray a{display:block; height:45px; line-height:45px; color:#fff;}
.mod-ct .qr-image img {
    width: 230px;
    height: 230px;
}
</style>
<body>
<h1 class="mod-title">
<span class="text"><img style="width:181px;height:33px;" alt="QQ钱包支付" src="<?php echo WEB_URL_S1.WDU.'static/'; ?>pay/static/assets/img/mqq_logo.png"></span>
</h1>
        <div class="orderList">
            <ul>
                <li style="text-align:center;">
                    <label>商户订单号：<?php echo $item['sn'];?> <br><span>(请您在5分钟内完成支付，否则订单会自动取消)</span></label>
                    <br />
                     <div class="qr-image"  id="qrcode" style="border-width:0px;margin-top:10px;" /></div>
                    <br>
                    <label1>应付金额：<?php echo $total_fee;?> <br><span>(登录手机QQ,进入"扫一扫",瞄准二维码扫码并支付)</span></label1>
                </li>
            </ul>
        </div>
        
        <div class="up_s">长按二维码,然后选【识别二维码】</div>
        <div class="orderList" style="padding-top:10px;">
            <ul>
                <li style="text-align: center;">
                    <label><span style="color: #055f17; "><strong>1、用手指按住二维码,等弹出文字了点【识别二维码】 </strong></span></label><br/>
                    <label><span style="color: #055f17; "><strong>2、也可截图本页面然后在通过选择相册图片扫码支付 </strong></span></label><br/>
                    <label><span style="color: #055f17; "><strong>3、本页面会自动弹窗打开QQ支付界面，请放心使用 </strong></span></label><br/>
                    <label><span style="color: #f10505; "><strong>提示：如果充值到遇到问题请联系网站客服获取帮助!</strong></span></label>
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
                    alert('支付链接已经创建,您可直接扫码完成支付！');
                }
            }
        });
    }
    window.onload = loadmsg();
</script>
</body>
</html>