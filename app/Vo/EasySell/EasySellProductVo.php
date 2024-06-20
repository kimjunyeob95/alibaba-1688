<?php

namespace App\Vo\EasySell;

use App\Constants\EasySellConstant;
use App\Constants\GosiConstants;
use App\Constants\WConstant;
use App\Vo\Vo;

class EasySellProductVo extends Vo
{
    protected string $referDetail;

    protected string $LinkerID;
    protected string $UserID;
    protected string $UserPW;
    protected int $ItemNo;
    protected string $ItemPartnerCode;
    protected string $ItemCategory;
    protected ?int $ItemBrand;
    protected string $ItemName;
    protected ?int $ItemGoodCode;
    protected string $Itemkeyword;
    protected string $Launchdate;
    protected string $ItemMaker;
    protected string $ItemMadeIn;
    protected string $ItemDesc;
    protected string $ItemDescDetail;
    protected array $ItemGoodsRequired;
    protected string $ItemGoodsRequiredDesc;
    protected string $ItemImage;
    protected string $ItemImageUpt;
    protected ?int $ItemCount;
    protected string $TaxYn;
    protected int $BuyPrice;
    protected $MarginRate;
    protected int $SalePrice;
    protected int $ConsumerPrice;
    protected string $DeliveryCharge;
    protected int $Delfee;
    protected string $Unittype;
    protected string $UnitInfo;
    protected ?int $OptPrice;
    protected int $SaleStatus;
    protected string $StockYn;
    protected string $ItemMode;

    protected string $ItemNoRefund;
    protected string $ItemReturnQualityAssurance;
    protected string $ItemCompensation;
    protected string $ItemTroubleShooting;
    protected string $TimeSaleUse;
    protected string $TimeSaleValidSdate;
    protected string $TimeSaleValidEdate;
    protected string $TimeSaleDC;
    protected string $TimeSaleViewType;
    protected string $TimeSaleSubjectTag;
    protected string $ItemCertification;
    protected string $ItemCertificationInfo;
    protected string $OptIf;
    protected string $Exhibition;
    protected int $MinEa;
    //상품 승인요청여부
    protected string $ItemApproveAuto;

    // 이지셀/dropK 언어 설정 수정
    public function __construct(string $type)
    {
        switch($type){
            case WConstant::WAPP_W1 :
                $this->UserID      = EasySellConstant::USER_ID_W;
                $this->referDetail = "상세페이지 참조";
                break;
            case WConstant::WAPP_W2 :
                $this->UserID      = EasySellConstant::USER_ID_DROPHUB;
                $this->referDetail = "See Details on Product Page";
                break;
            default :
                break;
        }
    }

