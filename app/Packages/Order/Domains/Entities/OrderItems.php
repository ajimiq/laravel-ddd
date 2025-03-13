<?php

namespace App\Packages\Order\Domains\Entities;

use Iterator;

class OrderItems implements Iterator
{
    private array $items = [];
    private int $position = 0;

    public function __construct(array $items = [])
    {
        $this->items = $items;
        $this->position = 0;
    }

    public function addItem(OrderItem $item): void
    {
        $this->items[] = $item;
    }

    public function current(): OrderItem
    {
        return $this->items[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }

    public function toArray(): array
    {
        return array_map(function (OrderItem $item) {
            return $item->toArray();
        }, $this->items);
    }

    /**
     * 商品小計（税込）を取得
     */
    public function getSubtotalWithTax(): int
    {
        return array_reduce(
            $this->items,
            fn (int $carry, OrderItem $item) => $carry + $item->getSubtotalWithTax(),
            0
        );
    }

    /**
     * 商品小計（税抜）を取得
     */
    public function getSubtotalWithoutTax(): int
    {
        return array_reduce(
            $this->items,
            fn (int $carry, OrderItem $item) => $carry + $item->getSubtotalWithoutTax(),
            0
        );
    }

    /**
     * 商品の税額合計を取得
     */
    public function getTotalTaxAmount(): int
    {
        return $this->getSubtotalWithTax() - $this->getSubtotalWithoutTax();
    }
}