<?php

namespace App\Packages\Order\UseCases;

use App\Packages\Order\Domains\Entities\Order;
use App\Packages\Order\Domains\OrderGetterInterface;
use App\Packages\Order\Domains\OrderRepositoryInterface;
use App\Packages\Order\UseCases\Dtos\OrderReceiveRequestDto;
use App\Packages\Order\UseCases\Dtos\OrderReceiveResponseDto;
use Illuminate\Support\Facades\Log;

class OrderReceiveUseCase
{
    // private OrderRepositoryInterface $orderGetter;

    public function __construct(
        private readonly OrderGetterInterface $orderGetter,
        private readonly OrderRepositoryInterface $orderRepository
    ) {
    }

    /**
     * 注文情報を取得して処理する
     *
     * @param OrderReceiveRequestDto $requestDto
     * @return OrderReceiveResponseDto
     */
    public function execute(OrderReceiveRequestDto $requestDto): OrderReceiveResponseDto
    {
        // 注文情報を取得
        $orders = $this->orderGetter->getOrders(
            $requestDto->getFromDays(),
            $requestDto->getToDays(),
            $requestDto->getLimit()
        );

        $processedCount = 0;
        $successCount = 0;
        $errorCount = 0;
        $errorMessages = [];

        foreach ($orders as $order) {
            $processedCount++;
            $result = $this->processOrder($order);
            if ($result['success']) {
                $successCount++;
            } else {
                $errorCount++;
                $errorMessages[] = $result['message'];
            }
        }

        // 処理結果をDTOで返す
        return new OrderReceiveResponseDto(
            $processedCount,
            $successCount,
            $errorCount,
            $errorMessages
        );
    }

    /**
     * 注文を処理する
     *
     * @param Order $order
     * @return array{success: bool, message: string}
     */
    private function processOrder(Order $order): array
    {
        Log::channel('batch')->info(var_export($order, true));
        echo "----------------------------------------\n";
        echo sprintf("注文: %s ステータス: %s 注文日時: %s\n", $order->getOrderId(), $order->getStatus(), $order->getOrderedAt()->format('Y-m-d H:i:s'));
        foreach ($order->getOrderItems() as $item) {
            echo sprintf("商品: %s 金額: %s 数量: %s\n", $item->getName(), $item->getPrice(), $item->getQuantity());
        }
        echo sprintf("注文合計金額: %s (送料: %s)\n", $order->getTotalAmountWithTax(), $order->getShippingFee()->getPriceWithTax());

        // 保留チェック
        if ($order->isPending()) {
            Log::channel('batch')->info(sprintf("保留注文: %s\n", $order->getOrderId()->getValue()));
            // echo sprintf("保留注文: %s\n", $order->getOrderId()->getValue());
            // return;
        }

        // 不正注文チェック
        if ($order->isFailure()) {
            Log::channel('batch')->info(sprintf("不正注文: %s\n", $order->getOrderId()->getValue()));
            echo sprintf("不正注文: %s\n", $order->getOrderId()->getValue());
            // return;
        }

        try {
            // 注文を保存
            $this->orderRepository->save($order);
            echo sprintf("注文保存成功: %s\n", $order->getOrderId()->getValue());
            return [
                'success' => true,
                'message' => sprintf("注文保存成功: %s", $order->getOrderId()->getValue())
            ];
        } catch (\Exception $e) {
            $errorMessage = sprintf(
                "注文保存失敗: %s エラー: %s",
                $order->getOrderId()->getValue(),
                $e->getMessage()
            );
            echo $errorMessage . "\n";
            return [
                'success' => false,
                'message' => $errorMessage
            ];
        }
    }
}
