<?php
/**
 * 昭通商城新的短信发送接口
 * 可以使用GET,POST,httprequest三种方法发送短信接口
 * 目前昭通网给到的是POST方法
 * Author:sunjun
 * Time:2012-02-16
*/
error_reporting(E_ALL ^ E_NOTICE);

function sendsms($mobile, $content)
{
	return sendsms_imtong($mobile, $content);
}

function sendsms_imtong($mobile, $content)
{

	//配置信息
	$smsapi = "www.ztso.cn/index.php/sendsms_web/sms";//短信网关
	
	$sendurl = "http://".$smsapi;

	$post_data = array(); 
	$post_data['username'] = "ztwhg";   
	$post_data['password'] = "ztwhg8888"; 
	$post_data['mobiles'] = $mobile;   
	$post_data['content'] = $content;

	$result = dopost($sendurl,$post_data);

	//返回1代表成功发到MAS短信服务器接口，0失败；-1为用户名密
	
	if ($result == 'succ')
	{
		return true;
	}
	else
	{
		return $result;
	}
}

function ismobile($mobile)
{
	return (strlen($mobile) == 11 || strlen($mobile) == 12) && (preg_match("/^13\d{9}$/", $mobile) || preg_match("/^14\d{9}$/", $mobile) || preg_match("/^15\d{9}$/", $mobile) || preg_match("/^18\d{9}$/", $mobile) || preg_match("/^0\d{10}$/", $mobile) || preg_match("/^0\d{11}$/", $mobile));
}

function getverifycode()
{
	$verifycode = rand(100000,999999);

	$verifycode = str_replace('1989','9819',$verifycode);
	$verifycode = str_replace('1259','9521',$verifycode);
	$verifycode = str_replace('12590','09521',$verifycode);
	$verifycode = str_replace('10086','68001',$verifycode);

	return $verifycode;
}

function httprequest($url, $data=array(), $abort=false) {
	if ( !function_exists('curl_init') ) { return empty($data) ? doget($url) : dopost($url, $data); }
	$timeout = $abort ? 1 : 2;
	$ch = curl_init();
	if (is_array($data) && $data) {
		$formdata = http_build_query($data);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $formdata);
	}
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	$result = curl_exec($ch);
	return (false===$result && false==$abort)? ( empty($data) ?  doget($url) : dopost($url, $data) ) : $result;
}

function doget($url){
	$url2 = parse_url($url);
	$url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
	$url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
	$host_ip = @gethostbyname($url2["host"]);
	$fsock_timeout = 2;  //2 second
	if(($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $fsock_timeout)) < 0){
		return false;
	}
	$request =  $url2["path"] .($url2["query"] ? "?".$url2["query"] : "");
	$in  = "GET " . $request . " HTTP/1.0\r\n";
	$in .= "Accept: */*\r\n";
	$in .= "User-Agent: Payb-Agent\r\n";
	$in .= "Host: " . $url2["host"] . "\r\n";
	$in .= "Connection: Close\r\n\r\n";
	if(!@fwrite($fsock, $in, strlen($in))){
		fclose($fsock);
		return false;
	}
	return gethttpcontent($fsock);
}

function dopost($url,$post_data=array()){
	$url2 = parse_url($url);
	$url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
	$url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
	$host_ip = @gethostbyname($url2["host"]);
	$fsock_timeout = 2; //2 second
	if(($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $fsock_timeout)) < 0){
		return false;
	}
	$request =  $url2["path"].($url2["query"] ? "?" . $url2["query"] : "");
	$request =  $url2["path"];
	$post_data2 = http_build_query($post_data);
	$in  = "POST " . $request . " HTTP/1.0\r\n";
	$in .= "Accept: */*\r\n";
	$in .= "Host: " . $url2["host"] . "\r\n";
	$in .= "User-Agent: Lowell-Agent\r\n";
	$in .= "Content-type: application/x-www-form-urlencoded\r\n";
	$in .= "Content-Length: " . strlen($post_data2) . "\r\n";
	$in .= "Connection: Close\r\n\r\n";
	$in .= $post_data2 . "\r\n\r\n";
	unset($post_data2);
	if(!@fwrite($fsock, $in, strlen($in))){
		fclose($fsock);
		return false;
	}
	return gethttpcontent($fsock);
}

function gethttpcontent($fsock=null) {
	$out = null;
	while($buff = @fgets($fsock, 2048)){
		$out .= $buff;
	}
	fclose($fsock);
	$pos = strpos($out, "\r\n\r\n");
	$head = substr($out, 0, $pos);    //http head
	$status = substr($head, 0, strpos($head, "\r\n"));    //http status line
	$body = substr($out, $pos + 4, strlen($out) - ($pos + 4));//page body
	if(preg_match("/^HTTP\/\d\.\d\s([\d]+)\s.*$/", $status, $matches)){
		if(intval($matches[1]) / 100 == 2){
			return $body;  
		}else{
			return false;
		}
	}else{
		return false;
	}
}

?>