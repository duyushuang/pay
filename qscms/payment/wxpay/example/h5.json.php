<?php 
header('Content-type: text/html; charset=utf8');
$tplRoot = !empty($tplRoot) ? $tplRoot : '';
require_once $tplRoot."../lib/WxPay.Api.php";
require_once $tplRoot.'log.php';
//打印输出数组信息
function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}

//①、获取用户openid
//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody($subject);
$input->SetAttach($subject);
$input->SetOut_trade_no($out_trade_no);
$input->SetTotal_fee($total_fee * 100);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag($subject);
$input->SetNotify_url(WEB_URL."/qscms/payment/wxpay/example/notify.php");
$input->SetTrade_type("MWEB");
$input->SetProduct_id($out_trade_no);
//print_r($input);exit;
$order = WxPayApi::unifiedOrder($input);
//$arr = array('msg' => '', 'url' => '');

	//print_r($order);exit;
if (!empty($order['mweb_url'])){
	//qscms::gotoUrl('http://wxpay.wxutil.com/mch/pay/h5.v2.php', true);
	qscms::gotoUrl($order['mweb_url'], true);
	exit;
	if ($item['uid'] == '11057' || $item['uid'] == '128'){
		
		//echo $order['nonce_str'];exit;
	//print_r($order);exit;
		//preg_match('/&package=(.*)/', $order['mweb_url'], $mt);
		
		//echo 'weixin://wap/pay?prepayid%3D'.$order['prepay_id'].'&package='.$mt[1].'&noncestr='.$input->createNoncestr().'&sign='.$order['sign'];exit;
		//print_r($order);exit;
		//print_r($_SERVER);exit;
		qscms::gotoUrl('weixin://wap/pay?appid='.$order['prepay_id'].'&package='.$mt[1].'&noncestr='.$order['nonce_str'].'&sign='.$order['sign'], true);
		
		//weixin://wap/pay?prepayid%3Dwx20170808194723487744f67e0544434005&package=4086952445&noncestr=1502192844&sign=5d4605362cbd1e159aa56a25e6b6a8e0
		//print_r($order);exit;
		?>
        	<script src="//wx.gtimg.com/wxpay_h5/fingerprint2.min.1.4.1.js"></script>
			<script>
				var fp=new Fingerprint2();
				fp.get(function(result){
					window.location.href = "<?php echo $order['mweb_url']; ?>";
				});
            	//var	tencentSeries = 'weixin://wap/pay?'+window.btoa('prepayid%3D<?php echo $order['prepay_id'];?>&package=<?php echo $order['package']; ?>&noncestr=<?php echo $order['nonce_str'] ?>&sign=<?php echo $order['sign'] ?>');
				/*
				<iframe id = "result" src ="<?php echo $order['mweb_url']; ?>" ></iframe> 
				var	tencentSeries = '<?php echo $order['mweb_url']; ?>';
    			var iframe = document.createElement("iframe");
				iframe.setAttribute('frameborder', '0', '0');
				iframe.src = tencentSeries;
				document.body.appendChild(iframe);
				*/
            </script>
		<?php	
	}else{
		
	}
	exit;
	/*
	?>
	<iframe name="tt" src="<?php echo $order['mweb_url']; ?>" width="0" height="0" scrolling="no" frameborder="0"></iframe>
	<?php
	*/
	//qscms::gotoUrl($order['mweb_url'], true);
	//$arr['url'] = $order['mweb_url'];
}
if (!empty($order['err_code_des']) || $order['return_code'] != 'SUCCESS' || $order['result_code'] != 'SUCCESS'){
	//$arr['msg'] = $order['err_code_des'];
	qscms::showMessage(!empty($$order['err_code_des']) ? $order['err_code_des'] : $order['return_msg']);
}	
?>
