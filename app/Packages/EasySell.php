<?php

namespace App\Packages;

use App\Abstracts\MallApiAbstract;
use App\Constants\EasySellConstant;
use App\Constants\ProductConstant;
use App\Models\ProductData;
use App\Vo\EasySellProductVo;
use Exception;

class EasySell extends MallApiAbstract
{
    public function __construct()
    {
    }

    public function productRegist(): array
    {
        $selObj = ProductData::where("trans_status","Y")
            ->limit(1);

        try{
            $offerId = $selObj->offerId;
            $params = $this->_getPrdParams($offerId, "I");

        }catch(Exception $e){

        }

        return ["result" => "productRegist / EasySell"];
    }

    public function getOrders(): array
    {
        return ["result" => "getOrders / EasySell"];
    }

    private function _getPrdParams(int $offerId, string $itemMode = EasySellConstant::ITEM_REGIST){
        $productObj = ProductData::where("offer_id",$offerId);
        if(!$productObj->exists()){
            throw new Exception("존재하지 않는 상품");
        }
        $productObj->where("trans_status",ProductConstant::IMG_TRANS_Y);
        if(!$productObj->exists()){
            throw new Exception("번역 미완료 상품");
        }
        $prdObj = $productObj->with([
            "images",
            "extends",
            "options",
            "notices",
            "category"
        ])
        ->first();

        //카테고리 매핑
        $categoryId = $prdObj->cateogry_id;

        $ItemName = $productObj->prd_name_trans;
        if(!productNameValidation($ItemName, "", 100)){
            throw new Exception("상품명 길이가 100byte를 초과했습니다.");
        }

        $ItemDescDetail = "<p style='font-size:18px;border: 1px solid #ec9821;background-color: #f0ad4e; color: #fff;padding: 15px 30px;'>{$productObj->return_comment}</p>{$productObj->prd_desc_trans}";

        $img = implode( '|', $productObj->images );
        pr($img);
        $voParams = [
            "ItemNo" => $offerId,
            "ItemName" => $ItemName,
            "Launchdate" => "2024-01",
            "ItemMadeIn" => "중국",
            "ItemDesc" => $ItemName,
            "ItemDescDetail" => $ItemDescDetail,
            "ItemGoodsRequiredDesc" => $test,
            "ItemImage" => $test,
            "TaxYn" => $productObj->tax_type == 1 ? "001" : "002", //과세 001, 면세 002
            "BuyPrice" => $test,
            "SalePrice" => $test,
            "ConsumerPrice" => $test,
            "DeliveryCharge" => $test,
            "Delfee" => $test,
            "Unittype" => $test,
            "UnitInfo" => $test,
            "OptPrice" => $test,
            "SaleStatus" => $test,
            "ItemMode" => $itemMode,
        ];
        $vo = new EasySellProductVo;
        $vo->bind($voParams);
        $goodinfo = array(
            "LinkerID" => $vo->LinkerID
        );

        ###인코딩
        array_walk_recursive($goodinfo_arr, array($this, "_iconvArr"));
    }

    /**
	 * 인코딩 변경
	 *
     * @param $item 변경할 array
	 *
     */
    private function _iconvArr (&$item, $key) {
        $item = iconv("utf-8","euc-kr//TRANSLIT",$item);
    }
}
