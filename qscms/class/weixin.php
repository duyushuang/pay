<?php
class weixin{
	public static $errCode=array(
        '-1'=>'系统繁忙',
        '0'=>'请求成功',
        '40001'=>'获取access_token时AppSecret错误，或者access_token无效',
        '40002'=>'不合法的凭证类型',
        '40003'=>'不合法的OpenID',
        '40004'=>'不合法的媒体文件类型',
        '40005'=>'不合法的文件类型',
        '40006'=>'不合法的文件大小',
        '40007'=>'不合法的媒体文件id',
        '40008'=>'不合法的消息类型',
        '40009'=>'不合法的图片文件大小',
        '40010'=>'不合法的语音文件大小',
        '40011'=>'不合法的视频文件大小',
        '40012'=>'不合法的缩略图文件大小',
        '40013'=>'不合法的APPID',
        '40014'=>'不合法的access_token',
        '40015'=>'不合法的菜单类型',
        '40016'=>'不合法的按钮个数',
        '40017'=>'不合法的按钮类型',
        '40018'=>'不合法的按钮名字长度',
        '40019'=>'不合法的按钮KEY长度',
        '40020'=>'不合法的按钮URL长度',
        '40021'=>'不合法的菜单版本号',
        '40022'=>'不合法的子菜单级数',
        '40023'=>'不合法的子菜单按钮个数',
        '40024'=>'不合法的子菜单按钮类型',
        '40025'=>'不合法的子菜单按钮名字长度',
        '40026'=>'不合法的子菜单按钮KEY长度',
        '40027'=>'不合法的子菜单按钮URL长度',
        '40028'=>'不合法的自定义菜单使用用户',
        '40029'=>'不合法的oauth_code',
        '40030'=>'不合法的refresh_token',
        '40031'=>'不合法的openid列表',
        '40032'=>'不合法的openid列表长度',
        '40033'=>'不合法的请求字符，不能包含\uxxxx格式的字符',
        '40035'=>'不合法的参数',
        '40038'=>'不合法的请求格式',
        '40039'=>'不合法的URL长度',
        '40050'=>'不合法的分组id',
        '40051'=>'分组名字不合法',
		'40073'=>'不合法的ID',
        '40099'=>'该 code 已被核销',
		'40130'=>'至少需要两个用户ID',
        '41001'=>'缺少access_token参数',
        '41002'=>'缺少appid参数',
        '41003'=>'缺少refresh_token参数',
        '41004'=>'缺少secret参数',
        '41005'=>'缺少多媒体文件数据',
        '41006'=>'缺少media_id参数',
        '41007'=>'缺少子菜单数据',
        '41008'=>'缺少oauth code',
        '41009'=>'缺少openid',
        '42001'=>'access_token超时',
        '42002'=>'refresh_token超时',
        '42003'=>'oauth_code超时',
        '42005'=>'调用接口频率超过上限',
        '43001'=>'需要GET请求',
        '43002'=>'需要POST请求',
        '43003'=>'需要HTTPS请求',
        '43004'=>'需要接收者关注',
        '43005'=>'需要好友关系',
        '44001'=>'多媒体文件为空',
        '44002'=>'POST的数据包为空',
        '44003'=>'图文消息内容为空',
        '44004'=>'文本消息内容为空',
        '45001'=>'多媒体文件大小超过限制',
        '45002'=>'消息内容超过限制',
        '45003'=>'标题字段超过限制',
        '45004'=>'描述字段超过限制',
        '45005'=>'链接字段超过限制',
        '45006'=>'图片链接字段超过限制',
        '45007'=>'语音播放时间超过限制',
        '45008'=>'图文消息超过限制',
        '45009'=>'接口调用超过限制',
        '45010'=>'创建菜单个数超过限制',
        '45015'=>'回复时间超过限制，需要关注者与公众号24小时内有交互',
        '45016'=>'系统分组，不允许修改',
        '45017'=>'分组名字过长',
        '45018'=>'分组数量超过上限',
        '45024'=>'账号数量超过上限',
        '46001'=>'不存在媒体数据',
        '46002'=>'不存在的菜单版本',
        '46003'=>'不存在的菜单数据',
        '46004'=>'不存在的用户',
        '47001'=>'解析JSON/XML内容错误',
        '48001'=>'api功能未授权',
        '50001'=>'用户未授权该api',
        '61450'=>'系统错误',
        '61451'=>'参数错误',
        '61452'=>'无效客服账号',
        '61453'=>'账号已存在',
        '61454'=>'客服帐号名长度超过限制(仅允许10个英文字符，不包括@及@后的公众号的微信号)',
        '61455'=>'客服账号名包含非法字符(英文+数字)',
        '61456'=>'客服账号个数超过限制(10个客服账号)',
        '61457'=>'无效头像文件类型',
        '61458'=>'客户正在被其他客服接待',
        '61459'=>'客服不在线',
        '61500'=>'日期格式错误',
        '61501'=>'日期范围错误',
        '7000000'=>'请求正常，无语义结果',
        '7000001'=>'缺失请求参数',
        '7000002'=>'signature 参数无效',
        '7000003'=>'地理位置相关配置 1 无效',
        '7000004'=>'地理位置相关配置 2 无效',
        '7000005'=>'请求地理位置信息失败',
        '7000006'=>'地理位置结果解析失败',
        '7000007'=>'内部初始化失败',
        '7000008'=>'非法 appid（获取密钥失败）',
        '7000009'=>'请求语义服务失败',
        '7000010'=>'非法 post 请求',
        '7000011'=>'post 请求 json 字段无效',
        '7000030'=>'查询 query 太短',
        '7000031'=>'查询 query 太长',
        '7000032'=>'城市、经纬度信息缺失',
        '7000033'=>'query 请求语义处理失败',
        '7000034'=>'获取天气信息失败',
        '7000035'=>'获取股票信息失败',
        '7000036'=>'utf8 编码转换失败',
    );
	public static function content($keyword){
		$the_one = db::one('byself_msg',"*","keyword='$keyword'");
		$the_one?$the_one:$the_one=db::one('byself_msg',"*","keyword like '%$keyword%'");
		$the_one?$the_one:$the_one=db::one('byself_msg',"*","wrong_key_answer=1");
		$texttype = isset($the_one['texttype'])?$the_one['texttype']:'';
		//file_put_contents(d("./debug.txt"),$the_one['text']."d");
		if($texttype=='0'){
			$content = $the_one['text'];
		}else if($texttype=='1'){
			$content = array();
			$content[] = array("Title"=>$the_one['title'],  "Description"=>$the_one['text'], "PicUrl"=>$the_one['pic'], "Url" =>$the_one['url']);
		}else if($texttype=='2'){
			$t_ids =$the_one['type2ids'];
			$ids= explode('|',$t_ids);
			array_shift($ids);
			$ids_count = count($ids);
			$content = array();
			for($i=0;$i<$ids_count;$i++){
				$id_v=$ids[$i];
				$the_one = db::one('byself_msg','*',"id='$id_v' and texttype=1");
				$content[] = array("Title"=>$the_one['title'],  "Description"=>$the_one['text'], "PicUrl"=>$the_one['pic'], "Url" =>$the_one['url']);
			}
		}else if ($texttype=='3'){
			$content = array();
			$content = array("Title"=>$the_one['title'], "Description"=>$the_one['description'], "MusicUrl"=>$the_one['musicurl'], "HQMusicUrl"=>$the_one['hqmusicurl']);
		}else $content = $keyword;
		return $content;
	}
	public function  __construct(){
		$this->appid = cfg::get("wxpay","AppId");
		$this->appsecret = cfg::get("wxpay","AppSecret");
		$this->token = cfg::get("wxpay","Token");
		$this->EncodingAESKey = cfg::get("wxpay","EncodingAESKey");
	}
	public  function login($referer = ''){
		//$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->appid.'&redirect_uri='.qscms::getUrl('/oauth2').'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
		//echo qscms::getUrl('/oauth2/?goto='.qscms::getUrl($referer));exit;
		$redirect_uri = urlencode(qscms::getUrl('/oauth2/?goto='.qscms::getUrl($referer)));
		//echo qscms::getUrl('/oauth2/?goto='.qscms::getUrl($referer));exit;
		$state = 'wechat';
		$scope = 'snsapi_base';
		$oauth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->appid . '&redirect_uri=' . $redirect_uri . '&response_type=code&scope=' . $scope . '&state=' . $state . '#wechat_redirect';
		//echo $oauth_url;exit;
		header("Location: ".$oauth_url);
		//$json = $webobj->https_request($url);
		//$arr =json_decode($json, true);
		//print_r($arr);exit;
		//$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->appsecret;	
	}
	public function GetOpenid($referer = '')
	{
		//通过code获得openid
		if (!isset($_GET['code'])){
			//触发微信返回code码
			if ($referer){
				$referer = urlencode(qscms::getUrl('/oauth2/?goto='.qscms::getUrl($referer)));
			}else{
				$referer = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']/*.$_SERVER['QUERY_STRING']*/);
			}
			$url = $this->__CreateOauthUrlForCode($referer);
			header("Location: $url");
			exit();
		} else {
			//获取code码，以获取openid
		    $code = $_GET['code'];
			$openid = $this->GetOpenidFromMp($code);
			//if (!$openid) exit($code.'---NO');
			return $openid;
		}
	}
	private function __CreateOauthUrlForCode($redirectUrl)
	{
		$urlObj["appid"] = $this->appid;
		$urlObj["redirect_uri"] = "$redirectUrl";
		$urlObj["response_type"] = "code";
		$urlObj["scope"] = "snsapi_base";
		$urlObj["state"] = "STATE"."#wechat_redirect";
		$bizString = $this->ToUrlParams($urlObj);
		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
	}
	private function ToUrlParams($urlObj)
	{
		$buff = "";
		foreach ($urlObj as $k => $v)
		{
			if($k != "sign"){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
	private function __CreateOauthUrlForOpenid($code)
	{
		$urlObj["appid"] = $this->appid;
		$urlObj["secret"] = $this->appsecret;
		$urlObj["code"] = $code;
		$urlObj["grant_type"] = "authorization_code";
		$bizString = $this->ToUrlParams($urlObj);
		return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
	}
	public function GetOpenidFromMp($code)
	{
		$url = $this->__CreateOauthUrlForOpenid($code);
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, '');
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//运行curl，结果以jason形式返回
		$res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		$data = json_decode($res,true);
		$this->data = $data;
		//print_r($data);
		$openid = $data['openid'];
		return $openid;
	}
	public static function curl_get_contents($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
		curl_setopt($ch, CURLOPT_REFERER, _REFERER_);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$r = curl_exec($ch);
		curl_close($ch);
		return $r;
	}
	public  function get_token(&$webobj = false){
		//if(!file_exists(d('./cache/token.json'))) file::createFile(d('./cache/token.json'));
		//$cache = file_get_contents(d('./cache/token.json'));
		$cache = cache::get_text("vx_access_token");
		include_once './vx/auth.class.php';
		$options = array(
			'appid' => $this->appid,
			'appsecret' => $this->appsecret,
		);
		$appid = $this->appid;
		$secret = $this->appsecret;
		$webobj = new Auth($options);
		//if(!file_exists(d('./cache/token.json'))) { mkdir($cache);chmod($cache,0755);}
		$access_token = '';
        $cache = json_decode($cache, true);
        if (!empty($cache['expires_in']) && !empty($cache['access_token']) && $cache['expires_in'] - 3600 > time::$timestamp) {
            $access_token = $cache['access_token'];
        } else {
           	$url  = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
           	$json = $webobj->https_request($url);
			//print_r($json);exit;
			$arr =json_decode($json, true);
			//file_put_contents(d('./cache/token.json'), string::json_encode(array('access_token' => $arr['access_token'], 'expires_in' => time() + 7000)));
			if(!empty($arr['access_token'])){
				cache::write_text("vx_access_token",string::json_encode(array('access_token' => $arr['access_token'], 'expires_in' => time() + 7000)));
				$access_token  = $arr['access_token'];
			}else{
				exit('请到公众号平台开启开发模式');
			}
			
        }
        return $access_token;
	}	
	public  function getErrText($err) {
        if (isset(self::$errCode[$err])) {
            return self::$errCode[$err];
        }else {
            return false;
        };
    }
}
?>