<?php

namespace App\Packages\Order\Domains;

use App\Packages\Order\Domains\Entities\Order;
use App\Packages\Order\Domains\Entities\Orders;
use App\Packages\Order\Domains\ValueObjects\OrderId;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * 検索条件付きの注文一覧を取得
     *
     * @param array $searchParams
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchOrders(array $searchParams, int $perPage): LengthAwarePaginator;

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
