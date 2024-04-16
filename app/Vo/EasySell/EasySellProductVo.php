<?php

namespace App\Vo\EasySell;

use App\Constants\EasySellConstant;
use App\Constants\GosiConstants;
use App\Vo\Vo;

class EasySellProductVo extends Vo
{
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

    public function bind(mixed $data): void
    {
        $this->LinkerID              = EasySellConstant::LINKER_ID;
        $this->UserID                = EasySellConstant::USER_ID;
        $this->UserPW                = EasySellConstant::USER_PW;
        $this->ItemNo                = $data['ItemNo'] ?? 0;
        $this->ItemPartnerCode       = $data['ItemPartnerCode'] ?? "";
        $this->ItemCategory          = $data['ItemCategory'] ?? "";
        $this->ItemBrand             = $data['ItemBrand'] ?? NULL;
        $this->ItemName              = $data['ItemName'] ?? "";
        $this->ItemGoodCode          = $data['ItemGoodCode'] ?? NULL;
        $this->Itemkeyword           = $data['Itemkeyword'] ?? "";
        $this->Launchdate            = date("Y0101");
        $this->ItemMaker             = $data['ItemMaker'] ?? "상세페이지 참조";
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

        $this->ItemNoRefund               = "상세설명참조";
        $this->ItemReturnQualityAssurance = "상세설명참조";
        $this->ItemCompensation           = "상세설명참조";
        $this->ItemTroubleShooting        = "상세설명참조";
        $this->TimeSaleUse                = $data['TimeSaleUse'] ?? "";
        $this->TimeSaleValidSdate         = $data['TimeSaleValidSdate'] ?? "";
        $this->TimeSaleValidEdate         = $data['TimeSaleValidEdate'] ?? "";
        $this->TimeSaleDC                 = $data['TimeSaleDC'] ?? "";
        $this->TimeSaleViewType           = $data['TimeSaleViewType'] ?? "";
        $this->TimeSaleSubjectTag         = $data['TimeSaleSubjectTag'] ?? "";
        $this->ItemCertification          = "상세설명참조";
        $this->ItemCertificationInfo      = $data['ItemCertificationInfo'] ?? "";
        $this->OptIf                      = $data['OptIf'] ?? "N";
        $this->Exhibition                 = $data['Exhibition'] ?? "N";
        $this->MinEa                      = $data['MinEa'] ?? 1;
        $this->ItemApproveAuto            = "Y";                                   // 테스트 완료시 N으로 수정

        $this->_makeItemGoodsRequired($data['noticeType']);
    }

