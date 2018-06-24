<?php
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
//error_reporting(E_ALL);
//ini_set('display_errors', 'on');

require_once $tplRoot."../lib/WxPay.Api.php";
require_once $tplRoot."WxPay.NativePay.php";
require_once $tplRoot.'log.php';
//模式一
/**
 * 流程：
 * 1、组装包含支付信息的url，生成二维码
 * 2、用户扫描二维码，进行支付
 * 3、确定支付之后，微信服务器会回调预先配置的回调地址，在【微信开放平台-微信支付-支付配置】中进行配置
 * 4、在接到回调通知之后，用户进行统一下单支付，并返回支付信息以完成支付（见：native_notify.php）
 * 5、支付完成之后，微信服务器会通知支付成功
 * 6、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
 * 7、二维码生成api （http://paysdk.weixin.qq.com/example/qrcode.php?data=）（https://pan.baidu.com/share/qrcode?w=300&h=300&url=）
 */
//echo WxPayConfig::$MCHID;exit;
$notify = new NativePay();
//$url1 = $notify->GetPrePayUrl("1111122223");
//echo 'http://paysdk.weixin.qq.com/example/qrcode.php?data='.urlencode($url1);exit;

//模式二
/**
 * 流程：
 * 1、调用统一下单，取得code_url，生成二维码
 * 2、用户扫描二维码，进行支付
 * 3、支付完成之后，微信服务器会通知支付成功
 * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
 */
$input = new WxPayUnifiedOrder();
$input->SetBody($subject);
$input->SetAttach($subject);
$input->SetOut_trade_no($out_trade_no);
$input->SetTotal_fee($total_fee * 100);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag($subject);
$input->SetNotify_url(WEB_URL."/qscms/payment/wxpay/example/notify.php");
$input->SetTrade_type("NATIVE");
$input->SetProduct_id($out_trade_no);
//print_r($input);
$result = $notify->GetPayUrl($input);
$url2 = $result["code_url"];
//print_r($result);exit;
if (!$url2) qscms::showMessage($result['err_code_des'] ? $result['err_code_des'] : $result['return_msg'], qscms::getUrl('/'));
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="<?php echo WEB_URL; ?>/qscms/static/js/jquery-1.7.2.min.js"></script>
    <script src="/qscms/static/layer/layer.js" type="text/javascript"></script>
<link href="<?php echo WEB_URL; ?>/style/css/jinmipay_wxpay.css" rel="stylesheet" media="screen">
    <title>微信支付</title>
</head>
<style type="text/css">
.order_label {
    font-size: 14px;
    color: #fe5400;
    padding-bottom: 5px;
    line-height: 25px;
}
</style>

<?php
if ($url2){
?>
<body style="text-align: center;">
<div class="jinmipayTitle">微信扫码收银台</div>
<div style="text-align:center;margin-top:5%;background: #fff url(/qscms/static/images/img/wave.png?spa=v1) top center repeat-x;">
<br/><br/>
<label class="order_label"><?php echo $subject;?></label>
<br/>
<span>(请您在5分钟内完成支付，否则订单会自动取消)</span>

<p><img alt="扫码支付" src="/jmpqrcode.php?u=<?php echo urlencode($url2);?>" style="width:300px;height:300px; text-align:center;border: 1px solid #05be02;margin-top: 20px;"/></p>
<div style="text-align:center;margin-top:1%;padding-bottom: 5%;">
<label1 >应付金额：<font style="font-weight: 600;"><?php echo $total_fee;?>元 </font><br><span style="color:green;">(登录手机微信,进入"扫一扫",瞄准二维码扫码并支付)<br/>(充值到遇到问题请联系网站客服获取帮助)</span></label1>
<br/>


<div class="weui_progress_inner_bar" id="progWait" style="width: 96%;"></div>
<div style="height:30px;padding: 1em;color: #666666;text-align: center;"><p>手机版没有自动拉起微信？</p>请点击<span style="font-size: 1.2em;">↓</span><span style="color: red;">立即支付</span>手动拉起微信</div>
<div style="text-align:center;margin-top:15px;"><a  class="jinmipay_lj" href="<?php echo '//'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']; ?>" target="_blank">立即支付</a><br></div>


<!--div class="ts_s"> <label><span style="color:#06a204; "><br/>提示①、若在手机浏览器内，本页面会自动<br/>弹出窗口请点击确定按钮才能进行付款。<br/>提示②、电脑版页面，请使用手机版微信扫<br/>描上面的二维码然后进行付款。如有不明白<br/>的请看：
	<a onClick="return layer.alert('当前页面若是手机版将弹出提示，请点击确定，以便于唤起手机版微信进行付款，如手机版页面无弹出提示请检查手机权限。充值到遇到问题请联系网站客服获取帮助!')" class="btn btn-xs blue ajaxify" title="点击查看">提示说明</a>，或联系网站客服协助</span></label>
                    <br>                    

                </li>
          </div-->


</div>
</div>
</body>
<?php
}
?>
<script>
    function loadmsg() {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "<?php echo WEB_URL;?>/getshop",
			cache:false,
            timeout: 10000, //ajax请求超时时间10s
            data: {payment: "wxpay", out_trade_no: "<?php echo $item['sn']; ?>"}, //post数据
            success: function (data, textStatus) {
                //从服务器得到数据，显示数据并继续查询
                if (data.status == true) {
					if (confirm("您已支付完成，需要跳转吗？")) {
						if (data.url){
                        	window.location.href=data.url;
						}else {
							window.location.href='/';
						}
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
                    alert('创建连接失败！');
                }
            }
        });
    }
    window.onload = loadmsg();
</script>
</html>