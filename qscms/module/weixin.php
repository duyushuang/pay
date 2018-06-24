<?php
/*echo '111';  
$content=file_get_contents('php://input');
file_put_contents(d("./123.txt"), "\r\n" . $content . string::formatArray($_SERVER));
exit;*/

//file_put_contents(d("./debug.txt"),'大声的撒' ."d");
error_reporting(0);

function https_request($url, $data = null)
{
		//print_r($data);exit;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url); 
     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
     curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
       curl_setopt($curl, CURLOPT_POST, 1);
  	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}




$weixin = new weixin();
//print_r($weixin);exit;
define("TOKEN", $weixin->token);
define("AppID", $weixin->appid);

define("EncodingAESKey",$weixin->EncodingAESKey);
//8f7f9aba66cf98723916fb7baa5ac222

require_once(d('./vx/wxBizMsgCrypt.php'));
$wechatObj = new wechatCallbackapiTest();
if (!isset($_GET['echostr'])) {
   $wechatObj->responseMsg();
}else{
	
    $wechatObj->valid();
}
function logger($log_content)
{
    if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
        sae_set_display_errors(false);
        sae_debug($log_content);
        sae_set_display_errors(true);
    }else{ //LOCAL
        $max_size = 500000;
        $log_filename = "log.xml";
        if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
        file_put_contents($log_filename, date('Y-m-d H:i:s').$log_content."\r\n", FILE_APPEND);
    }
}
function traceHttp()
{
    logger("\n\nREMOTE_ADDR:".$_SERVER["REMOTE_ADDR"].(strstr($_SERVER["REMOTE_ADDR"],'101.226')? " FROM WeiXin": "Unknown IP"));
    logger("QUERY_STRING:".$_SERVER["QUERY_STRING"]);
}
class wechatCallbackapiTest
{
    //验证签名
	
    public function valid()
    {
        $echoStr = $_GET["echostr"];
		
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $tmpArr = array(TOKEN, $timestamp, $nonce);
		
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
		
        if($tmpStr == $signature){
			//logger($tmpStr."++".$signature."++".$echoStr);
            echo $echoStr;
            exit;
        }
    }

    //响应消息
    public function responseMsg()
    {
        $timestamp  = $_GET['timestamp'];
		
        $nonce = $_GET["nonce"]; 
        $msg_signature  = $_GET['msg_signature'];
        $encrypt_type = (isset($_GET['encrypt_type']) && ($_GET['encrypt_type'] == 'aes')) ? "aes" : "raw";
        
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		
        if (!empty($postStr)){
            //解密
            if ($encrypt_type == 'aes'){
				$pc = new WXBizMsgCrypt(TOKEN, EncodingAESKey, AppID);                
                $this->logger(" D \r\n".$postStr);
                $decryptMsg = "";  //解密后的明文
                $errCode = $pc->DecryptMsg($msg_signature, $timestamp, $nonce, $postStr, $decryptMsg);
                $postStr = $decryptMsg;
            }
			
            $this->logger(" R \r\n".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
			
			
			
			$app_id_=$postObj->ToUserName;//添加粉丝账号
			//$authorizer_appid=db::one_one('vx_account',"authorizer_appid","or_id='$app_id_'");
			$FromUserName=$postObj->FromUserName;
			//$authorizer_appid && vx_usercenter::check_add($authorizer_appid,$FromUserName);

            //消息类型分离
            switch ($RX_TYPE)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                break;
                case "text":
                    $result = $this->receiveText($postObj);
                break;
            }
            $this->logger(" R \r\n".$result);
            //加密
            if ($encrypt_type == 'aes'){

                $encryptMsg = ''; //加密后的密文
                $errCode = $pc->encryptMsg($result, $timeStamp, $nonce, $encryptMsg);
                $result = $encryptMsg;
                $this->logger(" E \r\n".$result);
            }
			
            echo $result;
        }else {
            echo "";
            exit;
        }
    }

    //接收事件消息
    private function receiveEvent($object)
    {	
        $content = "";
		//file_put_contents(d('./debug.txt',$object));
        switch ($object->Event){
			//subscribe
            case 'subscribe':
				$FromUserName=$object->FromUserName;
				if ($pid = $object->EventKey){
					if (preg_match('/(\d+)/', $pid, $match)){
						$pid = $match[1];
					}
				}
				if ($pid){
					if (!db::exists("member","wxid='$FromUserName'")){//注册
						if ($content = member_base::wx($FromUserName, $pid)){
							return $this->transmitText($object, $content);
						}
					}	
				}
				$msg ='欢迎您关注'.$webName;
				$result = $this->transmitText($object, $msg);
				return $result;
			break;
			case 'SCAN':
				$FromUserName=$object->FromUserName;
				if ($pid = $object->EventKey){
					if (preg_match('/(\d+)/', $pid, $match)){
						$pid = $match[1];
					}
				}
				if ($pid && db::exists('member', "id=$pid")){
					if(!db::exists("member","wxid='$FromUserName'")){//注册
						if ($content = member_base::wx($FromUserName, $pid)){
							return $this->transmitText($object, $content);
						}
					}else $content = '您已经绑定了微信号';
				}else $content = '未能识别二维码中参数。';
				
				$result = $this->transmitText($object, $content);
				return $result;
				
			break;
			case 'CLICK'://点击事件回应
				
			}
			$result = $this->transmitText($object, $content);
			return $result;
		}

    //接收文本消息
    private function receiveText($object) {	
		$FromUserName = $object->FromUserName;
		$content = $object->Content;
		$keyword = trim($content);
		$result = $this->transmitText($object, $content);
        return $result;
    }

    //回复文本消息
    private function transmitText($object, $content)
    {
        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
		//echo $result;exit;
		
        return $result;
    }

    //回复图文消息
    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "        <item>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <PicUrl><![CDATA[%s]]></PicUrl>
            <Url><![CDATA[%s]]></Url>
        </item>
";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[news]]></MsgType>
    <ArticleCount>%s</ArticleCount>
    <Articles>
$item_str    </Articles>
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }

    //回复音乐消息
    private function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <MusicUrl><![CDATA[%s]]></MusicUrl>
        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
    </Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[music]]></MsgType>
    $item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
	
	
	//回复图片消息
	private function transmitpic($object, $mediaId = ''){
		$xmlTpl ="<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
<Image>
<MediaId><![CDATA[%s]]></MediaId>
</Image>
</xml>";
 $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $mediaId ? $mediaId : $object->MediaId);
        return $result;
		}
	  //回复多客服消息
    private function transmitService($object)
    {
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[transfer_customer_service]]></MsgType>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //日志记录
    public function logger($log_content)
    {
        if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
            $max_size = 500000;
            $log_filename = "log.xml";
            if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
            file_put_contents($log_filename, date('Y-m-d H:i:s').$log_content."\r\n", FILE_APPEND);
        }
    }
	
}
exit;
?>