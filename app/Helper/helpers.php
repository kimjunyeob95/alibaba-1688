<?php

use App\Constants\HttpConstant;
use App\Constants\ImageConstant;
use App\Constants\ProductConstant;
use App\Models\ProductData;
use App\Models\ProductImageData;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Psr\Log\LogLevel;

if (!function_exists("pr")) {
    function pr($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}

if (!function_exists('helpers_curl')) {
    /**
	 * helpers_curl
	 *
	 * @param  mixed $method (GET, POST)
	 * @param  mixed $url
	 * @param  mixed $data
     * @param  mixed $header
	 * @return array
	 */
	function helpers_curl($method, $url, $header, $data = '') : array
	{
		$curl   = curl_init();
		$method = strtoupper($method);
		if($method == 'GET') {
			$queryString = (($data)? http_build_query( $data ) : '');
			curl_setopt_array($curl, array(
				CURLOPT_URL            => $url.(($queryString)? '?'.$queryString : ''),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_MAXREDIRS      => 10,
				CURLOPT_TIMEOUT        => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST  => $method,
				CURLOPT_HTTPHEADER     => $header
			));
		} else if($method == 'POST'){
			curl_setopt_array($curl, array(
				CURLOPT_URL            => $url,
				CURLOPT_POST           => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_CUSTOMREQUEST  => $method,
				CURLOPT_POSTFIELDS     => json_encode($data, JSON_UNESCAPED_UNICODE),
				CURLOPT_HTTPHEADER     => $header
			));
		}
		$result = curl_exec($curl);
		curl_close($curl);
		$result = json_decode($result, JSON_UNESCAPED_UNICODE);
		if(is_array($result)){
			return $result;
		} else {
			return array();
		}
	}
}

if (!function_exists("microtime_float")) {
	function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$sec + (float)$usec);
	}
}

if (!function_exists("debug_log")) {
	global $debug_time;
	$debug_time = microtime_float();

    function debug_log($str, $dirname, $filename ,string $level = LogLevel::DEBUG)
	{
		global $debug_time;

		$new_time   = microtime(true); // Laravel에서는 이 함수를 직접 사용할 수 있습니다.
        $time_gap   = $new_time - $debug_time;
        $debug_time = $new_time;
        $time_str   = ($time_gap > 1000000000) ? "-.---" : number_format($time_gap, 3);

        $logStr = "[excuteTime: $time_str] $str";

        // 경로와 파일 이름을 사용하여 로그 채널 동적으로 생성
        $logChannel = Log::build([
            'driver' => 'single',
            'path'   => storage_path('logs/'.$dirname.'/'.$filename.date('Ymd').'.log'),
        ]);

        switch ($level) {
            case LogLevel::DEBUG:
                $logChannel->debug($logStr);
                break;
            case LogLevel::NOTICE:
                $logChannel->notice($logStr);
                break;
            case LogLevel::WARNING:
                $logChannel->warning($logStr);
                break;
            case LogLevel::ERROR:
                $logChannel->error($logStr);
                break;
            case LogLevel::INFO:
            default:
                $logChannel->info($logStr);
        }
    }
}

if (!function_exists("helpers_default_message")) {
    function helpers_default_message(bool $isSuccess = false, string $message = "잘못 된 접근입니다.", array $data = []): array
    {
        return [
            "isSuccess" => $isSuccess,
            "msg"       => $message,
            "data"      => $data,
        ];
    }
}

if (!function_exists("helpers_fail_message")) {
    function helpers_fail_message(bool $isSuccess = false, string $message = "변경 사항이 없거나 처리가 실패하였습니다. 관리자에 문의 바랍니다.", array $data = []): array
    {
        return [
            "isSuccess" => $isSuccess,
            "msg"       => $message,
            "data"      => $data,
        ];
    }
}

if (!function_exists("helpers_success_message")) {
    function helpers_success_message($data = [], string $message = "정상 처리 되었습니다.", int $affectRows = 0): array
    {
        if( $affectRows > 0){
            return [
                "isSuccess" => true,
                "msg"       => $message." 반영 수 :".$affectRows,
                "data"      => $data,
            ];
        }else{
            return [
                "isSuccess" => true,
                "msg"       => $message,
                "data"      => $data,
            ];
        }
    }
}

