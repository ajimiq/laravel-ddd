<?php

namespace App\Packages\Order\Domains\Entities;

use Iterator;
use Countable;
use ArrayAccess;
use App\Packages\Order\Domains\Entities\Order;

class Orders implements Iterator, Countable, ArrayAccess
{
    /**
     * @var array<Order>
     */
    private array $orders = [];
    
    private int $position = 0;

    public function __construct(array $orders = [])
    {
        $this->orders = $orders;
    }

    /**
     * 注文を追加
     */
    public function add(Order $order): void
    {
        $this->orders[] = $order;
    }

    /**
     * Iterator インターフェースの実装
     */
    public function current(): Order
    {
        return $this->orders[$this->position];
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
        return isset($this->orders[$this->position]);
    }

    /**
     * Countable インターフェースの実装
     */
    public function count(): int
    {
        return count($this->orders);
    }

    /**
     * ArrayAccess インターフェースの実装
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->orders[] = $value;
        } else {
            $this->orders[$offset] = $value;
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->orders[$offset]);
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->orders[$offset]);
    }

    public function offsetGet(mixed $offset): ?Order
    {
        return isset($this->orders[$offset]) ? $this->orders[$offset] : null;
    }

    /**
     * 配列に変換
     *
     * @return array<Order>
     */
    public function toArray(): array
    {
        return $this->orders;
    }

    /**
     * 空かどうかを確認
     */
    public function isEmpty(): bool
    {
        return empty($this->orders);
    }
} 