    private function _makeItemGoodsRequired(int $noticeType = GosiConstants::GOSI_CHANNEL_26):void
    {
        $gosiItem = [
            "1" => [
                "ItemGoodsRequired"  => "g8",
                "ItemKinds"          => "상세페이지 참조",
                "ItemMaker"          => "상세페이지 참조",
                "Launchdate"         => $this->Launchdate,
                "ItemMakeDtDetail"   => "상세페이지 참조",
                "ItemServiceLife"    => "상세페이지 참조",
                "ItemVolume"         => "상세페이지 참조",
                "ItemMatiere"        => "상세페이지 참조",
                "ItemIngredient"     => "상세페이지 참조",
                "ItemGeneticallyMod" => "상세페이지 참조",
                "ItemSpecialFood"    => "상세페이지 참조",
                "ItemImport"         => "상세페이지 참조",
                "ItemCaution"        => "상세페이지 참조",
                "ItemAsTel"          => "상세페이지 참조",
            ],
            "2" => [
                "ItemGoodsRequired"    => "g13",
                "ItemPartnerCode"      => "상세페이지 참조",
                "ItemColors"           => "상세페이지 참조",
                "ItemWarranty"         => "상세페이지 참조",
                "ItemWarranty_number"  => "상세페이지 참조",
                "ItemKinds"            => "상세페이지 참조",
                "ItemMatiere"          => "상세페이지 참조",
                "ItemMaker"            => "상세페이지 참조",
                "ItemSizes"            => "상세페이지 참조",
                "ItemServiceLife"      => "상세페이지 참조",
                "ItemQualityAssurance" => "상세페이지 참조",
                "ItemAsName"           => "상세페이지 참조",
                "ItemAsTel"            => "상세페이지 참조",
            ],
            "3" => [
                "ItemGoodsRequired"    => "g2",
                "ItemMatiere"          => "상세페이지 참조",
                "ItemColors"           => "상세페이지 참조",
                "ItemSizes"            => "상세페이지 참조",
                "ItemMaker"            => "상세페이지 참조",
                "ItemCaution"          => "상세페이지 참조",
                "ItemKinds"            => "상세페이지 참조",
                "ItemQualityAssurance" => "상세페이지 참조",
                "ItemAsName"           => "상세페이지 참조",
                "ItemAsTel"            => "상세페이지 참조",
            ],
            "4" => [
                "ItemGoodsRequired"      => "g10",
                "ItemPartnerCode"        => "상세페이지 참조",
                "ItemSpec1"              => "상세페이지 참조",
                "ItemWarranty"           => "상세페이지 참조",
                "ItemWarranty_number"    => "상세페이지 참조",
                "ItemEqualModelLaunchdt" => "상세페이지 참조",
                "ItemVolume"             => "상세페이지 참조",
                "ItemSizes"              => "상세페이지 참조",
                "ItemQualityAssurance"   => "상세페이지 참조",
                "ItemMaker"              => "상세페이지 참조",
                "ItemCaution"            => "상세페이지 참조",
                "ItemAsName"             => "상세페이지 참조",
                "ItemAsTel"              => "상세페이지 참조",
            ],
            "5" => [
                "ItemGoodsRequired"  => "g15",
                "ItemKinds"          => "상세페이지 참조",
                "ItemMaker"          => "상세페이지 참조",
                "Launchdate"         => $this->Launchdate,
                "ItemServiceLife"    => "상세페이지 참조",
                "ItemVolume"         => "상세페이지 참조",
                "ItemMatiere"        => "상세페이지 참조",
                "ItemIngredient"     => "상세페이지 참조",
                "ItemSpec1"          => "상세페이지 참조",
                "ItemCaution"        => "상세페이지 참조",
                "ItemSpec2"          => "상세페이지 참조",
                "ItemGeneticallyMod" => "상세페이지 참조",
                "ItemSpecialFood"    => "상세페이지 참조",
                "ItemQualityInfo"    => "상세페이지 참조",
                "ItemImport"         => "상세페이지 참조",
                "ItemAsTel"          => "상세페이지 참조",
            ],
            "6" => [
                "ItemGoodsRequired"      => "g17",
                "ItemPartnerCode"        => "상세페이지 참조",
                "ItemGoodscd"            => "상세페이지 참조",
                "ItemSpec1"              => "상세페이지 참조",
                "ItemSpec2"              => "상세페이지 참조",
                "ItemWarranty"           => "상세페이지 참조",
                "ItemWarranty_number"    => "상세페이지 참조",
                "ItemEqualModelLaunchdt" => "상세페이지 참조",
                "ItemMaker"              => "상세페이지 참조",
                "ItemImporter"           => "상세페이지 참조",
                "ItemOrigin"             => "상세페이지 참조",
                "ItemSizes"              => "상세페이지 참조",
                "ItemVolume"             => "상세페이지 참조",
                "ItemServiceLife"        => "상세페이지 참조",
                "ItemQualityAssurance"   => "상세페이지 참조",
                "ItemAsName"             => "상세페이지 참조",
                "ItemAsTel"              => "상세페이지 참조",
            ],
            "7" => [
                "ItemGoodsRequired"      => "g19",
                "ItemPartnerCode"        => "상세페이지 참조",
                "ItemGoodscd"            => "상세페이지 참조",
                "ItemEqualModelLaunchdt" => "상세페이지 참조",
                "ItemWarranty"           => "상세페이지 참조",
                "ItemWarranty_number"    => "상세페이지 참조",
                "ItemMaker"              => "상세페이지 참조",
                "ItemImporter"           => "상세페이지 참조",
                "ItemOrigin"             => "상세페이지 참조",
                "ItemSizes"              => "상세페이지 참조",
                "ItemVolume"             => "상세페이지 참조",
                "ItemQualityAssurance"   => "상세페이지 참조",
                "ItemAsName"             => "상세페이지 참조",
                "ItemAsTel"              => "상세페이지 참조",
            ],
            "8" => [
                "ItemGoodsRequired"    => "g1",
                "ItemMatiere"          => "상세페이지 참조",
                "ItemColors"           => "상세페이지 참조",
                "ItemSizes"            => "상세페이지 참조",
                "ItemMaker"            => "상세페이지 참조",
                "Launchdate"           => "상세페이지 참조",
                "ItemCaution"          => "상세페이지 참조",
                "ItemQualityAssurance" => "상세페이지 참조",
                "ItemAsName"           => "상세페이지 참조",
                "ItemAsTel"            => "상세페이지 참조",
            ],
            "9" => [
                "ItemGoodsRequired"    => "g5",
                "ItemMatiere"          => "상세페이지 참조",
                "ItemSizes"            => "상세페이지 참조",
                "ItemMaker"            => "상세페이지 참조",
                "ItemCaution"          => "상세페이지 참조",
                "ItemVolume"           => "상세페이지 참조",
                "ItemSpec1"            => "상세페이지 참조",
                "ItemWarranty"         => "상세페이지 참조",
                "ItemWarranty_number"  => "상세페이지 참조",
                "ItemQualityAssurance" => "상세페이지 참조",
                "ItemAsName"           => "상세페이지 참조",
                "ItemAsTel"            => "상세페이지 참조",
            ],
            "10" => [
                "ItemGoodsRequired"      => "g21",
                "ItemGoodscd"            => "상세페이지 참조",
                "ItemSpec1"              => "상세페이지 참조",
                "ItemWarranty"           => "상세페이지 참조",
                "ItemWarranty_number"    => "상세페이지 참조",
                "ItemEqualModelLaunchdt" => "상세페이지 참조",
                "ItemMaker"              => "상세페이지 참조",
                "ItemImporter"           => "상세페이지 참조",
                "ItemOrigin"             => "상세페이지 참조",
                "ItemSizes"              => "상세페이지 참조",
                "ItemVolume"             => "상세페이지 참조",
                "ItemServiceLife"        => "상세페이지 참조",
                "ItemQualityAssurance"   => "상세페이지 참조",
                "ItemAsName"             => "상세페이지 참조",
                "ItemAsTel"              => "상세페이지 참조",
            ],
            "11" => [
                "ItemGoodsRequired"      => "g18",
                "ItemPartnerCode"        => "상세페이지 참조",
                "ItemGoodscd"            => "상세페이지 참조",
                "ItemSpec1"              => "상세페이지 참조",
                "ItemSpec2"              => "상세페이지 참조",
                "ItemWarranty"           => "상세페이지 참조",
                "ItemWarranty_number"    => "상세페이지 참조",
                "ItemEqualModelLaunchdt" => "상세페이지 참조",
                "ItemMaker"              => "상세페이지 참조",
                "ItemImporter"           => "상세페이지 참조",
                "ItemOrigin"             => "상세페이지 참조",
                "ItemSizes"              => "상세페이지 참조",
                "ItemVolume"             => "상세페이지 참조",
                "ItemQualityAssurance"   => "상세페이지 참조",
                "ItemAsName"             => "상세페이지 참조",
                "ItemAsTel"              => "상세페이지 참조",
            ],
            "12" => [
                "ItemGoodsRequired"      => "g20",
                "ItemPartnerCode"        => "상세페이지 참조",
                "ItemGoodscd"            => "상세페이지 참조",
                "ItemSpec1"              => "상세페이지 참조",
                "ItemWarranty"           => "상세페이지 참조",
                "ItemWarranty_number"    => "상세페이지 참조",
                "ItemEqualModelLaunchdt" => "상세페이지 참조",
                "ItemMaker"              => "상세페이지 참조",
                "ItemImporter"           => "상세페이지 참조",
                "ItemOrigin"             => "상세페이지 참조",
                "ItemSizes"              => "상세페이지 참조",
                "ItemVolume"             => "상세페이지 참조",
                "ItemQualityAssurance"   => "상세페이지 참조",
                "ItemAsName"             => "상세페이지 참조",
                "ItemAsTel"              => "상세페이지 참조",
            ],
            "13" => [
                "ItemGoodsRequired"      => "g7",
                "ItemMatiere"            => "상세페이지 참조",
                "ItemColors"             => "상세페이지 참조",
                "ItemMaker"              => "상세페이지 참조",
                "ItemVolume"             => "상세페이지 참조",
                "ItemSpec1"              => "상세페이지 참조",
                "ItemEqualModelLaunchdt" => "상세페이지 참조",
                "ItemSpec2"              => "상세페이지 참조",
                "ItemPartnerCode"        => "상세페이지 참조",
                "ItemQualityAssurance"   => "상세페이지 참조",
                "ItemAsName"             => "상세페이지 참조",
                "ItemAsTel"              => "상세페이지 참조",
            ],
            "14" => [
               "ItemGoodsRequired" => "g14",
               "ItemPartnerCode"   => "상세페이지 참조",
               "ItemVolume"        => "상세페이지 참조",
               "ItemMaker"         => "상세페이지 참조",
               "Launchdate"        => $this->Launchdate,
               "ItemMakeDtDetail"  => "상세페이지 참조",
               "ItemServiceLife"   => "상세페이지 참조",
               "ItemSpec1"         => "상세페이지 참조",
               "ItemSpec2"         => "상세페이지 참조",
               "ItemCaution"       => "상세페이지 참조",
               "ItemUsages"        => "상세페이지 참조",
               "ItemAsTel"         => "상세페이지 참조",
            ],
            "15" => [
                "ItemGoodsRequired"      => "g25",
                "ItemPartnerCode"        => "상세페이지 참조",
                "ItemGoodscd"            => "상세페이지 참조",
                "ItemSizes"              => "상세페이지 참조",
                "ItemMatiere"            => "상세페이지 참조",
                "ItemImporter"           => "상세페이지 참조",
                "ItemEqualModelLaunchdt" => "상세페이지 참조",
                "ItemKinds"              => "상세페이지 참조",
                "ItemSpec1"              => "상세페이지 참조",
                "ItemColors"             => "상세페이지 참조",
                "ItemQualityAssurance"   => "상세페이지 참조",
                "ItemAsName"             => "상세페이지 참조",
                "ItemAsTel"              => "상세페이지 참조",
            ],
            "16" => [
                "ItemGoodsRequired"      => "g16",
                "ItemPartnerCode"        => "상세페이지 참조",
                "ItemSpec1"              => "상세페이지 참조",
                "ItemSpec2"              => "상세페이지 참조",
                "ItemWarranty"           => "상세페이지 참조",
                "ItemWarranty_number"    => "상세페이지 참조",
                "ItemEqualModelLaunchdt" => "상세페이지 참조",
                "ItemMaker"              => "상세페이지 참조",
                "ItemImporter"           => "상세페이지 참조",
                "ItemOrigin"             => "상세페이지 참조",
                "ItemSizes"              => "상세페이지 참조",
                "ItemVolume"             => "상세페이지 참조",
                "ItemQualityAssurance"   => "상세페이지 참조",
                "ItemAsName"             => "상세페이지 참조",
                "ItemAsTel"              => "상세페이지 참조",
            ],
            "17" => [
                "ItemGoodsRequired"      => "g6",
                "ItemMatiere"            => "상세페이지 참조",
                "ItemColors"             => "상세페이지 참조",
                "ItemMaker"              => "상세페이지 참조",
                "ItemCaution"            => "상세페이지 참조",
                "ItemVolume"             => "상세페이지 참조",
                "ItemWarranty"           => "상세페이지 참조",
                "ItemWarranty_number"    => "상세페이지 참조",
                "ItemWarrantyGov"        => "상세페이지 참조",
                "ItemUseAge"             => "상세페이지 참조",
                "ItemEqualModelLaunchdt" => "상세페이지 참조",
                "ItemPartnerCode"        => "상세페이지 참조",
                "ItemQualityAssurance"   => "상세페이지 참조",
                "ItemAsName"             => "상세페이지 참조",
                "ItemAsTel"              => "상세페이지 참조",
            ],
            "18" => [
                "ItemGoodsRequired"      => "g23",
                "ItemPartnerCode"        => "상세페이지 참조",
                "ItemGoodscd"            => "상세페이지 참조",
                "ItemSpec1"              => "상세페이지 참조",
                "ItemWarranty"           => "상세페이지 참조",
                "ItemWarranty_number"    => "상세페이지 참조",
                "ItemEqualModelLaunchdt" => "상세페이지 참조",
                "ItemMaker"              => "상세페이지 참조",
                "ItemImporter"           => "상세페이지 참조",
                "ItemOrigin"             => "상세페이지 참조",
                "ItemUsages"             => "상세페이지 참조",
                "ItemCaution"            => "상세페이지 참조",
                "ItemQualityAssurance"   => "상세페이지 참조",
                "ItemAsName"             => "상세페이지 참조",
                "ItemAsTel"              => "상세페이지 참조",
            ],
            "19" => [
                "ItemGoodsRequired"    => "g0",
                "ItemMatiere"          => "상세페이지 참조",
                "ItemColors"           => "상세페이지 참조",
                "ItemSizes"            => "상세페이지 참조",
                "ItemMaker"            => "상세페이지 참조",
                "Launchdate"           => $this->Launchdate,
                "ItemCaution"          => "상세페이지 참조",
                "ItemQualityAssurance" => "상세페이지 참조",
                "ItemAsName"           => "상세페이지 참조",
                "ItemAsTel"            => "상세페이지 참조",
            ],
            "20" => [
                "ItemGoodsRequired"      => "g22",
                "ItemPartnerCode"        => "상세페이지 참조",
                "ItemGoodscd"            => "상세페이지 참조",
                "ItemEqualModelLaunchdt" => "상세페이지 참조",
                "ItemWarranty"           => "상세페이지 참조",
                "ItemWarranty_number"    => "상세페이지 참조",
                "ItemMaker"              => "상세페이지 참조",
                "ItemImporter"           => "상세페이지 참조",
                "ItemOrigin"             => "상세페이지 참조",
                "ItemSizes"              => "상세페이지 참조",
                "ItemVolume"             => "상세페이지 참조",
                "ItemQualityAssurance"   => "상세페이지 참조",
                "ItemCaution"            => "상세페이지 참조",
                "ItemKinds"              => "상세페이지 참조",
                "ItemAsName"             => "상세페이지 참조",
                "ItemAsTel"              => "상세페이지 참조",
            ],
            "21" => [
                "ItemGoodsRequired"      => "g11",
                "ItemPartnerCode"        => "상세페이지 참조",
                "ItemMatiere"            => "상세페이지 참조",
                "ItemKinds"              => "상세페이지 참조",
                "ItemEqualModelLaunchdt" => "상세페이지 참조",
                "ItemSizes"              => "상세페이지 참조",
                "ItemQualityAssurance"   => "상세페이지 참조",
                "ItemMaker"              => "상세페이지 참조",
                "ItemImport"             => "상세페이지 참조",
                "ItemAsName"             => "상세페이지 참조",
                "ItemAsTel"              => "상세페이지 참조",
            ],
            "22" => [
                "ItemGoodsRequired"    => "g9",
                "ItemMatiere"          => "상세페이지 참조",
                "ItemColors"           => "상세페이지 참조",
                "ItemSizes"            => "상세페이지 참조",
                "ItemMaker"            => "상세페이지 참조",
                "ItemCaution"          => "상세페이지 참조",
                "ItemQualityAssurance" => "상세페이지 참조",
                "ItemKinds"            => "상세페이지 참조",
                "ItemAsName"           => "상세페이지 참조",
                "ItemAsTel"            => "상세페이지 참조",
            ],
            "23" => [
                "ItemGoodsRequired"    => "g3",
                "ItemMatiere"          => "상세페이지 참조",
                "ItemSizes"            => "상세페이지 참조",
                "ItemMaker"            => "상세페이지 참조",
                "ItemCaution"          => "상세페이지 참조",
                "ItemKinds"            => "상세페이지 참조",
                "ItemQualityAssurance" => "상세페이지 참조",
                "ItemAsName"           => "상세페이지 참조",
                "ItemAsTel"            => "상세페이지 참조",
            ],
            "24" => [
                "ItemGoodsRequired"    => "g4",
                "ItemMaker"            => "상세페이지 참조",
                "ItemCaution"          => "상세페이지 참조",
                "ItemVolume"           => "상세페이지 참조",
                "ItemSpec1"            => "상세페이지 참조",
                "ItemServiceLife"      => "상세페이지 참조",
                "ItemUsages"           => "상세페이지 참조",
                "ItemIngredient"       => "상세페이지 참조",
                "ItemWarranty"         => "상세페이지 참조",
                "ItemWarranty_number"  => "상세페이지 참조",
                "ItemQualityAssurance" => "상세페이지 참조",
                "ItemAsName"           => "상세페이지 참조",
                "ItemAsTel"            => "상세페이지 참조",
            ],
            "25" => [
                "ItemGoodsRequired"    => "g12",
                "ItemGoodscd"          => "상세페이지 참조",
                "ItemQualityAssurance" => "상세페이지 참조",
                "ItemMaker"            => "상세페이지 참조",
                "ItemWarranty"         => "상세페이지 참조",
                "ItemWarranty_number"  => "상세페이지 참조",
                "ItemAsName"           => "상세페이지 참조",
                "ItemAsTel"            => "상세페이지 참조",
            ],
            "26" => [
                "ItemGoodsRequired"    => "g12",
                "ItemGoodscd"          => "상세페이지 참조",
                "ItemQualityAssurance" => "상세페이지 참조",
                "ItemMaker"            => "상세페이지 참조",
                "ItemWarranty"         => "상세페이지 참조",
                "ItemWarranty_number"  => "상세페이지 참조",
                "ItemAsName"           => "상세페이지 참조",
                "ItemAsTel"            => "상세페이지 참조",
            ]
        ];

        $this->ItemGoodsRequired = $gosiItem[$noticeType];
    }
}