if (!function_exists("helpers_json_response")) {
    function helpers_json_response(int $status, array $params = [], string $message = ""): JsonResponse
    {
        $routeParts = explode(".", Route::currentRouteName());
        $apiVersion = empty($routeParts[0]) ? "v1" : $routeParts[0];

        $result = [
            "status" => $status,
            "meta" => [
                "timestamp"  => Carbon::now()->format('Y-m-d H:i:s'),
                // "apiVersion" => $apiVersion,
                "apiType" => $apiVersion,
            ]
        ];
        if( $status == HttpConstant::OK ){
            unset($params["isSuccess"]);
            $result = array_merge($result, $params);
            if( trim($message) != "" ){
                $result["message"] = $message;
            }
        }else{
            $error = [
                "error" => [
                    "code"    => $status,
                    "message" => trim($message) != "" ? $message : "잘못 된 접근입니다.",
                ]
            ];
            $result = array_merge($result, $error);
        }

        return response()->json($result, $status);
    }
}

if (!function_exists("helpers_format_YmdHis")) {
    function helpers_format_YmdHis($isoString = null) {
        $date = $isoString ? Carbon::parse($isoString, 'UTC') : Carbon::now('Asia/Seoul');
        return $date->setTimezone('Asia/Seoul')->format('Y-m-d H:i:s');
    }
}

if (!function_exists("helpersGetOnlyNumbers")) {
    function helpersGetOnlyNumbers(string $string = ""): string
    {
        return preg_replace('/[^0-9]/', '', $string);
    }
}

if (!function_exists("printQuery")) {
    function printQuery($model)
    {
        $sql = $model->toSql();
        $bindings = $model->getBindings();

        foreach ($bindings as $binding) {
            $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }

        dd($sql);
    }
}

if (!function_exists("curl_1688")) {
    function curl_1688(string $method, string $endPoint, array $payload, array $header = ["Content-Type: application/x-www-form-urlencoded"]): array
    {
        $returnMsg = helpers_fail_message();
        $apiDomain = env("1688_API_DOMAIN", "https://gw.open.1688.com/openapi/");
        $appSecret = env("1688_APP_SECRET_KEY");
        $appKey    = env("1688_APP_KEY");

        $endPoint = $endPoint . $appKey;
        $curlUrl  = $apiDomain . $endPoint;
        $apiInfo  = str_replace($apiDomain, "", $endPoint);

        $aliParams = [];
        foreach ($payload as $key => $val) {
            if( is_array($val) ){
                $aliParams[] = $key . json_encode($val);
            }else{
                $aliParams[] = $key . $val;
            }
        }
        sort($aliParams);
        $sign_str  = join('', $aliParams);
        $sign_str  = $apiInfo . $sign_str;
        $code_sign = strtoupper(bin2hex(hash_hmac("sha1", $sign_str, $appSecret, true)));
        $payload["_aop_signature"] = $code_sign;

        $finalPayload = "";
        $index = 0;
        foreach ($payload as $key => $val) {
            if( $index == 0 ){
                if( is_array($val) ){
                    $jsonValue     = json_encode($val);
                    $finalPayload .= "&" . $key . "=" . urlencode($jsonValue);
                }else{
                    $finalPayload .= $key . "=" . $val;
                }
            }else{
                if( is_array($val) ){
                    $jsonValue     = json_encode($val);
                    $finalPayload .= "&" . $key . "=" . urlencode($jsonValue);
                }else{
                    $finalPayload .= "&" . $key . "=" . $val;
                }
            }
            $index++;
        }

		$curl   = curl_init();
		$method = strtoupper($method);
		if($method == 'GET') {
			$queryString = (($payload)? http_build_query( $payload ) : '');
			curl_setopt_array($curl, array(
				CURLOPT_URL            => $curlUrl.(($queryString)? '?'.$queryString : ''),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_MAXREDIRS      => 10,
				CURLOPT_TIMEOUT        => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST  => $method,
				CURLOPT_HTTPHEADER     => $header
			));
		} else if($method == 'POST'){
			curl_setopt_array($curl, array(
				CURLOPT_URL            => $curlUrl,
				CURLOPT_POST           => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_CUSTOMREQUEST  => $method,
				CURLOPT_POSTFIELDS     => $finalPayload,
				CURLOPT_HTTPHEADER     => $header
			));
		}
		$result = curl_exec($curl);
		curl_close($curl);

        try {
            $apiResult = json_decode($result, JSON_UNESCAPED_UNICODE);
            if(!is_array($apiResult)) throw new InvalidArgumentException("결과가 배열이 아닙니다.");

            $returnMsg = helpers_success_message($apiResult);
        } catch (JsonException $e) {
            $returnMsg = helpers_fail_message(false, "결과가 Json이 아닙니다.");
        } catch (InvalidArgumentException $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }
}

if (!function_exists("ocPrice")) {
    function ocPrice(float $price_1688): array
    {
        $option_price     = round( $price_1688 * env("1688_EXCHANGE_RATE", 200) , -1);
        $option_price_sum = (int)intval($option_price) + intval($option_price * env("OPTION_PRICE_RATE", 0.12));
        $option_price_cal = round($option_price_sum / 10) * 10;
        $onch_price       = $option_price_cal;

        $recom_cus_price_sum = (int)intval($option_price) + intval($option_price * env("RECOM_CUS_PRICE_RATE", 0.45));
        $recom_cus_price_cal = round($recom_cus_price_sum / 10) * 10;
        $cus_price       = $recom_cus_price_cal;
        $recom_cus_price = $recom_cus_price_cal;

        return [
            "option_price"    => $option_price,
            "onch_price"      => $onch_price,
            "cus_price"       => $cus_price,
            "recom_cus_price" => $recom_cus_price,
        ];
    }
}

if (!function_exists("getPrice1688")) {
    function getPrice1688(array $detailProduct): float
    {
        $price_1688 = 0;
        if( isset($detailProduct["productSkuInfos"][0]["price"]) ){
            foreach ($detailProduct["productSkuInfos"] as $prdOptions) {
                if( $prdOptions["price"] > $price_1688 ){
                    $price_1688 = $prdOptions["price"];
                }
            }
        } else if( !isset($detailProduct["productSkuInfos"][0]["price"]) &&
            isset($detailProduct["productSaleInfo"]["priceRangeList"])
        ) {
            $price_1688 = $detailProduct["productSaleInfo"]["priceRangeList"][0]["price"];
        }

        return (float)$price_1688;
    }
}

if (!function_exists("chkTransStatus")) {
    function chkTransStatus(int $offerId): void
    {
       $TransCnt = ProductImageData::where("offer_id", $offerId)
       ->whereRaw("REPLACE(img_url_trans, ' ', '') != ''")
       ->where("is_except", ImageConstant::IS_EXCEPT_N)
       ->whereNotNull("trans_dated_at")
       ->whereNull("deleted_at")
       ->count();
       ProductData::where("offer_id", $offerId)->update([
           "trans_status" => $TransCnt > 0 ? ProductConstant::TRANS_STATUS_Y : ProductConstant::TRANS_STATUS_N
       ]);
    }
}

if (!function_exists("fileContents")) {
    function fileContents(string $filePath): string
    {
        $options  = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];

        $context     = stream_context_create($options);
        $fileContent = file_get_contents($filePath, false, $context);
        return $fileContent;
    }
}

