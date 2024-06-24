<?php

namespace Tests\Feature;

use App\Constants\MallConstant;
use App\Constants\OnchannelConstant;
use App\Constants\ProductConstant;
use App\Models\CategoryMapping;
use App\Models\OnchannelProductLog;
use App\Models\OnchCategoryExcelDataCopy2;
use App\Models\OnchProductData;
use App\Models\ProductData;
use App\Packages\Onchannel;
use Exception;
use Tests\TestCase;
use Illuminate\Pagination\Paginator;

class OnchannelTest extends TestCase
{

    # 온채널 관리자 상품 등록
    # php artisan test --filter testOnchProductCreate
    public function testOnchProductCreate()
    {
        $onchannel = app(Onchannel::class);

        $offerIds = [585842964281,735120551021,652907106270,686694195910,747354909348,761380858589,549971207521,671736839404,627330207779,720911347802,724394278891,702176659953,637093791751,558517432387,741533583055,672020055403,701221575317,766648812857,732834019282,670286384129,772678453278,571852703201,674380447856,647850353247,677110138929,736822214042,707286908954,710696827070,613576206934,690769948320,652407850539,728220339801,715510714930,656179958380,682244209400,774470937599,720106929060,705110641759,647096598175,730049757565,657376803347,775620260610,685668779237,678060817514,707205465335,656215819231,680193774067,651526017707,625362772134,669769971166,592663184491,685760116114,771868960020,637185533269,737828283791,625743264206,768772746001,652502834731,706670810197,694105102233,722003236187,610487235986,725305425042,631746583098,739654909233,781778058638,637683679907,785180345333,671008604806,709449879046,732357972170,776238859317,711188124608,646490178234,782458953492,746407625426,641126532285,726249635565,724196698530,693818025802,787351431102,769087551969,709430038381,631292704138,44493053871,626127884165,776864860051,732845027668,668379081397,730002349003,676956375287,669903338535,778926824310,695178524045,736211333077,674022620416,635539619277,666506305218,569213911565,702484349176,642125925410,664445972845,743676569051,44035481787,735221042225,713351981872,648619439517,687824613135,681537516174,617005767076,659472935008,595063369777,775105698746,688221907706,624338307516,739752490951,590146485546,662354209511,596374692375,711639482320,689645145027,618197198115,636087176287,669366888719,649850212263,716280500989,709959712336,699752463831,680551989068,673298177444,641482580238,703571740158,664469073756,728366025177,737545166792,747794049135,602336934213,736820971121,592664490918,742421685287,714442024753,694981793815,579685858834,669230302975,538640823764,740485411194,738906531179,661460750052,699094686251,606673703053,741974928015,728145124379,684473057828,710998790922,620966042966,672851612868,748137198041,681518718007,728444798874,742031007521,580759517032,705607927817,683907284033,703928195800,713440154235,732576393029,695386455735,1050101164,526053278919,744145059397,550776830322,725438353742,757393413636,713123139871,1278927614,571838278398,778902836838,794242178108,725687476703,771961405939,624144561621,683772117037,768619116474,723114688538,582635579724,713358650538,621691452400,688343070733,682148737502,724730573808,676860829584,37558056185,741956992690,723787064085,660829022356,749047954303,594201053065,43718892289,702495100477,746440610608,661449936061,737893204179,693916965077,647895949973,721184494943,664299576796,696038321970,565417511337,676073359195,640405868942,642008674640,620069617361,691119578771,636317768664,651825897245,713427518175,748141026425,648160092250,674196107341,632777211443,645042919668,606143197724,679641151801,720272812707,738023783330,617142650141,752944847567,702102012397,576485596020,638447526166,772513445571,661857201536,617712533679,592438117310,607764886005,722278843281,723853823382,704552580586,588998983628,694906331281,634481689510,677276521021,654640492516,684729076932,683419540421,620752925899,714332331499,622718151412,666045618386,688763845530,525967094022,770226284677,770212254951,694604736161,739504928746,671423954886,634071834022,748112930388,595873123335,605922252235,717736496105,741411820574,650248119975,714640780715,740028081159,696023945576,767697052609,731938994079,665428407406,704623764906,708206726769,675487127252,671843214428,577102174616,582943830653,679985853485,725252498667,663317887535,707854844705,742033327272,561193817413,636087264694,643380584555,687349950867,770803611849,723162674584,642749967768,728633199977,696737323266,587665122722,691472216436,672551350953,726805415174,735440518342,668318396761,546542165484,626347176728,633801208663,570768945572,688002278507,692851584337,714899735054,706386784228,621002470608,627714096267,727881915269,730317102314,653618026042,699555373037,693692620455,599875688636,600974630457,708320222620,608571367674,709251556403,682902752071,41188844312,696998098077,665727909705,710447219164,734772157333,642951501657,619686687721,675908611703,557207746367,770301627159,711737358621,724269034042,639484700016,735465946428,712413645592,592149079891,709969570375,639361924408,666588525929,724566935405,662848865915,45604485858,684639489585,675142423720,403495100,707621000071,632583094097,616841109746,573702419609,775877747946,677479378054,733891580704,778625223125,672002099604,676956389792,590163654428,622735446557,693062500362,563970146402,524563236108,662286809350,720386559502,760728403533,732447454041,619604712970,616484309916,670674581157,742927318833,617987942066,556149532729,645479950575,712438725112,729857189606,725403839543,708383544082,710006400891,617133935172,687002599764,623858520914,534998245920,698123144758,770900623982,524732185633,769413836919,590332649091,570982284152,549720330048,792897584552,761105764928,659227839979,634971539812,770022753103,732193271720,782020535075,737552978925,662861973674,594211899418,624201815995,668933820388,677273671230,672451333138,727462537020,629922393539,670431244821,693639947494,691634576956,642871580541,654278132983,611589313031,729987637400,41053795849,659525159267,730052794052,736182259257,576221993356,679309882302,606405128695,789934801139,556621885507,654754494013,669386025260,790030671546,712894297881,627917340956,676739307195,623517441182,674694707609,673427253700,642595556600,644666619515,702346589564,570917152167,708870350773,674981714275,692875194580,758878885959,668770442120,672722823796,785466363001,704273997656,522086841344,721601271230,784672510917,641137106204,571253899191,619056351738,719118609107,676364033622,537793138976,640533181244,732334823836,770818365050,520406910915,692735434171,708695872083,674834457288,567244166801,735608959171,608028166727,717587157948,788546022713,633623812321,552283511603,652544396183,702718342488,627494528419,613423585044,625765666528,775703514813,650201820708,553240787837,733443982931,569834353818,625834423325,587398117276,624312926680,746566787971,656015725023,680191502540,623815878427,569636089363,636570023795,597909519884,618248342122,570332700177,588923683130,575597818362,562882982243,599174589428,559449522125,609732785930,542071843331,557828190612,624282883613,599342548883,541164926921,532603776184,572291238200,538157248347,565105088821,555495473205,651449214990,694491738252,534018550312,544676991951,590025347418,669056146176,594756152939,642045272663,620273022957,559328418737,602402512920,667992076210,564997513982,634372432539,564650790727,35879293142,559773976730,37014358992,566106840054,670798933056,570178468919,693436325880,574173669601,534688129269,611362216165,607503273012,568951381533,674519889591,559911888704,653506484257,666226893263,709245705689,670715244933,703096080270,626249461087];
        $onchannel->productRegist($offerIds);
        dd("끝");
    }

