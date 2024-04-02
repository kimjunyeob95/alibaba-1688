<?php

namespace App\Http\Controllers;

use App\Services\MallApiService;
use Illuminate\Http\Request;

class MallController extends Controller
{
    private Request $request;
    private MallApiService $mallApiService;

    function __construct(Request $request, MallApiService $mallApiService)
    {
        $this->request        = $request;
        $this->mallApiService = $mallApiService;
    }

    public function productRegist()
    {
        $this->mallApiService->productRegist();
    }
}
