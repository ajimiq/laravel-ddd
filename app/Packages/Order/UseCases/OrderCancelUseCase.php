<?php

namespace App\Packages\Order\UseCases;

use App\Models\Order;
use App\Packages\Order\Domains\Events\OrderCancelledEvent;
use App\Packages\Order\Domains\OrderEventRepositoryInterface;
use App\Packages\Order\Domains\ValueObjects\OrderId;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

class OrderCancelUseCase
{
    public function __construct(
        private readonly OrderEventRepositoryInterface $orderEventRepository
    ) {
    }

    public function execute(string $orderId, string $cancelReason, string $cancelledAt): void
    {
        DB::transaction(function () use ($orderId, $cancelReason, $cancelledAt) {
            // 注文を取得
            $order = Order::lockForUpdate()->findOrFail($orderId);

            // キャンセル可能かチェック
            if ($order->status !== 'unshipped') {
                throw new \RuntimeException('この注文はキャンセルできません。');
            }

            // 注文をキャンセル
            $order->update([
                'status' => 'cancelled',
                'canceled_at' => now(),
                'cancel_reason' => $cancelReason,
            ]);

            // 更新後の状態を取得するため、モデルを再取得
            $order->refresh();

            // イベントを記録
            $event = new OrderCancelledEvent(
                new OrderId($orderId),
                $order->toArray(),
                $order->cancel_reason,
                $order->canceled_at,
                new DateTimeImmutable()
            );
            $this->orderEventRepository->saveOrderCancelledEvent($event);
        });
    }
} 