    public function bind(mixed $data): void
    {
        $this->LinkerID              = EasySellConstant::LINKER_ID;
        $this->UserPW                = EasySellConstant::USER_PW;
        $this->ItemNo                = $data['ItemNo'] ?? 0;
        $this->ItemPartnerCode       = $data['ItemPartnerCode'] ?? "";
        $this->ItemCategory          = $data['ItemCategory'] ?? "";
        $this->ItemBrand             = $data['ItemBrand'] ?? NULL;
        $this->ItemName              = $data['ItemName'] ?? "";
        $this->ItemGoodCode          = $data['ItemGoodCode'] ?? NULL;
        $this->Itemkeyword           = $data['Itemkeyword'] ?? "";
        $this->Launchdate            = date("Y0101");
        $this->ItemMaker             = $data['ItemMaker'] ?? $this->referDetail;
        $this->ItemMadeIn            = EasySellConstant::ITEM_MADE_IN;
        $this->ItemDesc              = $data['ItemDesc'] ?? "";
        $this->ItemDescDetail        = $data['ItemDescDetail'] ?? "";
        $this->ItemGoodsRequiredDesc = $data['ItemGoodsRequiredDesc'] ?? "";
        $this->ItemImage             = $data['ItemImage'] ?? "";
        $this->ItemImageUpt          = "N";
        $this->ItemCount             = $data['ItemCount'] ?? NULL;
        $this->TaxYn                 = $data['TaxYn'] ?? "001";
        $this->BuyPrice              = $data['BuyPrice'] ?? 0;
        $this->MarginRate            = $data['MarginRate'] ?? "";
        $this->SalePrice             = $data['SalePrice'] ?? 0;
        $this->ConsumerPrice         = $data['ConsumerPrice'] ?? 0;
        $this->Delfee                = $data['Delfee'] ?? 0;
        $this->DeliveryCharge        = $this->Delfee > 0 ? "003" : "001";
        $this->Unittype              = "single";
        $this->UnitInfo              = $data['UnitInfo'] ?? "";
        $this->OptPrice              = $data['OptPrice'] ?? NULL;
        $this->SaleStatus            = $data['SaleStatus'] ?? "";
        $this->StockYn               = $data['StockYn'] ?? "002";
        $this->ItemMode              = $data['ItemMode'] ?? "";

        $this->ItemNoRefund               = $this->referDetail;
        $this->ItemReturnQualityAssurance = $this->referDetail;
        $this->ItemCompensation           = $this->referDetail;
        $this->ItemTroubleShooting        = $this->referDetail;
        $this->TimeSaleUse                = $data['TimeSaleUse'] ?? "";
        $this->TimeSaleValidSdate         = $data['TimeSaleValidSdate'] ?? "";
        $this->TimeSaleValidEdate         = $data['TimeSaleValidEdate'] ?? "";
        $this->TimeSaleDC                 = $data['TimeSaleDC'] ?? "";
        $this->TimeSaleViewType           = $data['TimeSaleViewType'] ?? "";
        $this->TimeSaleSubjectTag         = $data['TimeSaleSubjectTag'] ?? "";
        $this->ItemCertification          = $this->referDetail;
        $this->ItemCertificationInfo      = $data['ItemCertificationInfo'] ?? "";
        $this->OptIf                      = $data['OptIf'] ?? "N";
        $this->Exhibition                 = $data['Exhibition'] ?? "N";
        $this->MinEa                      = $data['MinEa'] ?? 1;
        $this->ItemApproveAuto            = "N";                                   // 테스트 완료시 N으로 수정

        $this->_makeItemGoodsRequired($data['noticeType']);
    }

    private function _makeItemGoodsRequired(string $noticeType):void
    {
        $gosiItem = [
            "ItemGoodsRequired"      => $noticeType,
            "ItemKinds"              => $this->referDetail,
            "ItemMaker"              => $this->referDetail,
            "Launchdate"             => $this->Launchdate,
            "ItemServiceLife"        => $this->referDetail,
            "ItemVolume"             => $this->referDetail,
            "ItemMatiere"            => $this->referDetail,
            "ItemIngredient"         => $this->referDetail,
            "ItemCaution"            => $this->referDetail,
            "ItemAsTel"              => $this->referDetail,
            "ItemPartnerCode"        => $this->referDetail,
            "ItemColors"             => $this->referDetail,
            "ItemWarranty"           => "N",
            "ItemWarranty_number"    => "",
            "ItemSizes"              => $this->referDetail,
            "ItemQualityAssurance"   => $this->referDetail,
            "ItemAsName"             => $this->referDetail,
            "ItemSpec1"              => $this->referDetail,
            "ItemEqualModelLaunchdt" => $this->referDetail,
            "ItemSpec2"              => $this->referDetail,
            "ItemUsages"             => $this->referDetail,
            "ItemWarrantyGov"        => $this->referDetail,
            "ItemUseAge"             => $this->referDetail,
            "ItemOrigin"             => $this->referDetail,
            "ItemImport"             => $this->referDetail,
            "ItemImporter"           => $this->referDetail,
            "ItemGoodscd"            => $this->referDetail,
            "ItemQualityInfo"        => $this->referDetail,
            "ItemSpecialFood"        => $this->referDetail,
            "ItemGeneticallyMod"     => $this->referDetail,
            "ItemMakeDtDetail"       => $this->referDetail
        ];

        $this->ItemGoodsRequired = $gosiItem;
    }
}