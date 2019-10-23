<?php
/**
 *  Mi Framework
 *
 *  Copyright(c) 2013 by zhouping. All rights reserved
 *
 *  To contact the author write to {@link mailto:zhouping@xiaomi.com}
 *
 * @author zhouping
 * @version $Id: HttpSSL.php,v 1.0 2013-11-4
 * @package library
 */

// ------------------------------------------------------------------------

/**
 * http请求处理
 *
 *
 * @author zhouping
 *
 */
class HttpSSL
{
    // 当前的user-agent字符串
    public $ua_string= "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:14.0) Gecko/20100101 Firefox/14.0.1";

    //证书文件
	var $certFile;
	//证书密码
	var $certPasswd;
	//证书类型PEM
	var	$certType;
    
	//CA文件
	var $caFile;
	
	//超时时间
	var $timeOut;    
	
	var $response_info;

	var $response_http_code;


    // --------------------------------------------------------------------

    /**
     * 构造函数
     *
     * @param array $params 初始化参数
     */
    public function __construct()
    {
		$this->certFile = "";
		$this->certPasswd = "";
		$this->certType = "PEM";
        
		$this->caFile = "";
        
        $this->timeOut = 120;
    }
    
	//设置证书信息
	function setCertInfo($certFile, $certPasswd, $certType="PEM") {
		$this->certFile = $certFile;
		$this->certPasswd = $certPasswd;
		$this->certType = $certType;
	}
	
	//设置Ca
	function setCaInfo($caFile) {
		$this->caFile = $caFile;
	}
	
	//设置超时时间,单位秒
	function setTimeOut($timeOut) {
		$this->timeOut = $timeOut;
	}
	    

    // --------------------------------------------------------------------

    /**
     * 更改默认的ua信息
     *
     * 本方法常用于模拟各种浏览器
     *
     * @param string $ua_string
     */
    public function setUA($user_agent)
    {
        $this->ua_string = $user_agent;
        return $this;
    }


	public function curlGet($url, $user_agent='', $cookie=array(), $cookie_file='cookie.txt', $timeout=10){

		if($user_agent == '')
			{
				$user_agent = $this->ua_string;
			}

			

			if (!function_exists("curl_init"))
			{
				showError('undefined function curl_init');
			}

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
//			curl_setopt($ch, CURLOPT_POST, false);
		
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); //读取
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file); //保存  




				//设置证书信息
				if($this->certFile != "") {
					curl_setopt($ch, CURLOPT_SSLCERT, $this->certFile);
					curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->certPasswd);
					curl_setopt($ch, CURLOPT_SSLCERTTYPE, $this->certType);
				}
	
				//设置CA
				if($this->caFile != "") {
					// 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
					curl_setopt($ch, CURLOPT_CAINFO, $this->caFile);
				} else {
					// 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				}

			if ($timeout !== null)
			{
				curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			}
			$cookies = '';

			if (!empty($cookie)){
				foreach($cookie as $key => $value){
					$cookies .= (($cookies ? "; " : "") . $key."=".urlencode($value));
				}
			//echo $cookies . "\t";

		
				curl_setopt($ch, CURLOPT_COOKIE, $cookies);
			}

			//returntransfer(1.no output 0.direct output)
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			//user_agent
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);

			$rs = curl_exec($ch);
			$this->response_info = curl_getinfo($ch);
			$this->response_http_code = $this->response_info["http_code"];			
			curl_close($ch);

			return $rs;
	}

    // --------------------------------------------------------------------

    /**
     * curl SSL方式提交
     *
     * @param string $url 请求地址
     * @param mixed $data 提交的数据
     * @param string $user_agent 自定义的UA
     * @param int   $timeout    超时时间
     * @return mixed
     */
    public function curlPost($url, $data, $user_agent = '',  $timeout = null, $cookie= null, $cookie_file='cookie.txt')
    {
        if($user_agent == '')
        {
            $user_agent = $this->ua_string;
        }

        if (!is_array($data))
        {
            $data = array($data);
        }
	
	$is_upload = 0;
	foreach($data as $v){
		if(preg_match("/^@/", $v)){
			$is_upload = 1;
			break;
		}
	}
	// 没有文件上传
	if(!$is_upload){
	        $data = http_build_query($data);
	}

        if (!function_exists("curl_init"))
        {
            showError('undefined function curl_init');
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); //读取
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file); //保存  

	
	

		//设置证书信息
		if($this->certFile != "") {
			curl_setopt($ch, CURLOPT_SSLCERT, $this->certFile);
			curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->certPasswd);
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, $this->certType);
		}
		
		//设置CA
		if($this->caFile != "") {
			// 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($ch, CURLOPT_CAINFO, $this->caFile);
		} else {
			// 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		}

        if ($timeout !== null)
        {
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        }
	$cookies = '';

	if ($cookie !== null){
		foreach($cookie as $key => $value){
			$cookies .= (($cookies ? "; " : "") . $key."=". urlencode($value));
		}
		var_dump( $cookies );
		curl_setopt($ch, CURLOPT_COOKIE, $cookies);
	}

        //returntransfer(1.no output 0.direct output)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //user_agent
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);

        $rs = curl_exec($ch);
	$this->response_info = curl_getinfo($ch);
	$this->response_http_code = $this->response_info["http_code"];	
        curl_close($ch);

        return $rs;
    }    

}
