<?php

namespace App\Packages\Order\Infrastructures;

use App\Packages\Order\Domains\Events\OrderCancelledEvent;
use App\Packages\Order\Domains\OrderEventRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderEventRepository implements OrderEventRepositoryInterface
{
    /**
     * キャンセルイベントを保存
     */
    public function saveOrderCancelledEvent(OrderCancelledEvent $event): void
    {
        $this->saveEvent(
            $event->getOrderId()->getValue(),
            'order_cancelled',
            $event->toArray(),
            $event->getCancelledAt()
        );
    }

    /**
     * イベントを保存
     */
    public function saveEvent(
        string $orderId,
        string $eventType,
        array $eventData,
        string $triggeredBy
    ): void {
        DB::table('order_events')->insert([
            'order_id' => $orderId,
            'event_type' => $eventType,
            'event_data' => json_encode($eventData),
            'triggered_by' => $triggeredBy,
            'occurred_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 注文のイベント履歴を取得
     * 
     * @param string $orderId
     * @return array<array{
     *   id: int,
     *   order_id: string,
     *   event_type: string,
     *   event_data: array{
     *     order_id: string,
     *      status: string,
     *      canceled_at: string,
     *      cancel_reason: string,
     *   },
     *   triggered_by: string,
     *   occurred_at: string
     * }>
     */
    public function getOrderEvents(string $orderId): array
    {
        return DB::table('order_events')
            ->where('order_id', $orderId)
            ->orderBy('occurred_at', 'desc')
            ->get()
            ->map(function ($event) {
                $event->event_data = json_decode($event->event_data, true);
                return (array)$event;
            })
            ->all();
    }
} 