    # 온채널 관리자 상품 등록
    # php artisan test --filter testOnchProductModi
    public function testOnchProductModi()
    {
        $token    = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJtZW1iZXJfaWQiOiJvbmNoMTY4OCIsIm1tYnJfdHlwZSI6Im9uY2htYW4iLCJ0aW1lc3RhbXAiOjQ4NjkxODE5ODJ9.AijywuhAP6ZkxySsZWOqEU-ID8XoesePcm8lSB1w1rw";
        $endPoint = "https://task.onch3.co.kr/api/w/product/edit";

        $getPrdObjs = ProductData::with([
            "images",
            "options",
        ])
        ->select(["product_datas.*", "b.prd_code"])
        ->join("onchannel_product_logs as b","product_datas.offer_id", "=", "b.offer_id")
        ->where("b.regist_success", "Y");

        $perPage = 900;

        $totalCount = $getPrdObjs->count();
        $totalPages = ceil($totalCount / $perPage);

        debug_log("실행", "onchannel", "modiOnchannel");

        for ($page = 1; $page <= $totalPages; $page++) {
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        
            // paginate 메소드는 새 Paginator 인스턴스를 반환합니다.
            $pagedData = $getPrdObjs->paginate($perPage);
            $results   = $pagedData->items();

            $msg = "(" . $page . "/" . $totalPages. ") prdCnt: " . count($results) . " 실행시작";
            debug_log($msg, "onchannel", "modiOnchannel");

            foreach ($results as $prdObj) {
                try {
                    $offer_id = $prdObj->offer_id;

                    $payload = [
                        "prd_code"  => $prdObj->prd_code,
                        "min_count" => $prdObj->start_quantity,
                    ];
        
                    $images = [];
                    foreach ($prdObj->images as $img) {
                        if( $img->is_except == "Y" && $img->lang != "kr") continue;
                        $images[] = [
                            "img_type" => $img->img_type,
                            "img_url"  => $img->img_url_origin,
                        ];
                    }

                    $payload["images"] = $images;

                    $options = [];
                    foreach ($prdObj->options as $option) {
                        $options[] = [
                            "op_code"   => $option->id,
                            "option_nm" => $option->option_name_kr,
                        ];
                    }
                    $payload["options"] = $options;

                    $header = array(
                        'Content-type: application/json',
                        'Authorization: Bearer '.$token,
                    );

                    $result = helpers_curl("POST", $endPoint, $header, $payload);
                    
                    if( !isset($result["isSuccess"]) || $result["isSuccess"] != true ){
                        $res = [
                            "offer_id" => $offer_id,
                            "prd_code" => $prdObj->prd_code,
                            "result"   => $result
                        ];
                        debug_log(json_encode($res, JSON_UNESCAPED_UNICODE), "onchannel", "modiOnchannel");
                    }
                } catch (Exception $e) {
                    $res = [
                        "offer_id" => $offer_id,
                        "prd_code" => $prdObj->prd_code,
                        "error"    => $e->getMessage()
                    ];
                    debug_log(json_encode($res, JSON_UNESCAPED_UNICODE), "onchannel", "modiOnchannel");
                }

                sleep(1);
            }
        }
        

        debug_log("종료", "onchannel", "modiOnchannel");
    }

