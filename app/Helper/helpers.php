<?php

use App\Constants\CategoryConstant;
use App\Constants\EasySellConstant;
use App\Constants\ForbiddenWordConstant;
use App\Constants\GenuioConstant;
use App\Constants\HttpConstant;
use App\Constants\ImageConstant;
use App\Constants\InspectConstant;
use App\Constants\MallConstant;
use App\Constants\ProductConstant;
use App\Constants\WConstant;
use App\Models\Category;
use App\Models\CategoryWeightData;
use App\Models\EasysellProductLog;
use App\Models\GenuioAiData;
use App\Models\OnchannelProductLog;
use App\Models\ProductData;
use App\Models\ProductImageData;
use App\Models\ProductInspectData;
use App\Models\ProductModiData;
use App\Models\ProductOptionData;
use App\Models\ProductWeightData;
use App\Models\WeightData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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
	 * @return mixed
	 */
	function helpers_curl($method, $url, $header, $data = '', $return = "array") : mixed
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
				CURLOPT_HTTPHEADER     => $header,
                CURLOPT_ENCODING       => "utf-8"  // 지정된 인코딩으로 데이터를 디코드합니다.
			));
		} else if($method == 'POST' || $method == 'PUT'){
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

        if( $return == "string" ){
            $result = mb_convert_encoding($result, "UTF-8", "GBK");
            return $result;
        }

		$resultArr = json_decode($result, JSON_UNESCAPED_UNICODE);
		if(is_array($resultArr) && !empty($resultArr)){
			return $resultArr;
		} else {
			return $result;
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
	global $debugTime;
	$debugTime = microtime_float();

    function debug_log(mixed $str, string $dirname, string $filename = "", string $level = LogLevel::DEBUG)
	{
		global $debugTime;

        $newTime = microtime(true);
        $timeGap = $newTime - $debugTime;
        
        // 실행 시간 계산
        $runningTime = number_format($timeGap, 3);

        // Get caller information
        $backtrace = debug_backtrace();
        $caller    = $backtrace[0];
        $runFile   = basename($caller['file']);
        $location  = "{$runFile}:{$caller['line']}";

        $environment = app()->environment();

        // 로그 문자열 생성
        $logstr = sprintf(
            "[%s] %s.%s: [runningTime: %ss][location: %s] %s",
            date('Y-m-d H:i:s'),
            $environment,
            strtoupper($level),
            $runningTime,
            $location,
            is_array($str) ? json_encode($str, JSON_UNESCAPED_UNICODE) : $str
        );

        if( $filename == "" ){
			$filename = date('Y-m-d');
		} else {
			$filename = date('Y-m-d') . $filename;
		}

        $dirPath = "/" . $dirname;
        $logfile = $dirPath . "/" . $filename . ".log";

        // 파일이 없으면 생성하고 권한 설정
        if (!file_exists($logfile)) {
            touch($logfile);
            chmod($logfile, 0777); // 파일 권한을 777로 설정
        }

        // 경로가 존재하지 않으면 생성
        if (!Storage::disk('logs')->exists(dirname($dirPath))) {
            Storage::disk('logs')->makeDirectory(dirname($dirPath));
        }

        // 파일이 이미 존재하면 이어쓰기 작성
        if (Storage::disk('logs')->exists($logfile)) {
            Storage::disk('logs')->append($logfile, $logstr);
        } else {
            // 파일이 존재하지 않으면 새로 생성
            Storage::disk('logs')->put($logfile, $logstr);
        }

        // 터미널에서 실행 중일 때만 출력
        if (php_sapi_name() === 'cli') {
            echo $logstr . PHP_EOL;
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
    function helpers_fail_message(string $message = "변경 사항이 없거나 처리가 실패하였습니다. 관리자에 문의 바랍니다.", array $data = []): array
    {
        unset($data["isSuccess"]);
        unset($data["msg"]);
        return [
            "isSuccess" => false,
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
                "api_type" => $apiVersion,
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
            if( isset($params["error_code"]) && !empty($params["error_code"]) ){
                $error["error"]["code"] = $params["error_code"];
            }
            if( isset($params["data"]) && !empty($params["data"]) ){
                $error["data"] = $params["data"];
            }
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
            $returnMsg = helpers_fail_message("결과가 Json이 아닙니다.");
        } catch (InvalidArgumentException $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}

if (!function_exists("curl_1688_v2")) {
    function curl_1688_v2(string $method, string $endPoint, array $payload, array $header = ["Content-Type: application/x-www-form-urlencoded"]): array
    {
        $returnMsg    = helpers_fail_message();
        $apiDomain    = env("1688_API_DOMAIN", "https://gw.open.1688.com/openapi/");
        $appKey       = env("1688_WORLD_APP_KEY");
        $appSecret    = env("1688_WORLD_APP_SECRET_KEY");
        $refreshToken = env("1688_WORLD_REFRESH_TOKEN");

        $TokenEndPoint = "param2/1/system.oauth2/getToken/{$appKey}?grant_type=refresh_token&client_id={$appKey}&client_secret={$appSecret}&refresh_token={$refreshToken}";
        $apiDatas      = helpers_curl("GET", $apiDomain . $TokenEndPoint, []);
        if( isset($apiDatas["access_token"]) && $apiDatas["access_token"] ){
            $payload["access_token"] = $apiDatas["access_token"];
        } else {
            throw new Exception("W2 access_token error");
        }

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
            $returnMsg = helpers_fail_message("결과가 Json이 아닙니다.");
        } catch (InvalidArgumentException $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}

if (!function_exists("ocPrice")) {
    function ocPrice(float $price, int $delivery_price = 0): array
    {
        $recom_cus_price_sum = (int)intval($price) + intval($price * env("RECOM_CUS_PRICE_RATE", 0.35));
        $recom_cus_price_cal = round($recom_cus_price_sum / 10) * 10;
        $cus_price           = $recom_cus_price_cal;
        $recom_cus_price     = $recom_cus_price_cal;

        return [
            "option_price"    => $price,
            "onch_price"      => $price,
            "cus_price"       => $cus_price,
            "recom_cus_price" => $recom_cus_price,
        ];
    }
}

/** W 공급가 */
if (!function_exists("wOptionPrice")) {
    function wOptionPrice(float $price): int
    {
        $option_price = round( $price * config('1688_EXCHANGE_RATE', 200) , -1);

        return $option_price;
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

if (!function_exists("getPrice1688V2")) {
    function getPrice1688V2(array $detailProduct): float
    {
        $price_1688 = 0;
        if( isset($detailProduct["skuList"]) ){
            foreach ($detailProduct["skuList"] as $prdOptions) {
                if( $prdOptions["price"] > $price_1688 ){
                    $price_1688 = $prdOptions["price"];
                }
            }
        }

        return (float)$price_1688;
    }
}

/** 번역상태 변경 */
if (!function_exists("chkTransStatus")) {
    function chkTransStatus(int $offerId): void
    {
        $prdObj          = ProductData::where("offer_id", $offerId)->first();
        $trans_status    = ProductConstant::TRANS_STATUS_N;
        $trans_status_en = ProductConstant::TRANS_STATUS_N;
        if( $prdObj != null ){
            if( $prdObj->w_type == WConstant::WAPP_W1 ){
                $transCnt = ProductImageData::where("offer_id", $offerId)
                ->whereRaw("REPLACE(img_url_trans, ' ', '') != ''")
                ->where("is_except", ImageConstant::IS_EXCEPT_N)
                ->where("lang", WConstant::WAPP_KR)
                ->whereNotNull("trans_dated_at")
                ->whereNull("deleted_at")
                ->count();

                if( $transCnt > 0 ){
                    $trans_status = ProductConstant::TRANS_STATUS_Y;
                }
            } else if( $prdObj->w_type == WConstant::WAPP_W2 ){
                $transKrCnt = ProductImageData::where("offer_id", $offerId)
                ->whereRaw("REPLACE(img_url_trans, ' ', '') != ''")
                ->where("is_except", ImageConstant::IS_EXCEPT_N)
                ->where("lang", WConstant::WAPP_KR)
                ->whereNotNull("trans_dated_at")
                ->whereNull("deleted_at")
                ->count();

                $transEnCnt = ProductImageData::where("offer_id", $offerId)
                ->whereRaw("REPLACE(img_url_trans, ' ', '') != ''")
                ->where("is_except", ImageConstant::IS_EXCEPT_N)
                ->where("lang", WConstant::WAPP_EN)
                ->whereNotNull("trans_dated_at")
                ->whereNull("deleted_at")
                ->count();

                if( $transKrCnt > 0 ){
                    $trans_status = ProductConstant::TRANS_STATUS_Y;
                }

                if( $transEnCnt > 0 ){
                    $trans_status_en = ProductConstant::TRANS_STATUS_Y;
                }
            }

            ProductData::where("offer_id", $offerId)->update([
                "trans_status"    => $trans_status,
                "trans_status_en" => $trans_status_en,
            ]);
        }
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

            $geKrObj = GenuioAiData::where([
                "offer_id" => $offerId,
                "ai_apply" => GenuioConstant::AI_APPLY_DESC_KR
            ])->first();

            if( $geKrObj != null ){
                $prd_desc = $geKrObj->apply_data;
            } else {
                $prd_desc = $prdObj->prd_desc;
            }
            $imgKrObjs = ProductImageData::where([
                "offer_id" => $offerId,
                "img_type" => ImageConstant::IMAGE_TYPE_DESC,
                "lang"     => WConstant::WAPP_KR
            ])->get();
            foreach ($imgKrObjs as $imgObj) {
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
                "prd_desc_kr" => $prd_desc
            ]);

            if( $prdObj->w_type == WConstant::WAPP_W2 ){
                $geEnObj = GenuioAiData::where([
                    "offer_id"      => $offerId,
                    "ai_apply" => GenuioConstant::AI_APPLY_DESC_EN
                ])->first();

                if( $geEnObj != null ){
                    $prd_desc = $geEnObj->apply_data;
                } else {
                    $prd_desc = $prdObj->prd_desc;
                }

                $imgEnObjs = ProductImageData::where([
                    "offer_id" => $offerId,
                    "img_type" => ImageConstant::IMAGE_TYPE_DESC,
                    "lang"     => WConstant::WAPP_EN
                ])->get();

                foreach ($imgEnObjs as $imgObj) {
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
                    "prd_desc_en" => $prd_desc
                ]);
            }
        }
    }
}

//이지셀 판매가 계산
if (!function_exists("calcEasySellSalePrice")) {
    function calcEasySellSalePrice(?float $price = 0, ?int $md_price = 0, int $delivery_price = ProductConstant::WEIGHT_STATUS_NONE_PRICE, string $type = "dynamic", string $sendType = EasySellConstant::TYPE_W): array
    {
        if($sendType == EasySellConstant::TYPE_W){
            //1688 공급가
            $option_price = round( $price * env('EASYSELL_EXCHANGE_RATE', 196) , -1);
            //이지셀 공급가
            $buyPrice = ceil(($option_price) / 100) * 100 + $delivery_price;
            //마진률 설정
            $marginRating = 1;
            // $marginRating = env("EASYSELL_PRICE_RATE", "1");

            //이지셀 판매가
            $salePrice = ceil(($option_price * $marginRating) / 100) * 100 + $delivery_price;
        }else if($sendType == EasySellConstant::TYPE_DROPHUB){
            //1688 공급가 - 위안화 * 100
            $option_price = $price * 100;
            //이지셀 공급가
            $buyPrice     = $option_price;
            //마진률 설정
            // $marginRating = env("DROPHUB_PRICE_RATE", "1");

            //이지셀 판매가
            $salePrice = $option_price;
        }

        if($type != "static"){
            $salePrice = !empty($md_price) ? $md_price : $salePrice;
        }

        return [
            "option_price" => $option_price,
            "buyPrice"     => $buyPrice,
            "salePrice"    => $salePrice,
        ];
    }
}

/** 온채널 판매가 */
if (!function_exists("calcOnchannelSalePrice")) {
    function calcOnchannelSalePrice(float $price): int
    {
        $option_price = round( $price * config('1688_EXCHANGE_RATE', 200) , -1);

        return $option_price + env("ONCHANNEL_DELIVERY_PRICE", 12000);
    }
}

/** 온채널 공급가 */
if (!function_exists("calcOnchannelOptionPrice")) {
    function calcOnchannelOptionPrice(float $price, int $delivery_price = ProductConstant::WEIGHT_STATUS_NONE_PRICE): int
    {
        $option_price = round( $price * config('1688_EXCHANGE_RATE', 200) , -1);
        return $option_price + $delivery_price;
    }
}

// WApp 일반 판매가 계산
if (!function_exists("calcWSalePrice")) {
    function calcWSalePrice(float $price = 0, int $delivery_price = ProductConstant::WEIGHT_STATUS_NONE_PRICE): int
    {
        $option_price = round( $price * config('1688_EXCHANGE_RATE', 200) , -1);

        return ( ceil(($option_price * env("W_SALE_PRICE_RATE", "1.35")) / 100) * 100 ) + $delivery_price;
    }
}

// WApp 일반 판매가 <= MD 판매자가 bool
if (!function_exists("compareWSalePrice")) {
    function compareWSalePrice(int $salePrice = 0, int $mdPrice = 0): bool
    {
        if( $mdPrice > $salePrice ){
            return true;
        } else {
            return false;
        }
    }
}

/** 수정 상품 저장 */
if (!function_exists("saveModiProduct")) {
    function saveModiProduct(int $offerId): void
    {
        foreach (MallConstant::MALL_LIST as $channel) {
            $objs = collect();

            if( $channel == MallConstant::MALL_EASYSELL ){
                $objs = EasysellProductLog::where([
                    "offer_id"       => $offerId,
                    "regist_success" => MallConstant::REGIST_SUCCESS,
                ])->get();
            } else if($channel == MallConstant::MALL_ONCHANNEL ){
                $objs = OnchannelProductLog::where([
                    "offer_id"       => $offerId,
                    "regist_success" => MallConstant::REGIST_SUCCESS,
                ])->get();
            }

            foreach ($objs as $obj) {
                if( $channel == MallConstant::MALL_EASYSELL ){
                    $wType = $obj->w_type;
                } else if($channel == MallConstant::MALL_ONCHANNEL ){
                    $wType = $obj->send_type;
                }

                ProductModiData::updateOrCreate([
                    "offer_id"      => $offerId,
                    "w_type"        => $wType,
                    "is_send"       => ProductConstant::IS_SEND_N,
                    "channel"       => $channel,
                    "send_dated_at" => null,
                ],[
                    "msg"        => "",
                    "updated_at" => Carbon::now()
                ]);
            }
        }
    }
}

/** 검수상태 최종 변경 */
if (!function_exists("inspectStatusUpdate")) {
    function inspectStatusUpdate(int $offerId): void
    {
        $inspectCnt = ProductInspectData::where([
            "offer_id"   => $offerId,
            "is_inspect" => InspectConstant::IS_INSPECT_Y
        ])->whereIn("inspect_type", InspectConstant::INSPECT_STATUS)->count();

        $qry = ProductData::where("offer_id", $offerId);
        if( $inspectCnt == count(InspectConstant::INSPECT_STATUS) ){
            $qry->update([
                "inspect_status" => ProductConstant::INSPECT_STATUS_Y
            ]);
        } else {
            $qry->update([
                "inspect_status" => ProductConstant::INSPECT_STATUS_N
            ]);
        }
    }
}

/**
 * 고시 테이블 생성
 * ["name" => "value"] 전달
 */
if (!function_exists("getNoticeInfoTable")) {
    function getNoticeInfoTable(array $noticeInfo, string $type = ""): string
    {
        if($type == EasySellConstant::TYPE_DROPHUB){
            $title = "Product Description";
            $info = "The above table was written in accordance with the product information attribute.";
        }else{
            $title = "상품일반정보";
            $info = "위 내용은 상품정보제공 고시에 따라 작성되었습니다.";
        }
        $noticeTable = "<div style='width: 830px;margin:0 auto;'>
            <h4 style='font-size:20px;font-weight: 900;color:#000;margin-bottom: 10px;line-height:normal;text-align:left;display:block;font-family: \"Noto Sans KR Bold\";'>{$title}</h4>
            <table style='width: 100%;'>
              <colgroup>
                <col width='140'>
                <col width='275'>
                <col width='140'>
                <col width='275'>
              </colgroup>";
        $idx = 0;
        foreach($noticeInfo as $name => $value){
            if( $idx % 2 == 0){
                $noticeTable .="<tr>";
            }
            $noticeTable .= "<th style='background-color:#FFFDF5;padding:10px 14px;font-weight: bold;text-align: left;font-size:12px;word-break: keep-all;font-family: \"Noto Sans KR Bold\";border-bottom:2px solid #fff;line-height:150%;color:#000;'>{$name}</th>
                    <td style='background-color:#FFFFFC;padding:10px 14px;text-align: left;font-size:12px;border-bottom:2px solid #fff;line-height:150%;color:#000;'>{$value}</td>";

            if(($idx + 1) % 2 == 0 || ($idx + 1) == count($noticeInfo)){
                $noticeTable .="</tr>";
            }
            $idx++;
        }
        $noticeTable .= "</table>
            <p style='margin-top: 12px;font-size:12px;color:#8a9299;text-align:left;'>{$info}</p>
        </div>";

        return $noticeTable;
    }
}

/**
 * base64 확장자 추출
*/
if (!function_exists("getExtensionFromBase64")) {
    function getExtensionFromBase64($base64String) {
        // Base64 문자열에서 데이터 부분만 추출
        $data = explode(',', $base64String);
        if (count($data) > 1) {
            $base64String = $data[1];
        } else {
            $base64String = $data[0];
        }

        // 디코딩하여 바이너리 데이터로 변환
        $binaryData = base64_decode($base64String);

        // 파일 정보 객체 생성
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($binaryData);

        // MIME 타입에서 확장자 추출
        $extension = getExtensionFromMimeType($mimeType);

        return $extension;
    }
}

/**
 * base64 확장자 추출
*/
if (!function_exists("getExtensionFromMimeType")) {
    function getExtensionFromMimeType($mimeType) {
        $mimeMap = [
            'image/jpeg'      => 'jpg',
            'image/png'       => 'png',
            'image/gif'       => 'gif',
            'image/bmp'       => 'bmp',
            'image/webp'      => 'webp',
            'text/plain'      => 'txt',
            'text/html'       => 'html',
            'application/pdf' => 'pdf',
            // 필요한 MIME 타입들을 추가하세요
        ];

        return isset($mimeMap[$mimeType]) ? $mimeMap[$mimeType] : 'jpg';
    }
}

/** 중복 글자 제거 */
if (!function_exists("removeDuplicateWords")) {
    function removeDuplicateWords($input)
    {
        // 문자열을 공백을 기준으로 단어 배열로 변환
        $words = explode(' ', $input);

        // 고유한 단어들을 저장할 배열 초기화
        $uniqueWords = [];

        // 단어들을 순회하면서 고유한 단어만 추가
        foreach ($words as $word) {
            if (!in_array($word, $uniqueWords)) {
                $uniqueWords[] = $word;
            }
        }

        // 고유한 단어들을 다시 문자열로 결합
        $result = implode(' ', $uniqueWords);

        return $result;
    }
}

/** 해당 카테고리와 모든 자식 카테고리 추출 */
if (!function_exists("findChildCategoryIds")) {
    function findChildCategoryIds(Model $cateObj): array
    {
        $allChildCates = [$cateObj->category_id];

        if( $cateObj->level == 1 ){
            $secondChildCates  = Category::where("parent_cate_id", $cateObj->category_id)->pluck("category_id");
            $thirdChildCates   = Category::whereIn("parent_cate_id", $secondChildCates)->pluck("category_id");
            $allChildCatesObjs = $secondChildCates->merge($thirdChildCates)->toArray();
            $allChildCates     = array_merge($allChildCates, $allChildCatesObjs);
        } else if( $cateObj->level == 2 ) {
            $allChildCatesObjs = Category::where("parent_cate_id", $cateObj->category_id)->pluck("category_id")->toArray();
            $allChildCates     = array_merge($allChildCates, $allChildCatesObjs);
        }

        return $allChildCates;
    }
}

/** 중량 여부로 관련 상태 업데이트 */
if (!function_exists("upWeightStatus")) {
    function upWeightStatus(int $offerId): void
    {
        $obj = ProductOptionData::select('offer_id', DB::raw('MAX(weight) as max_weight'))
        ->where("offer_id", $offerId)
        ->groupBy("offer_id")->first();
        if( $obj != null ){
            if( $obj->max_weight >= 20 ){
                ProductData::where("offer_id", $offerId)->update([
                    "status" => ProductConstant::PRD_STATUS_EXCEPT
                ]);
            } else {
                $maxWeight         = $obj->max_weight;
                $getWeightDelivery = getWeightDelivery($maxWeight);
                $weight            = $getWeightDelivery["weight"];

                $maxWeight = (int)ceil($maxWeight);
                $weightType = ProductConstant::WEIGHT_STATUS_NONE;

                if( $maxWeight > 0 ){
                    $weightType = ProductConstant::WEIGHT_STATUS_PRODUCT;
                } else {
                    $prdObj = ProductData::where("offer_id", $offerId)->first();
                    if( $prdObj != null ){
                        $cateObj = CategoryWeightData::where("category_id", $prdObj->category_id)->first();
                        if( $cateObj != null ){
                            $weightType = ProductConstant::WEIGHT_STATUS_CATEGORY;
                        }
                    }
                }

                ProductWeightData::updateOrCreate(
                    [
                        "offer_id" => $offerId,
                    ],
                    [
                        "weight"      => $weight,
                        "weight_type" => $weightType,
                    ]
                );
            }
        }
    }
}

if (!function_exists("removeForbiddenText")) {
    /**
     * @func removeForbiddenText
     * @description '삭제어 처리'
     * @param Collection $removeForbiddenWords
     * @param string $text
     * @return string
     */
    function removeForbiddenText(Collection $removeForbiddenWords, string $text, string $apply_type): string
    {
        foreach ($removeForbiddenWords as $delObj) {
            if( $delObj->apply_type == ForbiddenWordConstant::KEYWORD_APPLY_ALL || $delObj->apply_type == $apply_type ){
                $removeWord = $delObj->target_keyword;

                // 1. 삭제어 앞과 뒤에 공백이 없는 경우 삭제어만 삭제
                //    예: "HelloWord"에서 "Word"를 삭제 -> "Hello"
                $pattern1 = '/(?<!\s)' . preg_quote($removeWord, '/') . '(?!\s)/';
                if (preg_match($pattern1, $text)) {
                    $text = preg_replace($pattern1, '', $text);
                }

                // 2. 삭제어 앞 또는 뒤에 공백이 있는 경우 삭제어만 삭제
                //    예: "Hello Word "에서 "Word"를 삭제 -> "Hello "
                $pattern2 = '/(?<=\s)' . preg_quote($removeWord, '/') . '(?!\s)|(?<!\s)' . preg_quote($removeWord, '/') . '(?=\s)/';
                if (preg_match($pattern2, $text)) {
                    $text = preg_replace($pattern2, '', $text);
                }

                // 3. 삭제어 앞과 뒤에 공백이 있는 경우 하나의 공백과 삭제어만 삭제
                //    예: "Hello Word Test"에서 "Word"를 삭제 -> "Hello Test"
                $pattern3 = '/\s+' . preg_quote($removeWord, '/') . '\s+/';
                if (preg_match($pattern3, $text)) {
                    $text = preg_replace($pattern3, ' ', $text);
                }
            }
        }

        return $text;
    }
}

if (!function_exists("replaceForbiddenText")) {
    /**
     * @func replaceForbiddenText
     * @description '교체어 처리'
     * @param Collection $replaceForbiddenWords
     * @param string $text
     * @return string
     */
    function replaceForbiddenText(Collection $replaceForbiddenWords, string $text, string $apply_type): string
    {
        foreach ($replaceForbiddenWords as $replaceObj) {
            if( $replaceObj->apply_type == ForbiddenWordConstant::KEYWORD_APPLY_ALL || $replaceObj->apply_type == $apply_type ){

                $originWord  = $replaceObj->target_keyword;
                $replaceWord = $replaceObj->replace_keyword;

                // 1. 해당 텍스트가 originWord에 걸릴 시 replaceWord로 교체
                $text = str_replace($originWord, $replaceWord, $text);
            }
        }

        return $text;
    }
}

if (!function_exists("formatToKST")) {
    function formatToKST(string $dateString): string
    {
        // 날짜 문자열을 파싱하여 DateTime 객체로 변환
        $date = DateTime::createFromFormat('YmdHisvO', $dateString);

        // 한국 시간대로 설정
        $date->setTimezone(new DateTimeZone('Asia/Seoul'));

        // 원하는 형식으로 출력 (예: Y-m-d H:i:s)
        return $date->format('Y-m-d H:i:s');
    }
}

if (!function_exists("formatToCST")) {
    function formatToCST(string $dateString): string
    {
        // 날짜 문자열을 파싱하여 DateTime 객체로 변환 (한국 시간대 기준)
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $dateString . ' 00:00:00', new DateTimeZone('Asia/Seoul'));

        // 중국 시간대로 설정
        $date->setTimezone(new DateTimeZone('Asia/Shanghai'));

        // 원하는 형식으로 출력 (예: YmdHisO)
        return $date->format('YmdHisvO');
    }
}

/** 카멜 케이스로 변환 */
if (!function_exists("convertCamelCase")) {
    function convertCamelCase(string $text): array
    {
        // 1. 카멜 케이스로 변경
        $camelCase = lcfirst(str_replace(' ', '', ucwords(strtolower(str_replace('_', ' ', $text)))));

        // 2. 모두 소문자로 변경
        $lowerCase = strtolower($camelCase);

        return [
            'camelCase' => $camelCase,
            'lowerCase' => $lowerCase
        ];
    }
}

/** 중량별 배송비 추출 */
if (!function_exists("getWeightDelivery")) {
    function getWeightDelivery(?float $weight = 0.0): array
    {
        $result = [
            "weight"             => $weight,
            "shipping_price"     => ProductConstant::WEIGHT_STATUS_NONE_PRICE,
            "air_shipping_price" => ProductConstant::WEIGHT_STATUS_NONE_PRICE,
        ];

        $weights = getCacheWeightDatas();

        // 주어진 무게를 0.5 단위로 올림
        $roundedWeight = number_format(ceil($weight * 2) / 2, 1);

        // 올림된 무게에 해당하는 배송 정보를 찾음
        if (isset($weights[$roundedWeight])) {
            $result = [
                "weight"             => $roundedWeight,
                "shipping_price"     => $weights[$roundedWeight]["shipping_price"],
                "air_shipping_price" => $weights[$roundedWeight]["air_shipping_price"],
            ];
        }
        return $result;
    }
}

/** 중량별 배송비 캐싱 데이터 가져오기 */
if (!function_exists("getCacheWeightDatas")) {
    function getCacheWeightDatas(): array
    {
        $weights = Cache::get('weight_data');

        if ($weights === null) {
            $weights = Cache::remember('weight_data', now()->addMinutes(180), function () {
                return WeightData::select(['weight', 'shipping_price', 'air_shipping_price'])->orderBy("weight", "asc")->get()->keyBy('weight')->map(function ($item) {
                    return $item->makeHidden('weight');
                })->toArray();
            });
        }

        return $weights;
    }
}

/** 중량별 배송비 캐싱 데이터 삭제 */
if (!function_exists("removeCacheWeightDatas")) {
    function removeCacheWeightDatas(): void
    {
        Cache::forget('weight_data');
    }
}

/** 스네이크 케이스를 카멜 케이스로 변환하는 함수 */
function snakeToCamelCase(string $string) {
      // 대문자 앞에 언더스코어를 추가하고 전체를 소문자로 변환
    $pattern = '/(?<=\w)(?=[A-Z])/';
    $result  = strtolower(preg_replace($pattern, '_', $string));
    return $result;
}