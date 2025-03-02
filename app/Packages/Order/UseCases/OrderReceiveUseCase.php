<?php

namespace App\Packages\Order\UseCases;

use App\Packages\Order\Domains\ValueObjects\Order;
use App\Packages\Order\Domains\OrderGetterInterface;
use App\Packages\Order\Domains\OrderRepositoryInterface;

use Illuminate\Support\Facades\Log;

class OrderReceiveUseCase
{
    // private OrderRepositoryInterface $orderGetter;

    public function __construct(
        private readonly OrderGetterInterface $orderGetter,
        private readonly OrderRepositoryInterface $orderRepository
    ) {
    }

    public function execute(): void
    {
        $orders = $this->orderGetter->getRecentOrders();
        // $dividableOrders = new DividableOrderList();

        foreach ($orders as $order) {
            // $this->processOrder($order, $dividableOrders);
            $this->processOrder($order);
        }

        // $this->processDividableOrders($dividableOrders);
    }

    // private function processOrder(Order $order, DividableOrderList $dividableOrders): void
    private function processOrder(Order $order): void
    {
        // echo sprintf("注文: %s %s %s\n", $order->getOrderId(), $order->getStatus(), $order->getOrderedAt());
        Log::channel('batch')->info(var_export($order, true));
        echo "----------------------------------------\n";
        echo sprintf("注文: %s ステータス: %s 注文日時: %s\n", $order->getOrderId(), $order->getStatus(), $order->getOrderedAt()->format('Y-m-d H:i:s'));
        echo sprintf("注文合計金額: %s (送料: %s)\n", $order->getTotalAmountWithTax(), $order->getShippingFee()->getPriceWithTax());


        // 保留チェック
        if ($order->isPending()) {
            Log::channel('batch')->info(sprintf("保留注文: %s\n", $order->getOrderId()->getValue()));
            echo sprintf("保留注文: %s\n", $order->getOrderId()->getValue());
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
        } catch (\Exception $e) {
            echo sprintf("注文保存失敗: %s エラー: %s\n", 
                $order->getOrderId()->getValue(), 
                $e->getMessage()
            );
        }
    }

}