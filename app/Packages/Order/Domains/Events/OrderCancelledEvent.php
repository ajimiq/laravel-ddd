<?php

namespace App\Packages\Order\Domains\Events;

use App\Packages\Order\Domains\ValueObjects\OrderId;
use DateTimeImmutable;

class OrderCancelledEvent
{
    public function __construct(
        private readonly OrderId $orderId,
        private readonly array $eventData,
        private readonly string $cancelReason,
        private readonly string $cancelledAt,
        private readonly DateTimeImmutable $occurredAt
    ) {
    }

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    public function getEventData(): array
    {
        return $this->eventData;
    }

    public function getCancelReason(): string
    {
        return $this->cancelReason;
    }

    public function getCancelledAt(): string
    {
        return $this->cancelledAt;
    }

    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function toArray(): array
    {
        return [
            'order_id' => $this->orderId->getValue(),
            'event_data' => $this->eventData,
            'cancel_reason' => $this->cancelReason,
            'cancelled_at' => $this->cancelledAt,
            'occurred_at' => $this->occurredAt->format('Y-m-d H:i:s'),
        ];
    }
} 