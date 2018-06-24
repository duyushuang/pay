<?php
class Auth {
	const API_URL_PREFIX = 'https://api.weixin.qq.com/cgi-bin';
	const API_COMPONENT_TOKEN_URL = '/component/api_component_token';
	const API_CREATE_PREAUTHCODE_URL = '/component/api_create_preauthcode?component_access_token=';
	const API_QUERY_AUTH_URL = '/component/api_query_auth?component_access_token=';
	const API_AUTHORIZER_TOKEN_URL = '/component/api_authorizer_token?component_access_token=';
	const API_GET_AUTHORIZER_INFO_URL = '/component/api_get_authorizer_info?component_access_token=';
	const API_GET_AUTHORIZER_OPTION_URL = '/component/api_get_authorizer_option?component_access_token=';
	const API_SET_AUTHORIZER_OPTION_URL = '/component/api_set_authorizer_option?component_access_token=';
	const API_REDIRECT = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?';
	const USER_GET_URL='/user/get?';
	private $appid;
	private $options;
	private $appsecret;
	private $component_verify_ticket;
	private $_funcflag = false;
	public $debug = true;
	public $errCode = 40001;
	public $errMsg = "no access";
	private $pre_auth_code;
	private $component_access_token;
	private $authorizer_access_token;
	private $next_openid;
	public function __construct($options) {
		$this -> options = $options;
		$this -> appid = isset($options['appid']) ? $options['appid'] : '';
		$this -> appsecret = isset($options['appsecret']) ? $options['appsecret'] : '';
		$this -> component_verify_ticket = isset($options['component_verify_ticket']) ? $options['component_verify_ticket'] : '';
	}

	//测试函数
	/*
	 * 微信对话框模块出故障时，可以将调试信息写入文件，从而分析问题
	 * 对应写入信息页面，还应该有一个可查询文件的页面，目前我都是直接在服务器上看文件的
	 *
	 */
	public function debug_info_write($info) {
		$filename = './a.txt';
		$fp = fopen($filename, 'w+');
		fwrite($fp, $info . "\n");
		fclose($fp);
	}

	/**
	 * 读取调试页面
	 */
	public function debug_info_read() {
		header("Content-Type: text/html; charset=utf-8");
		$filename = './a.txt';
		$fp = fopen($filename, "r");
		$contents = fread($fp, filesize($filename));
		fclose($fp);
		dump($contents);
	}

	public static function xmlSafeStr($str) {
		return '<![CDATA[' . preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/", '', $str) . ']]>';
	}

	/**
	 * 数据XML编码
	 * @param mixed $data 数据
	 * @return string
	 */
	public static function data_to_xml($data) {
		$xml = '';
		foreach ($data as $key => $val) {
			is_numeric($key) && $key = "item id=\"$key\"";
			$xml .= "<$key>";
			$xml .= (is_array($val) || is_object($val)) ? self::data_to_xml($val) : self::xmlSafeStr($val);
			list($key, ) = explode(' ', $key);
			$xml .= "</$key>";
		}
		return $xml;
	}

	/**
	 * XML编码
	 * @param mixed $data 数据
	 * @param string $root 根节点名
	 * @param string $item 数字索引的子节点名
	 * @param string $attr 根节点属性
	 * @param string $id   数字索引子节点key转换的属性名
	 * @param string $encoding 数据编码
	 * @return string
	 */
	public function xml_encode($data, $root = 'xml', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8') {
		if (is_array($attr)) {
			$_attr = array();
			foreach ($attr as $key => $value) {
				$_attr[] = "{$key}=\"{$value}\"";
			}
			$attr = implode(' ', $_attr);
		}
		$attr = trim($attr);
		$attr = empty($attr) ? '' : " {$attr}";
		$xml = "<{$root}{$attr}>";
		$xml .= self::data_to_xml($data, $item, $id);
		$xml .= "</{$root}>";
		return $xml;
	}

