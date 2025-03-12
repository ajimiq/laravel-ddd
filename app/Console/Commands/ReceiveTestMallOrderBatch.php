<?php

namespace App\Console\Commands;

use App\Batch\ImportDataBatch;
use App\Packages\Order\UseCases\OrderReceiveUseCase;
use App\Packages\Order\Infrastructures\TestMallOrderGetter;
use App\Packages\Order\Domains\OrderRepositoryInterface;
use App\Packages\Order\UseCases\Dtos\OrderReceiveRequestDto;
use App\Packages\Order\UseCases\Dtos\OrderReceiveResponseDto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReceiveTestMallOrderBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:receiveTestMallOrderBatch
                            {--from= : 取得開始日（n日前、デフォルト: 30）}
                            {--to= : 取得終了日（n日前、デフォルト: 現在日時）}
                            {--limit= : 取得件数（デフォルト: 10）}';

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
        // オプションから値を取得（デフォルト値を設定）
        $fromDays = $this->option('from') ? (int)$this->option('from') : 30;
        $toDays = $this->option('to') ? (int)$this->option('to') : 0;
        $limit = $this->option('limit') ? (int)$this->option('limit') : 10;

        // パラメータの表示
        $this->info("取得期間: {$fromDays}日前 ～ " . ($toDays === 0 ? "現在" : "{$toDays}日前"));
        $this->info("取得件数: {$limit}件");

        // リクエストDTOの作成
        $requestDto = new OrderReceiveRequestDto($fromDays, $toDays, $limit);

        // UseCaseの実行
        $orderReceiveUseCase = new OrderReceiveUseCase(
            $this->testMallOrderGetter,
            $this->orderRepository
        );
        $responseDto = $orderReceiveUseCase->execute($requestDto);

        // 処理結果の表示
        $this->info($responseDto->getSummary());
        
        if ($responseDto->getErrorCount() > 0) {
            $this->error("エラーが発生しました:");
            foreach ($responseDto->getErrorMessages() as $errorMessage) {
                $this->error("- {$errorMessage}");
            }
            return 1;
        }

        return 0;
    }
}