<?php

namespace App\Packages\Order\Domains;

use App\Packages\Order\Domains\ValueObjects\Order;
use App\Packages\Order\Domains\ValueObjects\Orders;
use App\Packages\Order\Domains\ValueObjects\OrderId;

interface OrderRepositoryInterface
{
    /**
     * 注文IDから注文を取得
     *
     * @param OrderId $orderId
     * @return Order|null
     */
    public function find(OrderId $orderId): ?Order;

    /**
     * 最近の注文一覧を取得
     *
     * @return Orders
     */
    public function getRecentOrders(): Orders;

    /**
     * 分割された注文を取得
     *
     * @param OrderId $orderId
     * @return Order
     */
    public function getDividedOrders(OrderId $orderId): Order;

    /**
     * 注文を保存
     *
     * @param Order $order
     * @return void
     */
    public function save(Order $order): void;

    /**
     * 注文を更新
     *
     * @param Order $order
     * @return void
     */
    public function update(Order $order): void;

    /**
     * 注文を削除
     *
     * @param OrderId $orderId
     * @return void
     */
    public function delete(OrderId $orderId): void;
} 