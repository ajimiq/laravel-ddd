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
     *
     * @param Order $order
     * @return void
     */
    public function add(Order $order): void
    {
        $this->orders[] = $order;
    }

    /**
     * Iterator インターフェースの実装
     *
     * @return Order
     */
    public function current(): Order
    {
        return $this->orders[$this->position];
    }

    /**
     * Iterator インターフェースの実装
     *
     * @return int
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Iterator インターフェースの実装
     *
     * @return void
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * Iterator インターフェースの実装
     *
     * @return void
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Iterator インターフェースの実装
     *
     * @return bool
     */
    public function valid(): bool
    {
        return isset($this->orders[$this->position]);
    }

    /**
     * Countable インターフェースの実装
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->orders);
    }

    /**
     * ArrayAccess インターフェースの実装
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->orders[] = $value;
        } else {
            $this->orders[$offset] = $value;
        }
    }

    /**
     * ArrayAccess インターフェースの実装
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->orders[$offset]);
    }

    /**
     * ArrayAccess インターフェースの実装
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->orders[$offset]);
    }

    /**
     * ArrayAccess インターフェースの実装
     *
     * @param mixed $offset
     * @return ?Order
     */
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
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->orders);
    }
}
