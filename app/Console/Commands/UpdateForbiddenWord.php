<?php
namespace App\Console\Commands;

use App\Constants\ForbiddenWordConstant;
use App\Models\ForbiddenWordData;
use App\Models\ProductData;
use App\Models\ProductForbiddenData;
use App\Services\Service1688Product;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class UpdateForbiddenWord extends Command
{
    protected $signature   = 'update_forbidden_word';
    protected $description = '금칙어 사전 적용';

    protected Service1688Product $service1688Product;

    public function __construct(Service1688Product $service1688Product)
    {
        parent::__construct();

        $this->service1688Product = $service1688Product;
    }
    /*
     * 실행 구문 
     * php artisan update_forbidden_word
    */
    public function handle()
    {
        $prdObjs     = ProductData::get();
        $delObjs     = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_DELETE)->get();
        $replaceObjs = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_REPLACE)->get();

        foreach ($prdObjs as $prdObj) {
            $forObj = ProductForbiddenData::where("offer_id", $prdObj->offer_id)->first();

            if( $forObj != null ){
                $prd_name_kr = $forObj->prd_name_trans_origin;
            } else {
                $prd_name_kr = $prdObj->prd_name_kr;
            }

            // 1. 삭제어
            $upText = $this->removeSpecialSequence($prd_name_kr, $delObjs);

            // 2. 교체어
            $upText = $this->replaceWord($upText, $replaceObjs);

            $upText = trim($upText);

            if( $prd_name_kr != $upText ){
                ProductForbiddenData::updateOrCreate(
                    ["offer_id" => $prdObj->offer_id],
                    [
                        "prd_name_trans_origin"    => $prd_name_kr,
                        "prd_name_trans_forbidden" => $upText
                    ]
                );
                ProductData::where("id", $prdObj->id)->update([
                    "prd_name_kr" => $upText
                ]);
            }
        }
    }

    function removeSpecialSequence(string $text, Collection $delObjs): string
    {
        foreach ($delObjs as $delObj) {
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

        return $text;
    }

    function replaceWord(string $text, Collection $replaceObjs)
    {
        foreach ($replaceObjs as $replaceObj) {
            $originWord  = $replaceObj->target_keyword;
            $replaceWord = $replaceObj->replace_keyword;

            // 1. 해당 텍스트가 originWord에 걸릴 시 replaceWord로 교체
            $text = str_replace($originWord, $replaceWord, $text);
        }

        return $text;
    }
}
