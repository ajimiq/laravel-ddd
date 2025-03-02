<?php

namespace App\Console\Commands;

use App\Batch\ImportDataBatch;
use App\Packages\Order\UseCases\OrderReceiveUseCase;
use App\Packages\Order\Infrastructures\TestMallOrderGetter;
use App\Packages\Order\Domains\OrderRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReceiveTestMallOrderBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:receiveTestMallOrderBatch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'テストモールから注文情報を取得する';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        private TestMallOrderGetter $testMallOrderGetter,
        private OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $orderReceiveUseCase = new OrderReceiveUseCase(
            $this->testMallOrderGetter,
            $this->orderRepository
        );
        $orderReceiveUseCase->execute();
    }
}