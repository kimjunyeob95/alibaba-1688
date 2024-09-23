<?php

namespace App\Packages\Taobao;

use App\Constants\TaobaoErrorMessageConstant;
use App\Models\TaobaoTokenData;
use Carbon\Carbon;
use Exception;
use Psr\Log\LogLevel;

class IopClient
{
	public string $appkey;

	public string $secretKey;

	public string $gatewayUrl;

	public $connectTimeout;

	public $readTimeout;

	protected $signMethod = "sha256";

	protected $sdkVersion = "iop-sdk-php-20200227";

	private string $accessToken = "";

	public $logLevel;

	public function getAppkey()
	{
		return $this->appkey;
	}

	public function __construct()
	{
		$this->gatewayUrl = UrlConstants::$api_gateway_url_tw;
		$this->appkey     = env("TAOBAO_APPKEY");
		$this->secretKey  = env("TAOBAO_SECRETKEY");
		$this->logLevel   = Constants::$log_level_error;

		$tokenObj = TaobaoTokenData::first();
		if( $tokenObj == null ){
			throw new Exception(TaobaoErrorMessageConstant::getNotHaveErrorMessage("TOKEN_OBJ"));
		}

		/** 토큰 재갱신 */
		$currentTime         = Carbon::now();
		$tokenExpirationTime = Carbon::parse($tokenObj->expires_in);
		$diffHours           = $currentTime->diffInHours($tokenExpirationTime);
		if( $diffHours < 48 ) {
			$request = new IopRequest('/auth/token/refresh');
			$request->addApiParam('refresh_token', $tokenObj->refresh_token);
			$response = $this->execute($request);
	
			if( !isset($response["access_token"]) || empty($response["access_token"]) ){
				throw new Exception(TaobaoErrorMessageConstant::getNotHaveErrorMessage("ACCESS_TOKEN"));
			}
			if( !isset($response["refresh_token"]) || empty($response["refresh_token"]) ){
				throw new Exception(TaobaoErrorMessageConstant::getNotHaveErrorMessage("REFRESH_TOKEN"));
			}
	
			TaobaoTokenData::where("id", $tokenObj->id)
			->update([
				"access_token"       => $response["access_token"],
				"refresh_token"      => $response["refresh_token"],
				"expires_in"         => $currentTime->addSeconds($response["expires_in"]),
				"refresh_expires_in" => $currentTime->addSeconds($response["refresh_expires_in"]),
			]);
			$this->accessToken = $response["access_token"];
		} else {
			$this->accessToken = $tokenObj->access_token;
		}
	}

	protected function generateSign($apiName,$params)
	{
		ksort($params);

		$stringToBeSigned = '';
		$stringToBeSigned .= $apiName;
		foreach ($params as $k => $v)
		{
			$stringToBeSigned .= "$k$v";
		}
		unset($k, $v);

		return strtoupper($this->hmac_sha256($stringToBeSigned,$this->secretKey));
	}


	function hmac_sha256($data, $key){
	    return hash_hmac('sha256', $data, $key);
	}

