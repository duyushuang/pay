<?php
/**
 * @copyright Copyright (C) 2011 www.qscms.com. All rights reserved.
 */
 
(!defined('IN_QSCMS') || IN_QSCMS !== true ) && exit('error');
if (!function_exists('gzdecode')) { 
	function gzdecode($data,&$filename='',&$error='',$maxlength=null) {
		$len = strlen($data);
		if ($len < 18 || strcmp(substr($data,0,2),"\x1f\x8b")) {
			$error = "Not in GZIP format.";
			return null;  // Not GZIP format (See RFC 1952)
		}
		$method = ord(substr($data,2,1));  // Compression method
		$flags  = ord(substr($data,3,1));  // Flags
		if ($flags & 31 != $flags) {
			$error = "Reserved bits not allowed.";
			return null;
		}
		// NOTE: $mtime may be negative (PHP integer limitations)
		$mtime = unpack("V", substr($data,4,4));
		$mtime = $mtime[1];
		$xfl   = substr($data,8,1);
		$os    = substr($data,8,1);
		$headerlen = 10;
		$extralen  = 0;
		$extra     = "";
		if ($flags & 4) {
			// 2-byte length prefixed EXTRA data in header
			if ($len - $headerlen - 2 < 8) {
				return false;  // invalid
			}
			$extralen = unpack("v",substr($data,8,2));
			$extralen = $extralen[1];
			if ($len - $headerlen - 2 - $extralen < 8) {
				return false;  // invalid
			}
			$extra = substr($data,10,$extralen);
			$headerlen += 2 + $extralen;
		}
		$filenamelen = 0;
		$filename = "";
		if ($flags & 8) {
			// C-style string
			if ($len - $headerlen - 1 < 8) {
				return false; // invalid
			}
			$filenamelen = strpos(substr($data,$headerlen),chr(0));
			if ($filenamelen === false || $len - $headerlen - $filenamelen - 1 < 8) {
				return false; // invalid
			}
			$filename = substr($data,$headerlen,$filenamelen);
			$headerlen += $filenamelen + 1;
		}
		$commentlen = 0;
		$comment = "";
		if ($flags & 16) {
			// C-style string COMMENT data in header
			if ($len - $headerlen - 1 < 8) {
				return false;    // invalid
			}
			$commentlen = strpos(substr($data,$headerlen),chr(0));
			if ($commentlen === false || $len - $headerlen - $commentlen - 1 < 8) {
				return false;    // Invalid header format
			}
			$comment = substr($data,$headerlen,$commentlen);
			$headerlen += $commentlen + 1;
		}
		$headercrc = "";
		if ($flags & 2) {
			// 2-bytes (lowest order) of CRC32 on header present
			if ($len - $headerlen - 2 < 8) {
				return false;    // invalid
			}
			$calccrc = crc32(substr($data,0,$headerlen)) & 0xffff;
			$headercrc = unpack("v", substr($data,$headerlen,2));
			$headercrc = $headercrc[1];
			if ($headercrc != $calccrc) {
				$error = "Header checksum failed.";
				return false;    // Bad header CRC
			}
			$headerlen += 2;
		}
		// GZIP FOOTER
		$datacrc = unpack("V",substr($data,-8,4));
		$datacrc = sprintf('%u',$datacrc[1] & 0xFFFFFFFF);
		$isize = unpack("V",substr($data,-4));
		$isize = $isize[1];
		// decompression:
		$bodylen = $len-$headerlen-8;
		if ($bodylen < 1) {
			// IMPLEMENTATION BUG!
			return null;
		}
		$body = substr($data,$headerlen,$bodylen);
		$data = "";
		if ($bodylen > 0) {
			switch ($method) {
			case 8:
				// Currently the only supported compression method:
				$data = gzinflate($body,$maxlength);
				break;
			default:
				$error = "Unknown compression method.";
				return false;
			}
		}  // zero-byte body content is allowed
		// Verifiy CRC32
		$crc   = sprintf("%u",crc32($data));
		$crcOK = $crc == $datacrc;
		$lenOK = $isize == strlen($data);
		if (!$lenOK || !$crcOK) {
			$error = ( $lenOK ? '' : 'Length check FAILED. ') . ( $crcOK ? '' : 'Checksum FAILED.');
			return false;
		}
		return $data;
	}
}
if (!function_exists('fsockopen')) {
	function _fsockopen($hostname, $port, &$error = '', &$errstr = '', $timeout = 50){
		return stream_socket_client($hostname.':'.$port, $error, $errstr, $timeout);
	}
} else {
	function _fsockopen($hostname, $port, &$error = '', &$errstr = '', $timeout = 50){
		return fsockopen($hostname, $port, $error, $errstr, $timeout);
	}
}
class http_socket{
	private $urlInfo, $gzip, $head, $proxyIp, $proxyPort, $proxy;
	public function __construct($url, $proxyInfo = false){
		$urlInfo = self::parseUrl($url, $gzip = false);
		if (!self::isIp($urlInfo['host'])) {
			$urlInfo['ip'] = gethostbyname($urlInfo['host']);
			$urlInfo['ip'] == $urlInfo['host'] && $urlInfo['ip'] = '';
		} else {
			$urlInfo['ip'] = $urlInfo['host'];
		}
		$urlInfo['openUrl'] = $urlInfo['ip'] ? (($urlInfo['scheme'] == 'https'?'ssl://':'').$urlInfo['ip']) : '';
		$this->urlInfo = $urlInfo;
		$this->gzip = $gzip;
		$this->head = false;
		if ($proxyInfo) {
			//$this->proxy($proxyIp, $proxyPort, $this->urlInfo['host'].':'.$this->urlInfo['port']);
			list($proxyIp, $proxyPort) = explode(':', $proxyInfo);
			$this->proxy = true;
			$this->proxyIp = $proxyIp;
			$this->proxyPort = $proxyPort;
		} else $this->proxy = false;
	}
	private function proxy($ip, $port, $host){
		$sendStr = "CONNECT $host HTTP/1.1
Host: $host
Proxy-Connection: Keep-Alive
Proxy-Authorization: Basic ".base64_encode('123:123')."
Content-Length: 0

";
$sendStr = "GET http://www.baidu.com/s?ie=utf-8&f=8&rsv_bp=1&tn=monline_5_dg&wd=%E6%88%91%E7%9A%84IP&rsv_pq=df9dfcf40000012b&rsv_t=05bepOgpc82ENVCWYzG3x9oHxL%2BgD38N%2F%2FvaGyZUL6pwqmZQFDGa9w84vCdJNxnL4e5S&rsv_enter=1&rsv_sug3=8&rsv_sug4=406&rsv_sug1=7&rsv_sug2=0&inputT=10706 HTTP/1.1
Host: www.baidu.com
Content-Length: 0

";
		if ($f = @_fsockopen($ip, $port, $error, $errstr, 50)) {
			fwrite($f, $sendStr);
			$readData = '';
			while(is_resource($f) && ($r = fread($f, 1024)) ){
				$readData .= $r;
				echo $readData;
			}
		}
	}
	public function open($path = false, $postFlag = false, $args = array(), $saveFile = ''){
		$ln = chr(13).chr(10);
		if ($this->urlInfo['openUrl']) {
			$path || $path = $this->urlInfo['path'];
			$headStr = '';
			$head = array();
			$getHead=false;
			$chunked = $gzip = false;
			$chunksize = 0;
			$readSize = 0;
			$rsize = 1024;
			$thisSize=0;
			$contentLength = 0;
			$leaveSize = 0;
			$html = '';
			//Transfer-Encoding: chunked
			if (empty($args['Accept-Encoding']) && ZLIB === true && $this->gzip) $args['Accept-Encoding'] = 'gzip';
			$save = false;
			if($saveFile && ($saveF = @fopen($saveFile,'wb'))) $save = true;
			if ($this->proxy) {
				$f = @_fsockopen($this->proxyIp, $this->proxyPort, $errno, $errstr, 50);
				$path = $this->urlInfo['scheme'].'://'.$this->urlInfo['host'].$path;
			} else {
				$f = @_fsockopen($this->urlInfo['openUrl'], $this->urlInfo['port'], $errno, $errstr, 50);
			}
			if ($f) {
				$out = '';
				$out = ($postFlag ? 'POST' : 'GET').' '.$path.' HTTP/1.1'.$ln;
				$out.= 'Host: '.$this->urlInfo['host'].$ln;
				$out.= self::args($args);
				if ($postFlag) {
					if (!is_array($postFlag)) {
						$postFlag = array(
							'type' => 'text',
							'data' => $postFlag
						);
					}
					$postLength = 0;
					switch ($postFlag['type']) {
						case 'file':
							$postLength = filesize($postFlag['data']);
							$out.='Content-Type: multipart/form-data; boundary=---------------------------7dc2ce5b0ace'.$ln;
						break;
						default:
							$out.='Content-Type: application/x-www-form-urlencoded'.$ln;
							$postLength = strlen($postFlag['data']);
						break;
					}
					//$out.='Content-Length: '.$postLength.$ln;
				}
				//$out.='Connection: Close'.$ln;
				//$out.=$ln;
				fwrite($f, $out);
				if ($postFlag) {
					switch ($postFlag['type']) {
						case 'file':
							$postDataPrefix = '-----------------------------7dc2ce5b0ace
Content-Disposition: form-data; name="'.$postFlag['varName'].'"; filename="'.$postFlag['data'].'"
Content-Type: application/octet-stream

';
							$postDataSuffix = '
-----------------------------7dc2ce5b0ace--
';
							$postLength += strlen($postDataPrefix);
							$postLength += strlen($postDataSuffix);
							fwrite($f, 'Content-Length: '.$postLength.$ln);
							fwrite($f, 'Connection: Close'.$ln);
							fwrite($f, $ln);
							//fwrite($f, $postDataPrefix.file::read($postFlag['data']).$postDataSuffix);
							fwrite($f, $postDataPrefix);
							if ($__f = fopen($postFlag['data'], 'rb')) {
								while ($__r = fread($__f, 1024 * 100)) {
									fwrite($f, $__r);
								}
								fclose($__f);
							}
							fwrite($f, $postDataSuffix);
						break;
						default:
							fwrite($f, 'Content-Length: '.$postLength.$ln);
							fwrite($f, 'Connection: Close'.$ln);
							fwrite($f, $ln);
							fwrite($f, $postFlag['data']);
						break;
					}
				} else {
					fwrite($f, 'Connection: Close'.$ln);
					fwrite($f, $ln);
				}
				//$out.=$post_data;
				//fwrite($f, $out);
				while(is_resource($f) && ($r = fread($f, $rsize)) ){
					$thisSize = strlen($r);
					if(!$getHead){
						if(($fa=strpos($r,$ln.$ln))===false)$headStr.=$r;
						else {
							$headStr.=substr($r,0,$fa);
							$this->head = new http_head($headStr);
							if($this->head->status){//2xx
								!is_null($this->head->content_length) && $contentLength = intval($this->head->content_length);//file size
								if($this->head->content_encoding == 'gzip' && ZLIB === true)$gzip = true;
								if($this->head->transfer_encoding == 'chunked'){
									$chunked = true;
									$html    = substr($r, $fa+4);
									$readSize += $thisSize - $fa - 4;
									if ($contentLength) {
										$leaveSize = $contentLength - $readSize;
										if ($leaveSize == 0) fclose($f);
										else $leaveSize < $rsize && $rsize = $leaveSize;
									}
								} else {
									if($save)fwrite($saveF,substr($r, $fa+4));
									else $html=substr($r, $fa+4);
									$readSize += $thisSize - $fa - 4;
									if ($contentLength) {
										$leaveSize = $contentLength - $readSize;
										if ($leaveSize == 0) fclose($f);
										else $leaveSize < $rsize && $rsize = $leaveSize;
									}
								}
								$getHead=true;
							} else {
								$html = substr($r, $fa+4);
								$readSize += $thisSize - $fa - 4;
								break;
							}
						}
					} else {
						if($save && !$chunked && !$gzip)fwrite($saveF, $r);
						else $html.=$r;
						$readSize += $thisSize;
						if ($contentLength) {
							$leaveSize = $contentLength - $readSize;
							if ($leaveSize == 0) fclose($f);
							else $leaveSize < $rsize && $rsize = $leaveSize;
						}
					}
				}
				is_resource($f) && fclose($f);
				if($chunked){
					$fa=strpos($html,"\r\n");
					$last_fa=0;
					$rn = '';
					while($fa!==false){
						$chunksize=hexdec(substr($html,$last_fa,$fa-$last_fa));
						if($chunksize==0)break;
						$fa+=2;
						$rn.=substr($html,$fa,$chunksize);
						
						$last_fa=$fa+2+$chunksize;
						$fa=strpos($html,"\r\n",$last_fa);
						
					}
					$html=$rn;
					unset($rn);
					if($save && !$gzip){
						fwrite($saveF, $html);
					}
				}
				if ($gzip) {
					$html = gzdecode($html);
					if($save)fwrite($saveF, $html);
				}
				if($save){
					fclose($saveF);
					return true;
				} else {
					return $html;
				}
			}
		
		}
	}
	public function get($path = false, $args = array()){
		return $this->open($path, false, $args);
	}
	public function post($path = false, $postFlag = false, $args = array()){
		return $this->open($path, $postFlag, $args);
	}
	public function open_html($path = false, $postFlag =  false, $args=array(), $return_encoding=ENCODING){
		if($data = $this->open($path, $postFlag, $args)){
			if (is_null($this->head->info)) return false;
			switch($this->head->statusNum1){
				case '2':
					$encoding='';
					if(!is_null($this->head->content_type)){
						if(preg_match('/charset=(.+)/', $this->head->content_type, $matches)){
							$encoding = $matches[1];
						}
					}
					if(!$encoding){
						if(preg_match('/<meta.*?http-equiv="Content-Type".*content=.*?charset=(.+)>/i', $data, $matches)){
							$encoding = $matches[1];
							$sp = preg_split('/\'|"/', $encoding);
							$encoding = trim($sp[0]);
						}
					}
					$encoding || $encoding=$return_encoding;
					return iconv($encoding, $return_encoding."//IGNORE", $data);
				break;
				case '3':
					$goto_url = $this->head->location;
					$info0 = parse_url($goto_url);
					if(!$info0['scheme']){
						$info1 = parse_url($url);
						if(substr($goto_url,0,1)=='/')$goto_url=$info1['scheme'].'://'.$info1['host'].$goto_url;
						else {
							!$info1['path'] && $info1['path']='/';
							$path=$info1['path'];
							if(substr($path,-1,1)=='/')$goto_url=$path.$goto_url;
							else $goto_url=substr($path,0,strrpos($path,'/')).'/'.$goto_url;
							//$goto_url=$info1['scheme'].'://'.$info1['host'].$goto_url;
						}
					}
					if($goto_url){
						return $this->open_html($goto_url);
					} else return '';
				break;
				case '4':
					return false;
				break;
			}
		}
	}
	public function get_html($path = false,$args=array(),$return_encoding=ENCODING){
		return $this->open_html($path, false, $args, $return_encoding);
	}
	public function post_html($path = false, $postFlag, $args = array(), $return_encoding = ENCODING){
		return $this->open_html($path, $postFlag, $args, $return_encoding);
	}
	public static function args($args){
		$rn = '';
		if(is_array($args)&&count($args)>0){
			foreach($args as $k=>$v){
				$rn.="$k: $v\r\n";
			}
		}
		return $rn;
	}
	public static function isIp($ipStr){
		return preg_match('/^\d{1,3}(?:\.\d{1,3}){3}$/', $ipStr) > 0?true:false;
	}
	public static function parseUrl($url){
		if(strpos($url, '://')===false)$url = 'http://'.$url;
		$urlInfo = parse_url($url);
		$urlInfo['scheme'] = strtolower($urlInfo['scheme']);
		empty($urlInfo['path']) && $urlInfo['path'] = '/';
		empty($urlInfo['port']) && $urlInfo['port'] = ($urlInfo['scheme'] == 'https'?'443':'80');
		$urlInfo['dir'] = $urlInfo['path'];
		!empty($urlInfo['query']) && $urlInfo['path'] .='?'.$urlInfo['query'];
		!empty($urlInfo['fragment']) && $urlInfo['path'] .= '#'.$urlInfo['fragment'];
		$urlInfo['url'] = $urlInfo['scheme'].'://'.$urlInfo['host'].($urlInfo['port'] != '80' ? ':'.$urlInfo['port'] : '').$urlInfo['path'];
		return $urlInfo;
	}
	private static function parseHead($head){
		$rn = $httpInfo = $args = array();
		$sp = explode("\r\n", $head);
		$sp2 = explode(' ', $sp[0]);
		$httpInfo['version'] = substr($sp2[0], 5);
		array_shift($sp2);
		$httpInfo['statusNum']  = $sp2[0];
		$httpInfo['statusNum1'] = substr($sp2[0],0,1);
		$httpInfo['status']     = $httpInfo['statusNum1'] == '2';
		array_shift($sp2);
		$httpInfo['info']   = implode('', $sp2);
		array_shift($sp);
		foreach($sp as $v){
			$f = strpos ( $v, ':' );
			if ($f> 0) {
				$key = trim(substr ($v, 0, $f));
				$val = trim(substr($v, $f + 1));
				$args[$key][] = $val;
			}
		}
		$rn['info'] = $httpInfo;
		$rn['args'] = $args;
		return $rn;
	}
}
?>