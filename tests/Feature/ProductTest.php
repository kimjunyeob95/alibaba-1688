<?php

namespace Tests\Feature;

use App\Constants\ImageConstant;
use App\Constants\InspectConstant;
use App\Constants\ProductConstant;
use App\Models\CategoryMapping;
use App\Models\ProductData;
use App\Models\ProductForbiddenData;
use App\Models\ProductImageData;
use App\Models\ProductInspectData;
use App\Models\ProductOptionData;
use App\Packages\S3;
use App\Services\GenuioService;
use App\Services\Product\ProductW2;
use App\Vo\Product\Product1688ImageDto;
use Exception;
use Tests\TestCase;
use Illuminate\Support\Facades\File;

class ProductTest extends TestCase
{
    # php artisan test --filter testConvertW1toW2
    public function testConvertW1toW2()
    {
        $productW2 = app(ProductW2::class);
        $productW2->convertW1toW2();
    }

    # php artisan test --filter testInspectStatus
    public function testInspectStatus()
    {
        $imgList = [774105505957, 768771981954, 759868055464, 772497358996, 753932778314, 753998755219, 774899398684, 775600338013, 775321041247, 775297618141, 774896754738, 752797231125, 773055203534, 770392441229, 772682143729, 772643096515, 772640152954, 772459553766, 772647520087, 776444427875, 769059011928, 755063976530, 769405131194, 742517946366, 38080885828, 574459064869, 646246973598, 630982191913, 593066581624, 40503327223, 613918622757, 610724536971, 564134216174, 742406091238, 780538948812, 782177132272, 754431924761, 772171209696, 761019453229, 704515764055, 733372958212, 772571930806, 772568970334, 757812686367, 761745820831, 713455979580, 716040225117, 569556811378, 768664403024, 776948319704, 768585358075, 770411004572, 778966629438, 779050831353, 755714371140, 679611521566, 780672484769, 761274931971, 774234313058, 780352318978, 731033821634, 694047975809, 706772418621, 600322556141, 774793069447, 773885886520, 780287694819, 777545511051, 777802118428, 758610153318, 759129526863, 732881536064, 743174774987, 675066274377, 777997602986, 773900207687, 778858949957, 774553945209, 768471514253, 773638847181, 778979271833, 683779921655, 734195051851, 779855721545, 773147798391, 776582390502, 782730048709, 776677758669, 738522857638, 652411782746, 666443460184, 768295574407, 774762551012, 759975669613, 780218420381, 642917442394, 770554852732, 775397276693, 775135725627, 781430653318, 778704134043, 716599561263, 778420867411, 773493897402, 777235756761, 767930799680, 770930949050, 762638595086, 779497620724, 768895515990, 773406953564, 774237253581, 702210125415, 772642286623, 563969885580, 777710710898, 608820691030, 762703893495, 703870169662, 694507881617, 744828682038, 770112392846, 777594386068, 739505665905, 756537311383, 730301197041, 780373568914, 758287004796, 777715032002, 779083415377, 776842566208, 726924122647, 725638803097, 783108753919, 727364725792, 737880543448, 733156361349, 660071490522, 774529832804, 781003243986, 575667632588, 772646018403, 643150382941, 775083814007, 717544162312, 679310964577, 772751932654, 746938328602, 767881804160, 688115220921, 753757896382, 711392860000, 693305094418, 713839900537, 783636377239, 686629858542, 707689173509, 669581298050, 774847040700, 649791062566, 763645471412, 691863245483, 672159721509, 771563136683, 672780191879, 653235285572, 680407630982, 711334157835, 717927384119, 755918937831, 782921296459, 674622853460, 774724033508, 669825159780, 672689064400, 723121419637, 710986235135, 727864738988, 777792836491, 713245694078, 770230452441, 768879033424, 737824492663, 747071809202, 772610269052, 653047300632, 774163246299, 753642584439, 752592171363, 752015897131, 750427434295, 753845967259, 674957663345, 752715035182, 670470199630, 771411090358, 694219643697, 751748813391, 778395509824, 556270334701, 782346380107, 750011357484, 758775398779, 733716570997, 675434707556, 682366917644, 732270667236, 773704739913, 764816808167, 750790401396, 730352233814, 44798792934, 564416838034, 714200599075, 777340992486, 770749181086, 687532288434, 710710800765, 698615553086, 693461279129, 622207035105, 770296099572, 773711631753, 773718379214, 779814674223, 778026182620, 748977519366, 732781531064, 773533691791, 759140246659, 733101657237, 733760641455];
        foreach ($imgList as $offerId) {
            ProductInspectData::updateOrCreate(
                [
                    "offer_id"     => $offerId,
                    "inspect_type" => InspectConstant::INSPECT_IMAGE,
                ],
                [
                    "is_inspect" => InspectConstant::IS_INSPECT_Y,
                ]
            );
        }

        $inspectAlllist = [44798792934, 774937173017, 775257416960, 772391335814, 753774782072, 719630001340, 703792918580, 753998755219, 753932778314, 676756658536, 733419122555, 688106223618, 675434707556, 768771981954, 747343384347, 704839318363, 671048632318, 625880005064, 714200599075, 770296099572, 774724033508, 670497313500, 774574296523, 691863245483, 774847040700, 713839900537, 528703635214, 730301197041, 704511187266, 775804062624, 769402323162, 730390930672, 777722094116, 729286481679, 684610022166, 769140355224, 779855721545, 761732754484, 734195051851, 683779921655, 755988284596, 778979271833, 773638847181, 650101307123, 679746054646, 779092891030, 769922853332, 732881536064, 777802118428, 780287694819, 780008576239, 774793069447, 774243261193, 779050831353, 707915136379, 779753731116, 768039345543, 761745820831, 759579775142, 756801615105, 756912043330, 748916718004, 773533691791, 763515463866, 748977519366, 693751688938, 755221871572, 742406091238, 587284549656, 593066581624, 777866155405, 601600019676, 608515084765, 574006493177, 560783163981, 659695233213, 678618907395, 773370837955, 773374181648, 750790401396, 764816808167, 775569428739, 771903123037, 772013125670, 769741166794, 773055203534, 760146861870, 732270667236, 714748365091, 725773721662, 772015578355, 718845555135, 743503632291, 716919739991, 718561592538, 692857048448, 729869226545, 698427105208, 739557971012, 723868032706, 726069061687, 574865514042, 744728958339, 719178567225, 730867070542, 733042539148, 673456814786, 712941351827, 735222993078, 751012989532, 580412061403, 581834214778, 777973373165, 737760892927, 711309685754, 729714651717, 710775979361, 750778869849, 770141962584, 769661716228, 758311655605, 724579905592, 681326146131, 773376524888, 704171723834, 704495314818, 770634736172, 779611292740, 753157790782, 643738021480, 776155631892, 768887417536, 735274263129, 773609054012, 712261982365, 771465798518, 767909513525, 779289105221, 610342350428];
        foreach ($inspectAlllist as $offerId) {
            ProductInspectData::updateOrCreate(
                [
                    "offer_id"     => $offerId,
                    "inspect_type" => InspectConstant::INSPECT_IMAGE,
                ],
                [
                    "is_inspect" => InspectConstant::IS_INSPECT_Y,
                ]
            );
            ProductInspectData::updateOrCreate(
                [
                    "offer_id"     => $offerId,
                    "inspect_type" => InspectConstant::INSPECT_PRODUCT,
                ],
                [
                    "is_inspect" => InspectConstant::IS_INSPECT_Y,
                ]
            );
            ProductInspectData::updateOrCreate(
                [
                    "offer_id"     => $offerId,
                    "inspect_type" => InspectConstant::INSPECT_NOTICE,
                ],
                [
                    "is_inspect" => InspectConstant::IS_INSPECT_Y,
                ]
            );

            ProductData::where("offer_id", $offerId)->update([
                "inspect_status" => ProductConstant::INSPECT_STATUS_Y
            ]);
        }
        dd("끝");
    }

