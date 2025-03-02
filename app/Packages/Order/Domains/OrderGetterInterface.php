<?php

namespace App\Packages\Order\Domains;

use App\Packages\Order\Domains\ValueObjects\Orders;

interface OrderGetterInterface
{
    /**
     * 最近の注文一覧を取得
     *
     * @return Orders
     */
    public function getRecentOrders(): Orders;

    // /**
    //  * 分割された注文を取得
    //  *
    //  * @param string $orderId
    //  * @return Order
    //  */
    // public function getDividedOrders(string $orderId): Order;

    // /**
    //  * 次の注文を取得
    //  *
    //  * @return Order
    //  */
    // public function next(): Order;
} 