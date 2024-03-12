<?php

namespace App\Services\Product;

use App\Models\ProductData;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    private array $returnFailMsg;

    public function __construct()
    {
        $this->returnFailMsg = helpers_fail_message();
    }

    public function getPrdList(array $params): LengthAwarePaginator
    {
        $pageSize  = $params["pageSize"];

        $prdBuilder = ProductData::with(["options"])->orderBy("created_at", "desc");
        $lists = $prdBuilder->paginate($pageSize)->appends($params);

        return $lists;
    }

    public function getPrdDetail(int $offerId): array
    {
        $returnMsg = $this->returnFailMsg;

        try {
            $prdObj = ProductData::with([
                "images",
                "extends",
                "options",
                "notices",
                "category"
            ])->where("offer_id", $offerId)->first();
            if( $prdObj == null ){
                throw new Exception("No Data");   
            }

            $returnMsg = helpers_success_message($prdObj);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}