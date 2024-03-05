<?php
namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Save1688AllProducts extends Command
{
    protected $signature   = 'save_1688_all_product {--killpid=}';
    protected $description = '1688 모든 카테고리 상품 수집';

    public function __construct()
    {
        parent::__construct();
    }
    /*
     * 실행 구문
     * 모든 카테고리에 대해서 병렬적으로 프로세스 실행
     * php artisan save_1688_all_product --killpid=false
    */
    public function handle()
    {
        $killpid = $this->option('killpid', 'false');

        $getCategoryObjs = Category::get();
        $processes = [];
        foreach ($getCategoryObjs as $getCategoryObj) {
            $categoryId = $getCategoryObj->category_id;
            $command    = "php artisan save_1688_product --categoryid=" . escapeshellarg($categoryId);

            // 1. 실행 중인 동일 커맨드 찾은 후 PID들에 대해 강제 종료 실행
            $findProcessCommand = "ps aux | grep '".escapeshellcmd($command)."' | grep -v grep | awk '{print $2}'";
            $output = [];
            exec($findProcessCommand, $output);
            if($killpid === "true"){
                $this->killExistingProcesses($output, $categoryId);
            }

            // 2. 커맨드 실행
            $process = Process::fromShellCommandline($command);
            $process->start();
            $processes[] = $process;
        }
        foreach ($processes as $process) {
            $process->wait();
        }
    }

    
    /**
     * @func killExistingProcesses
     * @description 'PID들에 대해 강제 종료 실행'
     * @param array $pids
     * @param $categoryId
     */
    protected function killExistingProcesses(array $pids, $categoryId)
    {
        foreach ($pids as $pid) {
            if (is_numeric($pid)) {
                echo "Gracefully terminating process(categoryId {$categoryId}) with PID: $pid\n";
                exec("kill $pid"); // SIGTERM 신호를 보냄
                sleep(1); // 프로세스가 종료될 시간을 줌
                exec("kill -9 $pid"); // 여전히 실행 중인 경우 강제 종료
            }
        }
    }
}