//글자 byte 확인 함수
if ( ! function_exists('productNameValidation'))
{
	function productNameValidation($prdName, $type, ?int $setVal = 0)
	{
		switch($type){
			case "product":
				$return = mb_strwidth( $prdName , "UTF-8") <= 100 ?? false;
				break;
			case "option":
				$return = mb_strwidth( $prdName , "UTF-8") <= 45 ?? false;
				break;
			default:
				$return = mb_strwidth( $prdName , "UTF-8") <= $setVal ?? false;
				break;
		}

		return $return;
	}
}

if (!function_exists("helperEscape")) {
    function helperEscape(string $string): string
    {
        $input = str_replace("'", "'\\''", $string);
        return "'" . $input . "'";
    }
}

if (!function_exists("upPrdDescTrans")) {
    function upPrdDescTrans(int $offerId): void
    {
        $prdObj = ProductData::where("offer_id", $offerId)->first();
        if( $prdObj != null ){
            $prd_desc = $prdObj->prd_desc;

            $imgObjs = ProductImageData::where([
                "offer_id" => $offerId,
                "img_type" => ImageConstant::IMAGE_TYPE_DESC,
            ])->get();
    
            foreach ($imgObjs as $imgObj) {
                if( $imgObj->is_except == ImageConstant::IS_EXCEPT_Y ){
                    $img_url_origin = $imgObj->img_url_origin;
                    $img_url_trans  = $imgObj->img_url_trans;
        
                    $pattern = '/<img[^>]+src\s*=\s*["\']' . preg_quote($img_url_origin, '/') . '["\'][^>]*>/i';
                    $prd_desc = preg_replace($pattern, '', $prd_desc);
        
                    $pattern = '/<img[^>]+src\s*=\s*["\']' . preg_quote($img_url_trans, '/') . '["\'][^>]*>/i';
                    $prd_desc = preg_replace($pattern, '', $prd_desc);
                } else {
                    $prd_desc = str_replace($imgObj->img_url_origin, $imgObj->img_url_trans, $prd_desc);
                }
            }

            ProductData::where("id", $prdObj->id)->update([
                "prd_desc_trans" => $prd_desc
            ]);
        }
    }
}