    # 온채널 이미지 콜백
    # php artisan test --filter testOnchCallBackImg
    public function testOnchCallBackImg()
    {
        $domain = env("OC_DOMAIN", "https://task.onch3.co.kr");

        $header = array(
            'Content-type: application/json'
        );

        $payload = [
            "channel_queue_id" => 1,
            "member_id"        => "tester123",
        ];

        $payload["images"][] = [
            "id"             => 1,
            "origin_url"     => "https://naver.png",
            "translated_url" => "https://onch-1688.s3.ap-northeast-2.amazonaws.com/onchannel/test123/product/20240523_143849_1.png",
            "error"          => ""
        ];
        $endPoint = $domain . "/api/w/image/callback";
        $result   = helpers_curl("POST", $endPoint, $header, $payload);
        dd($result);
    }

    # 온채널 카테고리 등록
    # php artisan test --filter testOnchCategoryCreate
    public function testOnchCategoryCreate()
    {
        $cateMappingObjs = CategoryMapping::where("mapping_channel", ProductConstant::MAPPING_WAPP)->get();
        foreach ($cateMappingObjs as $cateMappingObj) {
            $ocCateObj = OnchCategoryExcelDataCopy2::where("codenum", $cateMappingObj->mapping_code)->first();
            if( $ocCateObj != null ){
                CategoryMapping::updateOrCreate(
                    [
                        "mapping_channel" => ProductConstant::MAPPING_OC_CHANNEL,
                        "category_id"     => $cateMappingObj->category_id
                    ],
                    [
                        "mapping_code" => $cateMappingObj->mapping_code
                    ]
                );
            }
        }
        dd("끝");
    }

    # 온채널 상품 검색
    # php artisan test --filter testOnchPrdSearch
    public function testOnchPrdSearch()
    {
        debug_log("실행", "onchannel/prdSearch", "prdSearch");

        $channel = OnchannelConstant::PRD_CHANNEL;
        // $channel = OnchannelConstant::PRD_CHANNEL_PRIVATE;

        $builder = OnchannelProductLog::select(["offer_id"])
        ->where([
            "send_type"      => $channel,
            "regist_success" => MallConstant::REGIST_SUCCESS
        ]);

        $perPage = 2000;

        $totalCount = $builder->count();
        $totalPages = ceil($totalCount / $perPage);


        for ($page = 1; $page <= $totalPages; $page++) {
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        
            // paginate 메소드는 새 Paginator 인스턴스를 반환합니다.
            $pagedData = $builder->paginate($perPage);
            $results   = $pagedData->items();

            foreach ($results as $obj) {
                $ocObj = OnchProductData::where([
                    "prd_channel" => $channel,
                    "product_id"  => OnchannelConstant::ONCH1688,
                    "jejo_code"   => $obj->offer_id
                ])->first();

                if( $ocObj == null ){
                    $log = "empty " . $obj->offer_id;
                    debug_log($log, "onchannel/prdSearch", "prdSearch");
                }
            }

            $log = "완료 ({$page}/{$totalPages})";
            debug_log($log, "onchannel/prdSearch", "prdSearch");
        };

        debug_log("종료", "onchannel/prdSearch", "prdSearch");
    }

    # 온채널 상품 삭제
    # php artisan test --filter testOnchPrdDelete
    public function testOnchPrdDelete()
    {
        debug_log("실행", "onchannel/prdDelete", "prdDelete");

        $channel = OnchannelConstant::PRD_CHANNEL;
        // $channel = OnchannelConstant::PRD_CHANNEL_PRIVATE;

        $objs = OnchProductData::select("jejo_code")->where([
            "prd_channel" => $channel,
            "product_id"  => OnchannelConstant::ONCH1688
        ])->groupBy('jejo_code')
        ->havingRaw('COUNT(*) > 1')
        ->get();

        foreach ($objs as $obj) {
            $wObj = OnchannelProductLog::where([
                "offer_id"       => $obj->jejo_code,
                "send_type"      => $channel,
                "regist_success" => MallConstant::REGIST_SUCCESS
            ])->first();

            if( $wObj != null ){
                OnchProductData::where("jejo_code", $obj->jejo_code)
                ->where("prd_channel", $channel)
                ->where("prd_code", "!=", $wObj->prd_code)
                ->delete();
            } else {
                OnchProductData::where("jejo_code", $obj->jejo_code)
                ->where("prd_channel", $channel)
                ->delete();
            }
        }

        debug_log("종료", "onchannel/prdDelete", "prdDelete");
    }
}
