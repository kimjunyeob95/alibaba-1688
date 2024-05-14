<?php

namespace App\Services;

use App\Constants\ForbiddenWordConstant;
use App\Constants\ForbiddenWordErrorMessageConstant;
use App\Models\ForbiddenNoticeWordData;
use App\Models\ForbiddenWordData;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class ForbiddenWordService
{
    protected array $returnMsg;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }

    public function list(array $params): LengthAwarePaginator
    {
        $keyword_type = $params["keyword_type"];
        $keyword      = $params["keyword"];
        $pageSize     = $params["pageSize"];

        $builder = ForbiddenWordData::orderBy("updated_at", "desc");

        if( !empty($keyword_type) ){
            $builder->where("keyword_type", $keyword_type);
        }

        if( !empty($keyword) ){
            $builder->where(function($query1) use ($keyword) {
                $query1->where("target_keyword", "like", "%" . $keyword . "%")
                ->orWhere("replace_keyword", "like", "%" . $keyword . "%");
            });
        }

        $lists = $builder->paginate($pageSize)->appends($params);

        return $lists;
    }

    public function noticeList(array $params): LengthAwarePaginator
    {
        $keyword_type = $params["keyword_type"];
        $keyword      = $params["keyword"];
        $pageSize     = $params["pageSize"];

        $builder = ForbiddenNoticeWordData::orderBy("updated_at", "desc");

        if( !empty($keyword_type) ){
            $builder->where("keyword_type", $keyword_type);
        }

        if( !empty($keyword) ){
            $builder->where(function($query1) use ($keyword) {
                $query1->where("target_keyword", "like", "%" . $keyword . "%")
                ->orWhere("replace_keyword", "like", "%" . $keyword . "%");
            });
        }

        $lists = $builder->paginate($pageSize)->appends($params);

        return $lists;
    }

    /**
     * forbidden_word_datas get
     *
     * @param int $id
     * @return array
     */
    public function get(int $id): array
    {
        $returnMsg = $this->returnMsg;
        try {
            
            $obj = ForbiddenWordData::where("id", $id)->first();

            if( $obj != null ){
                $returnMsg = helpers_success_message($obj);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * forbidden_word_datas create
     *
     * @param array $params
     * @return array
     */
    public function create(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $keyword_type    = $params["keyword_type"];
            $target_keyword  = $params["target_keyword"];
            $replace_keyword = $params["replace_keyword"];
            $apply_type      = $params["apply_type"];

            if( $keyword_type == ForbiddenWordConstant::KEYWORD_REPLACE ){
                if( empty($replace_keyword) ){
                    throw new Exception(ForbiddenWordErrorMessageConstant::getFitErrorMessage("REPLACE_KEYWORD"));
                }
            }

            if( $keyword_type == ForbiddenWordConstant::KEYWORD_DELETE ){
                $replace_keyword = "";
            }

            $hadObj = ForbiddenWordData::where("target_keyword", $target_keyword)->first();
            if( $hadObj != null ){
                $errMsg = ForbiddenWordErrorMessageConstant::getFitErrorMessage("ALREADY_TARGET_KEYWORD") . "\r\n";

                if( $hadObj->keyword_type == ForbiddenWordConstant::KEYWORD_DELETE ){
                    $errMsg .= $target_keyword . " -> " . ForbiddenWordConstant::KEYWORD_STATUS[ForbiddenWordConstant::KEYWORD_DELETE];
                } else {
                    $errMsg .= $target_keyword . " -> " . $hadObj->replace_keyword;
                }
                throw new Exception($errMsg);
            }

            ForbiddenWordData::create([
                "keyword_type"    => $keyword_type,
                "target_keyword"  => $target_keyword,
                "replace_keyword" => $replace_keyword,
                "apply_type"      => $apply_type,
            ]);

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * forbidden_word_datas update
     *
     * @param array $params
     * @return array
     */
    public function update(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $id              = $params["id"];
            $keyword_type    = $params["keyword_type"];
            $target_keyword  = $params["target_keyword"];
            $replace_keyword = $params["replace_keyword"];
            $apply_type      = $params["apply_type"];

            if( $keyword_type == ForbiddenWordConstant::KEYWORD_REPLACE ){
                if( empty($replace_keyword) ){
                    throw new Exception(ForbiddenWordErrorMessageConstant::getFitErrorMessage("REPLACE_KEYWORD"));
                }
            }

            if( $keyword_type == ForbiddenWordConstant::KEYWORD_DELETE ){
                $replace_keyword = "";
            }

            $hadObj = ForbiddenWordData::where("target_keyword", $target_keyword)
            ->where("id", "!=", $id)
            ->first();

            if( $hadObj != null ){
                $errMsg = ForbiddenWordErrorMessageConstant::getFitErrorMessage("ALREADY_TARGET_KEYWORD") . "\r\n";

                if( $hadObj->keyword_type == ForbiddenWordConstant::KEYWORD_DELETE ){
                    $errMsg .= $target_keyword . " -> " . ForbiddenWordConstant::KEYWORD_STATUS[ForbiddenWordConstant::KEYWORD_DELETE];
                } else {
                    $errMsg .= $target_keyword . " -> " . $hadObj->replace_keyword;
                }
                throw new Exception($errMsg);
            }

            ForbiddenWordData::where("id", $id)->update([
                "keyword_type"    => $keyword_type,
                "target_keyword"  => $target_keyword,
                "replace_keyword" => $replace_keyword,
                "apply_type"      => $apply_type,
            ]);

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * forbidden_word_datas delete
     *
     * @param array $ids
     * @return array
     */
    public function delete(array $ids): array
    {
        $returnMsg = $this->returnMsg;
        try {
            ForbiddenWordData::whereIn("id", $ids)->delete();

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * forbidden_notice_word_datas get
     *
     * @param int $id
     * @return array
     */
    public function getNotice(int $id): array
    {
        $returnMsg = $this->returnMsg;
        try {
            
            $obj = ForbiddenNoticeWordData::where("id", $id)->first();

            if( $obj != null ){
                $returnMsg = helpers_success_message($obj);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * forbidden_notice_word_datas create
     *
     * @param array $params
     * @return array
     */
    public function createNotice(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $keyword_type    = $params["keyword_type"];
            $target_keyword  = $params["target_keyword"];
            $replace_keyword = $params["replace_keyword"];
            $apply_type      = $params["apply_type"];

            if( $keyword_type == ForbiddenWordConstant::KEYWORD_REPLACE ){
                if( empty($replace_keyword) ){
                    throw new Exception(ForbiddenWordErrorMessageConstant::getFitErrorMessage("REPLACE_KEYWORD"));
                }
            }

            if( $keyword_type == ForbiddenWordConstant::KEYWORD_DELETE ){
                $replace_keyword = "";
            }

            $hadObj = ForbiddenNoticeWordData::where("target_keyword", $target_keyword)->first();
            if( $hadObj != null ){
                $errMsg = ForbiddenWordErrorMessageConstant::getFitErrorMessage("ALREADY_TARGET_KEYWORD") . "\r\n";

                if( $hadObj->keyword_type == ForbiddenWordConstant::KEYWORD_DELETE ){
                    $errMsg .= $target_keyword . " -> " . ForbiddenWordConstant::KEYWORD_STATUS[ForbiddenWordConstant::KEYWORD_DELETE];
                } else {
                    $errMsg .= $target_keyword . " -> " . $hadObj->replace_keyword;
                }
                throw new Exception($errMsg);
            }

            ForbiddenNoticeWordData::create([
                "keyword_type"    => $keyword_type,
                "target_keyword"  => $target_keyword,
                "replace_keyword" => $replace_keyword,
                "apply_type"      => $apply_type,
            ]);

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * forbidden_notice_word_datas update
     *
     * @param array $params
     * @return array
     */
    public function updateNotice(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $id              = $params["id"];
            $keyword_type    = $params["keyword_type"];
            $target_keyword  = $params["target_keyword"];
            $replace_keyword = $params["replace_keyword"];
            $apply_type      = $params["apply_type"];

            if( $keyword_type == ForbiddenWordConstant::KEYWORD_REPLACE ){
                if( empty($replace_keyword) ){
                    throw new Exception(ForbiddenWordErrorMessageConstant::getFitErrorMessage("REPLACE_KEYWORD"));
                }
            }

            if( $keyword_type == ForbiddenWordConstant::KEYWORD_DELETE ){
                $replace_keyword = "";
            }

            $hadObj = ForbiddenNoticeWordData::where("target_keyword", $target_keyword)
            ->where("id", "!=", $id)
            ->first();

            if( $hadObj != null ){
                $errMsg = ForbiddenWordErrorMessageConstant::getFitErrorMessage("ALREADY_TARGET_KEYWORD") . "\r\n";

                if( $hadObj->keyword_type == ForbiddenWordConstant::KEYWORD_DELETE ){
                    $errMsg .= $target_keyword . " -> " . ForbiddenWordConstant::KEYWORD_STATUS[ForbiddenWordConstant::KEYWORD_DELETE];
                } else {
                    $errMsg .= $target_keyword . " -> " . $hadObj->replace_keyword;
                }
                throw new Exception($errMsg);
            }

            ForbiddenNoticeWordData::where("id", $id)->update([
                "keyword_type"    => $keyword_type,
                "target_keyword"  => $target_keyword,
                "replace_keyword" => $replace_keyword,
                "apply_type"      => $apply_type,
            ]);

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * forbidden_notice_word_datas delete
     *
     * @param array $ids
     * @return array
     */
    public function deleteNotice(array $ids): array
    {
        $returnMsg = $this->returnMsg;
        try {
            ForbiddenNoticeWordData::whereIn("id", $ids)->delete();

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }
}
