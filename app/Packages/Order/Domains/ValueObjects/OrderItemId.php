<?php

namespace App\Packages\Order\Domains\ValueObjects;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

class OrderItemId
{
    private string $value;

    public function __construct(string $value)
    {
        // if (!Uuid::isValid($value)) {
        //     throw new InvalidArgumentException('Invalid Item ID');
        // }

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