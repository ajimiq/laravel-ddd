<?php

namespace App\Packages\Order\Domains;

use App\Packages\Order\Domains\Entities\Orders;

interface OrderGetterInterface
{
    /**
     * 注文一覧を取得
     *
     * @param int $fromDays 取得開始日（n日前）
     * @param int $toDays 取得終了日（n日前）
     * @param int $limit 取得件数
     * @return Orders
     */
    public function getOrders(int $fromDays = 30, int $toDays = 0, int $limit = 10): Orders;
}