	/**
	 * GET 请求
	 * @param string $url
	 */
	private function http_get($url) {
		$oCurl = curl_init();
		if (stripos($url, "https://") !== FALSE) {
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if (intval($aStatus["http_code"]) == 200) {
			return $sContent;
		} else {
			return false;
		}
	}

	/**
	 * POST 请求
	 * @param string $url
	 * @param array $param
	 * @return string content
	 */
	public function http_post($url,$param='') {
		$oCurl = curl_init();
		if (stripos($url, "https://") !== FALSE) {
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
		}
		if (is_string($param)) {
			$strPOST = $param;
		} else {
			$aPOST = array();
			foreach ($param as $key => $val) {
				$aPOST[] = $key . "=" . urlencode($val);
			}
			$strPOST = join("&", $aPOST);
		}
        //file_put_contents('./mylog.txt',$url."\r\n".$strPOST."\r\n".date('Y-m-d H:i:s')."\r\n==============\r\n",FILE_APPEND);
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($oCurl, CURLOPT_POST, true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if (intval($aStatus["http_code"]) == 200) {
			return $sContent;
		} else {
			return false;
		}
	}

	/**
	 * 微信api不支持中文转义的json结构
	 * @param array $arr
	 */
	static function json_encode($arr) {
		$parts = array();
		$is_list = false;
		//Find out if the given array is a numerical array
		$keys = array_keys($arr);
		$max_length = count($arr) - 1;
		if (($keys[0] === 0) && ($keys[$max_length] === $max_length)) {//See if the first key is 0 and last key is length - 1
			$is_list = true;
			for ($i = 0; $i < count($keys); $i++) {//See if each key correspondes to its position
				if ($i != $keys[$i]) {//A key fails at position check.
					$is_list = false;
					//It is an associative array.
					break;
				}
			}
		}
		foreach ($arr as $key => $value) {
			if (is_array($value)) {//Custom handling for arrays
				if ($is_list)
					$parts[] = self::json_encode($value);
				/* :RECURSION: */
				else
					$parts[] = '"' . $key . '":' . self::json_encode($value);
				/* :RECURSION: */
			} else {
				$str = '';
				if (!$is_list)
					$str = '"' . $key . '":';
				//Custom handling for multiple data types
				if (is_numeric($value) && $value < 2000000000)
					$str .= $value;
				//Numbers
				elseif ($value === false)
					$str .= 'false';
				//The booleans
				elseif ($value === true)
					$str .= 'true';
				else
					$str .= '"' . addslashes($value) . '"';
				//All other things
				// :TODO: Is there any more datatype we should be in the lookout for? (Object?)
				$parts[] = $str;
			}
		}
		$json = implode(',', $parts);
		if ($is_list)
			return '[' . $json . ']';
		//Return numerical JSON
		return '{' . $json . '}';
		//Return associative JSON

	}

	/**通用post提交数据
	 *
	 * */
	public function authpost($url, $data) {
		if (!$this -> access_token && !$this -> checkAuth())
			return false;
		// echo $this->access_token;
		$result = $this -> http_post($url . 'access_token=' . $this -> access_token, self::json_encode($data));
		dump($result);
		//exit ;
		if ($result) {
			$json = json_decode($result, true);
			if (!$json || !empty($json['errcode'])) {
				$this -> errCode = $json['errcode'];
				$this -> errMsg = $json['errmsg'];
				return false;
			}
			return $json;
		}
		return false;
	}

	/*
	 * 获取第三方平台令牌（component_access_token）
	 * 数据实例
	 * {
	 "component_appid":"appid_value" ,
	 "component_appsecret": "appsecret_value",
	 "component_verify_ticket": "ticket_value"
	 }
	 * */
	public function get_access_token() {
		
        $cache = file::read('./cache/component_access_token.json');
		
		$cache = json_decode($cache, true);
		
		if ($cache['expires_in'] > time()) {
			$this -> component_access_token = $cache['component_access_token'];
			return $cache['component_access_token'];
		}
		
        $result = $this -> http_post(self::API_URL_PREFIX . self::API_COMPONENT_TOKEN_URL, json_encode($this -> options));
		//print_r($result);exit;
        if ($result) {
			$json = json_decode($result, true);
			if (!$json || !empty($json['errcode'])) {
				$this -> errCode = $json['errcode'];
				$this -> errMsg = $json['errmsg'];
				return false;
			}
			$this -> component_access_token = $json['component_access_token'];
			//写入文件来设置缓存
			file::write('./cache/component_access_token.json', json_encode(array('component_access_token' => $json['component_access_token'], 'expires_in' => time() + 3580)));
			return $this -> component_access_token;
		}
		return false;
	}

	/*
	 * 获取预授权码
	 * {
	 "component_appid":"appid_value"
	 * }
	 * */
	public function get_auth_code() {

		$component_access_token = $this -> getAccessToken();
		
		/*$cache = file::read('./cache/apre_auth_code.json');
		//读取缓存
		$cache = json_decode($cache, true);
		if ($cache['expires_in'] > time()) {

			return $cache['pre_auth_code'];
		}
		*/
		$result = $this -> http_post(self::API_URL_PREFIX . self::API_CREATE_PREAUTHCODE_URL . $component_access_token, json_encode(array('component_appid' => $this -> appid)));
		
		if ($result) {
			$json = json_decode($result, true);
			if (!$json || !empty($json['errcode'])) {
				$this -> errCode = $json['errcode'];
				$this -> errMsg = $json['errmsg'];
				return false;
			}

			//写入文件来设置缓存
		/*	file::write('./cache/apre_auth_code.json', json_encode(array('pre_auth_code' => $json['pre_auth_code'], 'expires_in' => time() + 500)));
			return $json['pre_auth_code'];*/
		}
		return false;
	}

	/*
	 * 通过返回授权码换取公众号的授权信息
	 * $auth_code  授权返回
	 * */
	public function get_authorization_info($auth_code) {
		$var=qscms::v('_G');
		$member=$var->member;
		$component_access_token = $this -> getAccessToken();//echo $component_access_token;exit;
		
		
		$result = $this -> http_post(self::API_URL_PREFIX . self::API_QUERY_AUTH_URL . $component_access_token, json_encode(array('component_appid' => $this -> appid, 'authorization_code' => $auth_code)));
	
        if ($result) {
			$json = json_decode($result, true);//print_r($json);exit;
			if (!$json || !empty($json['errcode'])) {
				//echo 12311;exit;
				$this -> errCode = $json['errcode'];
				$this -> errMsg = $json['errmsg'];
				return false;
			}

			$json = $json['authorization_info'];
			$authorizer_appid=$json['authorizer_appid'];
		/*	if(db::one_one('user',"authorizer_appid","id='$member->m_id'")==''){
				db::update('user',"authorizer_appid='$authorizer_appid'","id='$member->m_id'");
				}*/
			
				$info = $this -> http_post(self::API_URL_PREFIX . self::API_GET_AUTHORIZER_INFO_URL . $component_access_token, json_encode(array('component_appid' => $this -> appid, 'authorizer_appid' => $authorizer_appid)));
		//print_r($result);exit;
		if ($info) {
			$info = json_decode($info, true);
			}
			$nickname = $info['authorizer_info']['nick_name'] ?$info['authorizer_info']['nick_name']:"名称获取失败,请个人中心取消授权并重新授权";
			$or_id =$info['authorizer_info']['user_name'];
			
			if(!db::exists('vx_account',"uid='$member->m_id' and authorizer_appid='$authorizer_appid'")){
				db::update("vx_account","useing=0","uid='$member->m_id'");
				$in  = array("uid"=>$member->m_id,"name"=>$nickname,"authorizer_appid"=>$authorizer_appid,"useing"=>1,'or_id'=>$or_id);
				db::insert('vx_account',$in);
			}else{
				db::update("vx_account","useing=0","uid='$member->m_id'");
				db::update("vx_account","useing=1","uid='$member->m_id' and authorizer_appid='$authorizer_appid'");
				}
			file::write('./cache/authorizer_access_token/' . $json['authorizer_appid'] . '.json', json_encode(array('authorizer_access_token' => $json['authorizer_access_token'], 'authorizer_refresh_token' => $json['authorizer_refresh_token'], 'expires_in' => time() + 3580)));
			return $json;
		}
		return false;
	}

    /*
         * 获取授权公众号的authorizer_access_token
         * */
    public function get_authorizer_access_token($authorizer_appid) {
    // echo d('./cache/authorizer_access_token/'.$authorizer_appid.'.json');exit;
	if(!file_exists(d('./cache/authorizer_access_token/'.$authorizer_appid.'.json'))){
		
		return false;
		}
        $cache = file_get_contents(d('./cache/authorizer_access_token/'.$authorizer_appid.'.json'));
		//echo $cache.'1';exit;
        //读取缓存
        $cache = json_decode($cache, true);
        if ($cache['expires_in'] > time()) {
            $authorizer_access_token = $cache['authorizer_access_token'];
        } else {
            $authorizer_refresh_token = $cache['authorizer_refresh_token'];
			
            $authorizer_access_token = $this -> get_refresh_token($authorizer_appid,$authorizer_refresh_token);
        }
	  
        return $authorizer_access_token;
    }

	/*
	 * 获取（刷新）授权公众号的令牌
	 * 该API用于在授权方令牌（authorizer_access_token）失效时，可用刷新令牌（authorizer_refresh_token）获取新的令牌。
	 * */
	public function get_refresh_token($authorizer_appid, $authorizer_refresh_token) {

	
		$component_access_token = $this -> getAccessToken();
		$result = winsock::open(self::API_URL_PREFIX . self::API_AUTHORIZER_TOKEN_URL . $component_access_token, json_encode(array('component_appid' => $this -> appid, 'authorizer_appid' => $authorizer_appid, 'authorizer_refresh_token' => $authorizer_refresh_token)));
		
		if ($result) {
			$json = json_decode($result, true);
			if (!empty($json['errcode']) && $json['errcode']>0) {
				$this -> errCode = $json['errcode'];
				$this -> errMsg = $json['errmsg'];
				return false;
			}
			
			file::write('./cache/authorizer_access_token/' . $authorizer_appid . '.json', json_encode(array('authorizer_access_token' => $json['authorizer_access_token'], 'authorizer_refresh_token' => $json['authorizer_refresh_token'], 'expires_in' => time() + 3580)));
			
			return $json['authorizer_access_token'];
		}
		return false;
	}

	/*
	 * 获取授权方的账户信息
	 该API用于获取授权方的公众号基本信息，包括头像、昵称、帐号类型、认证类型、微信号、原始ID和二维码图片URL。
	 * */
	public function get_authorizer_info($authorizer_appid) {
		$var=qscms::v('_G');
		$member=$var->member;
		if(db::exists('update_authorizer_base_info',"uid='$member->m_id' and 'authorizer_appid'='$authorizer_appid'")){
				return string::json_decode(db::one_one('update_authorizer_base_info','base_info',"uid='$member->m_id' and 'authorizer_appid'='$authorizer_appid'"));
				}
		
		$component_access_token = $this -> getAccessToken();//echo $component_access_token;exit;
		//echo $authorizer_appid;exit;
		$result = $this -> http_post(self::API_URL_PREFIX . self::API_GET_AUTHORIZER_INFO_URL . $component_access_token, json_encode(array('component_appid' => $this -> appid, 'authorizer_appid' => $authorizer_appid)));
		//print_r($result);exit;
		if ($result) {
			$json = json_decode($result, true);
			if (!$json || !empty($json['errcode'])) {
				$this -> errCode = $json['errcode'];
				$this -> errMsg = $json['errmsg'];
				return false;
			}
			//print_r($json);exit;
			if(!db::exists('update_authorizer_base_info',"uid='$member->m_id' and 'authorizer_appid'='$authorizer_appid'")){
				db::insert('update_authorizer_base_info',array('base_info'=>$result,'uid'=>$member->m_id,'authorizer_appid'=>$authorizer_appid));
				}
			
			return $json;
		}
		return false;
	}

	/*
	 * 获取授权方的选项设置信息
	 * */
	public function get_authorizer_option($authorizer_appid, $option_name) {

		$component_access_token = $this -> getAccessToken();
		$result = $this -> http_post(self::API_URL_PREFIX . self::API_GET_AUTHORIZER_OPTION_URL . $component_access_token, json_encode(array('component_appid' => $this -> appid, 'authorizer_appid' => $authorizer_appid, 'option_name' => $option_name)));
		if ($result) {
			$json = json_decode($result, true);
			if (!$json || !empty($json['errcode'])) {
				$this -> errCode = $json['errcode'];
				$this -> errMsg = $json['errmsg'];
				return false;
			}
			
			return $json;
		}
		return false;
	}

	/*
	 * 获取授权方的选项设置信息
	 * */
	public function set_authorizer_option($authorizer_appid, $option_name, $option_value) {

		
		$component_access_token = $this -> getAccessToken();

		$result = $this -> http_post(self::API_URL_PREFIX . self::API_SET_AUTHORIZER_OPTION_URL . $component_access_token, json_encode(array('component_appid' => $this -> appid, 'authorizer_appid' => $authorizer_appid, 'option_name' => $option_name, 'option_value' => $option_value)));
		if ($result) {
			$json = json_decode($result, true);
			if (!$json || !empty($json['errcode'])) {
				$this -> errCode = $json['errcode'];
				$this -> errMsg = $json['errmsg'];
				return false;
			}
			
			return $json;
		}
		return false;
	}

	/*
	 * 获取跳转链接
	 * */
	public function getRedirect($callback, $pre_auth_code) {
		return self::API_REDIRECT . 'component_appid=' . $this -> appid . '&pre_auth_code=' . $pre_auth_code . '&redirect_uri=' . urlencode($callback);
	}

	/*
	 * 获取生成的token
	 * */
	public function getAccessToken() {

		$cache = file::read('./cache/component_access_token.json');
		
		
		
		$cache = json_decode($cache, true);
		
		if (!empty($cache) && $cache['expires_in'] > time()) {
			//echo $cache['expires_in'] > time()?1:0;exit;
			$component_access_token = $cache['component_access_token'];
		} else {
			
			$component_access_token = $this -> get_access_token();
		}
		
		return $component_access_token;
	}
	
	
	/**
	 * 批量获取关注用户列表
	 * @param unknown $next_openid
	 */
	public function getUserList($authorizer_access_token,$next_openid=''){
		//echo $authorizer_access_token;exit;
		if($next_openid){
			$result = $this->http_get(self::API_URL_PREFIX.self::USER_GET_URL.'access_token='.$authorizer_access_token.'&next_openid='.$next_openid);//echo 1;exit;
		}else{
			$result = $this->http_get(self::API_URL_PREFIX.self::USER_GET_URL.'access_token='.$authorizer_access_token);
			//print_r($result);exit;
		}
		if ($result)
		{
			$json = json_decode($result,true);
			if (isset($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			return $json;
		}
		return false;
	}
	function curl_grab_page($url, $data, $proxy = '', $proxystatus = '', $ref_url = '') {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if ($proxystatus == 'true') {
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
		} 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		if (!empty($ref_url)) {
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_REFERER, $ref_url);
		} 
		if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
			curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		} 
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		ob_start();
		return curl_exec ($ch);
		ob_end_clean();
		curl_close ($ch);
		unset($ch);
	} 
	function https_request($url, $data = null){
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
function up_image($data,$authorizer_access_token){
	$sd_pic= str_replace('http://qulanhai.com','',$data);//echo $sd_pic;exit;
			$data=d('.'.$sd_pic);//echo $data;exit;
	if (!file_exists($data)){echo '文件不存在' ;exit;}//  文件不存在的判定
			$filedata=array("media"=>"@".$data);
			//echo $filedata;exit;
			if(strlen($authorizer_access_token) >= 64) 
				{
					$url = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$authorizer_access_token;					
					$res_json =$this -> https_request($url, $filedata);
					$json = string::json_decode($res_json);	
					
				}
				$img_url='';
				!empty($json['url']) && $img_url = $json['url'];
				return $img_url;
	}
function up_image_doorstore($data,$authorizer_access_token){
	$sd_pic= str_replace('http://qulanhai.com','',$data);//echo $sd_pic;exit;
			$data=d('.'.$sd_pic);//echo $data;exit;
	if (!file_exists($data)){echo '文件不存在' ;exit;}//  文件不存在的判定
			$filedata=array("media"=>"@".$data);
			//echo $filedata;exit;
			if(strlen($authorizer_access_token) >= 64) 
				{
					$url = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$authorizer_access_token;					$res_json =$this -> https_request($url, $filedata);
					$json = string::json_decode($res_json);	
					
				}
				$img_url = $json['url'];
				  // "photo_list":[{"photo_url":"https:// XXX.com"}，{"photo_url":"https://XXX.com"}],
				return '{"photo_url":"'.$img_url.'"}';
	}	
	
function create_group($authorizer_access_token,$groupname){
	$str='{"group":{"name":"'.$groupname.'"}}';
	$posturl='https://api.weixin.qq.com/cgi-bin/groups/create?access_token='.$authorizer_access_token;
	$r=$this->http_post($posturl,$str);
	$res=string::json_decode($r);
	$return =!empty($res['errcode']) && $res['errcode']>0?'错误码：'.$res['errcode']:true;
	return $return;
	}
	
function get_group($authorizer_access_token){
	
	$posturl='https://api.weixin.qq.com/cgi-bin/groups/get?access_token='.$authorizer_access_token;
	$r=$this->http_post($posturl);
	$res=string::json_decode($r);
	if(!empty($res['errcode']))return false;
	return $res['groups'];
	}	
function del_group($authorizer_access_token,$groupid){
	$str='{"group":{"id":'.$groupid.'}}';
	$posturl='https://api.weixin.qq.com/cgi-bin/groups/delete?access_token='.$authorizer_access_token;
	$r=$this->http_post($posturl,$str);
	$res=string::json_decode($r);
	$return =$res['errcode']?false:true;
	return $return;
	}
function change_groupname($authorizer_access_token,$groupid,$newname){
	$str='{"group":{"id":'.$groupid.',"name":"'.$newname.'"}}';
	$posturl='https://api.weixin.qq.com/cgi-bin/groups/update?access_token='.$authorizer_access_token;
	$r=$this->http_post($posturl,$str);//echo $r;exit;
	$res=string::json_decode($r);
	$return =$res['errcode']>0?'错误码：'.$res['errcode']:true;
	return $return;
	}
	function change_to_new_group($authorizer_access_token,$openids,$groupid){
	
		$rs=implode('","',$openids);
		$str ='{"openid_list":["'.$rs.'"],"to_groupid":'.$groupid.'}';
		//echo $str;exit;
		$posturl='https://api.weixin.qq.com/cgi-bin/groups/members/batchupdate?access_token='.$authorizer_access_token;
		$r=$this->http_post($posturl,$str);
		$res=string::json_decode($r);
	$return =$res['errcode']>0?'错误码：'.$res['errcode']:true;
	return $return;
}
	//	增加客服
	function add_kf($authorizer_appid,$authorizer_access_token,$kfname,$kf_account,$kf_pwd,$vx_name,$toux){//第二个参数是客服昵称，三位客服工号账号
		$var=qscms::v('_G');
		$member=$var->member;
		$sd_pic= str_replace('http://qulanhai.com','',$toux);//echo $sd_pic;exit;
			$data=d('.'.$sd_pic);//echo $data;exit;
		if (!file_exists($data)){echo '文件不存在' ;exit;}//  文件不存在的判定
		
		$filedata=array("media"=>"@".$data);
			
			
				/*$img_url = $json['url'];
				return $img_url;*/
	
		$str='{ "kf_account" : "'.$kf_account.'@'.$vx_name.'","nickname" : "'.$kfname.'","password" : "'.md5($kf_pwd).'"}';
		//echo $str;exit;
		/*<!--上传头像-->*/
		$posturl='https://api.weixin.qq.com/customservice/kfaccount/add?access_token='.$authorizer_access_token;
		$r=$this->http_post($posturl,$str);
		$res=string::json_decode($r);
		//print_r($res);exit;
		$insert=array('uid'=>$member->m_id,'kf_pwd'=>$kf_pwd,'kf_toux'=>$toux,"kf_nickname"=>$kfname,'kf_vx_orderby'=>$vx_name,'kf_account'=>$kf_account,'authorizer_appid'=>$authorizer_appid);
		
		$return =$res['errcode']>0?'错误码：'.$res['errcode']:true;
		if($return=true){
			if(strlen($authorizer_access_token) >= 64) 
				{
					$url = 'http://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token='.									$authorizer_access_token.'&kf_account='.$kf_account.'@'.$vx_name;						
					$res_json =$this -> https_request($url, $filedata);
					$json = string::json_decode($res_json);	
					
				}
				//print_r($json);exit;
			db::insert('vx_kf',$insert);
			
			}else{
				unlink($data);
				}
		return $return;
		}
		
		//上传客服头像
	function add_kf_toux($authorizer_access_token,$kf_account,$toux,$vx_name){
		$sd_pic= str_replace('http://qulanhai.com','',$toux);//echo $sd_pic;exit;
			$data=d('.'.$sd_pic);//echo $data;exit;
		if (!file_exists($data)){echo '文件不存在' ;exit;}//  文件不存在的判定
		
		$filedata=array("media"=>"@".$data);
		
		$url = 'http://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token='.									$authorizer_access_token.'&kf_account='.$kf_account.'@'.$vx_name;	
		//echo $url;exit;				
					$res_json =$this -> https_request($url, $filedata);
					$json = string::json_decode($res_json);	
					//print_r($json);exit;
		}
		//删除客服
		function del_kf($authorizer_appid,$authorizer_access_token,$kf_account,$vx_name){
		$str=$kf_account.'@'.$vx_name;
		//echo $str;exit;
		$posturl="https://api.weixin.qq.com/customservice/kfaccount/del?access_token=$authorizer_access_token&kf_account=$str";		//echo $posturl;exit;
		$r=$this->http_post($posturl);
		$res=string::json_decode($r);
		//print_r($res);exit;
		$return =$res['errcode']>0?'错误码：'.$res['errcode']:true;
		if($return=true){
			$the_toux=db::one_one('vx_kf','kf_toux',"kf_account='$kf_account' and authorizer_appid='$authorizer_appid'");
			$the_img=array_pop(explode('/',$the_toux));
			db::delete('vx_kf',"kf_account='$kf_account' and authorizer_appid='$authorizer_appid'");
			unlink($saveDir  = d(qscms::getCfgPath('/system/imgRoot')).'vx_kf/'.$the_img);
		}
		return $return;
		}
		
	//查看现有客服
	function show_kf($authorizer_access_token){
		$posturl='https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token='.$authorizer_access_token;
		$r=$this->http_post($posturl);
		$res=string::json_decode($r);
		//print_r($res);exit;
		$return =$res['errcode']>0?'错误码：'.$res['errcode']:true;
		return $return;
		}
	//查看客服在线状态
	function kf_online_info($authorizer_appid,$authorizer_access_token){
		$var=qscms::v('_G');
		$member=$var->member;
		$posturl='https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token='.$authorizer_access_token;
		//echo $posturl;exit;
		$r=winsock::open($posturl);//echo $r;exit;
		$res=string::json_decode($r);
		if(empty($res['errcode'])){
			$arr=array();//print_r($res['kf_online_list']);exit;
			foreach($res['kf_online_list'] as $k=>$v){
				
				$the_account=array_shift(explode('@',$v['kf_account']));
				if(db::exists('vx_kf',"uid='$member->m_id' and kf_account='$the_account' and authorizer_appid='$authorizer_appid'")){
					$this_nickname=db::one_one('vx_kf','kf_nickname',"uid='$member->m_id' and kf_account='$the_account' and authorizer_appid='$authorizer_appid'");
					$this_toux=db::one_one('vx_kf','kf_toux',"uid='$member->m_id' and kf_account='$the_account' and authorizer_appid='$authorizer_appid'");
					$v['kf_nickname']=$this_nickname;
					$v['kf_toux']=$this_toux;
					}
				$arr[]=$v;	
				}
				echo string::json_encode($arr);exit;
			}
		//$return =$res['errcode']>0?'错误码：'.$res['errcode']:$r;
		print_r($r);exit;
		}
	//获取聊天记录
	function get_kf_chat($authorizer_access_token,$start,$end,$page,$authorizer_appid){
		//$page=1;$end=time::$timestamp;echo time::$todayStart;echo '+'.$start;exit;
		$var=qscms::v('_G');
		$member=$var->member;
		$poststr='{
				"endtime" : '.$end.',
				"pageindex" : '.$page.',
				"pagesize" : 50,
				"starttime" : '.$start.'
					 }';
				//echo $poststr;exit;	
 		$posturl='https://api.weixin.qq.com/customservice/msgrecord/getrecord?access_token='.$authorizer_access_token;
		$r=$this->http_post($posturl,$poststr);//echo $r;exit;
		$res=string::json_decode($r);
		if($res['errcode']>0){
			
			}else{
			//echo string::hex_str(db::one_one('vx_fans','fans',"uid='$member->m_id'"));exit;
			//print_r($res['kf_online_list']);
			$fansjson=string::hex_str(db::one_one('vx_fans','fans',"uid='$member->m_id' and authorizer_appid='$authorizer_appid'"));
			$fansjson2=explode('||',$fansjson);
			array_pop($fansjson2);
			//print_r($fansjson2);exit;
			$arr0=array();
			foreach($fansjson2 as $v){
				//echo $v;exit;
			$arr0[]=string::json_decode($v);
				}
			function a21($key,$val,$arr){
					$rn=array();
					foreach($arr as $v){
						$rn+=array($v[$key]=>$v[$val]);
					}
					return $rn;
				}
			$new_oid_nname=a21('openid','nickname',$arr0);
			$new_oid_toux=a21('openid','headimgurl',$arr0);
			$arr=array();//print_r($res);exit;
			foreach($res['recordlist'] as $k=>$v){
				
				if($v['opercode']>2000){
					
					$this_nickname=$new_oid_nname[$v['openid']];//echo $this_nickname;exit;
					$this_toux=$new_oid_toux[$v['openid']];
					$v['nickname']=$this_nickname;
					$v['toux']=$this_toux;
					$arr[]=$v;
					ksort($v);
					}
				//$the_account=array_shift(explode('@',$v['kf_account']));
				
				
				
				}
				if(!empty($arr)){
					  foreach ($arr as $key => $row)  
				   {  
					   $vals[$key] = $row['time'];  
					   $nums[$key] = $row['nickname'];  
				   } 
				  	array_multisort($vals,SORT_ASC,$arr);
					//$arr=array_reverse($arr);
					array_multisort($nums,SORT_ASC,$arr);
					
					}
				
					//print_r($arr);exit;
				echo string::json_encode($arr);exit;
			
			}
		echo $r;exit;
		}
	//获取二维码ticket
	function get_qcode_ticket($authorizer_access_token){
		$scene_id=rand(1,1000000);
		$post_str='{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$scene_id.'}}}';
		$post_url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$authorizer_access_token;
		$rs= string::json_decode(winsock::open($post_url,$post_str));
		if($rs['errcode']){
			$r=array('errcode'=>$rs['errcode'],'errmsg'=>$rs['errmsg']);
		}else{
			$r=array('ticket'=>$rs['ticket'],'scenc_id'=>$scenc_id,'url'=>$rs['url']);
		}
		echo string::json_encode($r);
	}	
	
}