    # s3 upload
    # php artisan test --filter testS3Upload
    public function testS3Upload()
    {
        $s3 = new S3();
        $offerId = 737834654023; 
        
        $prdObj   = ProductData::where("offer_id", $offerId)->first();
        $dateName = $prdObj->created_at->format('Y/m/d');

        $imgObj = ProductImageData::where([
            "offer_id" => $offerId,
            "img_type" => "main",
        ])->first();
        $mime = pathinfo($imgObj->img_url_origin, PATHINFO_EXTENSION);
        if (preg_match('/^(jpg|jpeg|png|gif)/i', $mime, $matches)) {
            $mime = $matches[0];
        }
        if( $imgObj->img_type == ImageConstant::IMAGE_TYPE_MAIN ){
            $imgName  = "/product/" . $dateName . "/" . $offerId . "_" . $imgObj->img_type . "." . $mime;
        } else {
            $imgName  = "/product/" . $dateName . "/" . $offerId . "_" . $imgObj->id . "_" . $imgObj->img_type . "." . $mime;
        }
        $options = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];
        $context = stream_context_create($options);
        $fileContent = file_get_contents($imgObj->img_url_origin, false, $context);
        $imgEncodeBase64 = base64_encode($fileContent);
        $uploadResult    = $s3->uploadFile($imgName, base64_decode($imgEncodeBase64));
        if( $uploadResult == true ) {
            $img_url_trans = env("AWS_URL") . $imgName;
            dd($img_url_trans);
        }
        
    }

    # Genuio img queue create
    # php artisan test --filter testGenuioImgCreate
    public function testGenuioImgCreate()
    {
        $offerId                 = 740275289359;
        $product1688ImageDtoList = [];
        $imgObjs                 = ProductImageData::where("offer_id", $offerId)->get();
        foreach ($imgObjs as $imgObj) {
            $product1688ImageDto = new Product1688ImageDto();
            $product1688ImageDto->bind([
                "offerId"        => $offerId,
                "imgType"        => $imgObj->img_type,
                "is_except"      => $imgObj->is_except,
                "img_url_origin" => $imgObj->img_url_origin,
                "img_url_trans"  => "",
                "isChangeImg"    => true,
                "width"          => 800,
                "height"         => 800,
                "byte"           => 8,
                "mime"           => "image/jpeg"
            ]);
            $product1688ImageDtoList[] = $product1688ImageDto;
        }

        $geService = app(GenuioService::class);
        $geService->createTransProductImg($product1688ImageDtoList, $offerId);
    }

    # php artisan test --filter testProductPrice
    public function testProductPrice()
    {
        $prdObjs = ProductOptionData::groupBy("offer_id")->get();

        foreach ($prdObjs as $prdObj) {
            $price_1688 = $prdObj->price_1688;
            $option_price = round( $price_1688 * env("1688_EXCHANGE_RATE", 200) , -1);  // 1의 자리 반올림

            $option_price_sum = (int)intval($option_price) + intval($option_price * env("OPTION_PRICE_RATE", 0.12));
            $option_price_cal = round($option_price_sum / 10) * 10;
            $onch_price = $option_price_cal;
    
            $recom_cus_price_sum = (int)intval($option_price) + intval($option_price * env("RECOM_CUS_PRICE_RATE", 0.45));
            $recom_cus_price_cal = round($recom_cus_price_sum / 10) * 10;
            $cus_price           = $recom_cus_price_cal;
            $recom_cus_price     = $recom_cus_price_cal;

            ProductOptionData::where("offer_id", $prdObj->offer_id)->update([
                "option_price"    => $option_price,
                "onch_price"      => $onch_price,
                "cus_price"       => $cus_price,
                "recom_cus_price" => $recom_cus_price,
            ]);
        };

        dd("끝");
    }

    # php artisan test --filter testProductName
    public function testProductName()
    {
        $prdObjs = ProductData::get();
        // $prdObjs = ProductForbiddenData::get();

        $filePath     = public_path('app/w_forbidden_word.txt');
        $removeWords  = [];
        $replaceWords = [];
        if (File::exists($filePath)) {
            $lines = File::lines($filePath);
            foreach ($lines as $line) {
                $lineArr = explode(',', $line);

                $type     = $lineArr[0];
                $status   = $lineArr[1];
                $offerId  = $lineArr[2];
                $prevWord = $lineArr[3];
                $nextWord = $lineArr[4];

                if( $status == "검토완료" ){
                    if( $type == "삭제어" ){
                        $removeWords[] = $prevWord;
                    } else if( $type == "교체어" ){
                        $replaceWords[] = [
                            "prevWord" => $prevWord,
                            "nextWord" => $nextWord,
                        ];
                    }
                }
            }
        } else {
            throw new Exception("파일이 존재하지 않습니다.");
        }
        foreach ($prdObjs as $prdObj) {
            $forObj = ProductForbiddenData::where("offer_id", $prdObj->offer_id)->first();

            if( $forObj != null ){
                $prd_name_kr = $forObj->prd_name_trans_origin;
            } else {
                $prd_name_kr = $prdObj->prd_name_kr;
            }

            // 1. 삭제어
            $upText = $this->removeSpecialSequence($prd_name_kr, $removeWords);

            // 2. 교체어
            $upText = $this->replaceWord($upText, $replaceWords);

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
        };

        dd("끝");
    }

    function removeSpecialSequence(string $text, array $removeWords)
    {
        foreach ($removeWords as $removeWord) {
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

    function replaceWord(string $text, array $replaceWords)
    {
        foreach ($replaceWords as $replaceWordArr) {
            $originWord  = $replaceWordArr["prevWord"];
            $replaceWord = $replaceWordArr["nextWord"];

            // 1. 해당 텍스트가 originWord에 걸릴 시 replaceWord로 교체
            $text = str_replace($originWord, $replaceWord, $text);
    
        }
        return $text;
    }
}
