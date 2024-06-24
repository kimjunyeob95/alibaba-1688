<?php

namespace App\Services\Collect;

use App\Abstracts\CollectAbstract;

class CollectW1 extends CollectAbstract
{
    protected array $returnMsg;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }

}
