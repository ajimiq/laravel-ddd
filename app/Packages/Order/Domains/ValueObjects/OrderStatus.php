<?php

namespace App\Packages\Order\Domains\ValueObjects;

use InvalidArgumentException;

class OrderStatus
{
    private const VALID_STATUSES = [
        'pending',
        'unshipped',
        'completed',
        'failed',
        'cancelled',
    ];

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException('Invalid Order Status');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isPending(): bool
    {
        return $this->value === 'pending';
    }

    public function isFailure(): bool
    {
        return $this->value === 'failed';
    }

    public function __toString(): string
    {
        return $this->value;
    }
}