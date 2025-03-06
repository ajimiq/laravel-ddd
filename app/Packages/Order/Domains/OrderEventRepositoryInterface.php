<?php

namespace App\Packages\Order\Domains;

use App\Packages\Order\Domains\Events\OrderCancelledEvent;

interface OrderEventRepositoryInterface
{
    /**
     * キャンセルイベントを保存
     */
    public function saveOrderCancelledEvent(OrderCancelledEvent $event): void;

    /**
     * 注文のイベント履歴を取得
     * 
     * @param string $orderId
     * @return array<array{
     *   id: int,
     *   order_id: string,
     *   event_type: string,
     *   event_data: array{
     *     status: string,
     *     canceled_at: string,
     *     cancel_reason: string,
     *   },
     *   triggered_by: string,
     *   occurred_at: string
     * }>
     */
    public function getOrderEvents(string $orderId): array;

    /**
     * イベントを保存
     * 
     * @param string $orderId
     * @param string $eventType
     * @param array $eventData
     * @param string $triggeredBy
     */
    public function saveEvent(
        string $orderId,
        string $eventType,
        array $eventData,
        string $triggeredBy
    ): void;
}