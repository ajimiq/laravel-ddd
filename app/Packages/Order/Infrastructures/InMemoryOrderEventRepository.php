<?php

namespace App\Packages\Order\Infrastructures;

use App\Packages\Order\Domains\Events\OrderCancelledEvent;
use App\Packages\Order\Domains\OrderEventRepositoryInterface;

class InMemoryOrderEventRepository implements OrderEventRepositoryInterface
{
    private array $events = [];

    public function saveOrderCancelledEvent(OrderCancelledEvent $event): void
    {
        $this->saveEvent(
            $event->getOrderId()->getValue(),
            'order_cancelled',
            $event->toArray(),
            $event->getCancelledAt()
        );
    }

    public function saveEvent(
        string $orderId,
        string $eventType,
        array $eventData,
        string $triggeredBy
    ): void {
        $this->events[] = [
            'id' => count($this->events) + 1,
            'order_id' => $orderId,
            'event_type' => $eventType,
            'event_data' => $eventData,
            'triggered_by' => $triggeredBy,
            'occurred_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    public function getOrderEvents(string $orderId): array
    {
        return array_filter($this->events, fn($event) => $event['order_id'] === $orderId);
    }

    public function clearEvents(): void
    {
        $this->events = [];
    }
} 