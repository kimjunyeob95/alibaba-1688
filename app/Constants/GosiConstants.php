<?php
namespace App\Constants;

/**
 * 정보고시 정보...
 */
class GosiConstants
{
    public const GOSI_CHANNEL_1 = 1;
    public const GOSI_CHANNEL_2 = 2;
    public const GOSI_CHANNEL_3 = 3;
    public const GOSI_CHANNEL_4 = 4;
    public const GOSI_CHANNEL_5 = 5;
    public const GOSI_CHANNEL_6 = 6;
    public const GOSI_CHANNEL_7 = 7;
    public const GOSI_CHANNEL_8 = 8;
    public const GOSI_CHANNEL_9 = 9;
    public const GOSI_CHANNEL_10 = 10;
    public const GOSI_CHANNEL_11 = 11;
    public const GOSI_CHANNEL_12 = 12;
    public const GOSI_CHANNEL_13 = 13;
    public const GOSI_CHANNEL_14 = 14;
    public const GOSI_CHANNEL_15 = 15;
    public const GOSI_CHANNEL_16 = 16;
    public const GOSI_CHANNEL_17 = 17;
    public const GOSI_CHANNEL_18 = 18;
    public const GOSI_CHANNEL_19 = 19;
    public const GOSI_CHANNEL_20 = 20;
    public const GOSI_CHANNEL_21 = 21;
    public const GOSI_CHANNEL_22 = 22;
    public const GOSI_CHANNEL_23 = 23;
    public const GOSI_CHANNEL_24 = 24;
    public const GOSI_CHANNEL_25 = 25;
    public const GOSI_CHANNEL_26 = 26;

    public const GOSI_CHANNEL = [
        "CHANNEL_1"  => "가공식품",
        "CHANNEL_2"  => "가구(침대/소파/싱크대/DIY제품)",
        "CHANNEL_3"  => "가방",
        "CHANNEL_4"  => "가정용 전기제품(냉장고/세탁기)",
        "CHANNEL_5"  => "건강기능식품",
        "CHANNEL_6"  => "계절가전(에어컨/온풍기)",
        "CHANNEL_7"  => "광학기기(디지털카메라/캠코더)",
        "CHANNEL_8"  => "구두/신발",
        "CHANNEL_9"  => "귀금속/보석/시계류",
        "CHANNEL_10" => "내비게이션",
        "CHANNEL_11" => "사무용기기(컴퓨터/노트북/프린터)",
        "CHANNEL_12" => "소형전자(MP3/전자사전 등)",
        "CHANNEL_13" => "스포츠용품",
        "CHANNEL_14" => "식품(농수산물)",
        "CHANNEL_15" => "악기",
        "CHANNEL_16" => "영상가전(TV류)",
        "CHANNEL_17" => "영유아용품",
        "CHANNEL_18" => "의료기기",
        "CHANNEL_19" => "의류",
        "CHANNEL_20" => "자동차용품(자동차부품/기타 자동차)",
        "CHANNEL_21" => "주방용품",
        "CHANNEL_22" => "침구류/커튼",
        "CHANNEL_23" => "패션잡화(모자/벨트/액세서리)",
        "CHANNEL_24" => "화장품",
        "CHANNEL_25" => "휴대폰",
        "CHANNEL_26" => "기타"
    ];

