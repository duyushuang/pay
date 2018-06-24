<?php
/**

 */

(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
class qq_api{
	const VERSION = '2.0';
    const GET_AUTH_CODE_URL = 'https://graph.qq.com/oauth2.0/authorize';
    const GET_ACCESS_TOKEN_URL = 'https://graph.qq.com/oauth2.0/token';
    const GET_OPENID_URL = 'https://graph.qq.com/oauth2.0/me';
	const SCOPE = 'get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo,check_page_fans,add_t,add_pic_t,del_t,get_repost_list,get_info,get_other_info,get_fanslist,get_idolist,add_idol,del_idol,get_tenpay_addr';
	protected $appid, $appkey, $callback, $state;
	public function __construct($appid = '', $appkey = ''){
		$this->appid  = $appid;
		$this->appkey = $appkey;
		$this->callback = qscms::getUrl('/api/qq/login/');
	}
	protected function writeData($key, $val){
		qscms::setcookie('qq_'.$key, $val);
	}
	protected function readData($key){
		$key = 'qq_'.$key;
		return isset($_COOKIE[$key]) ? $_COOKIE[$key] : '';
	}
	public function loginUrl(){
		$this->state = md5(uniqid(rand(), true));
		$this->writeData('state', $this->state);
		$keysArr = array(
            "response_type" => 'code',
            "client_id"     => $this->appid,
            "redirect_uri"  => $this->callback,
            "state"         => $this->state,
            "scope"         => self::SCOPE
        );
		//echo http_build_query($keysArr);exit;
		return $this->combineUrl(self::GET_AUTH_CODE_URL, $keysArr);
	}
	protected function combineUrl($url, $arr){
		$str = '';
		foreach ($arr as $k => $v) {
			$str && $str .= '&';
			$str .= $k.'='.$v;
		}
		return $url.'?'.$str;
	}
	protected function checkError($str){
		if(strpos($str, "callback") !== false){
			$str = string::getJsonData($str) ;
			$arr = string::json_decode($str);
			if (isset($arr['error'])) {
				$this->showMessage($arr['error_description']);
			}
		}
		return $str;
	}
	public function checkCallback(){
		$var = qscms::v('_G');
		$code  = $var->gp_code;
		$state = $var->gp_state;
		if ($code && $state) {
			if ($state == $this->readData('state')) {//获取TOKEN
				$keysArr = array(
					"grant_type"    => "authorization_code",
					"client_id"     => $this->appid,
					"redirect_uri"  => urlencode($this->callback),
					"client_secret" => $this->appkey,
					"code"          => $code
				);
		
				//------构造请求access_token的url
				$token_url = $this->combineURL(self::GET_ACCESS_TOKEN_URL, $keysArr);
				$html = winsock::open($token_url);
				if ($html) {
					$this->checkError($html);
					parse_str($html, $arr);
					$this->writeData("access_token", $arr["access_token"]);//$arr['expires_in'] 超时时间 一般为7776000 90天
       				return $arr["access_token"];
				} else $this->showMessage('获取QQ登录数据失败，请重试');
			} else $this->showMessage('非法操作');
		} else $this->showMessage('非法操作');
	}
	public function getOpenId(){
		//-------请求参数列表
        $keysArr = array(
            "access_token" => $this->readData("access_token")
        );
        $graph_url = $this->combineURL(self::GET_OPENID_URL, $keysArr);
        $response = winsock::open($graph_url);

        //--------检测错误是否发生
        $str = $this->checkError($response);
        $user = string::json_decode($str);
        //------记录openid
        $this->writeData("openid", $user['openid']);
        return $user['openid'];
	}
	protected function showMessage($msg){
		qscms::showMessage($msg);
	}
}