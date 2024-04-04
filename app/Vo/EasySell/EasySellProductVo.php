<?php

namespace App\Vo;

use App\Constants\EasySellConstant;
use App\Constants\ProductConstant;
use App\Vo\Vo;

class EasySellProductVo extends Vo
{
    protected $LinkerID;
    protected $UserID;
    protected $UserPW;
    protected $ItemNo;
    protected $ItemPartnerCode;
    protected $ItemCategory;
    protected $ItemBrand;
    protected $ItemName;
    protected $ItemGoodCode;
    protected $Itemkeyword;
    protected $Launchdate;
    protected $ItemMaker;
    protected $ItemMadeIn;
    protected $ItemDesc;
    protected $ItemDescDetail;
    protected $ItemGoodsRequiredDesc;
    protected $ItemImage;
    protected $ItemImageUpt;
    protected $ItemCount;
    protected $TaxYn;
    protected $BuyPrice;
    protected $MarginRate;
    protected $SalePrice;
    protected $ConsumerPrice;
    protected $DeliveryCharge;
    protected $Delfee;
    protected $Unittype;
    protected $UnitInfo;
    protected $OptPrice;
    protected $SaleStatus;
    protected $StockYn;
    protected $ItemMode;

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
    //상품 승인요청여부
    protected string $ItemApproveAuto;

    public function bind(mixed $data): void
    {
        $this->LinkerID              = EasySellConstant::LINKER_ID;
        $this->UserID                = EasySellConstant::USER_ID;
        $this->UserPW                = EasySellConstant::USER_PW;
        $this->ItemNo                = "";
        $this->ItemPartnerCode       = "";
        $this->ItemCategory          = "";
        $this->ItemBrand             = "";
        $this->ItemName              = "";
        $this->ItemGoodCode          = $data['ItemGoodCode'] ?? "";
        $this->Itemkeyword           = "";
        $this->Launchdate            = "";
        $this->ItemMaker             = "";
        $this->ItemMadeIn            = "";
        $this->ItemDesc              = "";
        $this->ItemDescDetail        = "";
        $this->ItemGoodsRequiredDesc = "";
        $this->ItemImage             = "";
        $this->ItemImageUpt          = "N";
        $this->ItemCount             = "";
        $this->TaxYn                 = $data['TaxYn'] ?? "001";
        $this->BuyPrice              = "";
        $this->MarginRate            = "";
        $this->SalePrice             = "";
        $this->ConsumerPrice         = "";
        $this->DeliveryCharge        = "";
        $this->Delfee                = "";
        $this->Unittype              = "single";
        $this->UnitInfo              = "";
        $this->OptPrice              = "";
        $this->SaleStatus            = "";
        $this->StockYn               = $data['StockYn'] ?? "002";
        $this->ItemMode              = "";

        $this->ItemNoRefund               = "상세설명참조";
        $this->ItemReturnQualityAssurance = "상세설명참조";
        $this->ItemCompensation           = "상세설명참조";
        $this->ItemTroubleShooting        = "상세설명참조";
        $this->TimeSaleUse                = "";
        $this->TimeSaleValidSdate         = "";
        $this->TimeSaleValidEdate         = "";
        $this->TimeSaleDC                 = "";
        $this->TimeSaleViewType           = "";
        $this->TimeSaleSubjectTag         = "";
        $this->ItemCertification          = "상세설명참조";
        $this->ItemCertificationInfo      = "";
        $this->OptIf                      = "";
        $this->Exhibition                 = "";
        $this->ItemApproveAuto            = "Y";       // 테스트 완료시 N으로 수정
    }
}