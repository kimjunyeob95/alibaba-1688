<?php

namespace Tests\Feature;

use App\Constants\ForbiddenWordConstant;
use App\Models\ForbiddenWordData;
use Exception;
use Tests\TestCase;
use Illuminate\Support\Facades\File;

class ForbiddenTest extends TestCase
{
    # php artisan test --filter testCreateForbidden
    public function testCreateForbidden()
    {
        $filePath     = public_path('app/w_forbidden_word.txt');
        if (File::exists($filePath)) {
            $lines = File::lines($filePath);
            foreach ($lines as $line) {
                $lineArr = explode(',', $line);

                $type           = $lineArr[0];
                $status         = $lineArr[1];
                $targetKeyword  = $lineArr[3];
                $replcaeKeyword = $lineArr[4];

                $keywordType = ForbiddenWordConstant::KEYWORD_DELETE;
                if( $status == "검토완료" ){
                    if( $type == "삭제어" ){
                        $replcaeKeyword = "";
                    } else if( $type == "교체어" ){
                        $keywordType = ForbiddenWordConstant::KEYWORD_REPLACE;
                    }

                    ForbiddenWordData::create([
                        "keyword_type"    => $keywordType,
                        "target_keyword"  => $targetKeyword,
                        "replace_keyword" => $replcaeKeyword,
                        "apply_type"      => ForbiddenWordConstant::KEYWORD_APPLY_ALL
                    ]);
                }
            }
        } else {
            throw new Exception("파일이 존재하지 않습니다.");
        }

        dd("끝");
    }
}