	public function curl_get($url,$apiFields = null,$headerFields = null)
	{
		$ch = curl_init();

		foreach ($apiFields as $key => $value)
		{
			$url .= "&" ."$key=" . urlencode($value);
		}

	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

	    if($headerFields)
	    {
	    	$headers = array();
	    	foreach ($headerFields as $key => $value)
			{
				$headers[] = "$key: $value";
			}
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			unset($headers);
	    }

		if ($this->readTimeout) 
		{
			curl_setopt($ch, CURLOPT_TIMEOUT, $this->readTimeout);
		}

		if ($this->connectTimeout) 
		{
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
		}
		
		curl_setopt ( $ch, CURLOPT_USERAGENT, $this->sdkVersion );

		//https ignore ssl check ?
		if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) 
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}

	    $output = curl_exec($ch);
		
		$errno = curl_errno($ch);

		if ($errno)
		{
			curl_close($ch);
			throw new Exception($errno,0);
		}
		else
		{
			$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if (200 !== $httpStatusCode)
			{
				throw new Exception($output, $httpStatusCode);
			}
		}

		return $output;
	}

	public function curl_post($url, $postFields = null, $fileFields = null,$headerFields = null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if ($this->readTimeout) 
		{
			curl_setopt($ch, CURLOPT_TIMEOUT, $this->readTimeout);
		}

		if ($this->connectTimeout) 
		{
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
		}

		if($headerFields)
	    {
	    	$headers = array();
	    	foreach ($headerFields as $key => $value)
			{
				$headers[] = "$key: $value";
			}
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			unset($headers);
	    }

		curl_setopt ( $ch, CURLOPT_USERAGENT, $this->sdkVersion );

		//https ignore ssl check ?
		if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) 
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}

		$delimiter = '-------------' . uniqid();
		$data = '';
		if($postFields != null)
		{
			foreach ($postFields as $name => $content) 
			{
			    $data .= "--" . $delimiter . "\r\n";
			    $data .= 'Content-Disposition: form-data; name="' . $name . '"';
			    $data .= "\r\n\r\n" . $content . "\r\n";
			}
			unset($name,$content);
		}

		if($fileFields != null)
		{
			foreach ($fileFields as $name => $file) 
			{
			    $data .= "--" . $delimiter . "\r\n";
			    $data .= 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $file['name'] . "\" \r\n";
			    $data .= 'Content-Type: ' . $file['type'] . "\r\n\r\n";
			    $data .= $file['content'] . "\r\n";
			}
			unset($name,$file);
		}
		$data .= "--" . $delimiter . "--";

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER , 
			array(
				'Content-Type: multipart/form-data; boundary=' . $delimiter,
			    'Content-Length: ' . strlen($data)
			)
		);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		$response = curl_exec($ch);
		unset($data);
		
		$errno = curl_errno($ch);
		if ($errno)
		{
			curl_close($ch);
			throw new Exception($errno,0);
		}
		else
		{
			$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if (200 !== $httpStatusCode)
			{
				throw new Exception($response,$httpStatusCode);
			}
		}

		return $response;
	}

	public function execute(IopRequest $request)
	{
		$sysParams["app_key"]      = $this->appkey;
		$sysParams["sign_method"]  = $this->signMethod;
		$sysParams["timestamp"]    = $this->msectime();

		if( !empty($this->accessToken) ){
			$sysParams["access_token"] = $this->accessToken;
		}

		$apiParams = $request->udfParams;
		
		$requestUrl = $this->gatewayUrl;

		if($this->endWith($requestUrl,"/"))
		{
			$requestUrl = substr($requestUrl, 0, -1);
		}

		$requestUrl .= $request->apiName;
		$requestUrl .= '?';

		$sysParams["partner_id"] = $this->sdkVersion;

		if($this->logLevel == Constants::$log_level_debug)
		{
			$sysParams["debug"] = 'true';
		}

		$sysParams["sign"] = $this->generateSign($request->apiName,array_merge($apiParams, $sysParams));

		foreach ($sysParams as $sysParamKey => $sysParamValue)
		{
			$requestUrl .= "$sysParamKey=" . urlencode($sysParamValue) . "&";
		}

		$requestUrl = substr($requestUrl, 0, -1);
		
		$resp = '';

		try
		{
			if($request->httpMethod == 'POST')
			{
				$resp = $this->curl_post($requestUrl, $apiParams, $request->fileParams,$request->headerParams);
			}
			else
			{
				$resp = $this->curl_get($requestUrl, $apiParams,$request->headerParams);			
			}
		}
		catch (Exception $e)
		{
			$this->logApiError($requestUrl,"HTTP_ERROR_" . $e->getCode(),$e->getMessage());
			throw $e;
		}

		unset($apiParams);

		$respObject = json_decode($resp, JSON_UNESCAPED_UNICODE);
		if(isset($respObject->code) && $respObject->code != "0") 
		{
			$this->logApiError($requestUrl, $respObject->code, $respObject->message);
		} else 
		{
			if($this->logLevel == Constants::$log_level_debug || $this->logLevel == Constants::$log_level_info) 
			{

			}
		}
		return $respObject;
	}

	protected function logApiError($requestUrl, $errorCode, $responseTxt, string $logLevel = LogLevel::ERROR)
	{
		$localIp = isset($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"] : "CLI";

		$params = [
			"appkey"      => $this->appkey,
			"localIp"     => $localIp,
			"PHP_OS"      => PHP_OS,
			"sdkVersion"  => $this->sdkVersion,
			"requestUrl"  => $requestUrl,
			"errorCode"   => $errorCode,
			"responseTxt" => str_replace("\n","",$responseTxt),
		];
		debug_log(json_encode($params, JSON_UNESCAPED_UNICODE), "taobao/api-log", "log", $logLevel);
	}

	function msectime() {
	   list($msec, $sec) = explode(' ', microtime());
	   return $sec . '000';
	}

	 function endWith($haystack, $needle) {   
	    $length = strlen($needle);
	    if($length == 0)
	    {    
	        return false;  
	    }
	    return (substr($haystack, -$length) === $needle);
	 }

}
