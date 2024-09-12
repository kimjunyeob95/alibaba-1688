<?php

namespace Tests\Feature;

use App\Packages\Ebay;
use Tests\TestCase;

class EbayTest extends TestCase
{
    # php artisan test --filter testGetCategories
    public function testGetCategories()
    {
        $eBay   = new Ebay();
        $result = $eBay->getCategories();

        $this->assertTrue($result["isSuccess"]);
    }

}