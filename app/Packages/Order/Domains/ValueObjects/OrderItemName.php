<?php

namespace App\Packages\Order\Domains\ValueObjects;

use InvalidArgumentException;

class OrderItemName
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Item name cannot be empty');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}