    public const GOSI_CHANNEL_ITEM = [
        "CHANNEL_1" => [
            "food_type"       => "식품의 유형",
            "import_foot_sec" => "수입식품 여부",
            "mem_order_phone" => "소비자상담 관련 전화번호",
            "kc_type"         => "KC 인증유형",
            "kc_gov"          => "KC 인증기관",
            "kc_sec"          => "KC 인증번호",
            "kc_name"         => "KC 인증상호",
            "deliver_time"    => "주문후 예상 배송기간",
            "prd_loc_import"  => "생산자/소재지/수입자",
            "make_ymd"        => "제조년월일",
            "exp_date"        => "유통기한 또는 품질유지기한",
            "pack_vol"        => "포장단위별 용량(중량)/수량",
            "org_mat"         => "원재료명 및 함량",
            "nut_comp"        => "영양성분",
            "gene_sec"        => "유전자재조합식품 유무",
            "show_sec"        => "표시광고 사전심의 유무",
        ],
        "CHANNEL_2" => [
            "prd_model"         => "품명",
            "make_ymd"          => "제조년월일",
            "warr_prov"         => "품질보증기준",
            "as_phone"          => "A/S 책임자와 전화번호",
            "deliver_time"      => "주문후 예상 배송기간",
            "kc_type"           => "KC 인증유형",
            "kc_gov"            => "KC 인증기관",
            "kc_sec"            => "KC 인증번호",
            "kc_name"           => "KC 인증상호",
            "prd_color"         => "색상",
            "prd_comp"          => "구성품",
            "main_mat"          => "주요소재",
            "make_import"       => "제조자/수입자",
            "make_con"          => "제조국",
            "size_weight"       => "크기",
            "deliver_ins_price" => "배송/설치비용",
        ],
        "CHANNEL_3" => [
            "prd_kind"     => "종류",
            "make_ymd"     => "제조년월일",
            "kc_type"      => "KC 인증유형",
            "kc_gov"       => "KC 인증기관",
            "kc_sec"       => "KC 인증번호",
            "kc_name"      => "KC 인증상호",
            "deliver_time" => "주문후 예상 배송기간",
            "main_mat"     => "소재",
            "prd_color"    => "색상",
            "size_weight"  => "크기",
            "make_import"  => "제조자/수입자",
            "make_con"     => "제조국",
            "note_bene"    => "취급시 주의사항",
            "warr_prov"    => "품질보증기준",
            "as_phone"     => "A/S 책임자와 전화번호",
        ],
        "CHANNEL_4" => [
            "prd_model"          => "품명 및 모델명",
            "make_ymd"           => "제조년월일",
            "kc_type"            => "KC 인증유형",
            "kc_gov"             => "KC 인증기관",
            "kc_sec"             => "KC 인증번호",
            "kc_name"            => "KC 인증상호",
            "deliver_time"       => "주문후 예상 배송기간",
            "pwr_goods_sec"      => "전기용품 안전인증 필 유무",
            "volt_elec_ener_sec" => "정격전압/소비전력/에너지소비효율등급",
            "same_model"         => "동일모델의 출시년월",
            "make_import"        => "제조사/수입자",
            "make_con"           => "제조국",
            "size_weight"        => "크기",
            "warr_prov"          => "품질보증기준",
            "as_phone"           => "A/S 책임자와 전화번호",
        ],
        "CHANNEL_5" => [
            "food_type"       => "식품의 유형",
            "gene_sec"        => "유전자재조합식품 유무",
            "show_sec"        => "표시광고 사전심의 유무",
            "import_foot_sec" => "수입식품 여부",
            "mem_order_phone" => "소비자상담 관련 전화번호",
            "kc_type"         => "KC 인증유형",
            "kc_gov"          => "KC 인증기관",
            "kc_sec"          => "KC 인증번호",
            "kc_name"         => "KC 인증상호",
            "deliver_time"    => "주문후 예상 배송기간",
            "prd_loc_import"  => "생산자/소재지/수입자",
            "make_ymd"        => "제조년월일",
            "exp_date"        => "유통기한 또는 품질유지기한",
            "pack_vol"        => "포장단위별 용량(중량)/수량",
            "org_mat"         => "원재료명 및 함량",
            "nut_comp"        => "영양성분",
            "func_info"       => "기능정보",
            "intake_info"     => "섭취량/섭취방법 및 섭취 시 주의사항",
        ],
        "CHANNEL_6" => [
            "prd_model"          => "품명 및 모델명",
            "make_ymd"           => "제조년월일",
            "warr_prov"          => "품질보증기준",
            "as_phone"           => "A/S 책임자와 전화번호",
            "kc_type"            => "KC 인증유형",
            "kc_gov"             => "KC 인증기관",
            "kc_sec"             => "KC 인증번호",
            "kc_name"            => "KC 인증상호",
            "deliver_time"       => "주문후 예상 배송기간",
            "pwr_goods_sec"      => "전기용품 안전인증 필 유무",
            "volt_elec_ener_sec" => "정격전압/소비전력/에너지소비효율등급",
            "same_model"         => "동일모델의 출시년월",
            "make_import"        => "제조자/수입자",
            "make_con"           => "제조국",
            "size_weight"        => "크기",
            "air_area"           => "냉난방면적",
            "ins_price"          => "추가설치비용",
        ],
        "CHANNEL_7" => [
            "prd_model"    => "품명 및 모델명",
            "make_ymd"     => "제조년월일",
            "deliver_time" => "주문후 예상 배송기간",
            "kc_type"      => "KC 인증유형",
            "kc_gov"       => "KC 인증기관",
            "kc_sec"       => "KC 인증번호",
            "kc_name"      => "KC 인증상호",
            "same_model"   => "동일모델의 출시년월",
            "make_import"  => "제조자/수입자",
            "make_con"     => "제조국",
            "size_weight"  => "크기/무게",
            "main_spec"    => "주요 사양",
            "warr_prov"    => "품질보증기준",
            "as_phone"     => "A/S 책임자와 전화번호",
        ],
        "CHANNEL_8" => [
            "main_mat"     => "제품소재",
            "make_ymd"     => "제조년월일",
            "prd_color"    => "색상",
            "prd_meas"     => "치수",
            "make_import"  => "제조자/수입자",
            "make_con"     => "제조국",
            "note_bene"    => "취급시 주의사항",
            "warr_prov"    => "품질보증기준",
            "as_phone"     => "A/S 책임자와 전화번호",
            "kc_type"      => "KC 인증유형",
            "kc_gov"       => "KC 인증기관",
            "kc_sec"       => "KC 인증번호",
            "kc_name"      => "KC 인증상호",
            "deliver_time" => "주문후 예상 배송기간",
        ],
        "CHANNEL_9" => [
            "mat_pur_qual" => "소재/순도/밴드재질",
            "make_ymd"     => "제조년월일",
            "as_phone"     => "A/S 책임자와 전화번호",
            "kc_type"      => "KC 인증유형",
            "kc_gov"       => "KC 인증기관",
            "kc_sec"       => "KC 인증번호",
            "kc_name"      => "KC 인증상호",
            "deliver_time" => "주문후 예상 배송기간",
            "size_weight"  => "중량",
            "make_import"  => "제조자/수입자",
            "make_con"     => "제조국",
            "prd_meas"     => "치수",
            "wear_bene"    => "착용 시 주의사항",
            "main_spec"    => "주요 사양",
            "warr_offer"   => "보증서 제공여부",
            "warr_prov"    => "품질보증기준",
        ],
        "CHANNEL_10" => [
            "prd_model"          => "품명 및 모델명",
            "make_ymd"           => "제조년월일",
            "warr_prov"          => "품질보증기준",
            "as_phone"           => "A/S 책임자와 전화번호",
            "deliver_time"       => "주문후 예상 배송기간",
            "kc_type"            => "KC 인증유형",
            "kc_gov"             => "KC 인증기관",
            "kc_sec"             => "KC 인증번호",
            "kc_name"            => "KC 인증상호",
            "volt_elec_ener_sec" => "정격전압, 소비전력",
            "same_model"         => "동일모델의 출시년월",
            "make_import"        => "제조자/수입자",
            "make_con"           => "제조국",
            "size_weight"        => "크기/무게",
            "main_spec"          => "주요 사양",
            "map_update_price"   => "맵 업데이트 비용 및 무상기간",
        ],
        "CHANNEL_11" => [
            "prd_model"          => "품명 및 모델명",
            "make_ymd"           => "제조년월일",
            "as_phone"           => "A/S 책임자와 전화번호",
            "kc_type"            => "KC 인증유형",
            "kc_gov"             => "KC 인증기관",
            "kc_sec"             => "KC 인증번호",
            "kc_name"            => "KC 인증상호",
            "deliver_time"       => "주문후 예상 배송기간",
            "pwr_goods_sec"      => "전기용품 안전인증 필 유무",
            "volt_elec_ener_sec" => "정격전압/소비전력/에너지소비효율등급",
            "same_model"         => "동일모델의 출시년월",
            "make_import"        => "제조자/수입자",
            "make_con"           => "제조국",
            "size_weight"        => "크기/무게",
            "main_spec"          => "주요사양",
            "warr_prov"          => "품질보증기준",
        ],
        "CHANNEL_12" => [
            "prd_model"          => "품명 및 모델명",
            "make_ymd"           => "제조년월일",
            "as_phone"           => "A/S 책임자와 전화번호",
            "deliver_time"       => "주문후 예상 배송기간",
            "kc_type"            => "KC 인증유형",
            "kc_gov"             => "KC 인증기관",
            "kc_sec"             => "KC 인증번호",
            "kc_name"            => "KC 인증상호",
            "volt_elec_ener_sec" => "정격전압/소비전력",
            "same_model"         => "동일모델의 출시년월",
            "make_import"        => "제조자/수입자",
            "make_con"           => "제조국",
            "size_weight"        => "크기/무게",
            "main_spec"          => "주요 사양",
            "warr_prov"          => "품질보증기준",
        ],
        "CHANNEL_13" => [
            "prd_model"    => "품명 및 모델명",
            "make_ymd"     => "제조년월일",
            "warr_prov"    => "품질보증기준",
            "as_phone"     => "A/S 책임자와 전화번호",
            "kc_type"      => "KC 인증유형",
            "kc_gov"       => "KC 인증기관",
            "kc_sec"       => "KC 인증번호",
            "kc_name"      => "KC 인증상호",
            "deliver_time" => "주문후 예상 배송기간",
            "size_weight"  => "크기/중량",
            "prd_color"    => "색상",
            "prd_quality"  => "재질",
            "prd_comp"     => "제품 구성",
            "same_model"   => "동일모델의 출시년월",
            "make_import"  => "제조자/수입자",
            "make_con"     => "제조국",
            "detail_spec"  => "상품별 세부 사양",
        ],
        "CHANNEL_14" => [
            "pack_vol"        => "포장단위별 용량(중량)/수량/크기",
            "kc_type"         => "KC 인증유형",
            "kc_gov"          => "KC 인증기관",
            "kc_sec"          => "KC 인증번호",
            "kc_name"         => "KC 인증상호",
            "deliver_time"    => "주문후 예상 배송기간",
            "prd_loc_import"  => "생산자/수입자",
            "org_make"        => "원산지",
            "make_ymd"        => "제조년월일",
            "exp_date"        => "유통기한 또는 품질유지기한",
            "law_bene"        => "관련법상 표시사항",
            "prd_comp"        => "상품구성",
            "treat_method"    => "보관방법 또는 취급방법",
            "mem_order_phone" => "소비자상담 관련 전화번호",
        ],
        "CHANNEL_15" => [
            "prd_model"    => "품명 및 모델명",
            "make_ymd"     => "제조년월일",
            "warr_prov"    => "품질보증기준",
            "as_phone"     => "A/S 책임자와 전화번호",
            "kc_type"      => "KC 인증유형",
            "kc_gov"       => "KC 인증기관",
            "kc_sec"       => "KC 인증번호",
            "kc_name"      => "KC 인증상호",
            "deliver_time" => "주문후 예상 배송기간",
            "size_weight"  => "크기",
            "prd_color"    => "색상",
            "prd_quality"  => "재질",
            "prd_comp"     => "제품 구성",
            "same_model"   => "동일모델의 출시년월",
            "make_import"  => "제조자/수입자",
            "make_con"     => "제조국",
            "detail_spec"  => "상품별 세부 사양",
        ],
        "CHANNEL_16" => [
            "prd_model"          => "품명 및 모델명",
            "make_ymd"           => "제조년월일",
            "as_phone"           => "A/S 책임자와 전화번호",
            "kc_type"            => "KC 인증유형",
            "kc_gov"             => "KC 인증기관",
            "kc_sec"             => "KC 인증번호",
            "kc_name"            => "KC 인증상호",
            "deliver_time"       => "주문후 예상 배송기간",
            "pwr_goods_sec"      => "전기용품 안전인증 필 유무",
            "volt_elec_ener_sec" => "정격전압/소비전력/에너지소비효율등급",
            "same_model"         => "동일모델의 출시년월",
            "make_import"        => "제조자/수입자",
            "make_con"           => "제조국",
            "size_weight"        => "크기",
            "monitor_spec"       => "화면사양",
            "warr_prov"          => "품질보증기준",
        ],
        "CHANNEL_17" => [
            "prd_model"    => "품명 및 모델명",
            "make_ymd"     => "제조년월일",
            "note_bene"    => "취급방법 및 취급시 주의사항,안전표시",
            "warr_prov"    => "품질보증기준",
            "as_phone"     => "A/S 책임자와 전화번호",
            "deliver_time" => "주문후 예상 배송기간",
            "kc_type"      => "KC 인증유형",
            "kc_gov"       => "KC 인증기관",
            "kc_sec"       => "KC 인증번호",
            "kc_name"      => "KC 인증상호",
            "size_weight"  => "크기/중량",
            "prd_color"    => "색상",
            "prd_quality"  => "재질",
            "use_age"      => "사용연령",
            "same_model"   => "동일모델의 출시년월",
            "make_import"  => "제조자/수입자",
            "make_con"     => "제조국",
        ],
        "CHANNEL_18" => [
            "prd_model"          => "품명 및 모델명",
            "make_ymd"           => "제조년월일",
            "note_bene"          => "취급시 주의사항",
            "warr_prov"          => "품질보증기준",
            "as_phone"           => "A/S 책임자와 전화번호",
            "deliver_time"       => "주문후 예상 배송기간",
            "medi_license"       => "의료기기법상 허가번호",
            "show_sec"           => "광고사전심의필 유무",
            "kc_type"            => "KC 인증유형",
            "kc_gov"             => "KC 인증기관",
            "kc_sec"             => "KC 인증번호",
            "kc_name"            => "KC 인증상호",
            "volt_elec_ener_sec" => "정격전압/소비전력",
            "same_model"         => "동일모델의 출시년월",
            "make_import"        => "제조자/수입자",
            "make_con"           => "제조국",
            "use_object"         => "제품의 사용목적 및 사용방법",
        ],
        "CHANNEL_19" => [
            "main_mat"     => "제품소재",
            "kc_type"      => "KC 인증유형",
            "kc_gov"       => "KC 인증기관",
            "kc_sec"       => "KC 인증번호",
            "kc_name"      => "KC 인증상호",
            "deliver_time" => "주문후 예상 배송기간",
            "prd_color"    => "색상",
            "prd_meas"     => "치수",
            "make_import"  => "제조자/수입자",
            "make_con"     => "제조국",
            "wash_bene"    => "세탁방법 및 취급시 주의사항",
            "make_ymd"     => "제조년월일",
            "warr_prov"    => "품질보증기준",
            "as_phone"     => "A/S 책임자와 전화번호",
        ],
        "CHANNEL_20" => [
            "prd_model"    => "품명 및 모델명",
            "make_ymd"     => "제조년월일",
            "kc_type"      => "KC 인증유형",
            "kc_gov"       => "KC 인증기관",
            "kc_sec"       => "KC 인증번호",
            "kc_name"      => "KC 인증상호",
            "deliver_time" => "주문후 예상 배송기간",
            "same_model"   => "동일모델의 출시년월",
            "car_comp_sec" => "자동차 부품 자기인증 유무",
            "make_import"  => "제조자/수입자",
            "make_con"     => "제조국",
            "size_weight"  => "크기",
            "app_car"      => "적용차종",
            "warr_prov"    => "품질보증기준",
            "as_phone"     => "A/S 책임자와 전화번호",
        ],
        "CHANNEL_21" => [
            "prd_model"    => "품명 및 모델명",
            "make_ymd"     => "제조년월일",
            "as_phone"     => "A/S 책임자와 전화번호",
            "kc_type"      => "KC 인증유형",
            "kc_gov"       => "KC 인증기관",
            "kc_sec"       => "KC 인증번호",
            "kc_name"      => "KC 인증상호",
            "deliver_time" => "주문후 예상 배송기간",
            "prd_quality"  => "재질",
            "prd_comp"     => "구성품",
            "size_weight"  => "크기",
            "same_model"   => "동일모델의 출시년월",
            "make_import"  => "제조자/수입자",
            "make_con"     => "제조국",
            "import_bowl"  => "수입 기구/용기",
            "warr_prov"    => "품질보증기준",
        ],
        "CHANNEL_22" => [
            "main_mat"     => "제품소재",
            "make_ymd"     => "제조년월일",
            "kc_type"      => "KC 인증유형",
            "kc_gov"       => "KC 인증기관",
            "kc_sec"       => "KC 인증번호",
            "kc_name"      => "KC 인증상호",
            "deliver_time" => "주문후 예상 배송기간",
            "prd_color"    => "색상",
            "prd_meas"     => "치수",
            "prd_comp"     => "제품구성",
            "make_import"  => "제조자/수입자",
            "make_con"     => "제조국",
            "wash_bene"    => "세탁방법 및 취급시 주의사항",
            "warr_prov"    => "품질보증기준",
            "as_phone"     => "A/S 책임자와 전화번호",
        ],
        "CHANNEL_23" => [
            "prd_kind"     => "종류",
            "make_ymd"     => "제조년월일",
            "main_mat"     => "소재",
            "prd_meas"     => "치수",
            "make_import"  => "제조자/수입자",
            "make_con"     => "제조국",
            "note_bene"    => "취급시 주의사항",
            "warr_prov"    => "품질보증기준",
            "as_phone"     => "A/S 책임자와 전화번호",
            "kc_type"      => "KC 인증유형",
            "kc_gov"       => "KC 인증기관",
            "kc_sec"       => "KC 인증번호",
            "kc_name"      => "KC 인증상호",
            "deliver_time" => "주문후 예상 배송기간",
        ],
        "CHANNEL_24" => [
            "prd_weight"      => "용량(중량) 또는 중량",
            "make_ymd"        => "제조년월일",
            "warr_prov"       => "품질보증기준",
            "mem_order_phone" => "소비자상담 관련 전화번호",
            "kc_type"         => "KC 인증유형",
            "kc_gov"          => "KC 인증기관",
            "kc_sec"          => "KC 인증번호",
            "kc_name"         => "KC 인증상호",
            "deliver_time"    => "주문후 예상 배송기간",
            "main_spec"       => "제품 주요 사양",
            "use_by_date"     => "사용기한 또는 개봉 후 사용기간",
            "use_object"      => "사용방법",
            "make_import"     => "제조자 및 제조판매업자",
            "make_con"        => "제조국",
            "the_chief"       => "주요성분",
            "func_cos_sec"    => "기능성 화장품 심사 필 유무",
            "note_bene"       => "사용할 때 주의사항",
        ],
        "CHANNEL_25" => [
            "prd_model"       => "품명 및 모델명",
            "make_ymd"        => "제조년월일",
            "be_license"      => "허가 관련",
            "make_con"        => "제조국 또는 원산지",
            "make_import"     => "제조자/수입자",
            "mem_order_phone" => "관련 연락처",
            "kc_type"         => "KC 인증유형",
            "kc_gov"          => "KC 인증기관",
            "kc_sec"          => "KC 인증번호",
            "kc_name"         => "KC 인증상호",
            "deliver_time"    => "주문후 예상 배송기간",
        ],
        "CHANNEL_26" => [
            "prd_model"       => "품명 및 모델명",
            "make_ymd"        => "제조년월일",
            "be_license"      => "허가 관련",
            "make_con"        => "제조국 또는 원산지",
            "make_import"     => "제조자/수입자",
            "mem_order_phone" => "관련 연락처",
            "kc_type"         => "KC 인증유형",
            "kc_gov"          => "KC 인증기관",
            "kc_sec"          => "KC 인증번호",
            "kc_name"         => "KC 인증상호",
            "deliver_time"    => "주문후 예상 배송기간",
        ]
    ];

}
