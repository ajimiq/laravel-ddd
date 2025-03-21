<?php

namespace App\Packages\Order\UseCases\Dtos;

use App\Models\Order;

/**
 * 注文詳細の結果DTO
 */
class OrderShowResponseDto
{
    /**
     * @param Order $order 注文
     * @param array<float, array{
     *   tax_rate: float,
     *   subtotal_with_tax: int,
     *   subtotal_without_tax: int,
     *   tax_amount: int
     * }> $taxAmountsByRate 税率ごとの金額
     * @param array<string, string> $statuses ステータス一覧
     */
    public function __construct(
        private readonly Order $order,
        private readonly array $taxAmountsByRate,
        private readonly array $statuses
    ) {
    }

    /**
     * 注文を取得
     *
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * 税率ごとの金額を取得
     *
     * @return array<float, array{
     *   tax_rate: float,
     *   subtotal_with_tax: int,
     *   subtotal_without_tax: int,
     *   tax_amount: int
     * }>
     */
    public function getTaxAmountsByRate(): array
    {
        return $this->taxAmountsByRate;
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
     * ビューに渡すデータを取得
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'order' => $this->order,
            'taxAmountsByRate' => $this->taxAmountsByRate,
            'statuses' => $this->statuses,
        ];
    }
}
