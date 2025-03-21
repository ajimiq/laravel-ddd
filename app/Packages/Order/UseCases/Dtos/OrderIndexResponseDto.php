<?php

namespace App\Packages\Order\UseCases\Dtos;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 注文一覧の結果DTO
 */
class OrderIndexResponseDto
{
    /**
     * @param LengthAwarePaginator $orders 注文一覧
     * @param array<string, string> $statuses ステータス一覧
     * @param array{
     *   status: ?string,
     *   ordered_from: ?string,
     *   ordered_to: ?string
     * } $search 検索条件
     */
    public function __construct(
        private readonly LengthAwarePaginator $orders,
        private readonly array $statuses,
        private readonly array $search
    ) {
    }

    /**
     * 注文一覧を取得
     *
     * @return LengthAwarePaginator
     */
    public function getOrders(): LengthAwarePaginator
    {
        return $this->orders;
    }

    /**
     * ステータス一覧を取得
     *
     * @return array<string, string>
     */
    public function getStatuses(): array
    {
        return $this->statuses;
    }

    /**
     * 検索条件を取得
     *
     * @return array{
     *   status: ?string,
     *   ordered_from: ?string,
     *   ordered_to: ?string
     * }
     */
    public function getSearch(): array
    {
        return $this->search;
    }

    /**
     * ビューに渡すデータを取得
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'orders' => $this->orders,
            'statuses' => $this->statuses,
            'search' => $this->search,
        ];
    }
}
