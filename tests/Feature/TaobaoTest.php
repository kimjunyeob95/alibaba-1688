<?php

namespace Tests\Feature;

use App\Packages\Taobao\IopClient;
use App\Packages\Taobao\IopRequest;
use Tests\TestCase;

class TaobaoTest extends TestCase
{

    # php artisan test --filter testTaobaoGetProduct
    /** 상품 리스트 조회 */
    public function testTaobaoGetProduct()
    {
        $c       = new IopClient();
        $request = new IopRequest('/product/spus/get');
        $request->addApiParam('sort_field','modified');
        $request->addApiParam('page_size','20');
        $request->addApiParam('end_modified_time','1608877283000');
        $request->addApiParam('status','NORMAL');
        $request->addApiParam('item_id','44464342434533322');
        $request->addApiParam('spus_get_type','PAGINATION');
        $request->addApiParam('sort_type','DESC');
        $request->addApiParam('page_no','1');
        $request->addApiParam('start_modified_time','1607667683000');
        $rs = $c->execute($request);
        dd($rs);
    }
}
