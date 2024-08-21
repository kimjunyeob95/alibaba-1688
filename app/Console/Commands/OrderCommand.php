<?php
namespace App\Console\Commands;

use App\Services\Order\OrderService;
use App\Services\Order\OrderW1;
use Illuminate\Console\Command;

class OrderCommand extends Command
{
    protected $signature   = 'order_command {--func=} {--orderids=}';
    protected $description = 'order command';

    protected OrderService $orderService;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $func = $this->option('func');

        if( !$func ) return null;

        $this->orderService = new OrderService(app(OrderW1::class));
        switch ($func) {
            /**
             * W -> WApp 주문 업데이트
             * php artisan order_command --func=orderUpdate --orderids=2262601849864135493
             */
            case 'orderUpdate':
                $orderIds = explode(",", $this->option('orderids'));
                if (!empty($orderIds)) {
                    $this->orderService->orderUpdate($orderIds);
                }
                break;
            /**
             * WApp 주문 배치 업데이트
             * php artisan order_command --func=orderBatchUpdate
             */
            case 'orderBatchUpdate':
                $this->orderService->orderBatchUpdate();
                break;
            default:
                break;
        }
    }
}
